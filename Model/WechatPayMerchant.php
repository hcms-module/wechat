<?php

declare(strict_types=1);

namespace App\Application\Wechat\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $merchant_id
 * @property int            $merchant_type
 * @property string         $pay_mch_id
 * @property string         $pay_secret_key
 * @property string         $pay_v2_secret_key
 * @property string         $serial
 * @property string         $pay_cert_key
 * @property string         $platform_cert_pem
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class WechatPayMerchant extends Model
{
    protected string $primaryKey = "merchant_id";
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'wechat_pay_merchant';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'merchant_id' => 'integer',
        'merchant_type' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
