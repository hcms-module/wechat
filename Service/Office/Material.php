<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/11/17 18:00
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Office;

use App\Application\Wechat\Service\Lib\AbstractOfficeComponent;
use App\Exception\ErrorException;
use EasyWeChat\Kernel\HttpClient\Response;
use Hyperf\Cache\Annotation\Cacheable;

class Material extends AbstractOfficeComponent
{
    /**
     * @param $path
     * @return array
     * @throws ErrorException
     */
    #[Cacheable(prefix: "WechatUploadImage", ttl: 86400, listener: "wechat-upload-image")]
    public function uploadImage($path): array
    {
        try {
            if (!file_exists($path)) {
                throw new ErrorException('文件不存在');
            }
            $result = $this->httpUpload($path, 'image');
            if (empty($result['media_id'])) {
                throw new ErrorException('上传失败' . $result['errmsg'] ?? '');
            }

            return $result->toArray();
        } catch (\Throwable $exception) {
            throw new ErrorException($exception->getMessage());
        }
    }


    protected function httpUpload(string $path, string $type): Response
    {
        $url = "/cgi-bin/media/upload?type={$type}";

        return $this->api_client->withFile($path, 'media')
            ->post($url);
    }
}