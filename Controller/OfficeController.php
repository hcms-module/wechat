<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/15 14:39
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Controller;

use App\Annotation\Api;
use App\Application\Wechat\Service\OfficeService;
use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;

#[Controller(prefix: "/wechat/office")]
class OfficeController extends AbstractController
{
    /**
     * 生成参数二维码示例
     */
    #[Api]
    #[GetMapping("qrcode")]
    function qrcode()
    {
        try {
            // 如果是多个公众号可以加入app_key作为可选参数
            $office_service = new OfficeService();
            $qrcode = $office_service->qrcode()
                ->temporary("OK_" . time());

            return $this->returnSuccessJson(compact('qrcode'));
        } catch (\Throwable $exception) {
            return $this->returnErrorJson($exception->getMessage());
        }
    }

    /**
     * 发送模板消息
     */
    #[Api]
    #[GetMapping("template")]
    function template()
    {
        try {
            // 如果是多个公众号可以加入app_key作为可选参数
            $office_service = new OfficeService();
            $open_id = 'open_id';
            $template_id = 'template_id';
            $data = ['a' => 'x', 'd' => time()];
            $url = 'url';
            $mini_program_appid = 'mini_program_appid';
            $res = $office_service->template()
                ->sendTemplateMsg(open_id: $open_id, template_id: $template_id, data: $data, url: $url,
                    mini_program_appid: $mini_program_appid);

            return $res ? [] : $this->returnErrorJson();
        } catch (\Throwable $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * 公众号消息触发机制
     */
    #[RequestMapping("message")]
    function officeMessage(string $app_key = '')
    {
        try {
            // 如果是多个公众号可以加入app_key作为可选参数
            $office_service = new OfficeService($app_key);
            $res = $office_service->message()
                ->push($this->request);
        } catch (\Throwable $exception) {
            $res = $exception->getMessage();
        }

        return $res;
    }

    #[Api]
    #[GetMapping("jssdk")]
    function getJssdk()
    {
        $url = $this->request->input('url', $this->request->url());
        try {
            $office_service = new OfficeService();

            return $office_service->jssdk()
                ->getConfig($url);
        } catch (\Throwable $exception) {
            //请求数据失败
            return $exception->getMessage();
        }
    }

    #[GetMapping("auth/callback")]
    function officeAuthCallBack()
    {
        $office_service = new OfficeService();
        try {
            $office_user = $office_service->user()
                ->oauth($this->request->input('code'));

            //TODO 这里拿到用户的model，下面就可以开发自己的业务
            return "success";
        } catch (\Throwable $exception) {
            //请求数据失败
            return $exception->getMessage();
        }
    }

    #[GetMapping("auth")]
    function officeAuth()
    {
        $redirect_url = url('wechat/office/auth/callback', [], true);
        $office_service = new OfficeService();

        // snsapi_base 静默授权、snsapi_userinfo显示授权页面
        $oauth_url = $office_service->user()
            ->getOauthUrl($redirect_url, 'snsapi_base');

        return $this->response->redirect($oauth_url);
    }
}