<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/15 17:57
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Listener;

use App\Application\Wechat\Event\OfficeMessageEvent;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;

/**
 * 根据自己的业务需要定义你希望的监听处理
 * @Listener()
 */
class OfficeMessageListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            OfficeMessageEvent::class
        ];
    }

    public function process(object $event)
    {
        //收到消息触发时间

        /**
         * @var OfficeMessageEvent $event
         */
//        var_dump($event->message->id);
    }
}