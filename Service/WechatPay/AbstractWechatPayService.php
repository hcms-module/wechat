<?php

namespace App\Application\Wechat\Service\WechatPay;

use App\Application\Wechat\Model\WechatPayMerchant;
use App\Exception\ErrorException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Logger\LoggerFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;
use WeChatPay\Builder;
use WeChatPay\BuilderChainable;
use WeChatPay\Crypto\Rsa;
use WeChatPay\Util\PemUtil;

abstract class AbstractWechatPayService
{
    #[Inject]
    protected LoggerFactory $loggerFactory;

    protected WechatPayMerchant $merchant;

    #[Inject]
    protected EventDispatcherInterface $eventDispatcher;
    protected BuilderChainable $app;

    public function __construct(int $merchant_id = 0, string $pay_mch_id = '')
    {
        if ($pay_mch_id) {
            $merchant = WechatPayMerchant::where('pay_mch_id', $pay_mch_id)
                ->first();
        } elseif ($merchant_id) {
            $merchant = WechatPayMerchant::find($merchant_id);
        } else {
            $merchant = WechatPayMerchant::orderByDesc('merchant_id')
                ->first();
        }
        if ($merchant instanceof WechatPayMerchant) {
            $this->merchant = $merchant;
            // 商户号
            $merchantId = $merchant->pay_mch_id;

            // 从本地文件中加载「商户API私钥」，「商户API私钥」会用来生成请求的签名
            $merchantPrivateKeyInstance = Rsa::from($merchant->pay_cert_key);

            // 「商户API证书」的「证书序列号」
            $merchantCertificateSerial = $merchant->serial;

            // 从本地文件中加载「微信支付平台证书」，用来验证微信支付应答的签名
            $platformPublicKeyInstance = Rsa::from($merchant->platform_cert_pem, Rsa::KEY_TYPE_PUBLIC);
            $this->platformPublicKeyInstance = $platformPublicKeyInstance;
            // 从「微信支付平台证书」中获取「证书序列号」
            $platformCertificateSerial = PemUtil::parseCertificateSerialNo($merchant->platform_cert_pem);
            $this->platformCertificateSerial = $platformCertificateSerial;

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
        } else {
            throw new ErrorException('找不到商户');
        }
    }

    private function encryptor($msg): string
    {
        return Rsa::encrypt($msg, $this->platformPublicKeyInstance);
    }
}