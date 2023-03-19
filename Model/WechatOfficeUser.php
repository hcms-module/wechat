<?php

declare (strict_types=1);

namespace App\Application\Wechat\Model;

use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $office_user_id
 * @property string         $app_id
 * @property string         $openid
 * @property string         $nickname
 * @property string         $headimgurl
 * @property string         $unionid
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 */
class WechatOfficeUser extends Model
{
    use SoftDeletes;

    protected string $primaryKey = 'office_user_id';
    /**
     * The table associated with the model.
     *
     * @var ?string
     */
    protected ?string $table = 'wechat_office_user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = ['office_user_id', 'openid', 'app_id'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = ['office_user_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}