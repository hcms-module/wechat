<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/5/25 16:25
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\OpenWork\EventHandle;

use App\Application\Wechat\Model\WechatOpenworkCorp;
use App\Application\Wechat\Service\OpenWorkService;
use Hyperf\Utils\Codec\Json;
use Throwable;

class CreateAuth implements EventHandleInterface
{
    /**
     * @param array $event_content
     * @return bool
     * @throws Throwable
     */
    public function handle(array $event_content): bool
    {
        $auth_code = $event_content['AuthCode'] ?? [];
        $open_work = new OpenWorkService();
        $res = $open_work->getApp()->corp->getPermanentByCode($auth_code);
        $errcode = $res['errcode'] ?? 0;
        $errmsg = $res['errmsg'] ?? '请求错误';
        if ($errcode != 0) {
            throw new \Exception($errmsg);
        }
        $corpid = $res['auth_corp_info']['corp-id'] ?? '';
        $auth_corp = WechatOpenworkCorp::firstOrNew(['corpid' => $corpid]);
        $auth_corp->corpid = $res['auth_corp_info']['corpid'] ?? '';
        $auth_corp->corp_name = $res['auth_corp_info']['corp_name'] ?? '';
        $auth_corp->permanent_code = $res['permanent_code'] ?? '';
        $auth_corp->access_token = $res['access_token'] ?? '';
        $auth_corp->expires_in = time() + ($res['expires_in'] ?? 0);
        $auth_corp->auth_corp_info = Json::encode($res['auth_corp_info'] ?? []);
        $auth_corp->auth_info = Json::encode($res['auth_info'] ?? []);
        $auth_corp->auth_user_info = Json::encode($res['auth_user_info'] ?? []);
        $auth_corp->register_code_info = Json::encode($res['register_code_info'] ?? []);
        $auth_corp->access_token = $res['state'] ?? '';
        if (!$auth_corp->save()) {
            throw  new \Exception('保存授权信息错误');
        }

        return true;
    }
}