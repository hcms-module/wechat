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
 * @property string         $event
 * @property string         $event_key
 * @property string         $ticket
 * @property string         $latitude
 * @property string         $longitude
 * @property string         $precision
 * @property string         $info_md5
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class WechatOfficeEventMessage extends Model
{
    /**
     * The table associated with the model.
     *
     * @var ?string
     */
    protected ?string $table = 'wechat_office_event_message';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = [
        'app_id',
        'to_user_name',
        'from_user_name',
        'create_time',
        'event',
        'event_key',
        'ticket',
        'latitude',
        'longitude',
        'precision',
        'info_md5'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = [
        'id' => 'integer',
        'create_time' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}