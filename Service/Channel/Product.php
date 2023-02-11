<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/9/15 14:32
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Channel;

use App\Application\Wechat\Service\Lib\AbstractMiniChannelComponent;
use App\Exception\ErrorException;

class Product extends AbstractMiniChannelComponent
{

    /**
     * 获取商品详情
     *
     * @param string $product_id
     * @param int    $data_type
     * @return array
     * @throws ErrorException
     */
    public function getDetail(string $product_id, int $data_type = 1): array
    {
        $uri = "/channels/ec/product/get";
        $result = $this->channel->request($uri, compact('product_id', 'data_type'));

        return $result['product'] ?? [];
    }

    /**
     * 获取商品列表【只获取id列表】
     *
     * @param int    $status
     * @param int    $page_size
     * @param string $next_key
     * @return array
     */
    public function getList(int $status = 5, int $page_size = 30, string $next_key = ''): array
    {
        $uri = "/channels/ec/product/list/get";
        $result = $this->channel->request($uri, [
            'status' => $status,
            'page_size' => $page_size,
            'next_key' => $next_key
        ]);

        return [
            'product_ids' => $result['product_ids'] ?? [],
            'next_key' => $result['next_key'] ?? '',
            'total_num' => $result['total_num'] ?? 0,
        ];
    }
}