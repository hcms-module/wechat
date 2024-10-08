<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2023/1/7 09:23
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Mini;

use App\Application\Wechat\Event\MinEventMessageEvent;
use App\Application\Wechat\Model\WechatMinEventMessage;
use App\Application\Wechat\Service\Lib\AbstractMiniComponent;
use App\Exception\ErrorException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Codec\Json;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Message extends AbstractMiniComponent
{
    #[Inject]
    protected EventDispatcherInterface $event_dispatcher;

    public function push(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $server = $this->service->getApp()
                ->getServer();
            $message = $server->getRequestMessage($request);
            $this->handleMessage($message->toArray());

            return $server->serve();
        } catch (\Exception $exception) {
            throw new ErrorException($exception->getMessage());
        }
    }

    private function handleMessage(array $message)
    {
        $data = [
            'app_id' => $this->service->getAppId(),
            'to_user_name' => $message['ToUserName'] ?? '',
            'from_user_name' => $message['FromUserName'] ?? '',
            'create_time' => intval($message['CreateTime'] ?? 0),
            'msg_type' => $message['MsgType'] ?? '',
            'event' => $message['Event'] ?? '',
            'isrisky' => intval($message['isrisky'] ?? 0),
            'extra_info_json' => $message['extra_info_json'] ?? '',
            'status_code' => intval($message['status_code'] ?? 0),
            'trace_id' => $message['trace_id'] ?? '',
            'version' => intval($message['version'] ?? 0),
            'result' => Json::encode($message['result'] ?? []),
            'detail' => Json::encode($message['MediaId'] ?? []),
        ];

        $info_md5 = md5(Json::encode($data));
        $min_event_message = WechatMinEventMessage::firstOrCreate(['info_md5' => $info_md5], $data);
        //触发公众号消息事件
        $this->event_dispatcher->dispatch(new MinEventMessageEvent($min_event_message));
    }
}