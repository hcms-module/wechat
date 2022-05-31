<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/5/25 16:27
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\OpenWork\EventHandle;

interface EventHandleInterface
{
    function handle(array $event_content): bool;
}