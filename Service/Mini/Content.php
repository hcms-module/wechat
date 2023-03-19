<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2023/1/6 21:46
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Mini;

use App\Application\Wechat\Service\Lib\AbstractMiniComponent;
use App\Exception\ErrorException;

class Content extends AbstractMiniComponent
{

    /**
     * 音频检测
     *
     * @param string $media_url
     * @return string
     * @throws ErrorException
     */
    public function checkVideo(string $media_url): string
    {
        return $this->checkMedia($media_url, 1);
    }

    /**
     * 视频检测
     *
     * @param string $media_url
     * @return string
     * @throws ErrorException
     */
    public function checkImage(string $media_url): string
    {
        return $this->checkMedia($media_url, 2);
    }

    private function checkMedia(string $media_url, int $media_type): string
    {
        try {
            $res = $this->service->postJson('/wxa/media_check_async', compact('media_url', 'media_type'));
            $errcode = $res['errcode'] ?? -1;
            $errmsg = $res['errmsg'] ?? '有错误';
            if ($errcode !== 0) {
                throw new ErrorException($errmsg);
            }

            return $res['trace_id'] ?? '';
        } catch (\Throwable $exception) {
            throw new ErrorException($exception->getMessage());
        }
    }

    /**
     * 文本内容
     *
     * @param string $content
     * @param int    $scene
     * @param string $openid
     * @return bool
     * @throws ErrorException
     */
    public function checkText(string $content, int $scene = 3, string $openid = ''): bool
    {
        try {
            $res = $this->service->postJson('/wxa/msg_sec_check', compact('content', 'scene', 'openid'));
            $errcode = $res['errcode'] ?? -1;
            $errmsg = $res['errmsg'] ?? '不通过';
            if ($errcode !== 0) {
                throw new ErrorException($errmsg);
            }

            return true;
        } catch (\Throwable $exception) {
            throw new ErrorException($exception->getMessage());
        }

    }
}