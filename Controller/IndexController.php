<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/15 14:39
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Controller;

use App\Application\Wechat\Service\OfficeService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\HttpServer\Contract\RequestInterface;

/**
 * @Controller(prefix="/wechat/office")
 */
class IndexController
{
    /**
     * 生成参数二维码示例
     * @GetMapping(path="qrcode")
     */
    function qrcode()
    {
        try {
            // 如果是多个公众号可以加入app_key作为可选参数
            $office_service = new OfficeService();
            $res = $office_service->qrcode()
                ->temporary("OK_" . time());

            return $res ? 'success' : 'failed';
        } catch (\Throwable $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * 发送模板消息
     * @GetMapping(path="template")
     */
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
                ->sendTemplateMsg($open_id, $template_id, $data, $url, $mini_program_appid);

            return $res ? 'success' : 'failed';
        } catch (\Throwable $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * 公众号消息触发机制
     * @RequestMapping(path="message")
     */
    function officeMessage(ResponseInterface $response, string $app_key = '')
    {
        try {
            // 如果是多个公众号可以加入app_key作为可选参数
            $office_service = new OfficeService($app_key);

            return $response->write($office_service->message()
                ->push());
        } catch (\Throwable $exception) {
            return $response->write($exception->getMessage());
        }
    }

    /**
     * @GetMapping(path="jssdk")
     */
    function getJssdk(RequestInterface $request, ResponseInterface $response)
    {
        $url = $request->input('url', $request->url());
        try {
            $office_service = new OfficeService();
            $res = $office_service->jssdk()
                ->getConfig($url);

            return $response->json($res);
        } catch (\Throwable $exception) {
            //请求数据失败
            return $exception->getMessage();
        }
    }

    /**
     * @GetMapping(path="auth/callback")
     */
    function officeAuthCallBack(RequestInterface $request)
    {
        $office_service = new OfficeService();
        try {
            $office_user = $office_service->user()
                ->oauth($request->input('code'));

            //TODO 这里拿到用户的model，下面就可以开发自己的业务
            return "success";
        } catch (\Throwable $exception) {
            //请求数据失败
            return $exception->getMessage();
        }
    }

    /**
     * @GetMapping(path="auth")
     */
    function officeAuth(ResponseInterface $response)
    {
        $redirect_url = url('wechat/index/path="auth/callback', [], true);
        $office_service = new OfficeService();

        // snsapi_base 静默授权、snsapi_userinfo显示授权页面
        $oauth_url = $office_service->user()
            ->getOauthUrl($redirect_url, 'snsapi_base');

        return $response->redirect($oauth_url);
    }
}