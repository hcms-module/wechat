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
 * @property string         $msg_id
 * @property string         $content
 * @property string         $pic_url
 * @property string         $media_id
 * @property string         $format
 * @property string         $Recognition
 * @property string         $thumb_media_id
 * @property string         $location_x
 * @property string         $location_y
 * @property string         $scale
 * @property string         $label
 * @property string         $title
 * @property string         $description
 * @property string         $url
 * @property string         $info_md5
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class WechatOfficeMessage extends Model
{
    /**
     * The table associated with the model.
     *
     * @var ?string
     */
    protected ?string $table = 'wechat_office_message';
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
        'msg_type',
        'msg_id',
        'content',
        'pic_url',
        'media_id',
        'format',
        'recognition',
        'thumb_media_id',
        'location_x',
        'location_y',
        'scale',
        'label',
        'title',
        'description',
        'url',
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