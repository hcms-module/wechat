<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/4/27 10:19
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Lib;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;

class WechatRequest
{
    /**
     * @Inject()
     */
    protected RequestInterface $request;

    public function __call($name, $arguments)
    {
        return $this->request->$name(...$arguments);
    }

    function get($key, $default = '')
    {
        return $this->request->input($key, $default);
    }

    function getContentType()
    {
        return $this->request->header('content-type', '');
    }

    function getContent()
    {
        return $this->request->getBody()
            ->getContents();
    }
}