<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2023/2/13 15:03
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Mini;

use App\Application\Wechat\Service\Lib\AbstractMiniComponent;
use App\Application\Wechat\Service\WxpayService;

class Pay extends AbstractMiniComponent
{
    public function getPayConfig(string $open_id, string $out_trade_no, int $total_fee, string $description = ''): array
    {
        $pay_service = new WxpayService();

        return $pay_service->getMiniAppConfig($this->service->getAppId(), $open_id, $out_trade_no, $total_fee,
            $description);
    }
}