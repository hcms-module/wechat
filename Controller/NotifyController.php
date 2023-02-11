<?php

declare(strict_types=1);

namespace App\Application\Wechat\Controller;

use App\Application\Wechat\Service\WxpayService;
use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;

#[Controller(prefix: "/wechat/notify")]
class NotifyController extends AbstractController
{

    #[RequestMapping("pay")]
    public function pay()
    {
        try {
            $wxpay_service = new WxpayService();

            return $wxpay_service->notify($this->request);
        } catch (\Throwable $exception) {
            return $this->response->raw($exception->getMessage());
        }
    }

    #[RequestMapping("refund")]
    public function refund()
    {
        try {
            $wxpay_service = new WxpayService();

            return $wxpay_service->refund($this->request);
        } catch (\Throwable $exception) {
            return $this->response->raw($exception->getMessage());
        }
    }
}
