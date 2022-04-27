<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/15 16:52
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Office;

use App\Application\Wechat\Event\OfficeEventMessageEvent;
use App\Application\Wechat\Event\OfficeMessageEvent;
use App\Application\Wechat\Model\WechatOfficeEventMessage;
use App\Application\Wechat\Model\WechatOfficeMessage;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Codec\Json;
use Psr\EventDispatcher\EventDispatcherInterface;

class Message extends AbstractOfficeComponent
{
    /**
     * @Inject()
     */
    private EventDispatcherInterface $event_dispatcher;

    /**
     * 公众号服务消息处理
     *
     * @return string
     */
    public function push(): string
    {
        try {
            $this->service->getApp()->server->push(function ($message) {
                $msg_type = $message['MsgType'] ?? '';
                if ($msg_type === 'event') {
                    $this->handleEventMessage($message);
                } else {
                    $this->handleMessage($message);
                }

                return "SUCCESS";
            });

            return $this->service->getApp()->server->serve()
                ->getContent();
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * 处理公众号消息
     *
     * @param $message
     */
    private function handleMessage($message)
    {
        $data = [
            'app_id' => $this->service->getAppId(),
            'to_user_name' => $message['ToUserName'] ?? '',
            'from_user_name' => $message['FromUserName'] ?? '',
            'create_time' => $message['CreateTime'] ?? 0,
            'msg_type' => $message['MsgType'] ?? '',
            'msg_id' => $message['MsgId'] ?? '',
            'content' => $message['Content'] ?? '',
            'pic_url' => $message['PicUrl'] ?? '',
            'media_id' => $message['MediaId'] ?? '',
            'format' => $message['Format'] ?? '',
            'recognition' => $message['Recognition'] ?? '',
            'thumb_media_id' => $message['ThumbMediaId'] ?? '',
            'location_x' => $message['Location_X'] ?? '',
            'location_y' => $message['Location_Y'] ?? '',
            'scale' => $message['Scale'] ?? '',
            'label' => $message['Label'] ?? '',
            'title' => $message['Title'] ?? '',
            'description' => $message['Description'] ?? '',
            'url' => $message['Url'] ?? '',
        ];

        $info_md5 = md5(Json::encode($data));

        $office_message = WechatOfficeMessage::firstOrCreate(['info_md5' => $info_md5], $data);
        //触发公众号消息事件
        $this->event_dispatcher->dispatch(new OfficeMessageEvent($office_message));
    }

    /**
     * 处理公众号事件
     *
     * @param $message
     */
    private function handleEventMessage($message)
    {
        $data = [
            'app_id' => $this->service->getAppId(),
            'to_user_name' => $message['ToUserName'] ?? '',
            'from_user_name' => $message['FromUserName'] ?? '',
            'create_time' => $message['CreateTime'] ?? 0,
            'event' => $message['Event'] ?? '',
            'event_key' => $message['EventKey'] ?? '',
            'ticket' => $message['Ticket'] ?? '',
            'latitude' => $message['Latitude'] ?? '',
            'longitude' => $message['Longitude'] ?? '',
            'precision' => $message['Precision'] ?? '',
        ];
        $info_md5 = md5(Json::encode($data));

        $office_event_message = WechatOfficeEventMessage::firstOrCreate(['info_md5' => $info_md5], $data);
        $this->event_dispatcher->dispatch(new OfficeEventMessageEvent($office_event_message));
    }
}