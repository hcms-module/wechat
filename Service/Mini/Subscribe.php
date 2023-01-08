<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/21 11:21
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\Mini;

use App\Application\Wechat\Model\WechatMinSubscribeSendRecord;
use App\Exception\ErrorException;
use Hyperf\Utils\Codec\Json;

class Subscribe extends AbstractMiniComponent
{
    /**
     * 发送微信公众号模板消息
     *
     * @param string $openid       小程序或公众号用户openid
     * @param string $office_appid 公众号appid
     * @param string $template_id  公众号模板id
     * @param string $page         小程序页面
     * @param array  $data
     * @return bool
     * @throws \Throwable
     */
    function uniform(string $openid, string $office_appid, string $template_id, string $page, array $data): bool
    {
        $post_data = [
            'touser' => $openid,
            'mp_template_msg' => [
                'appid' => $office_appid,
                'template_id' => $template_id,
                'url' => '',
                'data' => $data,
                'miniprogram' => [
                    'appid' => $this->service->getAppId(),
                    'pagepath' => $page,
                ]
            ]
        ];
        $res = $this->service->getApp()->uniform_message->send($post_data);
        $errcode = intval($res['errcode'] ?? -1);
        $errmsg = $res['errmsg'] ?? '发送请求错误';
        $model = WechatMinSubscribeSendRecord::firstOrCreate([
            'app_id' => $this->service->getAppId() . '/' . $office_appid,
            'template_id' => $template_id,
            'openid' => $openid,
            'page' => $page,
            'post_data' => Json::encode($data)
        ]);
        if ($errcode !== 0) {
            $model->status = WechatMinSubscribeSendRecord::STATUS_NO;
            $model->result = $errmsg;
            $model->save();
            throw new ErrorException($errmsg);
        }
        //发送成功
        $ms_id = $res['msgid'] ?? 'ok';

        $model->status = WechatMinSubscribeSendRecord::STATUS_YES;
        $model->result = $ms_id;

        return $model->save();
    }

    /**
     * 发送订阅消息
     *
     * @param string $template_id
     * @param string $openid
     * @param string $page
     * @param array  $data
     * @return bool
     * @throws \Throwable
     */
    function send(string $template_id, string $openid, string $page, array $data): bool
    {
        $post_data = [
            'template_id' => $template_id,
            'touser' => $openid,
            'page' => $page,
            'data' => $data,
        ];
        $res = $this->service->getApp()->subscribe_message->send($post_data);
        $errcode = intval($res['errcode'] ?? -1);
        $errmsg = $res['errmsg'] ?? '发送请求错误';
        $model = WechatMinSubscribeSendRecord::firstOrCreate([
            'app_id' => $this->service->getAppId(),
            'template_id' => $template_id,
            'openid' => $openid,
            'page' => $page,
            'post_data' => Json::encode($data)
        ]);
        if ($errcode !== 0) {
            $model->status = WechatMinSubscribeSendRecord::STATUS_NO;
            $model->result = $errmsg;
            $model->save();
            throw new ErrorException($errmsg);
        }
        //发送成功
        $model->status = WechatMinSubscribeSendRecord::STATUS_YES;
        $model->result = $res['msgid'] ?? '';

        return $model->save();
    }
}