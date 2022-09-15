<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/9/15 11:58
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\MiniShop;

use App\Application\Wechat\Service\Mini\Shop;

abstract class AbstractMiniShopComponent
{
    protected Shop $shop;

    /**
     * @param Shop $shop
     */
    public function __construct(Shop $shop) { $this->shop = $shop; }
}