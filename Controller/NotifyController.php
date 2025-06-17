<?php

declare(strict_types=1);

namespace App\Application\Wechat\Controller;

use App\Application\Wechat\Service\WechatPay\WxpayService;
use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;

#[Controller(prefix: "/wechat/notify")]
class NotifyController extends AbstractController
{

    #[RequestMapping("pay/{id}")]
    public function pay(int $id)
    {
        try {
            $wxpay_service = new WxpayService($id);

            return $wxpay_service->notify($this->request) ? "success" : "fail";
        } catch (\Throwable $exception) {
            return $this->response->raw($exception->getMessage());
        }
    }

    #[RequestMapping("refund/{id}")]
    public function refund(int $id)
    {
        try {
            $wxpay_service = new WxpayService($id);

            return $wxpay_service->refundNotify($this->request) ? "success" : "fail";
        } catch (\Throwable $exception) {
            return $this->response->raw($exception->getMessage());
        }
    }
}
