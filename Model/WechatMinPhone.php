<?php

declare (strict_types=1);

namespace App\Application\Wechat\Model;

use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $id
 * @property string         $app_id
 * @property string         $phone
 * @property string         $country_code
 * @property string         $target
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 */
class WechatMinPhone extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var ?string
     */
    protected ?string $table = 'wechat_min_phone';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = ['app_id', 'phone', 'country_code'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}