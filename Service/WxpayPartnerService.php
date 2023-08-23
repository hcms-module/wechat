<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/11/2 15:07
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service;

use App\Exception\ErrorException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Logger\LoggerFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use WeChatPay\Builder;
use WeChatPay\BuilderChainable;
use WeChatPay\Crypto\Rsa;
use WeChatPay\Util\PemUtil;

class WxpayPartnerService
{
    #[Inject]
    protected WechatSetting $setting;

    #[Inject]
    protected LoggerFactory $loggerFactory;
    protected LoggerInterface $logger;

    #[Inject]
    protected EventDispatcherInterface $eventDispatcher;


    protected BuilderChainable $app;

    public function __construct()
    {
        $setting = $this->setting->getWxpayPartnerSetting();
        // 商户号
        $merchantId = $setting['pay_mch_id'] ?? '';
        if ($merchantId !== "") {
            // 从本地文件中加载「商户API私钥」，「商户API私钥」会用来生成请求的签名
            $merchantPrivateKeyInstance = Rsa::from($setting['pay_cert_key'] ?? '');

            // 「商户API证书」的「证书序列号」
            $merchantCertificateSerial = $setting['serial'] ?? '';

            // 从本地文件中加载「微信支付平台证书」，用来验证微信支付应答的签名
            $platformPublicKeyInstance = Rsa::from($setting['platform_cert_pem'] ?? '', Rsa::KEY_TYPE_PUBLIC);

            // 从「微信支付平台证书」中获取「证书序列号」
            $platformCertificateSerial = PemUtil::parseCertificateSerialNo($setting['platform_cert_pem'] ?? '');

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
     * @return BuilderChainable
     */
    public function getApp(): BuilderChainable
    {
        return $this->app;
    }

    protected function getAppV3()
    {
        return $this->app->v3;
    }
}