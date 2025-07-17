<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/5/25 10:06
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service;

use App\Application\Wechat\Service\Work\Department;
use App\Application\Wechat\Service\Work\OA;
use App\Application\Wechat\Service\Work\User;
use App\Exception\ErrorException;
use EasyWeChat\Kernel\Contracts\Server;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use EasyWeChat\Work\Application;
use Psr\Http\Message\ServerRequestInterface;


/**
 * @property Department $department;
 * @property User       $user      ;
 * @property OA         $oa        ;
 */
class WorkService
{

    protected Application $app;

    protected string $corp_id;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct()
    {
        $work_setting = (new WechatSetting())->getWorkSetting();
        $corp_id = $work_setting['wechat_work_corpid'] ?? '';
        $config = [
            'corp_id' => $corp_id,
            'secret' => $work_setting['wechat_work_secret'] ?? '',
            'token' => $work_setting['wechat_work_token'] ?? '',
            'aes_key' => $work_setting['wechat_work_aes_key'] ?? '',
            'suite_id' => $work_setting['wechat_work_suite_id'] ?? '',
            'suite_secret' => $work_setting['wechat_work_suite_secret'] ?? '',
        ];
        $app = new Application($config);
        $this->app = $app;
        $this->corp_id = $corp_id;
    }

    public function getClient(): AccessTokenAwareClient
    {
        return $this->getApp()
            ->getClient();
    }

    /**
     * @throws \ReflectionException
     * @throws InvalidArgumentException
     * @throws \Throwable
     */
    public function getServer(ServerRequestInterface $request): \EasyWeChat\Work\Server|Server
    {
        return $this->app->setRequest($request)
            ->getServer();
    }

    /**
     * @return Application
     */
    public function getApp(): Application
    {
        return $this->app;
    }

    public function __get($name)
    {
        $name = ucfirst($name);
        $class_name = "App\\Application\\Wechat\\Service\\Work\\{$name}";
        if (!class_exists($class_name)) {
            throw new ErrorException('对象不存在' . $class_name);
        }

        return new  $class_name($this->getClient(), $this->corp_id);
    }
}