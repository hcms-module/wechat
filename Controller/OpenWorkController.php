<?php

declare(strict_types=1);

namespace App\Application\Wechat\Controller;

use App\Application\Wechat\Model\WechatOpenworkEvent;
use App\Application\Wechat\Model\WechatOpenworkMsg;
use App\Application\Wechat\Service\OpenWork\EventHandleService;
use App\Application\Wechat\Service\OpenWorkService;
use App\Controller\AbstractController;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\Utils\Codec\Json;

/**
 * @Controller(prefix="/wechat/work/open")
 */
class OpenWorkController extends AbstractController
{

    /**
     * @Inject()
     */
    private OpenWorkService $open_work;

    /**
     * @GetMapping(path="index")
     */
    public function index()
    {
        $code = $this->request->input('code', '');
        if ($code === '') {
            $callback_url = url('wechat/work/open/index', [], true);
            $redirect = $this->open_work->getApp()->corp->getOAuthRedirectUrl($callback_url, 'snsapi_base');

            return $this->response->redirect($redirect);
        } else {
            $res = $this->open_work->getApp()->corp->getUserByCode($code);

            return $this->returnSuccessJson(compact('res'));
        }
    }

    /**
     * @RequestMapping(path="event")
     */
    public function openEvent()
    {
        $server = $this->open_work->getApp()->server;
        $server->push(function ($message) {
            $res = false;
            if (!empty($message)) {
                $suite_id = $message['SuiteId'] ?? '';
                $info_type = $message['InfoType'] ?? '';
                $event = WechatOpenworkEvent::create([
                    'suite_id' => $suite_id,
                    'info_type' => $info_type,
                    'event_content' => Json::encode($message),
                ]);
                $event_handle = new EventHandleService($event);
                $res = $event_handle->handle();
            }

            return $res ? 'success' : 'fail';
        });

        return $server->serve()
            ->send()
            ->getContent();
    }

    /**
     * @RequestMapping(path="msg")
     */
    public function openMsg()
    {
        $server = $this->open_work->getApp()->server;
        $server->push(function ($message) {
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

        return $server->serve()
            ->send()
            ->getContent();
    }
}
