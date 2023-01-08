<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/17 16:34
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service;

use App\Application\Wechat\Model\WechatApp;
use App\Application\Wechat\Service\Lib\WechatRequest;
use App\Application\Wechat\Service\Mini\channel;
use App\Application\Wechat\Service\Mini\Content;
use App\Application\Wechat\Service\Mini\Message;
use App\Application\Wechat\Service\Mini\Qrcode;
use App\Application\Wechat\Service\Mini\Shop;
use App\Application\Wechat\Service\Mini\Subscribe;
use App\Application\Wechat\Service\Mini\Url;
use App\Application\Wechat\Service\Mini\User;
use App\Exception\ErrorException;
use EasyWeChat\Factory;
use EasyWeChat\MiniProgram\Application;
use Hyperf\Utils\Codec\Json;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * @method User user()
 * @method Qrcode qrcode()
 * @method Url url()
 * @method Subscribe subscribe()
 * @method Shop shop()
 * @method Channel channel()
 * @method Content content()
 * @method Message message()
 */
class MiniProgramService
{
    protected Application $app;
    protected string $app_id;

    const API_HOST = "https://api.weixin.qq.com";

    /**
     * @param string $app_key
     * @throws ErrorException
     */
    public function __construct(string $app_key = '')
    {
        $where = [
            ['app_type', '=', WechatApp::APP_TYPE_MINI]
        ];
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
        if ($wechat_app->token) {
            $config['token'] = $wechat_app->token;
        }
        if ($wechat_app->aes_key) {
            $config['aes_key'] = $wechat_app->aes_key;
        }
        $this->app = Factory::miniProgram($config);
        //用于swoole的request原因，所以在这里需要重写
        $this->app['request'] = new WechatRequest();
    }

    public function __call($name, $arguments)
    {
        $name = ucfirst($name);
        $class_name = "App\\Application\\Wechat\\Service\\Mini\\{$name}";
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

    /**
     * @return string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     */
    public function getToken(): string
    {
        return $this->app->access_token->getToken()['access_token'] ?? '';
    }

    /**
     * 接口请求封装
     *
     * @param string $uri
     * @param array  $data
     * @return mixed
     * @throws ErrorException
     */
    public function request(string $uri, array $data)
    {
        try {
            $token = $this->getToken();
            $url = self::API_HOST . $uri . "?access_token={$token}";
            $res = $this->getApp()->http_client->post($url, [
                'json' => $data
            ]);
        } catch (\Throwable $exception) {
            throw new ErrorException("请求错误" . $exception->getMessage());
        } catch (InvalidArgumentException $exception) {
            throw new ErrorException("请求参数错误" . $exception->getMessage());
        }


        $result = Json::decode($res->getBody()
            ->getContents());
        $errcode = $result['errcode'] ?? -1;
        if ($errcode !== 0) {
            throw new ErrorException("请求错误" . $result['errmsg'] ?? '');
        }

        return $result;
    }
}