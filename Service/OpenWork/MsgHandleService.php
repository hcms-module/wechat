<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/6/2 17:50
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\OpenWork;

use App\Application\Wechat\Model\WechatOpenworkMsg;
use App\Application\Wechat\Service\OpenWork\MsgHandle\EventMsgHandleInterface;
use App\Application\Wechat\Service\OpenWork\MsgHandle\Unlicensed;
use Hyperf\Utils\Codec\Json;

class MsgHandleService
{
    protected WechatOpenworkMsg $msg;


    private array $event_handle_list = [
//        'enter_agent' => '',//用户进入应用
        'unlicensed_notify' => Unlicensed::class,//用户未激活接口许可
    ];

    /**
     * @param WechatOpenworkMsg $msg
     */
    public function __construct(WechatOpenworkMsg $msg) { $this->msg = $msg; }

    public function handle(): bool
    {
        $msg_content = Json::decode($this->msg->msg_content, true);
        $msg_type = $msg_content['MsgType'] ?? '';
        if ($msg_type === 'event') {
            //事件消息
            $event = $msg_content['Event'] ?? '';
            $handle_class = $this->event_handle_list[$event] ?? '';
            if ($handle_class !== '') {
                /**
                 * @var EventMsgHandleInterface $object
                 */
                $object = new $handle_class();
                try {
                    $res = $object->handle($msg_content);
                    $this->msg->handle_res = $res ? 1 : 0;
                } catch (\Throwable $exception) {
                    $this->msg->handle_res = 0;
                    $this->msg->handle_msg = $exception->getMessage();
                }
                $this->msg->save();
            }
        }

        return true;
    }
}