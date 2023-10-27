<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/15 14:51
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Office;

use App\Application\Wechat\Model\WechatOfficeUser;
use App\Application\Wechat\Service\Lib\AbstractOfficeComponent;
use App\Application\Wechat\Service\OfficeService;
use Overtrue\Socialite\Contracts\ProviderInterface;

class User extends AbstractOfficeComponent
{
    protected ProviderInterface $oauth;

    public function __construct(OfficeService $service)
    {
        parent::__construct($service);
        $this->oauth = $this->service->getApp()
            ->getOAuth();
    }

    /**
     *  授权方式 snsapi_userinfo 获取用户信息，snsapi_base 静默授权只获取open_id
     *
     * @param string $redirect_url
     * @param string $type
     * @param string $state
     * @return string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function getOauthUrl(string $redirect_url = '', string $type = 'snsapi_userinfo', string $state = ''): string
    {
        return $this->oauth->scopes([$type])
            ->redirect($redirect_url);
    }

    /**
     * @param string $code
     * @return WechatOfficeUser
     * @throws \Throwable
     */
    public function oauth(string $code = '', array $scopes = ['snsapi_base']): WechatOfficeUser
    {
        $user = $this->oauth->scopes($scopes)
            ->userFromCode($code);
        $original_info = $user->getRaw();
        $office_user = WechatOfficeUser::firstOrNew([
            'app_id' => $this->service->getAppId(),
            'openid' => $user->getId(),
        ]);
        $office_user->openid = $user->getId();
        $office_user->nickname = $original_info['nickname'] ?? '';
        $office_user->headimgurl = $original_info['headimgurl'] ?? '';
        $office_user->unionid = $original_info['unionid'] ?? '';
        $office_user->save();

        return $office_user;
    }
}