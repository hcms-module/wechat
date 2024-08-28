<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/5/25 16:12
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Wechat\Service\OpenWork;

use App\Application\Wechat\Model\WechatOpenworkEvent;
use App\Application\Wechat\Service\OpenWork\EventHandle\CreateAuth;
use App\Application\Wechat\Service\OpenWork\EventHandle\EventHandleInterface;
use Hyperf\Codec\Json;

class EventHandleService
{
    protected WechatOpenworkEvent $event;

    private array $handle_list = [
        'create_auth' => CreateAuth::class
    ];

    /**
     * @param WechatOpenworkEvent $event
     */
    public function __construct(WechatOpenworkEvent $event) { $this->event = $event; }

    public function handle(): bool
    {
        $event_content = Json::decode($this->event->event_content, true);
        $handle_class = $this->handle_list[$this->event->info_type] ?? '';
        if ($handle_class !== '') {
            /**
             * @var EventHandleInterface $object
             */
            $object = new $handle_class();
            try {
                $res = $object->handle($event_content);
                $this->event->handle_res = $res ? 1 : 0;
            } catch (\Throwable $exception) {
                $this->event->handle_res = 0;
                $this->event->handle_msg = $exception->getMessage();
            }
            $this->event->save();
        }

        return true;
    }
}