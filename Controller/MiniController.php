<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/17 17:33
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Controller;

use App\Annotation\Api;
use App\Application\Wechat\Service\MiniProgramService;
use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;


#[Controller(prefix: "wechat/mini")]
class MiniController extends AbstractController
{

    #[RequestMapping("message/{app_key}")]
    function miniMessage(string $app_key = '')
    {
        try {
            $service = new MiniProgramService($app_key);
            $res = $service->message()
                ->push($this->request);
        } catch (\Throwable $exception) {
            $res = $exception->getMessage();
        }

        return $res;
    }

    #[Api]
    #[GetMapping("check")]
    function check()
    {
        $service = new MiniProgramService();
        $res = $service->content()
            ->checkText("test content");

        return $res;
    }

    /**
     * 发送统一模板消息，发送给公众号
     */
    #[Api]
    #[GetMapping("uniform")]
    public function uniform()
    {
        $template_id = 'template_id';
        $office_appid = 'office_appid';
        $openid = 'openid';
        $page = 'page';
        $data = [];
        $mini_service = new MiniProgramService();
        $mini_service->subscribe()
            ->uniform($openid, $office_appid, $template_id, $page, $data);

        return [];
    }

    /**
     * 发送订阅消息
     */
    #[Api]
    #[GetMapping("subscribe")]
    public function subscribe()
    {
        $template_id = 'template_id';
        $openid = 'openid';
        $page = 'page';
        $data = [];
        $mini_service = new MiniProgramService();
        $mini_service->subscribe()
            ->send($template_id, $openid, $page, $data);

        return [];
    }

    #[GetMapping("scene")]
    public function handScene()
    {
        //处理参数 scene 一般我们使用小程序码、url_link都是通过该指定到首页并通过scene来告知前端需要跳转的目标页面。
        //所以建议后端专门使用一个api来处理跳转
        return [
            'path' => '/',//告诉前端需要跳转的目标页面
        ];
    }

    #[Api]
    #[GetMapping("url")]
    public function url()
    {
        $mini_service = new MiniProgramService();
        $res = $mini_service->url()
            ->generate('scene=userid_123');

        return $res->toArray();
    }

    #[Api]
    #[GetMapping("qrcode")]
    public function qrcode()
    {
        $mini_service = new MiniProgramService();
        $res = $mini_service->qrcode()
            ->getQrcodeByScene('s_' . time());

        return $res->toArray();
    }

    #[Api]
    #[GetMapping("login")]
    public function login()
    {
        $code = $this->request->input('code', '');
        $mini_service = new MiniProgramService();
        $res = $mini_service->user()
            ->getOpenIdByCode($code);

        return $res->setVisible(['target', 'token', 'openid'])
            ->toArray();
    }

    #[Api]
    #[GetMapping("phone")]
    public function phone()
    {
        $code = $this->request->input('code', '');
        $mini_service = new MiniProgramService();
        $res = $mini_service->user()
            ->getPhoneByCode($code);

        return $res->setVisible(['phone', 'country_code'])
            ->toArray();
    }
}