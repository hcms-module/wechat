<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/11/17 18:00
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Office;

use App\Exception\ErrorException;
use Hyperf\Cache\Annotation\Cacheable;

class Material extends AbstractOfficeComponent
{
    /**
     * @Cacheable(prefix="WechatUploadImage", ttl=86400, listener="wechat-upload-image")
     * @param $path
     * @return array
     * @throws ErrorException
     */
    public function uploadImage($path): array
    {
        try {
            $result = $this->service->getApp()->material->uploadThumb($path);
            if (empty($result['media_id'])) {
                throw new ErrorException('上传失败' . $result['errmsg'] ?? '');
            }

            return $result;
        } catch (\Throwable $exception) {
            throw new ErrorException($exception->getMessage());
        }
    }
}