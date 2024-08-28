<?php

declare(strict_types=1);

namespace App\Application\Wechat\Controller;

use App\Application\Wechat\Model\WechatOpenworkEvent;
use App\Application\Wechat\Model\WechatOpenworkMsg;
use App\Application\Wechat\Service\OpenWork\EventHandleService;
use App\Application\Wechat\Service\OpenWork\MsgHandleService;
use App\Application\Wechat\Service\OpenWorkService;
use App\Controller\AbstractController;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\Codec\Json;


#[Controller(prefix: "/wechat/work/open")]
class OpenWorkController extends AbstractController
{

    #[Inject]
    protected OpenWorkService $open_work;

    #[RequestMapping("event")]
    public function openEvent()
    {
        $server = $this->open_work->getApp()
            ->getServer();
        $message = $server->getRequestMessage($this->request);
        if (!empty($message)) {
            $suite_id = $message['SuiteId'] ?? '';
            $info_type = $message['InfoType'] ?? '';
            $event = WechatOpenworkEvent::create([
                'suite_id' => $suite_id,
                'info_type' => $info_type,
                'event_content' => Json::encode($message),
            ]);
            (new EventHandleService($event))->handle();
        }

        return $server->serve();
    }

    #[RequestMapping("msg")]
    public function openMsg()
    {
        $server = $this->open_work->getApp()
            ->getServer();
        $message = $server->getRequestMessage($this->request);
        $to_user_name = $message['ToUserName'] ?? '';
        $from_user_name = $message['FromUserName'] ?? '';
        $msg_type = $message['MsgType'] ?? '';
        $msg = WechatOpenworkMsg::create([
            'to_user_name' => $to_user_name,
            'from_user_name' => $from_user_name,
            'msg_type' => $msg_type,
            'msg_content' => Json::encode($message),
        ]);

        (new MsgHandleService($msg))->handle();

        return $server->serve();
    }
}
