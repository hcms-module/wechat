<?php

declare(strict_types=1);

namespace App\Application\Wechat\Controller;

use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Middleware\AdminMiddleware;
use App\Application\Wechat\Service\WechatSetting;
use App\Controller\AbstractController;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;

/**
 * @Middleware(AdminMiddleware::class)
 * @Controller(prefix="/wechat/wxpay")
 */
class WxpayController extends AbstractController
{

    /**
     * @Inject()
     */
    protected WechatSetting $setting;

    /**
     * @GetMapping(path="index")
     */
    public function index()
    {
        return "hello wechat/wxpay index";
    }

    /**
     * @Api()
     * @PostMapping(path="setting")
     */
    public function settingSubmit()
    {
        $setting = $this->request->input('setting', []);
        $res = $this->setting->saveWxpaySetting($setting);

        return $res ? [] : $this->returnErrorJson();
    }

    /**
     * @Api()
     * @GetMapping(path="setting/info")
     */
    public function settingInfo()
    {
        $setting = $this->setting->getWxpaySetting();

        return compact('setting');
    }

    /**
     * @View()
     * @GetMapping(path="setting")
     */
    public function setting() { }
}
