<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/15 17:52
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Event;

use App\Application\Wechat\Model\WechatOfficeMessage;

class OfficeMessageEvent
{
    public WechatOfficeMessage $message;

    /**
     * @param WechatOfficeMessage $message
     */
    public function __construct(WechatOfficeMessage $message) { $this->message = $message; }
}