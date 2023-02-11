<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/11/2 15:07
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service;

use App\Application\Wechat\Event\WxpayPayNotifyEvent;
use App\Application\Wechat\Event\WxpayRefundNotifyEvent;
use App\Exception\ErrorException;
use EasyWeChat\Pay\Application;
use EasyWeChat\Pay\Message;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Logger\LoggerFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class WxpayService
{
    #[Inject]
    protected WechatSetting $setting;
    protected Application $app;

    #[Inject]
    protected LoggerFactory $loggerFactory;
    protected LoggerInterface $logger;

    #[Inject]
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct()
    {
        $setting = $this->setting->getWxpaySetting();
        $config = [
            // 必要配置
            'mch_id' => $setting['pay_mch_id'] ?? '',
            'secret_key' => $setting['pay_secret_key'] ?? '',   // API v3 密钥 (注意: 是v3密钥 是v3密钥 是v3密钥)
            'v2_secret_key' => $setting['pay_v2_secret_key'] ?? '',
            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            'certificate' => $this->getTempFilePath($setting['pay_cert_path'] ?? '', 'cert.pem'), // XXX: 绝对路径！！！！
            'private_key' => $this->getTempFilePath($setting['pay_key_path'] ?? '', 'key.pem'),      // XXX: 绝对路径！！！！
        ];
        $this->logger = $this->loggerFactory->get('notify', 'request');
        try {
            $this->app = new Application($config);
        } catch (\Throwable $exception) {
            throw new ErrorException($exception->getMessage());
        }
    }


    /**
     * 退款回调
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ErrorException
     */
    public function refund(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $server = $this->app->setRequest($request)
                ->getServer();

            $server->handleRefunded(function (Message $message) {
                $message = $message->toArray();
                $this->logger->info('get refund notify', $message->toArray());
                //微信支付回调事件
                $this->eventDispatcher->dispatch(new WxpayRefundNotifyEvent($message));
                if ($message['return_code'] === 'SUCCESS' && $message['result_code'] === 'SUCCESS') {
                    $out_trade_no = $message['out_trade_no'] ?? "";
                    //TODO 支付成功回调
                }
            });

            return $server->serve();
        } catch (\Throwable $exception) {
            throw new ErrorException($exception->getMessage());
        }
    }

    /**
     * 微信支付回调
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ErrorException
     */
    public function notify(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $server = $this->app->setRequest($request)
                ->getServer();

            $server->handlePaid(function (Message $message) {
                $message = $message->toArray();
                $this->logger->info('get notify', $message->toArray());
                //微信支付回调事件
                $this->eventDispatcher->dispatch(new WxpayPayNotifyEvent($message));
                if ($message['return_code'] === 'SUCCESS' && $message['result_code'] === 'SUCCESS') {
                    $out_trade_no = $message['out_trade_no'] ?? "";
                    //TODO 支付成功回调
                }
            });

            return $server->serve();
        } catch (\Throwable $exception) {
            throw new ErrorException($exception->getMessage());
        }
    }

    /**
     *  获取小程序支付配置
     *
     * @param string $open_id
     * @param string $out_trade_no
     * @param int    $total_fee
     * @param string $body
     * @return array|string
     * @throws ErrorException
     */
    public function getJssdkPay(
        string $open_id,
        string $out_trade_no,
        int $total_fee,
        string $body = ''
    ) {
        $unify_info = $this->unify($open_id, $out_trade_no, $total_fee, $body);
        $prepay_id = $unify_info['prepay_id'] ?? '';

        return $this->app->jssdk->bridgeConfig($prepay_id, false);
    }

    /**
     * 统一下单接口
     *
     * @param string $open_id
     * @param string $out_trade_no
     * @param int    $total_fee
     * @param string $body
     * @param string $trade_type
     * @return array
     * @throws ErrorException
     */
    public function unify(
        string $open_id,
        string $out_trade_no,
        int $total_fee,
        string $body = '',
        string $trade_type = 'JSAPI'
    ): array {
        try {
            $notify_url = url('wechat/notify/index', [], true);
            $order_data = [
                'body' => $body,
                'out_trade_no' => $out_trade_no,
                'total_fee' => $total_fee,
                'trade_type' => $trade_type, // 请对应换成你的支付方式对应的值类型
                'openid' => $open_id,
                'notify_url' => $notify_url
            ];
            $result = $this->app->order->unify($order_data);
            $this->logger->info('wxpay unify ', $order_data);
            $return_code = $result['return_code'] ?? 'FAIL';
            if ($return_code != 'SUCCESS') {
                throw new ErrorException('创建支付订单失败：' . $result['return_msg'] ?? "");
            }

            $prepay_id = $result['prepay_id'] ?? "";
            if ($prepay_id == '') {
                throw new ErrorException('创建支付订单失败：' . $result['err_code_des'] ?? "");
            }

            return $result;
        } catch (\Throwable $exception) {
            throw new ErrorException('创建支付订单失败：' . $exception->getMessage());
        }
    }

    /**
     * 获取证书路径
     *
     * @param string $content
     * @param string $file_name
     * @return string
     */
    private function getTempFilePath(string $content, string $file_name): string
    {
        $file_path_dir = BASE_PATH . '/runtime/temp/cert/';
        if (!is_dir($file_path_dir)) {
            mkdir($file_path_dir, 0700, true);
        }
        $file_path = $file_path_dir . $file_name;
        if (!file_exists($file_path)) {
            file_put_contents($file_path, $content);
        }

        return $file_path;
    }
}