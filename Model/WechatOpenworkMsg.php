<?php

declare (strict_types=1);

namespace App\Application\Wechat\Model;

use Hyperf\Codec\Json;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $msg_id
 * @property string         $to_user_name
 * @property string         $from_user_name
 * @property string         $msg_type
 * @property array          $msg_content
 * @property int            $handle_res
 * @property string         $handle_msg
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class WechatOpenworkMsg extends Model
{
    protected string $primaryKey = 'msg_id';
    /**
     * The table associated with the model.
     *
     * @var ?string
     */
    protected ?string $table = 'wechat_openwork_msg';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = [
        'to_user_name',
        'from_user_name',
        'msg_type',
        'msg_content'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = [
        'msg_id' => 'integer',
        'handle_res' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];


    public function getMsgContentAttribute($value): array
    {
        try {
            if ($value) {
                return Json::decode($value);
            } else {
                return [];
            }
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function setMsgContentAttribute($value): void
    {
        $this->attributes['msg_content'] = is_array($value) ? Json::encode($value) : $value;
    }

}