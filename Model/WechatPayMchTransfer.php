<?php

declare(strict_types=1);

namespace App\Application\Wechat\Model;

use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $id
 * @property string         $pay_mch_id
 * @property string         $appid
 * @property string         $out_bill_no
 * @property string         $transfer_scene_id
 * @property string         $openid
 * @property int            $transfer_amount
 * @property string         $transfer_remark
 * @property string         $transfer_scene_report_infos
 * @property string         $package_info
 * @property string         $state
 * @property string         $fail_reason
 * @property string         $transfer_bill_no
 * @property string         $target
 * @property string         $target_type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 */
class WechatPayMchTransfer extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'wechat_pay_mch_transfer';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['out_bill_no', 'appid', 'pay_mch_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'transfer_amount' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
