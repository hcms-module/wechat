<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/17 17:06
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Mini;

use App\Application\Wechat\Model\WechatMinPhone;
use App\Application\Wechat\Model\WechatMinUser;
use App\Application\Wechat\Service\Lib\AbstractMiniComponent;
use App\Exception\ErrorException;

class User extends AbstractMiniComponent
{

    const EXPIRE_TIME = 2592000; //默认30天


    /**
     * 通过授权的code获取手机号码
     *
     * @param string $code
     * @return WechatMinPhone
     * @throws \Throwable
     */
    function getPhoneByCode(string $code): WechatMinPhone
    {
        $res = $this->service->postJson('/wxa/business/getuserphonenumber', compact('code'));
        $errcode = $res['errcode'] ?? -1;
        if ($errcode === 0) {
            $phone_info = $res['phone_info'] ?? [];
            //国外手机会带有区号
            $phone_number = $phone_info['phoneNumber'] ?? '';
            //国家区号
            $country_code = $phone_info['countryCode'] ?? '';

            return WechatMinPhone::firstOrCreate([
                'app_id' => $this->service->getAppId(),
                'phone' => $phone_number,
                'country_code' => $country_code
            ]);
        } else {
            throw new ErrorException($res['errmsg'] ?? "");
        }
    }

    /**
     * 根据授权的code获取openid 或 unionid
     *
     * @param string $code
     * @return WechatMinUser
     * @throws \Throwable
     */
    function getOpenIdByCode(string $code): WechatMinUser
    {
        $res = $this->service->getApp()
            ->getUtils()
            ->codeToSession($code);
        $session_key = $res['session_key'] ?? '';
        $openid = $res['openid'] ?? '';
        $unionid = $res['unionid'] ?? '';

        $app_id = $this->service->getAppId();
        $user = WechatMinUser::firstOrNew([
            'app_id' => $app_id,
            'openid' => $openid,
        ]);
        $user->session_key = $session_key;
        $user->unionid = $unionid;
        $user->token = sha1($app_id . $openid . $session_key . time());
        $user->expire_time = time() + self::EXPIRE_TIME;
        if (!$user->save()) {
            throw new ErrorException('保存登录信息错误');
        }

        return $user;
    }
}