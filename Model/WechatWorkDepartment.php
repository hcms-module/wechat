<?php

declare(strict_types=1);

namespace App\Application\Wechat\Model;

use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $id
 * @property string         $corp_id
 * @property string         $name
 * @property int            $parentid
 * @property int            $order
 * @property string         $department_leader
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 */
class WechatWorkDepartment extends Model
{

    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'wechat_work_department';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'corp_id',
        'id',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'parentid' => 'integer',
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected array $hidden = ['deleted_at'];

    public function setDepartmentLeaderAttribute($value): void
    {
        $this->attributes['department_leader'] = implode(',', $value);
    }

    public function getDepartmentLeaderAttribute($value): array
    {
        if (empty($value)) {
            return [];
        }

        return explode(',', $value);
    }
}
