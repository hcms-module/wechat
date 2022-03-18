<?php

declare (strict_types=1);

namespace App\Application\Wechat\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $id
 * @property string         $app_id
 * @property string         $path
 * @property string         $query
 * @property int            $is_expire
 * @property int            $expire_time
 * @property string         $env_version
 * @property string         $url_link
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class WechatMinUrl extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wechat_min_url';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['app_id', 'query', 'path', 'env_version'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'is_expire' => 'integer',
        'expire_time' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}