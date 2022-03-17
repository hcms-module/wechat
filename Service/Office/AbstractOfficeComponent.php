<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/17 16:18
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Office;

use App\Application\Wechat\Service\OfficeService;

abstract class AbstractOfficeComponent
{
    protected OfficeService $service;

    /**
     * @param OfficeService $service
     */
    public function __construct(OfficeService $service) { $this->service = $service; }
}