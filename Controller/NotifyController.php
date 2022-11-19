<?php

declare(strict_types=1);

namespace App\Application\Wechat\Controller;

use App\Application\Wechat\Service\WxpayService;
use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * @Controller(prefix="/wechat/notify")
 */
class NotifyController extends AbstractController
{

    /**
     * @RequestMapping(path="index")
     */
    public function index()
    {
        $wxpay_service = new WxpayService();
        $wxpay_service->notify();
    }
}
