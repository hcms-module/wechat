<?php

namespace App\Application\Wechat\Service\Work;

use App\Application\Wechat\Event\Work\OaCancelEvent;
use App\Application\Wechat\Event\Work\OaPassEvent;
use App\Application\Wechat\Event\Work\OaRejectEvent;
use App\Application\Wechat\Model\WechatWorkOa;
use App\Exception\ErrorException;
use Hyperf\Codec\Json;
use Hyperf\Di\Annotation\Inject;
use Psr\EventDispatcher\EventDispatcherInterface;

class Oa extends AbstractWorkObject
{

    #[Inject]
    protected EventDispatcherInterface $eventDispatcher;

    /**
     * 提交审批
     *
     * @param array $data
     * @param int   $debug
     * @return array
     * @throws ErrorException
     */
    public function applyEvent(array $data, int $debug = 0): array
    {
        //创建记录
        $work_oa = new WechatWorkOa();
        $work_oa->creator_userid = $data['creator_userid'] ?? '';
        $work_oa->template_id = $data['template_id'] ?? '';
        $work_oa->department_id = $data['choose_department'] ?? 0;
        $work_oa->submit_param = Json::encode($data);
        $work_oa->save();

        $res = $this->postJson('/cgi-bin/oa/applyevent', $data, [
            'query' => [
                "debug" => $debug,
            ]
        ]);
        $errcode = $res['errcode'] ?? 0;
        if ($errcode === 0) {
            //提交成功
            $sp_no = $res['sp_no'] ?? "";
            $work_oa->sp_no = $sp_no;
            $work_oa->save();
        }

        return $res;
    }

    /**
     * 获取审批记录的详情
     *
     * @param string $sp_no
     * @return WechatWorkOa
     * @throws ErrorException
     */
    public function getApprovalDetail(string $sp_no): WechatWorkOa
    {
        $res = $this->postJson('/cgi-bin/oa/getapprovaldetail', compact('sp_no')) ?? [];
        $errcode = intval($res['errcode'] ?? -1);
        if ($errcode === 0) {
            $info = $res['info'] ?? [];
            $sp_no = $info['sp_no'] ?? "";
            $work_oa = WechatWorkOa::firstOrNew(['sp_no' => $sp_no, 'corp_id' => $this->corp_id]);
            $work_oa->sp_name = $info['sp_name'] ?? "";
            $sp_status = intval($info['sp_status'] ?? 0);
            $is_pass = false;
            $is_reject = false;
            $is_cancel = false;
            if ($work_oa->sp_status != WechatWorkOa::SP_STATUS_PASS && $sp_status == WechatWorkOa::SP_STATUS_PASS) {
                //状态变更为通过
                $is_pass = true;
            }
            if ($work_oa->sp_status != WechatWorkOa::SP_STATUS_REJECT && $sp_status == WechatWorkOa::SP_STATUS_REJECT) {
                //状态变更为驳回
                $is_reject = true;
            }
            if ($work_oa->sp_status != WechatWorkOa::SP_STATUS_CANCEL && $sp_status == WechatWorkOa::SP_STATUS_CANCEL) {
                //状态变更为已撤销
                $is_cancel = true;
            }
            $work_oa->sp_status = $sp_status;
            $work_oa->template_id = $info['template_id'] ?? "";
            $work_oa->apply_time = $info['apply_time'] ?? 0;
            $work_oa->creator_userid = $info['applyer']['userid'] ?? "";
            $work_oa->department_id = $info['applyer']['partyid'] ?? 0;
            $work_oa->sp_result = Json::encode($info);
            $work_oa->save();
            if ($is_pass) {
                $this->eventDispatcher->dispatch(new OaPassEvent($work_oa));
            }
            if ($is_reject) {
                $this->eventDispatcher->dispatch(new OaRejectEvent($work_oa));
            }
            if ($is_cancel) {
                $this->eventDispatcher->dispatch(new OaCancelEvent($work_oa));
            }

            return $work_oa;
        } else {
            throw new ErrorException($res['errmsg'] ?? '获取审批详情失败');
        }
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