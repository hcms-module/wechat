<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/18 14:36
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Mini;

use App\Application\Wechat\Model\WechatMinQrcode;
use App\Exception\ErrorException;
use EasyWeChat\Kernel\Http\StreamResponse;

class Qrcode extends AbstractMiniComponent
{
    /**
     * 获取永久小程序码，通过scene形式
     *
     * @param        $scene
     * @param string $page
     * @param string $env_version
     * @param int    $width
     * @return WechatMinQrcode
     * @throws \Throwable
     * @throws ErrorException
     */
    public function getQrcodeByScene(
        $scene,
        string $page = '',
        string $env_version = 'release',
        int $width = 600
    ): WechatMinQrcode {

        $qrcode_model = WechatMinQrcode::firstOrCreate([
            'app_id' => $this->service->getAppId(),
            'scene' => $scene
        ]);
        if ($qrcode_model->id && file_exists($qrcode_model->file_path)) {
            //如果存在
            return $qrcode_model;
        }
        $optional = [
            'check_path' => $env_version === 'release', //如果是线上的小程序码，就需要检查
            'env_version' => $env_version,
            'width' => $width
        ];
        if ($page !== '') {
            //如果页面不传或是空，就默认是首页
            $optional['page'] = $page;
        }

        $file_name = md5($scene . $page . $env_version) . '.jpg';
        $file_path_dir = BASE_PATH . '/runtime/temp/mini/qrcode/';
        if (!is_dir($file_path_dir)) {
            mkdir($file_path_dir, 0700, true);
        }
        $file_path = $file_path_dir . $file_name;

        $response = $this->service->getApp()->app_code->getUnlimit($scene, $optional);
        // 保存小程序码到文件
        if ($response instanceof StreamResponse) {
            $response->save($file_path_dir, $file_name);
        } else {
            throw new ErrorException('生成小程序码错误');
        }
        $qrcode_model->page = $page;
        $qrcode_model->width = $width;
        $qrcode_model->env_version = $env_version;
        $qrcode_model->file_path = $file_path;
        $qrcode_model->save();

        return $qrcode_model;
    }
}