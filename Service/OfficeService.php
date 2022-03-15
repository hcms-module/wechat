<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/15 14:44
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service;

use App\Application\Wechat\Model\WechatApp;
use App\Application\Wechat\Service\Office\Jssdk;
use App\Application\Wechat\Service\Office\Message;
use App\Application\Wechat\Service\Office\User;
use App\Exception\ErrorException;
use EasyWeChat\Factory;
use EasyWeChat\OfficialAccount\Application;

/**
 * @method User user()
 * @method Jssdk jssdk()
 * @method Message message()
 */
class OfficeService
{
    protected Application $app;
    protected string $app_id;

    /**
     * @param string $app_key
     * @throws ErrorException
     */
    public function __construct(string $app_key = '')
    {
        $where = [];
        if ($app_key !== '') {
            $where[] = ['app_key', '=', $app_key];
        }
        /**
         * @var  WechatApp $wechat_app
         */
        $wechat_app = WechatApp::where($where)
            ->orderBy('id', 'DESC')
            ->first();
        if (!$wechat_app) {
            throw new ErrorException('找不到该应用' . $app_key);
        }
        $this->app_id = $wechat_app->app_id;
        $config = ['app_id' => $wechat_app->app_id, 'secret' => $wechat_app->app_secret, 'response_type' => 'array'];
        if (!$wechat_app->token) {
            $config['token'] = $wechat_app->token;
        }
        if (!$wechat_app->aes_key) {
            $config['aes_key'] = $wechat_app->aes_key;
        }
        $this->app = Factory::officialAccount($config);
    }

    public function __call($name, $arguments)
    {
        $name = ucfirst($name);
        $class_name = "App\\Application\\Wechat\\Service\\Office\\{$name}";
        if (!class_exists($class_name)) {
            throw new ErrorException('对象不存在' . $class_name);
        }

        return new  $class_name($this);
    }


    /**
     * @return Application
     */
    public function getApp(): Application
    {
        return $this->app;
    }

    /**
     * @return string
     */
    public function getAppId(): string
    {
        return $this->app_id;
    }
}