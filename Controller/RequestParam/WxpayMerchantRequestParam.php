<?php

namespace App\Application\Wechat\Controller\RequestParam;

use App\Annotation\RequestParam;
use App\Controller\RequestParam\BaseRequestParam;

#[RequestParam]
class WxpayMerchantRequestParam extends BaseRequestParam
{
    protected array $rules = [
        'pay_mch_id' => 'required',
        'merchant_type' => 'required',
    ];

    protected array $message = [
        'pay_mch_id.required' => '请输入商户号',
        'merchant_type.required' => '请选择商户类型',
    ];

    private int $merchant_id = 0;
    private int $merchant_type = 0;

    private string $pay_mch_id = '';
    private string $pay_secret_key = '';
    private string $pay_v2_secret_key = '';
    private string $serial = '';
    private string $pay_cert_key = '';
    private string $platform_cert_pem = '';

    /**
     * @return int
     */
    public function getMerchantId(): int
    {
        return $this->merchant_id;
    }

    /**
     * @return int
     */
    public function getMerchantType(): int
    {
        return $this->merchant_type;
    }

    /**
     * @return string
     */
    public function getPayMchId(): string
    {
        return $this->pay_mch_id;
    }

    /**
     * @return string
     */
    public function getPaySecretKey(): string
    {
        return $this->pay_secret_key;
    }

    /**
     * @return string
     */
    public function getPayV2SecretKey(): string
    {
        return $this->pay_v2_secret_key;
    }

    /**
     * @return string
     */
    public function getSerial(): string
    {
        return $this->serial;
    }

    /**
     * @return string
     */
    public function getPayCertKey(): string
    {
        return $this->pay_cert_key;
    }

    /**
     * @return string
     */
    public function getPlatformCertPem(): string
    {
        return $this->platform_cert_pem;
    }

}