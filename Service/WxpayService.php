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
use EasyWeChat\Pay\Client;
use EasyWeChat\Pay\Message;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Logger\LoggerFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\ClientException;
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
        } catch (Throwable $exception) {
            throw new ErrorException($exception->getMessage());
        }
    }

    protected function postJson(string $uri, array $data): array
    {
        try {
            $api = $this->app->getClient();
            if ($api instanceof Client) {
                $result = $api->postJson($uri, [
                    'json' => $data,
                ]);

                return $result->toArray();
            } else {
                throw new ErrorException('获取接口对象错误');
            }
        } catch (ClientException $exception) {
            throw new ErrorException("连接错误，请检查支付配置是否正确。" . $exception->getMessage());
        } catch (Throwable $exception) {
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
    public function refundNotify(ServerRequestInterface $request): ResponseInterface
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
                    //TODO 退款成功回调
                }
            });

            return $server->serve();
        } catch (Throwable $exception) {
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
        } catch (Throwable $exception) {
            throw new ErrorException($exception->getMessage());
        }
    }

    /**
     * 获取小程序支付配置
     *
     * @param string $app_id
     * @param string $open_id
     * @param string $out_trade_no
     * @param int    $total_fee
     * @param string $description
     * @return array
     * @throws ErrorException
     */
    public function getMiniAppConfig(
        string $app_id,
        string $open_id,
        string $out_trade_no,
        int $total_fee,
        string $description = ''
    ): array {
        try {
            $prepay_id = $this->unifyJsApi($app_id, $open_id, $out_trade_no, $total_fee, $description);
            $utils = $this->app->getUtils();

            return $utils->buildMiniAppConfig($prepay_id, $app_id);
        } catch (Throwable $exception) {
            throw new ErrorException($exception->getMessage());
        }
    }

    /**
     * 统一下单接口
     *
     * @param string $app_id
     * @param string $open_id
     * @param string $out_trade_no
     * @param int    $total_fee
     * @param string $description
     * @return string
     * @throws ErrorException
     */
    public function unifyJsApi(
        string $app_id,
        string $open_id,
        string $out_trade_no,
        int $total_fee,
        string $description = ''
    ): string {
        try {
            $notify_url = url('wechat/notify/index', [], true);
            $data = [
                'appid' => $app_id,
                'mchid' => $this->app->getConfig()
                    ->get('mch_id'),
                'description' => $description,
                'out_trade_no' => $out_trade_no,
                'notify_url' => $notify_url,
                'amount' => [
                    'total' => $total_fee,
                ],
                'payer' => [
                    'openid' => $open_id,
                ],
            ];
            $result = $this->postJson('/v3/pay/transactions/jsapi', $data);
            $prepay_id = $result['prepay_id'] ?? "";
            if ($prepay_id == '') {
                throw new ErrorException('创建支付订单失败2：' . $result['err_code_des'] ?? "");
            }

            return $prepay_id;
        } catch (Throwable $exception) {
            throw new ErrorException('创建支付订单失败3：' . $exception->getMessage());
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