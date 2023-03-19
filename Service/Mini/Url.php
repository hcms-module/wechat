<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/18 15:24
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Mini;

use App\Application\Wechat\Model\WechatMinUrl;
use App\Application\Wechat\Service\Lib\AbstractMiniComponent;
use App\Exception\ErrorException;

class Url extends AbstractMiniComponent
{
    /**
     * @param string $query
     * @param string $path
     * @param int    $expire_time
     * @param string $env_version
     * @return WechatMinUrl
     * @throws \Throwable
     */
    public function generate(
        string $query = '',
        string $path = '',
        int $expire_time = 2592000,
        string $env_version = 'release'
    ): WechatMinUrl {
        /**
         * @var WechatMinUrl $url_model
         */
        $url_model = WechatMinUrl::where(function ($query) {
            $query->where('expire_time', 0)
                ->orWhere('expire_time', '>', time());
        })
            ->firstOrCreate([
                'app_id' => $this->service->getAppId(),
                'query' => $query,
                'path' => $path,
                'env_version' => $env_version
            ]);
        if ($url_model->url_link) {
            return $url_model;
        }
        $is_expire = $expire_time !== 0;
        $expire_time = time() + $expire_time;
        $res = $this->service->postJson('/wxa/generate_urllink', [
            'query' => $query,
            'path' => $path,
            'is_expire' => $is_expire,
            'expire_type' => 0,
            'expire_time' => $expire_time,
            'env_version' => $env_version,
        ]);
        $errcode = $res['errcode'] ?? -1;
        if ($errcode === 0) {
            $url_link = $res['url_link'] ?? '';
            $url_model->is_expire = $is_expire;
            $url_model->expire_time = $expire_time;
            $url_model->env_version = $env_version;
            $url_model->url_link = $url_link;
            $url_model->save();

            return $url_model;
        } else {
            throw new ErrorException($res['errmsg'] ?? '');
        }
    }
}