<?php

declare (strict_types=1);

namespace App\Application\Wechat\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $id
 * @property string         $app_id
 * @property string         $to_user_name
 * @property string         $from_user_name
 * @property int            $create_time
 * @property string         $msg_type
 * @property string         $event
 * @property string         $trace_id
 * @property int            $version
 * @property string         $result
 * @property string         $detail
 * @property int            $isrisky
 * @property string         $extra_info_json
 * @property int            $status_code
 * @property string         $info_md5
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class WechatMinEventMessage extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wechat_min_event_message';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'app_id',
        'to_user_name',
        'from_user_name',
        'create_time',
        'msg_type',
        'event',
        'trace_id',
        'version',
        'result',
        'detail',
        'isrisky',
        'extra_info_json',
        'status_code',
        'info_md5',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'create_time' => 'integer',
        'version' => 'integer',
        'isrisky' => 'integer',
        'status_code' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}