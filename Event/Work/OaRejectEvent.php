<?php

namespace App\Application\Wechat\Event\Work;

use App\Application\Wechat\Model\WechatWorkOa;

class OaRejectEvent
{
    public function __construct(protected WechatWorkOa $oa)
    {

    }

    /**
     * @return WechatWorkOa
     */
    public function getOa(): WechatWorkOa
    {
        return $this->oa;
    }

}