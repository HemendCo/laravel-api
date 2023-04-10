<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\PermissionRegistrar;

class AclRoles extends SpatieRole
{
    protected $hidden = ['guard_name'];

    protected static function findByParam(array $params = [])
    {
        $query = static::query();

        if (PermissionRegistrar::$teams) {
            $query->where(function ($q) use ($params) {
                $q->whereNull(PermissionRegistrar::$teamsKey)
                    ->orWhere(PermissionRegistrar::$teamsKey, $params[PermissionRegistrar::$teamsKey] ?? getPermissionsTeamId());
            });
            unset($params[PermissionRegistrar::$teamsKey]);
        }

        $query->where('activated', '1');

        foreach ($params as $key => $value) {
            $query->where($key, $value);
        }

        return $query->first();
    }
}
