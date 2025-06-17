<?php

namespace App\Application\Wechat\Service\WechatPay;

use App\Application\Wechat\Event\WxpayMchTransferCancelEvent;
use App\Application\Wechat\Event\WxpayMchTransferSuccessEvent;
use App\Application\Wechat\Model\WechatPayMchTransfer;
use App\Exception\ErrorException;
use Hyperf\Codec\Json;

class MchTransferService extends AbstractWechatPayService
{
    /**
     * 处理正在进行的转账单
     *
     * @return void
     */
    static public function handleList(): void
    {
        $mch_list = WechatPayMchTransfer::whereNotIn('state', ['SUCCESS', 'CANCELLED', 'FAILED'])
            ->where('created_at', '>', date('Y-m-d H:i:s', time() - 3600))
            ->get();
        foreach ($mch_list as $mch) {
            if ($mch instanceof WechatPayMchTransfer) {
                try {
                    (new self(pay_mch_id: $mch->pay_mch_id))->getTransferBills($mch->out_bill_no);
                } catch (\Throwable $throwable) {
                    var_dump($throwable->getMessage());
                }
            }
        }
    }

    public function cancelTransferBills(string $out_bill_no): WechatPayMchTransfer
    {
        $mch_transfer = WechatPayMchTransfer::where('out_bill_no', $out_bill_no)
            ->first();
        if ($mch_transfer instanceof WechatPayMchTransfer) {
            try {
                $resp = $this->app->v3->fundApp->mchTransfer->transferBills->outBillNo->_out_bill_no_->cancel->post([
                    'out_bill_no' => $out_bill_no,
                ]);
                $res = Json::decode($resp->getBody()
                    ->getContents());
                $state = $res['state'] ?? '';
                if ($state === "CANCELING" || $state === "CANCELLED") {
                    $is_cancel = $mch_transfer->state !== 'CANCELLED' && $state === 'CANCELLED';
                    $mch_transfer->state = $state;
                    $mch_transfer->save();
                    if ($is_cancel) {
                        //定义转账撤回事件，撤回完成
                        $this->eventDispatcher->dispatch(new WxpayMchTransferCancelEvent($mch_transfer));
                    }

                    return $mch_transfer;
                } else {
                    throw new \Exception('查询转账单失败');
                }

            } catch (\Throwable $e) {
                throw new ErrorException($e->getMessage());
            }
        } else {
            throw new \Exception('未找到转账单');
        }
    }

    public function getTransferBills($out_bill_no): WechatPayMchTransfer
    {
        $mch_transfer = WechatPayMchTransfer::where('out_bill_no', $out_bill_no)
            ->first();
        if ($mch_transfer instanceof WechatPayMchTransfer) {
            try {
                $resp = $this->app->v3->fundApp->mchTransfer->transferBills->outBillNo->_out_bill_no_->get([
                    'out_bill_no' => $out_bill_no
                ]);
                $res = Json::decode($resp->getBody()
                    ->getContents());
                $state = $res['state'] ?? '';
                $fail_reason = $res['fail_reason'] ?? '';
                if ($state) {
                    $is_success = $mch_transfer->state !== 'SUCCESS' && $state === 'SUCCESS';
                    $is_cancel = $mch_transfer->state !== 'CANCELLED' && $state === 'CANCELLED';
                    $mch_transfer->state = $state;
                    $mch_transfer->fail_reason = $fail_reason;
                    $mch_transfer->save();

                    if ($is_success) {
                        //定义转账成功事件，转账完成
                        $this->eventDispatcher->dispatch(new WxpayMchTransferSuccessEvent($mch_transfer));
                    }

                    if ($is_cancel) {
                        //定义转账撤回事件，撤回完成
                        $this->eventDispatcher->dispatch(new WxpayMchTransferCancelEvent($mch_transfer));
                    }

                    return $mch_transfer;
                } else {
                    throw new \Exception('查询转账单失败');
                }

            } catch (\Throwable $e) {
                throw new ErrorException($e->getMessage());
            }
        } else {
            throw new \Exception('未找到转账单');
        }
    }

    public function createTransferBills(
        string $appid,
        string $out_bill_no,
        string $openid,
        int $transfer_amount,
        string $activity_name,
        string $activity_desc,
        string $target = '',
        string $target_type = '',
        int $transfer_scene_id = 1000,
    ) {
        $transfer_scene_report_infos = [
            ['info_type' => "活动名称", 'info_content' => $activity_name],
            ['info_type' => '奖励说明', 'info_content' => $activity_desc]
        ];
        $transfer_remark = $activity_desc;
        //创建数据库记录
        $mch_transfer = WechatPayMchTransfer::firstOrNew([
            'out_bill_no' => $out_bill_no,
            'pay_mch_id' => $this->merchant->pay_mch_id,
            'appid' => $appid
        ]);
        if ($mch_transfer->id > 0) {
            //已经存在，先查询，返回最新的状态
            $this->getTransferBills($out_bill_no);

            return [
                'package_info' => $mch_transfer->package_info,
                'mchId' => $this->merchant->pay_mch_id,
                'appId' => $appid,
                'out_bill_no' => $mch_transfer->out_bill_no,
                'state' => $mch_transfer->state,
            ];
        }

        $mch_transfer->openid = $openid;
        $mch_transfer->transfer_scene_id = $transfer_scene_id;
        $mch_transfer->transfer_amount = $transfer_amount;
        $mch_transfer->transfer_remark = $transfer_remark;
        $mch_transfer->transfer_scene_report_infos = Json::encode($transfer_scene_report_infos);
        try {
            $resp = $this->app->v3->fundApp->mchTransfer->transferBills->post([
                'json' => [
                    'appid' => $appid,
                    'out_bill_no' => $out_bill_no,
                    'transfer_scene_id' => $transfer_scene_id . '',
                    'openid' => $openid,
                    'transfer_scene_report_infos' => $transfer_scene_report_infos,
                    'transfer_amount' => $transfer_amount,
                    'transfer_remark' => $transfer_remark,
                ]
            ]);
            $res = Json::decode($resp->getBody()
                ->getContents());
            $package_info = $res['package_info'] ?? '';
            $out_bill_no = $res['out_bill_no'] ?? '';
            if ($package_info === '' || $out_bill_no === '') {
                throw new \Exception('创建转账单失败');
            }

            $mch_transfer->package_info = $package_info;
            $mch_transfer->state = $res['state'] ?? '';
            $mch_transfer->target = $target;
            $mch_transfer->target_type = $target_type;
            $mch_transfer->transfer_bill_no = $res['transfer_bill_no'] ?? '';
            $mch_transfer->save();

            return $res + ['mchId' => $this->merchant->pay_mch_id, 'appId' => $appid];
        } catch (\Throwable $e) {
            $mch_transfer->state = "FAIL";
            $mch_transfer->fail_reason = substr($e->getMessage(), 0, 240);
            $mch_transfer->target = $target;
            $mch_transfer->target_type = $target_type;
            $mch_transfer->save();
            throw new ErrorException($e->getMessage());
        }
    }
}