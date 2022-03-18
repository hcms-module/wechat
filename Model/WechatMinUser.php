<?php

declare (strict_types=1);

namespace App\Application\Wechat\Model;

use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $mini_user_id
 * @property string         $app_id
 * @property string         $openid
 * @property string         $session_key
 * @property string         $unionid
 * @property string         $token
 * @property string         $target
 * @property int            $expire_time
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 */
class WechatMinUser extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'mini_user_id';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wechat_min_user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['app_id', 'openid'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'mini_user_id' => 'integer',
        'expire_time' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}