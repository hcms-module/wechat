<?php

declare (strict_types=1);

namespace App\Application\Wechat\Model;

use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $id
 * @property string         $app_key
 * @property int            $app_type
 * @property string         $app_id
 * @property string         $app_secret
 * @property string         $token
 * @property string         $aes_key
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 */
class WechatApp extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wechat_app';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['app_key', 'app_id', 'app_secret', 'token', 'aes_key'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'app_type' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}