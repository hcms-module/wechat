<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/9/15 11:58
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Channel;

use App\Application\Wechat\Service\Mini\Channel;

abstract class AbstractMiniChannelComponent
{
    protected Channel $channel;

    /**
     * @param Channel $channel
     */
    public function __construct(Channel $channel) { $this->channel = $channel; }
}