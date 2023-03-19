<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/17 16:18
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Lib;

use App\Application\Wechat\Service\MiniProgramService;
use App\Exception\ErrorException;

abstract class AbstractMiniComponent
{
    protected MiniProgramService $service;

    /**
     * @param MiniProgramService $service
     */
    public function __construct(MiniProgramService $service) { $this->service = $service; }

    /**
     * @throws ErrorException
     */
    public function request(string $uri, array $data): array
    {
        return $this->service->postJson($uri, $data);
    }
}