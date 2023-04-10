<?php

namespace App\Models;

use Hemend\Library\Laravel\Traits\PositionModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Guard;

class AclGroups extends Model
{
    use HasFactory, PositionModel;

    protected $guarded = [];
    protected $hidden = ['guard_name', 'position'];

    static public function updatePosition(int $from, int $to, string $guard_name = null)
    {
        $guard_name = $guard_name ?? Guard::getDefaultName(static::class);
        return static::changePosition($from, $to, function ($q) use ($guard_name) {
            $q->where('guard_name', '=', $guard_name);
        });
    }

    static public function lastPosition(string $guard_name = null)
    {
        $guard_name = $guard_name ?? Guard::getDefaultName(static::class);
        return static::getLastPosition(function ($q) use ($guard_name) {
            $q->where('guard_name', '=', $guard_name);
        });
    }

    static public function newPosition(string $guard_name = null)
    {
        return (static::lastPosition($guard_name) ?? 0) + 1;
    }

    public function permissions() {
        return $this->hasMany(AclPermissions::class, 'group_id', 'id');
    }
}
