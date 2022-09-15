<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/9/15 11:14
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\MiniShop;

use App\Exception\ErrorException;

class Order extends AbstractMiniShopComponent
{

    /**
     * 获取订单列表
     *
     * @param int    $status
     * @param int    $page
     * @param int    $page_size
     * @param string $end_create_time
     * @param string $start_create_time
     * @return array|mixed
     * @throws ErrorException
     */
    public function getList(
        int $status,
        int $page = 1,
        int $page_size = 100,
        string $end_create_time = '',
        string $start_create_time = '1970-01-01 08:00:00'
    ) {
        $uri = "/product/order/get_list";
        $data = [
            'start_create_time' => $start_create_time,
            'end_create_time' => $end_create_time ?: date('Y-m-d H:i:s'),
            'page' => $page,
            'status' => $status,
            'page_size' => $page_size,
        ];

        $result = $this->shop->request($uri, $data);

        return $result['orders'] ?? [];
    }

    /**
     * 获取订单详情
     *
     * @param string $order_id
     * @return array|mixed
     * @throws ErrorException
     */
    public function getDetail(string $order_id)
    {
        $uri = "/product/order/get";
        $data = [
            'order_id' => $order_id,
        ];

        $result = $this->shop->request($uri, $data);

        return $result['order'] ?? [];
    }
}