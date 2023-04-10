<?php

namespace App\Models;

use Hemend\Library\Laravel\Traits\PositionModel;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Permission as SpatiePermission;

class AclPermissions extends SpatiePermission
{
    use PositionModel;

    protected $hidden = ['guard_name', 'position'];

    /**
     * Find or create permission by its name (and optionally guardName).
     *
     * @param string $name
     * @param string $title
     * @param int $group_id
     * @param string|null $guard_name
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|PermissionContract
     */
    public static function upsert(string $name, string $title, int $group_id, string $guard_name = null): PermissionContract
    {
        $guard_name = $guard_name ?? Guard::getDefaultName(static::class);
        $permission = static::getPermission(['name' => $name, 'guard_name' => $guard_name]);

        if (! $permission) {
            return static::query()->create([
                'group_id' => $group_id,
                'name' => $name,
                'title' => $title,
                'guard_name' => $guard_name,
                'position' => self::newPosition($group_id, $guard_name)
            ]);
        } else {
            $permission->update([
                'group_id' => $group_id,
                'name' => $name,
                'title' => $title,
                'guard_name' => $guard_name,
                ...($group_id === $permission->group_id ? [] : ['position' => self::newPosition($group_id, $guard_name)])
            ]);
        }

        return $permission;
    }

    static public function updatePosition(int $group_id, int $from, int $to, string $guard_name = null)
    {
        $guard_name = $guard_name ?? Guard::getDefaultName(static::class);
        return static::changePosition($from, $to, function ($q) use ($group_id, $guard_name) {
            $q->where('group_id', '=', $group_id)
                ->where('guard_name', '=', $guard_name);
        });
    }

    static public function lastPosition(int $group_id, string $guard_name = null)
    {
        $guard_name = $guard_name ?? Guard::getDefaultName(static::class);
        return static::getLastPosition(function ($q) use ($group_id, $guard_name) {
            $q->where('group_id', '=', $group_id)
                ->where('guard_name', '=', $guard_name);
        });
    }

    static public function newPosition(int $group_id, string $guard_name = null)
    {
        return (static::lastPosition($group_id, $guard_name) ?? 0) + 1;
    }

    public function group() {
        return $this->hasOne(AclGroups::class, 'id', 'group_id');
    }

}
