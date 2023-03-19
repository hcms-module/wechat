<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/5/25 10:06
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service;

use App\Application\Wechat\Service\Lib\WechatRequest;
use EasyWeChat\Factory;
use EasyWeChat\Work\Application;
use Hyperf\Di\Annotation\Inject;

class WorkService
{
    #[Inject]
    protected WechatSetting $wechat_setting;
    protected Application $app;

    public function __construct()
    {
        $work_setting = $this->wechat_setting->getWorkSetting();
        $config = [
            'corp_id' => $work_setting['wechat_work_corpid'] ?? '',
            'secret' => $work_setting['wechat_work_secret'] ?? '',
            'agent_id' => $work_setting['wechat_work_agent_id'] ?? '',
            'token' => $work_setting['wechat_work_token'] ?? '',
            'aes_key' => $work_setting['wechat_work_aes_key'] ?? '',
        ];

        $this->app = Factory::work($config);
        $this->app['request'] = new WechatRequest();
    }

    /**
     * @return Application
     */
    public function getApp(): Application
    {
        return $this->app;
    }
}