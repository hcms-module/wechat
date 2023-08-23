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
use App\Application\Wechat\Model\WechatPayMerchant;
use App\Application\Wechat\Model\WechatPayOrder;
use App\Application\Wechat\Model\WechatPayOrderRefund;
use App\Application\Wechat\Service\Lib\PayUtils;
use App\Exception\ErrorException;
use Hyperf\Codec\Json;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Logger\LoggerFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use EasyWeChat\Pay\Message;
use WeChatPay\Builder;
use WeChatPay\BuilderChainable;
use WeChatPay\Crypto\AesGcm;
use WeChatPay\Crypto\Rsa;
use WeChatPay\Util\PemUtil;

class WxpayService
{
    #[Inject]
    protected WechatSetting $setting;

    #[Inject]
    protected LoggerFactory $loggerFactory;
    protected LoggerInterface $logger;

    #[Inject]
    protected EventDispatcherInterface $eventDispatcher;
    protected BuilderChainable $app;
    protected WechatPayMerchant $merchant;

    public function __construct($id)
    {
        $merchant = WechatPayMerchant::find($id);
        if (!$merchant instanceof WechatPayMerchant) {
            throw new ErrorException('找不到商户');
        }
        $this->merchant = $merchant;
        // 商户号
        $merchantId = $merchant->pay_mch_id;

        // 从本地文件中加载「商户API私钥」，「商户API私钥」会用来生成请求的签名
        $merchantPrivateKeyInstance = Rsa::from($merchant->pay_cert_key);

        // 「商户API证书」的「证书序列号」
        $merchantCertificateSerial = $merchant->serial;

        // 从本地文件中加载「微信支付平台证书」，用来验证微信支付应答的签名
        $platformPublicKeyInstance = Rsa::from($merchant->platform_cert_pem, Rsa::KEY_TYPE_PUBLIC);

        // 从「微信支付平台证书」中获取「证书序列号」
        $platformCertificateSerial = PemUtil::parseCertificateSerialNo($merchant->platform_cert_pem);

        $this->logger = $this->loggerFactory->get('notify', 'request');
        try {
            $config = [
                'mchid' => $merchantId,
                'serial' => $merchantCertificateSerial,
                'privateKey' => $merchantPrivateKeyInstance,
                'certs' => [
                    $platformCertificateSerial => $platformPublicKeyInstance,
                ],
            ];
            $this->app = Builder::factory($config);
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
    public function refundNotify(ServerRequestInterface $request): bool
    {
        try {
            $message = $this->getNotifyInfoByRequest($request);
            $this->eventDispatcher->dispatch(new WxpayRefundNotifyEvent($message->toArray()));

            return true;
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
        $resp = $this->app->v3->refund->domestic->refunds->_out_refund_no_->get([
            'out_refund_no' => $out_refund_no,
        ]);
        $res = Json::decode($resp->getBody()
            ->getContents());
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

        $resp = $this->app->v3->refund->domestic->refunds->post(["json" => $data]);

        $res = Json::decode($resp->getBody()
            ->getContents());

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
    public function notify(ServerRequestInterface $request): bool
    {
        try {
            $message = $this->getNotifyInfoByRequest($request);
            $this->logger->info('get notify', $message->toArray());
            //微信支付回调事件，建议使用 getOrderByOutTradeNo 查看订单是否支付成功
            $this->eventDispatcher->dispatch(new WxpayPayNotifyEvent($message->toArray()));

            return true;
        } catch (Throwable $exception) {
            throw new ErrorException($exception->getMessage());
        }
    }

    private function getNotifyInfoByRequest(ServerRequestInterface $request): Message
    {
        try {
            $originContent = (string)$request->getBody();
            $attributes = Json::decode($originContent);

            if (!is_array($attributes)) {
                throw new ErrorException('Invalid request body.');
            }

            if (empty($attributes['resource']['ciphertext'])) {
                throw new ErrorException('Invalid request.');
            }

            $attributes = json_decode(AesGcm::decrypt($attributes['resource']['ciphertext'],
                $this->merchant->pay_secret_key, $attributes['resource']['nonce'],
                $attributes['resource']['associated_data']), true);

            if (!is_array($attributes)) {
                throw new ErrorException('Failed to decrypt request message.');
            }

            $message = new Message($attributes, $originContent);
            $this->logger->info('get notify', $message->toArray());

            return $message;
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
        $resp = $this->app->v3->pay->transactions->outTradeNo->_out_trade_no_->get([
            'query' => ['mchid' => $this->merchant->pay_mch_id],
            'out_trade_no' => $out_trade_no,
        ]);
        $res = Json::decode($resp->getBody()
            ->getContents());
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
            $utils = new PayUtils($this->merchant);

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
                'mchid' => $this->merchant->pay_mch_id,
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
            $resp = $this->app->v3->pay->transactions->jsapi->post(['json' => $data]);
            $result = Json::decode($resp->getBody()
                ->getContents());
            $prepay_id = $result['prepay_id'] ?? "";
            if ($prepay_id == '') {
                throw new ErrorException('创建支付订单失败2：' . ($result['err_code_des'] ?? ""));
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
}