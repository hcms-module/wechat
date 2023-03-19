<?php

declare(strict_types=1);

namespace App\Application\Wechat\Controller;

use App\Annotation\Api;
use App\Application\Wechat\Model\WechatOpenworkMsg;
use App\Application\Wechat\Service\WorkService;
use App\Controller\AbstractController;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\Utils\Codec\Json;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[Controller(prefix: "/wechat/work")]
class WorkController extends AbstractController
{
    #[Inject]
    private WorkService $work_service;

    #[GetMapping("external")]
    public function external()
    {
        $userid = $this->request->input('userid', '');
        $app = $this->work_service->getApp();
        $res = $app->external_contact->list($userid);

        return compact('res');
    }

    #[Api]
    #[GetMapping("user")]
    public function user()
    {
        $departments = $this->work_service->getApp()->department->list()['department'] ?? '';

        foreach ($departments as &$department) {
            $user_list = $this->work_service->getApp()->user->getDepartmentUsers($department['id'])['userlist'] ?? '';
            $department['user_list'] = $user_list;
        }

        return compact('departments');
    }

    #[GetMapping("index")]
    public function index()
    {
        $code = $this->request->input('code', '');
        if ($code === '') {
            $callback_url = url('wechat/work/index', [], true);
            $redirect = $this->work_service->getApp()->oauth->redirect($callback_url);
            if ($redirect instanceof RedirectResponse) {
                // 获取企业微信跳转目标地址
                $redirect = $redirect->getTargetUrl();
            }

            return $this->response->redirect($redirect);
        } else {
            $res = $this->work_service->getApp()->oauth->detailed()
                ->userFromCode($code);

            return $this->returnSuccessJson(compact('res'));
        }
    }

    #[RequestMapping("msg")]
    public function openMsg()
    {
        $app = $this->work_service->getApp();
        $app->server->push(function ($message) {
            if (!empty($message)) {
                $to_user_name = $message['ToUserName'] ?? '';
                $from_user_name = $message['FromUserName'] ?? '';
                $msg_type = $message['MsgType'] ?? '';
                WechatOpenworkMsg::create([
                    'to_user_name' => $to_user_name,
                    'from_user_name' => $from_user_name,
                    'msg_type' => $msg_type,
                    'msg_content' => Json::encode($message),
                ]);
            }
        });

        return $app->server->serve()
            ->send()
            ->getContent();
    }
}
