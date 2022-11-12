<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/5/27 18:37
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service;

use App\Service\AbstractSettingService;

class WechatSetting extends AbstractSettingService
{

    public function getWxpaySetting(string $key = '', $default = '')
    {
        return $this->getSettings('wxpay', $key, $default);
    }

    public function saveWxpaySetting(array $setting_data): bool
    {
        return $this->saveSetting($setting_data, 'wxpay');
    }

    public function getWorkSetting(string $key = '', $default = '')
    {
        return $this->getSettings('wechat_work', $key, $default);
    }

    public function saveWorkSetting(array $setting_data): bool
    {
        return $this->saveSetting($setting_data, 'wechat_work');
    }
}