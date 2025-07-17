<?php

namespace App\Application\Wechat\Service\Work;

use App\Exception\ErrorException;

class OA extends AbstractWorkObject
{


    /**
     * 获取审批记录的详情
     *
     * @param string $sp_no
     * @return array
     * @throws ErrorException
     */
    public function getApprovalDetail(string $sp_no): array
    {
        return $this->postJson('/cgi-bin/oa/getapprovaldetail', compact('sp_no')) ?? [];
    }

    /**
     * 获取审批记录列表
     *
     * @param string $template_id
     * @param string $creator
     * @param int    $department
     * @param int    $sp_status
     * @param int    $starttime
     * @param int    $endtime
     * @return array
     * @throws ErrorException
     */
    public function getApprovalInfo(
        string $template_id,
        string $creator = '',
        int $department = 0,
        int $sp_status = 0,
        int $starttime = 0,
        int $endtime = 0
    ): array {
        $starttime = $starttime ?: time() - 86400 * 30;
        $endtime = $endtime ?: time();
        $filters = [
            [
                'key' => 'template_id',
                'value' => $template_id
            ]
        ];
        if ($creator) {
            $filters[] = [
                'key' => 'creator',
                'value' => $creator
            ];
        }
        if ($department) {
            $filters[] = [
                'key' => 'department',
                'value' => $department . ''
            ];
        }
        if ($sp_status) {
            $filters[] = [
                'key' => 'sp_status',
                'value' => $sp_status . ''
            ];
        }

        return $this->postJson('/cgi-bin/oa/getapprovalinfo', [
            'starttime' => $starttime,
            'endtime' => $endtime,
            'new_cursor' => "",
            'size' => 100,
            'filters' => $filters,
        ])['sp_no_list'] ?? [];
    }

    /**
     * 获取模版参数
     *
     * @param string $templateId
     * @return array
     * @throws ErrorException
     */
    public function getTemplateDetail(string $templateId): array
    {
        return $this->postJson('/cgi-bin/oa/gettemplatedetail', [
            'template_id' => $templateId
        ]);
    }
}