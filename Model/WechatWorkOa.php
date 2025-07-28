<?php

declare(strict_types=1);

namespace App\Application\Wechat\Model;

use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $id
 * @property string         $corp_id
 * @property string         $sp_no
 * @property string         $sp_name
 * @property int            $sp_status
 * @property string         $template_id
 * @property int            $apply_time
 * @property string         $creator_userid
 * @property int            $department_id
 * @property string         $submit_param
 * @property string         $sp_result
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 */
class WechatWorkOa extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'wechat_work_oa';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['sp_no', 'corp_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'sp_status' => 'integer',
        'apply_time' => 'integer',
        'department_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * 审核中
     */
    const SP_STATUS_PENDING = 1;
    /**
     * 通过
     */
    const SP_STATUS_PASS = 2;
    /**
     * 驳回
     */
    const SP_STATUS_REJECT = 3;
    /**
     * 撤销
     */
    const SP_STATUS_CANCEL = 4;
}
