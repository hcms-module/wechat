<?php

declare(strict_types=1);

namespace App\Application\Wechat\Model;

use Hyperf\DbConnection\Model\Model;
use Hyperf\Codec\Json;

/**
 * @property int            $id
 * @property string         $appid
 * @property string         $mchid
 * @property string         $openid
 * @property string         $out_trade_no
 * @property int            $total_fee
 * @property string         $description
 * @property string         $notify_url
 * @property string         $prepay_id
 * @property string         $trade_type
 * @property string         $transaction_id
 * @property string         $trade_state
 * @property string         $trade_state_desc
 * @property string         $meta_info
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class WechatPayOrder extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'wechat_pay_order';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['appid', 'mchid', 'out_trade_no'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'total_fee' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function getMetaInfoAttribute()
    {
        $meta_info = $this->attributes['meta_info'] ?? [];
        if (is_string($meta_info)) {
            return Json::decode($meta_info);
        }

        return $meta_info;
    }
}
