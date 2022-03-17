<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/15 16:32
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Office;

class Jssdk extends AbstractOfficeComponent
{
    /**
     * @param string $url
     * @param array  $apis
     * @param bool   $debug
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Throwable
     */
    function getConfig(string $url, array $apis = [], bool $debug = false): array
    {
        $this->service->getApp()->jssdk->setUrl($url);
        $res = $this->service->getApp()->jssdk->buildConfig($apis, $debug, false, false);

        return $res ?: [];
    }
}