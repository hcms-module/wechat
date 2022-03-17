<?php

declare (strict_types=1);

namespace App\Application\Wechat\Model;

use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $id
 * @property string         $app_id
 * @property string         $scene
 * @property int            $expire_time
 * @property string         $file_path
 * @property string         $qrcode_url
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 */
class WechatOfficeQrcode extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wechat_office_qrcode';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['app_id', 'scene', 'expire_time', 'file_path', 'qrcode_url'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'expire_time' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}