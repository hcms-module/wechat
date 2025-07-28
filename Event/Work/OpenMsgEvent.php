<?php

namespace App\Application\Wechat\Event\Work;

use App\Application\Wechat\Model\WechatOpenworkMsg;

class OpenMsgEvent
{
    public function __construct(protected WechatOpenworkMsg $msg)
    {
    }

    /**
     * @return WechatOpenworkMsg
     */
    public function getMsg(): WechatOpenworkMsg
    {
        return $this->msg;
    }
}