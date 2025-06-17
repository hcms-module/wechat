<?php

namespace App\Application\Wechat\Event;

use App\Application\Wechat\Model\WechatPayMchTransfer;

class WxpayMchTransferCancelEvent
{
    protected WechatPayMchTransfer $wechatPayMchTransfer;

    /**
     * @param WechatPayMchTransfer $wechatPayMchTransfer
     */
    public function __construct(WechatPayMchTransfer $wechatPayMchTransfer)
    {
        $this->wechatPayMchTransfer = $wechatPayMchTransfer;
    }

    /**
     * @return WechatPayMchTransfer
     */
    public function getWechatPayMchTransfer(): WechatPayMchTransfer
    {
        return $this->wechatPayMchTransfer;
    }
}