<?php

declare(strict_types=1);

namespace App\Application\Wechat\Model;

use Hyperf\DbConnection\Model\Model;
use Hyperf\Utils\Codec\Json;

/**
 * @property int            $id
 * @property string         $refund_id
 * @property string         $out_refund_no
 * @property string         $transaction_id
 * @property string         $out_trade_no
 * @property string         $channel
 * @property string         $status
 * @property int            $refund_fee
 * @property int            $total
 * @property string         $meta_info
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class WechatPayOrderRefund extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'wechat_pay_order_refund';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['out_refund_no', 'out_trade_no'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'refund_fee' => 'integer',
        'total' => 'integer',
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
