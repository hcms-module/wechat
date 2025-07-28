<?php

namespace App\Application\Wechat\Listener\Work;

use App\Application\Wechat\Event\Work\OpenMsgEvent;
use App\Application\Wechat\Service\WorkService;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;

#[Listener]
class ApprovalChangeMsgListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            OpenMsgEvent::class
        ];
    }

    public function process(object $event): void
    {
        if ($event instanceof OpenMsgEvent) {
            $message = $event->getMsg();
            if ($message->msg_type === 'event') {
                $msg_event = $message->msg_content['Event'] ?? '';
                if ($msg_event === 'sys_approval_change') {
                    $SpNo = $message->msg_content['ApprovalInfo']['SpNo'] ?? '';
                    try {
                        (new WorkService())->oa->getApprovalDetail($SpNo);
                    } catch (\Throwable $e) {
                        var_dump($e->getMessage());
                    }
                }
            }
        }
    }
}