<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2023/1/7 09:38
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Event;


use App\Application\Wechat\Model\WechatMinEventMessage;

class MinEventMessageEvent
{
    public WechatMinEventMessage $min_event_message;

    /**
     * @param WechatMinEventMessage $min_event_message
     */
    public function __construct(WechatMinEventMessage $min_event_message)
    {
        $this->min_event_message = $min_event_message;
    }
}