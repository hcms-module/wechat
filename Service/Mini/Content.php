<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2023/1/6 21:46
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Mini;

use App\Exception\ErrorException;

class Content extends AbstractMiniComponent
{

    public function checkVideo($path): string
    {
        try {
            $res = $this->service->getApp()->content_security->checkAudioAsync($path);
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

    public function checkImage($path): string
    {
        try {
            $res = $this->service->getApp()->content_security->checkImageAsync($path);
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
     * @param string $text
     * @return bool
     * @throws ErrorException
     */
    public function checkText(string $text): bool
    {
        try {
            $res = $this->service->getApp()->content_security->checkText($text);
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