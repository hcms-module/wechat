<?php

declare (strict_types=1);

namespace App\Application\Wechat\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $id
 * @property string         $app_id
 * @property string         $open_id
 * @property string         $template_id
 * @property string         $url
 * @property string         $mini_program_appid
 * @property string         $post_data
 * @property int            $status
 * @property string         $result
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class WechatOfficeTemplateSendRecord extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wechat_office_template_send_record';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['app_id', 'open_id', 'template_id', 'post_data', 'url', 'mini_program_appid'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    const SEND_STATUS_SUCCESS = 1;
    const SEND_STATUS_FAILED = 0;
}