<?php

declare (strict_types=1);

namespace App\Application\Wechat\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $event_id
 * @property string         $suite_id
 * @property string         $info_type
 * @property string         $event_content
 * @property int            $handle_res
 * @property string         $handle_msg
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class WechatOpenworkEvent extends Model
{

    protected string $primaryKey = 'event_id';
    /**
     * The table associated with the model.
     *
     * @var ?string
     */
    protected ?string $table = 'wechat_openwork_event';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = ['suite_id', 'info_type', 'event_content'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = [
        'event_id' => 'integer',
        'handle_res' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}