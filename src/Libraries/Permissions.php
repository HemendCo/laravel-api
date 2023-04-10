<?php

namespace Hemend\Api\Libraries;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Artisan;

class Permissions
{
    /**
     * @param string $service
     * @param string $version_namespace
     * @param Collection $permissions
     * @return array
     */
    static public function getPermissionsFromApi(string $service, string $version_namespace, Collection $permissions) : array {
        $perms = $permissions->toArray();

        foreach ($perms as $k => $perm) {
            $p = self::getPermissionFromApi($service, $version_namespace, $perm);
            if(!!$p) {
                $perms[$k] = $p;
            }
        }

        return $perms;
    }

    /**
     * @param string $service
     * @param string $version_namespace
     * @param int|null $role
     * @return array|array[]
     * @throws \Exception
     */
    static public function getRolePermissionsFromApi(string $service, string $version_namespace, int $role = null) : array|bool {
        return self::getPermissionFromApi($service, $version_namespace, $role);
    }

    /**
     * @param string $service
     * @param string $version_namespace
     * @param int|array $permission    permission | 0 = all | 1 = Public
     * @return array
     * @throws \Exception
     */
    static public function getPermissionFromApi(string $service, string $version_namespace, Array|int $permission = null) : array|bool {
        $pp = ['allow' => [], 'deny' => []];
        if(is_array($permission)) {
            $class = $version_namespace .'\\'.substr($permission['name'], strlen($service));
            try {
                $nps = $class::permissions();
            } catch (\Throwable $e) {
                self::delete([$permission['id']]);
                return false;
            }
        } else {
            if(!in_array($permission, [null, 1], true)) {
                throw new \Exception('Role is not supported');
            }

            $class = $version_namespace;
            if($permission == 1) {
                $nps = $class::publicRolePermissions();
            } else {
                $nps = $class::allRolesPermissions();
            }
        }

        foreach ($nps['allow'] as $p) {
            $pp['allow'][] = trim($service . substr($p, strrpos($p, '\\') + 1));
        }
        foreach ($nps['deny'] as $p) {
            $pp['deny'][] = trim($service . substr($p, strrpos($p, '\\') + 1));
        }

        if(is_array($permission)) {
            $permission['priority_permissions'] = $pp;
        } else {
            $permission = $pp;
        }

        return $permission;
    }

    static public function merge(array $permissions, array $priority_permissions) : array {
        $allow = [];
        $deny = [];
        $perms = [];
        foreach ($permissions as $p) {
            $allow = [...$allow, ...$p['priority_permissions']['allow']];
            $deny = [...$deny, ...$p['priority_permissions']['deny']];
            $perms[] = $p['name'];
        }

        $allow = [...$allow, ...$priority_permissions['allow']];
        $deny = [...$deny, ...$priority_permissions['deny']];

        $perms = array_merge($perms, $allow);
        $perms = array_unique([...array_diff($perms, $deny)]);

        return $perms;
    }

    static public function delete(array $permission_ids) {
        \App\Models\AclPermissions::whereIn('id', $permission_ids)->delete();
        Artisan::call('permission:cache-reset');
    }
}
