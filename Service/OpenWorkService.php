<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/5/25 10:06
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service;

use App\Application\Wechat\Model\WechatOpenworkCorp;
use App\Application\Wechat\Service\Lib\WechatRequest;
use EasyWeChat\Factory;
use EasyWeChat\OpenWork\Application;
use Hyperf\Di\Annotation\Inject;

/**
 * 企业微信服务商
 */
class OpenWorkService
{

    /**
     * @Inject()
     */
    protected WechatSetting $wechat_setting;
    protected Application $app;

    public function __construct()
    {
        $work_setting = $this->wechat_setting->getWorkSetting();
        $config = [
            'corp_id' => $work_setting['wechat_openwork_corpid'] ?? '',
            'secret' => $work_setting['wechat_openwork_secret'] ?? '',
            'suite_id' => $work_setting['wechat_work_suite_id'] ?? '',
            'suite_secret' => $work_setting['wechat_work_secret'] ?? '',
            'token' => $work_setting['wechat_work_token'] ?? '',
            'aes_key' => $work_setting['wechat_work_aes_key'] ?? '',
        ];

        $this->app = Factory::openWork($config);
        $this->app['request'] = new WechatRequest();
    }

    /**
     * @return Application
     */
    public function getApp(): Application
    {
        return $this->app;
    }

    public function getWork($corpid): \EasyWeChat\OpenWork\Work\Application
    {
        $permanent_code = WechatOpenworkCorp::where('corpid', $corpid)
            ->value('permanent_code', '');

        return $this->getApp()
            ->work($corpid, $permanent_code);
    }
}