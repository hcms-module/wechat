<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/15 14:51
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Office;

use App\Application\Wechat\Model\WechatOfficeUser;

class User extends AbstractOfficeComponent
{

    /**
     * @param string $redirect_url
     * @param string $type 授权方式 snsapi_userinfo 获取用户信息，snsapi_base静默授权只获取open_id
     * @return string
     */
    public function getOauthUrl(string $redirect_url = '', string $type = 'snsapi_userinfo'): string
    {
        return $this->service->getApp()->oauth->scopes([$type])
            ->redirect($redirect_url);
    }

    /**
     * @param string $code
     * @return WechatOfficeUser
     * @throws \Throwable
     */
    public function oauth(string $code = ''): WechatOfficeUser
    {
        $user = $this->service->getApp()->oauth->userFromCode($code);
        $original_info = $user->getRaw();
        $office_user = WechatOfficeUser::firstOrNew([
            'app_id' => $this->service->getAppId(),
            'openid' => $original_info['openid'] ?? ''
        ]);
        $office_user->nickname = $original_info['nickname'] ?? '';
        $office_user->sex = $original_info['sex'] ?? 0;
        $office_user->province = $original_info['province'] ?? '';
        $office_user->city = $original_info['city'] ?? '';
        $office_user->country = $original_info['country'] ?? '';
        $office_user->headimgurl = $original_info['headimgurl'] ?? '';
        $office_user->unionid = $original_info['unionid'] ?? '';
        $office_user->save();

        return $office_user;
    }
}