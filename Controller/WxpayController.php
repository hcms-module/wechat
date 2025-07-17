<?php

declare(strict_types=1);

namespace App\Application\Wechat\Controller;

use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Middleware\AdminMiddleware;
use Hyperf\Session\Middleware\SessionMiddleware;
use App\Application\Wechat\Controller\RequestParam\WxpayMerchantRequestParam;
use App\Application\Wechat\Model\WechatPayMerchant;
use App\Application\Wechat\Service\WechatSetting;
use App\Controller\AbstractController;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;

#[Middleware(SessionMiddleware::class)]
#[Middleware(AdminMiddleware::class)]
#[Controller(prefix: "/wechat/wxpay")]
class WxpayController extends AbstractController
{

    #[Inject]
    protected WechatSetting $setting;


    #[Api]
    #[DeleteMapping("delete/{merchant_id}")]
    public function delete(int $merchant_id = 0)
    {
        $merchant = WechatPayMerchant::find($merchant_id);

        return $merchant->delete() ? [] : $this->returnErrorJson();
    }

    #[Api]
    #[PostMapping("edit")]
    public function editSubmit()
    {
        $request = new WxpayMerchantRequestParam();
        $request->validatedThrowMessage();
        $merchant = WechatPayMerchant::findOrNew($request->getMerchantId());
        $merchant->pay_mch_id = $request->getPayMchId();
        $merchant->merchant_type = $request->getMerchantType();
        $merchant->pay_mch_id = $request->getPayMchId();
        $merchant->pay_secret_key = $request->getPaySecretKey();
        $merchant->pay_v2_secret_key = $request->getPayV2SecretKey();
        $merchant->serial = $request->getSerial();
        $merchant->pay_cert_key = $request->getPayCertKey();
        $merchant->platform_cert_pem = $request->getPlatformCertPem();

        return $merchant->save() ? [] : $this->returnErrorJson();
    }

    #[Api]
    #[GetMapping("edit/{merchant_id}")]
    public function editInfo(int $merchant_id = 0)
    {
        $merchant = WechatPayMerchant::find($merchant_id);

        return compact('merchant');
    }

    #[View]
    #[GetMapping("edit")]
    public function edit()
    {
        return ['title' => "编辑商户"];
    }

    #[View(template: "edit")]
    #[GetMapping("add")]
    public function add()
    {
        return ['title' => "新增商户"];
    }

    #[Api]
    #[GetMapping("lists")]
    public function lists()
    {
        $where = [];
        $lists = WechatPayMerchant::where($where)
            ->orderByDesc('created_at')
            ->paginate();

        return compact('lists');
    }

    #[View]
    #[GetMapping("index")]
    public function index()
    {
    }

    #[Api]
    #[PostMapping("setting")]
    public function settingSubmit()
    {
        $setting = $this->request->input('setting', []);
        $res = $this->setting->saveWxpaySetting($setting);

        return $res ? [] : $this->returnErrorJson();
    }

    #[Api]
    #[GetMapping("setting/info")]
    public function settingInfo()
    {
        $setting = $this->setting->getWxpaySetting();

        return compact('setting');
    }

    #[View]
    #[GetMapping("setting")]
    public function setting()
    {
    }
}
