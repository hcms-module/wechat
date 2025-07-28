<?php

namespace App\Application\Wechat\Service\Work;

use App\Application\Wechat\Model\WechatWorkUser;

abstract class AbstractOaObject
{

    protected function getProcessNode(array $userid, int $type = 1, int $apv_rel = 1): array
    {
        return compact('userid', 'type', 'apv_rel');
    }

    protected function getSummaryList(array $summary_list, string $lang = "zh_CN"): array
    {
        $res = [];
        foreach ($summary_list as $summary) {
            $res[] = [
                "summary_info" => [
                    [
                        "text" => $summary,
                        "lang" => $lang
                    ]
                ]
            ];
        }

        return $res;
    }

    protected function getContactControl(string $id, array $userids): array
    {
        $members = [];
        foreach ($userids as $userid) {
            $members[] = [
                'userid' => $userid,
                "name" => WechatWorkUser::where('userid', $userid)
                        ->first()->name ?? $userid,
            ];
        }

        return [
            "control" => "Contact",
            "id" => $id,
            "value" => [
                "members" => $members
            ],
        ];
    }

    protected function getDateControl(string $id, string $date, string $type = "day"): array
    {
        return [
            "control" => "Date",
            "id" => $id,
            "value" => [
                "date" => [
                    "type" => $type,
                    "s_timestamp" => strtotime($date)
                ]
            ],
        ];
    }

    protected function getTextControl(string $id, string $value): array
    {
        return [
            "control" => "Text",
            "id" => $id,
            "value" => [
                "text" => $value
            ],
        ];
    }

    protected function getTextAreaControl(string $id, string $value): array
    {
        return [
            "control" => "Textarea",
            "id" => $id,
            "value" => [
                "text" => $value
            ],
        ];
    }
}