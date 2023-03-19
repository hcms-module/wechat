<?php

declare (strict_types=1);

namespace App\Application\Wechat\Model;

use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $id
 * @property string         $app_id
 * @property string         $scene
 * @property string         $page
 * @property string         $env_version
 * @property int            $width
 * @property string         $file_path
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 */
class WechatMinQrcode extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var ?string
     */
    protected ?string $table = 'wechat_min_qrcode';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = ['app_id', 'scene'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = [
        'id' => 'integer',
        'width' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}