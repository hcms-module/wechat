<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/16 11:20
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Office;

use App\Application\Wechat\Model\WechatOfficeTemplateSendRecord;
use App\Application\Wechat\Service\Lib\AbstractOfficeComponent;
use Hyperf\Codec\Json;

class Template extends AbstractOfficeComponent
{

    /**
     * @param string $open_id
     * @param string $template_id
     * @param array  $data
     * @param string $url
     * @param string $mini_program_appid
     * @param string $client_msg_id
     * @return bool
     */
    public function sendTemplateMsg(
        string $open_id,
        string $template_id,
        array $data,
        string $url = '',
        string $mini_program_appid = '',
        string $client_msg_id = ''
    ): bool {
        foreach ($data as &$item) {
            //调整格式
            if (!is_array($item)) {
                $item = ['value' => $item, 'color' => '#173177'];
            }
        }
        $post_data = [
            'touser' => $open_id,
            'template_id' => $template_id,
            'url' => $mini_program_appid === '' ? $url : '',
            'client_msg_id' => $client_msg_id,
            'miniprogram' => [
                'appid' => $mini_program_appid,
                'pagepath' => $url,
            ],
            'data' => $data,
        ];
        $model = WechatOfficeTemplateSendRecord::firstOrCreate([
            'open_id' => $open_id,
            'template_id' => $template_id,
            'post_data' => Json::encode($data),
            'url' => $url,
            'mini_program_appid' => $mini_program_appid,
            'app_id' => $this->service->getAppId(),
        ]);
        try {
            $res = $this->api_client->postJson('/cgi-bin/message/template/send', $post_data);
            $errcode = intval($res['errcode'] ?? -1);
            if ($errcode === 0) {
                $model->status = WechatOfficeTemplateSendRecord::SEND_STATUS_SUCCESS;
                $model->result = "SUCCESS";
            } else {
                $model->status = WechatOfficeTemplateSendRecord::SEND_STATUS_FAILED;
                $model->result = substr($res['errmsg'] ?? 'FAILED', 0, 500);
            }

            return $model->save();
        } catch (\Throwable $exception) {
            $model->status = WechatOfficeTemplateSendRecord::SEND_STATUS_FAILED;
            $model->result = substr($exception->getMessage(), 0, 500);
            $model->save();

            return false;
        }
    }
}