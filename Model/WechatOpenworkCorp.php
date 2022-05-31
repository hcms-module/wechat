<?php

declare (strict_types=1);

namespace App\Application\Wechat\Model;

use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $id
 * @property string         $corpid
 * @property string         $corp_name
 * @property string         $permanent_code
 * @property string         $access_token
 * @property int            $expires_in
 * @property string         $auth_corp_info
 * @property string         $auth_info
 * @property string         $auth_user_info
 * @property string         $register_code_info
 * @property string         $state
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 */
class WechatOpenworkCorp extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wechat_openwork_corp';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['corpid', 'corp_name'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'expires_in' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}