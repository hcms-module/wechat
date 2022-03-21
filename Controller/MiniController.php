<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/17 17:33
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Controller;

use App\Application\Wechat\Service\MiniProgramService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

/**
 * @Controller(prefix="wechat/mini")
 */
class MiniController
{

    /**
     * @Inject()
     */
    protected RequestInterface $request;

    /**
     * @Inject()
     */
    protected ResponseInterface $response;

    /**
     * 发送统一模板消息，发送给公众号
     * @GetMapping(path="uniform")
     */
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

        return $this->response->json([]);
    }

    /**
     * 发送订阅消息
     * @GetMapping(path="subscribe")
     */
    public function subscribe()
    {
        $template_id = 'template_id';
        $openid = 'openid';
        $page = 'page';
        $data = [];
        $mini_service = new MiniProgramService();
        $mini_service->subscribe()
            ->send($template_id, $openid, $page, $data);

        return $this->response->json([]);
    }

    /**
     * @GetMapping(path="scene")
     */
    public function handScene()
    {
        //处理参数 scene 一般我们使用小程序码、url_link都是通过该指定到首页并通过scene来告知前端需要跳转的目标页面。
        //所以建议后端专门使用一个api来处理跳转
        return $this->response->json([
            'path' => '/',//告诉前端需要跳转的目标页面
        ]);
    }

    /**
     * @GetMapping(path="url")
     */
    public function url()
    {
        $mini_service = new MiniProgramService();
        $res = $mini_service->url()
            ->generate('scene=userid_123');

        return $this->response->json($res->toArray());
    }

    /**
     * @GetMapping(path="qrcode")
     */
    public function qrcode()
    {
        $mini_service = new MiniProgramService();
        $res = $mini_service->qrcode()
            ->getQrcodeByScene('s_' . time());

        return $this->response->json($res->toArray());
    }

    /**
     * @GetMapping(path="login")
     */
    public function login()
    {
        $code = $this->request->input('code', '');
        $mini_service = new MiniProgramService();
        $res = $mini_service->user()
            ->getOpenIdByCode($code);

        return $this->response->json($res->setVisible(['target', 'token', 'openid'])
            ->toArray());
    }

    /**
     * @GetMapping(path="phone")
     */
    public function phone()
    {
        $code = $this->request->input('code', '');
        $mini_service = new MiniProgramService();
        $res = $mini_service->user()
            ->getPhoneByCode($code);

        return $this->response->json($res->setVisible(['phone', 'country_code'])
            ->toArray());
    }
}