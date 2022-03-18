<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/17 16:18
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Mini;

use App\Application\Wechat\Service\MiniProgramService;

abstract class AbstractMiniComponent
{
    protected MiniProgramService $service;

    /**
     * @param MiniProgramService $service
     */
    public function __construct(MiniProgramService $service) { $this->service = $service; }
}