<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/5/25 10:06
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service;

use EasyWeChat\OpenWork\Application;

/**
 * 企业微信服务商
 */
class OpenWorkService
{
    protected Application $app;

    public function __construct()
    {
        $work_setting = (new WechatSetting())->getWorkSetting();
        $config = [
            'corp_id' => $work_setting['wechat_openwork_corpid'] ?? '',
            'provider_secret' => $work_setting['wechat_openwork_secret'] ?? '',
            'suite_id' => $work_setting['wechat_work_suite_id'] ?? '',
            'suite_secret' => $work_setting['wechat_work_secret'] ?? '',
            'token' => $work_setting['wechat_work_token'] ?? '',
            'aes_key' => $work_setting['wechat_work_aes_key'] ?? '',
        ];

        $this->app = new Application($config);
    }

    /**
     * @return Application
     */
    public function getApp(): Application
    {
        return $this->app;
    }
}