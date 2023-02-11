<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/15 16:32
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Office;

use App\Application\Wechat\Service\Lib\AbstractOfficeComponent;
use App\Exception\ErrorException;
use Psr\SimpleCache\InvalidArgumentException;

class Jssdk extends AbstractOfficeComponent
{
    /**
     * @param string $url
     * @param array  $apis
     * @param bool   $debug
     * @return array
     * @throws ErrorException
     */
    function getConfig(string $url, array $apis = [], bool $debug = false): array
    {
        try {
            $utils = $this->service->getApp()
                ->getUtils();

            $res = $utils->buildJsSdkConfig(url: $url, jsApiList: $apis, debug: $debug);

            return $res ?: [];
        } catch (\Throwable $exception) {
            throw new ErrorException($exception->getMessage());
        } catch (InvalidArgumentException $exception) {
            throw new ErrorException($exception->getMessage());
        }
    }
}