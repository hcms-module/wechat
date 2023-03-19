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
use App\Application\Wechat\Model\WechatPayOrder;
use App\Application\Wechat\Model\WechatPayOrderRefund;
use App\Exception\ErrorException;
use EasyWeChat\Pay\Application;
use EasyWeChat\Pay\Client;
use EasyWeChat\Pay\Message;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\Codec\Json;
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

    protected string $mchid = '';

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

    protected function requestJson(string $uri, array $data = [], string $method = "POST"): array
    {
        try {
            $api = $this->app->getClient();
            if ($api instanceof Client) {
                if ($method == "POST") {
                    $result = $api->postJson($uri, [
                        'json' => $data,
                    ]);
                } else {
                    $result = $api->get($uri);
                }

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
                //微信支付回调事件，最好是通过  getRefundByOutRefundNo 方法获取最终的退款信息
                $this->eventDispatcher->dispatch(new WxpayRefundNotifyEvent($message));
            });

            return $server->serve();
        } catch (Throwable $exception) {
            throw new ErrorException($exception->getMessage());
        }
    }

    /**
     * @param string $out_refund_no
     * @return WechatPayOrderRefund
     * @throws ErrorException
     */
    public function getRefundByOutRefundNo(string $out_refund_no): WechatPayOrderRefund
    {
        $uri = "/v3/refund/domestic/refunds/{$out_refund_no}";
        $res = $this->requestJson($uri, method: "GET");
        $order_refund = WechatPayOrderRefund::firstOrCreate(compact('out_refund_no'));
        $order_refund->transaction_id = $res['transaction_id'] ?? '';
        $order_refund->refund_id = $res['refund_id'] ?? '';
        $order_refund->channel = $res['channel'] ?? '';
        $order_refund->status = $res['status'] ?? '';
        $order_refund->refund_fee = $res['amount']['refund'] ?? 0;
        $order_refund->total = $res['amount']['total'] ?? 0;
        $order_refund->meta_info = Json::encode($res ?: "{}");
        $order_refund->save();

        return $order_refund;
    }

    /**
     * @param string $out_trade_no
     * @param int    $refund_fee
     * @param string $out_refund_no
     * @param string $reason
     * @return WechatPayOrderRefund
     * @throws ErrorException
     */
    public function refund(
        string $out_trade_no,
        int $refund_fee,
        string $out_refund_no = '',
        string $reason = ''
    ): WechatPayOrderRefund {
        $order = $this->getOrderByOutTradeNo($out_trade_no);
        $order_meta = $order->meta_info;
        $notify_url = url('wechat/notify/refund', [], true);
        $uri = "/v3/refund/domestic/refunds";
        if ($out_refund_no === '') {
            $out_refund_no = 'refund_' . $out_trade_no . '_' . rand(1000, 9999);
        }
        $data = [
            'out_trade_no' => $out_trade_no,
            'out_refund_no' => $out_refund_no,
            'notify_url' => $notify_url,
            'amount' => [
                'refund' => $refund_fee,
                'total' => $order->total_fee,
                'currency' => $order_meta['amount']['currency'] ?? 'CNY'
            ],
        ];
        if ($reason !== '') {
            $data['reason'] = $reason;
        }
        $res = $this->requestJson($uri, $data);
        $order_refund = WechatPayOrderRefund::firstOrCreate(compact('out_trade_no', 'out_refund_no'));
        $order_refund->transaction_id = $res['transaction_id'] ?? '';
        $order_refund->refund_id = $res['refund_id'] ?? '';
        $order_refund->channel = $res['channel'] ?? '';
        $order_refund->status = $res['status'] ?? '';
        $order_refund->refund_fee = $res['amount']['refund'] ?? 0;
        $order_refund->total = $res['amount']['total'] ?? 0;
        $order_refund->meta_info = Json::encode($res ?: "{}");
        $order_refund->save();

        return $order_refund;
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
                //微信支付回调事件，建议使用 getOrderByOutTradeNo 查看订单是否支付成功
                $this->eventDispatcher->dispatch(new WxpayPayNotifyEvent($message));
            });

            return $server->serve();
        } catch (Throwable $exception) {
            throw new ErrorException($exception->getMessage());
        }
    }

    /**
     * 获取订单状态
     *
     * @param string $out_trade_no
     * @return WechatPayOrder
     * @throws ErrorException
     */
    function getOrderByOutTradeNo(string $out_trade_no): WechatPayOrder
    {
        $uri = "/v3/pay/transactions/out-trade-no/{$out_trade_no}?mchid=" . $this->getMchid();
        $res = $this->requestJson($uri, method: "GET");
        $mchid = $res['mchid'] ?? '';
        $out_trade_no = $res['out_trade_no'] ?? '';
        $order = WechatPayOrder::firstOrCreate([
            'mchid' => $mchid,
            'out_trade_no' => $out_trade_no,
        ]);
        $order->appid = $res['appid'] ?? '';
        $order->openid = $res['payer']['openid'] ?? '';
        $order->total_fee = $res['amount']['total'] ?? 0;
        $order->trade_state = $res['trade_state'] ?? 'NOTPAY';
        $order->trade_state_desc = $res['trade_state_desc'] ?? '';
        $order->trade_type = $res['trade_type'] ?? '';
        $order->transaction_id = $res['transaction_id'] ?? '';
        $order->meta_info = Json::encode($res);
        $order->save();

        return $order;
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
     * @param string $appid
     * @param string $openid
     * @param string $out_trade_no
     * @param int    $total_fee
     * @param string $description
     * @return string
     * @throws ErrorException
     */
    public function unifyJsApi(
        string $appid,
        string $openid,
        string $out_trade_no,
        int $total_fee,
        string $description = ''
    ): string {
        try {
            $notify_url = url('wechat/notify/index', [], true);
            $data = [
                'appid' => $appid,
                'mchid' => $this->getMchid(),
                'description' => $description,
                'out_trade_no' => $out_trade_no,
                'notify_url' => $notify_url,
                'amount' => [
                    'total' => $total_fee,
                ],
                'payer' => [
                    'openid' => $openid,
                ],
            ];
            $result = $this->requestJson('/v3/pay/transactions/jsapi', $data);
            $prepay_id = $result['prepay_id'] ?? "";
            if ($prepay_id == '') {
                throw new ErrorException('创建支付订单失败2：' . $result['err_code_des'] ?? "");
            }
            //创建成功，存入数据。
            $pay_order = WechatPayOrder::firstOrCreate([
                'appid' => $appid,
                'mchid' => $data['mchid'] ?? '',
                'out_trade_no' => $out_trade_no
            ]);
            $pay_order->openid = $openid;
            $pay_order->total_fee = $total_fee;
            $pay_order->notify_url = $notify_url;
            $pay_order->description = $description;
            $pay_order->prepay_id = $prepay_id;
            $pay_order->save();

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

    /**
     * @return string
     */
    public function getMchid(): string
    {
        if ($this->mchid == '') {
            $this->mchid = $this->app->getConfig()
                    ->get('mch_id') . '';
        }

        return $this->mchid;
    }

    /**
     * @param string $mchid
     * @return $this
     */
    public function setMchid(string $mchid): self
    {
        $this->mchid = $mchid;

        return $this;
    }
}