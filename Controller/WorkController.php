<?php

declare(strict_types=1);

namespace App\Application\Wechat\Controller;

use App\Application\Wechat\Model\WechatOpenworkEvent;
use App\Application\Wechat\Model\WechatOpenworkMsg;
use App\Application\Wechat\Service\OpenWork\EventHandleService;
use App\Application\Wechat\Service\OpenWorkService;
use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\Utils\Codec\Json;

/**
 * @Controller(prefix="/wechat/work")
 */
class WorkController extends AbstractController
{

    /**
     * @GetMapping(path="index")
     */
    public function index()
    {
        $code = $this->request->input('code', '');
        $open_work = new OpenWorkService();
        if ($code === '') {
            $callback_url = url('wechat/work/index', [], true);
            $redirect = $open_work->getApp()->corp->getOAuthRedirectUrl($callback_url, 'snsapi_base');

            return $this->response->redirect($redirect);
        } else {
            $res = $open_work->getApp()->corp->getUserByCode($code);

            return $this->returnSuccessJson(compact('res'));
        }
    }

    /**
     * @RequestMapping(path="open/event")
     */
    public function openEvent()
    {
        $open_work = new OpenWorkService();
        $server = $open_work->getApp()->server;
        $server->push(function ($message) {
            $suite_id = $message['SuiteId'] ?? '';
            $info_type = $message['InfoType'] ?? '';
            $event = WechatOpenworkEvent::create([
                'suite_id' => $suite_id,
                'info_type' => $info_type,
                'event_content' => Json::encode($message),
            ]);
            $event_handle = new EventHandleService($event);
            $res = $event_handle->handle();

            return $res ? 'success' : 'fail';
        });

        return $server->serve()
            ->send()
            ->getContent();
    }

    /**
     * @RequestMapping(path="open/msg")
     */
    public function openMsg()
    {
        $open_work = new OpenWorkService();
        $server = $open_work->getApp()->server;
        $server->push(function ($message) {
            $to_user_name = $message['ToUserName'] ?? '';
            $from_user_name = $message['FromUserName'] ?? '';
            $msg_type = $message['MsgType'] ?? '';
            WechatOpenworkMsg::create([
                'to_user_name' => $to_user_name,
                'from_user_name' => $from_user_name,
                'msg_type' => $msg_type,
                'msg_content' => Json::encode($message),
            ]);
        });

        return $server->serve()
            ->send()
            ->getContent();
    }
}
