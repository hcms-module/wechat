<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/16 18:30
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Office;

use App\Application\Wechat\Model\WechatOfficeQrcode;
use App\Application\Wechat\Service\Lib\AbstractOfficeComponent;
use App\Exception\ErrorException;
use Throwable;

class Qrcode extends AbstractOfficeComponent
{
    /**
     * 该接口存在一些问题，暂不开放
     *
     * @param $prefix
     * @throws Throwable
     */
    protected function qrcodeJumpPublish($prefix): void
    {
        $res = $this->api_client->postJson('/cgi-bin/wxopen/qrcodejumppublish', [
            'prefix' => $prefix
        ]);
        var_dump($res->toArray());
    }

    /**
     * 该接口存在一些问题，暂不开放
     *
     * @param string $prefix
     * @param string $mini_program_appid
     * @param string $page_path
     * @param bool   $is_edit
     * @throws Throwable
     */
    protected function qrcodeJumpAdd(
        string $prefix,
        string $mini_program_appid,
        string $page_path,
        bool $is_edit = false
    ): void {
        $res = $this->api_client->postJson('/cgi-bin/wxopen/qrcodejumpadd', [
            'prefix' => $prefix,
            'appid' => $mini_program_appid,
            'path' => $page_path,
            'is_edit' => $is_edit ? 1 : 0
        ]);
        var_dump($res->toArray());
    }

    /**
     * 永久参数二维码
     *
     * @param string $scene_str
     * @return WechatOfficeQrcode
     * @throws ErrorException
     */
    public function forever(string $scene_str): WechatOfficeQrcode
    {
        return $this->makeQrcode($scene_str);
    }

    /**
     * 临时参数二维码
     *
     * @param string $scene_str
     * @param int    $expire_time
     * @return WechatOfficeQrcode
     * @throws ErrorException
     */
    public function temporary(string $scene_str, int $expire_time = 2592000): WechatOfficeQrcode
    {
        return $this->makeQrcode($scene_str, $expire_time);
    }

    private function makeQrcode(string $scene_str, int $expire_time = 0): WechatOfficeQrcode
    {
        if ($expire_time === 0) {
            $res = $this->api_client->postJson('/cgi-bin/qrcode/create', [
                'action_info' => [
                    'scene' => [
                        'scene_str' => $scene_str,
                    ]
                ],
                'action_name' => 'QR_LIMIT_STR_SCENE'
            ]);
        } else {
            $res = $this->api_client->postJson('/cgi-bin/qrcode/create', [
                'scene_str' => $scene_str,
                'expire_seconds' => $expire_time ?: 60,
                'action_name' => 'QR_STR_SCENE'
            ]);
        }
        $ticket = $res['ticket'] ?? '';
        if (!$ticket) {
            throw new ErrorException("获取参数二维码 ticket 错误");
        }
        $expire_seconds = $res['expire_seconds'] ?? 0;
        $wechat_qrcode_url = $res['url'] ?? 0;
        $url = sprintf('https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=%s', urlencode($ticket));
        $file_name = md5($scene_str . $expire_seconds) . '.jpg';
        $file_path_dir = BASE_PATH . '/runtime/temp/office/qrcode/';
        if (!is_dir($file_path_dir)) {
            mkdir($file_path_dir, 0700, true);
        }
        $file_path = $file_path_dir . $file_name;
        file_put_contents($file_path, file_get_contents($url));

        return WechatOfficeQrcode::firstOrCreate([
            'app_id' => $this->service->getAppId(),
            'expire_time' => $expire_seconds == 0 ? 0 : (time() + $expire_seconds),
            'scene' => $scene_str,
            'file_path' => $file_path,
            'qrcode_url' => $wechat_qrcode_url,
        ]);
    }
}