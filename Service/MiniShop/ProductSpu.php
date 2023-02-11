<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/9/15 11:59
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\MiniShop;

use App\Exception\ErrorException;

class ProductSpu extends AbstractMiniShopComponent
{
    /**
     * 获取商品列表
     *
     * @param int $status
     * @param int $page
     * @param int $page_size
     * @param int $need_edit_spu
     * @param int $source
     * @return array
     * @throws ErrorException
     */
    public function getList(
        int $status = 5,
        int $page = 1,
        int $page_size = 100,
        int $need_edit_spu = 0,
        int $source = 1
    ): array {
        $uri = "/product/spu/get_list";
        $result = $this->shop->request($uri, compact('status', 'page_size', 'page', 'need_edit_spu', 'source'));

        return $result['spus'] ?? [];
    }
}