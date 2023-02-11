<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/17 16:18
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Lib;

use App\Application\Wechat\Service\OfficeService;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;

abstract class AbstractOfficeComponent
{
    protected OfficeService $service;
    protected AccessTokenAwareClient $api_client;

    /**
     * @param OfficeService $service
     */
    public function __construct(OfficeService $service)
    {
        $this->service = $service;
        $this->api_client = $this->service->getApp()
            ->getClient();
    }
}