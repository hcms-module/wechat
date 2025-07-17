<?php

declare(strict_types=1);

namespace App\Application\Wechat\Model;

use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $id
 * @property string         $corp_id
 * @property string         $userid
 * @property string         $name
 * @property string         $department
 * @property int            $main_department
 * @property string         $position
 * @property int            $status
 * @property int            $isleader
 * @property string         $telephone
 * @property int            $enable
 * @property string         $alias
 * @property string         $direct_leader
 * @property int            $gender
 * @property string         $email
 * @property string         $avatar
 * @property string         $qr_code
 * @property string         $address
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 */
class WechatWorkUser extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'wechat_work_user';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['userid', 'corp_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'main_department' => 'integer',
        'status' => 'integer',
        'isleader' => 'integer',
        'enable' => 'integer',
        'gender' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function setDepartmentAttribute($value): void
    {
        if (!empty($value) && is_array($value)) {
            $this->attributes['department'] = implode(',', $value);
        } else {
            $this->attributes['department'] = "";
        }
    }

    public function getDepartmentAttribute($value): array
    {
        if (empty($value)) {
            return [];
        }

        return explode(',', $value);
    }

    public function setDirectLeaderAttribute($value): void
    {
        if (!empty($value) && is_array($value)) {
            $this->attributes['direct_leader'] = implode(',', $value);
        } else {
            $this->attributes['direct_leader'] = "";
        }
    }

    public function getDirectLeaderAttribute($value): array
    {
        if (empty($value)) {
            return [];
        }

        return explode(',', $value);
    }
}
