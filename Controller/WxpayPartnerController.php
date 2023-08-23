<?php

declare(strict_types=1);

namespace App\Application\Wechat\Controller;

use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Middleware\AdminMiddleware;
use App\Application\Wechat\Service\WechatSetting;
use App\Application\Wechat\Service\WxpayPartnerService;
use App\Controller\AbstractController;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;

#[Middleware(AdminMiddleware::class)]
#[Controller(prefix: "/wechat/wxpay/partner")]
class WxpayPartnerController extends AbstractController
{

    #[Inject]
    protected WechatSetting $setting;


    #[Inject]
    protected WxpayPartnerService $wxpayPartnerService;

    #[GetMapping("index")]
    public function index()
    {
        try {
            $resp = $this->wxpayPartnerService->getApp()->v3->certificates->get();

            return $resp->getBody();
        } catch (\Throwable $exception) {
            return $exception->getMessage();
        }
    }

    #[Api]
    #[PostMapping("setting")]
    public function settingSubmit()
    {
        $setting = $this->request->input('setting', []);
        $res = $this->setting->saveWxpayPartnerSetting($setting);

        return $res ? [] : $this->returnErrorJson();
    }

    #[Api]
    #[GetMapping("setting/info")]
    public function settingInfo()
    {
        $setting = $this->setting->getWxpayPartnerSetting();

        return compact('setting');
    }

    #[View]
    #[GetMapping("setting")]
    public function setting()
    {
    }
}
