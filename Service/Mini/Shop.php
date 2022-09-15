<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/9/15 10:40
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Mini;

use App\Application\Wechat\Service\MiniShop\Order;
use App\Application\Wechat\Service\MiniShop\ProductSpu;
use App\Exception\ErrorException;

/**
 * @method Order order()
 * @method ProductSpu productSpu()
 */
class Shop extends AbstractMiniComponent
{

    public function __call($name, $arguments)
    {
        $name = ucfirst($name);
        $class_name = "App\\Application\\Wechat\\Service\\MiniShop\\{$name}";
        if (!class_exists($class_name)) {
            throw new ErrorException('对象不存在' . $class_name);
        }

        return new  $class_name($this);
    }
}