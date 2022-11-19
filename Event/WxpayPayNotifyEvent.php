<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/11/18 17:53
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Event;

class WxpayPayNotifyEvent
{
    public array $message;

    /**
     * @param array $message
     */
    public function __construct(array $message) { $this->message = $message; }

}