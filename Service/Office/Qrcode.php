<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/16 18:30
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Office;

use App\Application\Wechat\Model\WechatOfficeQrcode;

class Qrcode extends AbstractOfficeComponent
{
    /**
     * 该接口存在一些问题，暂不开放
     *
     * @param $prefix
     * @return bool
     * @throws \Throwable
     */
    protected function qrcodeJumpPublish($prefix)
    {
        $access_token = $this->service->getApp()->access_token->getToken()['access_token'] ?? '';
        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/qrcodejumppublish?access_token=' . $access_token;
        $res = $this->service->getApp()->http_client->post($url, [
            'json' => [
                'prefix' => $prefix
            ]
        ]);
        var_dump($res->getBody()
            ->getContents());

        return true;
    }

    /**
     * 该接口存在一些问题，暂不开放
     *
     * @param $prefix
     * @param $mini_program_appid
     * @param $page_path
     * @param $is_edit
     * @return bool
     * @throws \Throwable
     */
    protected function qrcodeJumpAdd($prefix, $mini_program_appid, $page_path, $is_edit = false)
    {
        $access_token = $this->service->getApp()->access_token->getToken()['access_token'] ?? '';
        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/qrcodejumpadd?access_token=' . $access_token;
        $res = $this->service->getApp()->http_client->post($url, [
            'json' => [
                'prefix' => $prefix,
                'appid' => $mini_program_appid,
                'path' => $page_path,
                'is_edit' => $is_edit ? 1 : 0
            ]
        ]);
        var_dump($res->getBody()
            ->getContents());

        return true;
    }

    /**
     * 永久参数二维码
     *
     * @param string $scene_str
     * @return bool
     */
    public function forever(string $scene_str)
    {

        return $this->makeQrcode($scene_str);
    }

    /**
     * 临时参数二维码
     *
     * @param string $scene_str
     * @param int    $expire_time
     * @return bool
     */
    public function temporary(string $scene_str, int $expire_time = 2592000): bool
    {
        return $this->makeQrcode($scene_str, $expire_time);
    }

    private function makeQrcode(string $scene_str, int $expire_time = 0)
    {
        if ($expire_time === 0) {
            $res = $this->service->getApp()->qrcode->forever($scene_str);
        } else {
            $res = $this->service->getApp()->qrcode->temporary($scene_str, $expire_time);
        }
        $ticket = $res['ticket'] ?? '';
        $expire_seconds = $res['expire_seconds'] ?? 0;
        $wechat_qrcode_url = $res['url'] ?? 0;
        $url = $this->service->getApp()->qrcode->url($ticket);
        $file_name = md5($scene_str . $expire_seconds) . '.jpg';
        $file_path_dir = BASE_PATH . '/runtime/temp/office/qrcode-temporary/';
        if (!is_dir($file_path_dir)) {
            mkdir($file_path_dir, 0700, true);
        }
        $file_path = $file_path_dir . $file_name;
        file_put_contents($file_path, file_get_contents($url));
        WechatOfficeQrcode::firstOrCreate([
            'app_id' => $this->service->getAppId(),
            'expire_time' => $expire_seconds == 0 ? 0 : (time() + $expire_seconds),
            'scene' => $scene_str,
            'file_path' => $file_path,
            'qrcode_url' => $wechat_qrcode_url,
        ]);

        return true;
    }
}