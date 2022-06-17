<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/6/2 17:54
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\OpenWork\MsgHandle;

interface EventMsgHandleInterface
{
    function handle(array $msg_content): bool;
}