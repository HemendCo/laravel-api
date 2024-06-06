<?php

namespace Hemend\Api\Libraries;

use Hemend\Library\Glob;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role as SpatieRole;

class Permissions
{
  static public $DEFAULT_NAMESPACE = '\App\Http\Controllers\Api\\';

  /**
   * Grant the given permission(s) to a role.
   *
   * @param string $service
   * @param SpatieRole $role
   * @param string $flags
   * @return void
   */
  static public function roleGivePermissionTo(string $service, SpatieRole $role, array $flags) : void {
    $serviceClass = self::$DEFAULT_NAMESPACE . $service;

    if (!class_exists($serviceClass)) {
      throw new \Error('Class "'.$serviceClass.'" does not exist');
    }
    $reflector = new \ReflectionClass($serviceClass);
    $piMain = pathinfo($reflector->getFileName());
    $service_dir_path = $piMain['dirname'] . DIRECTORY_SEPARATOR . $piMain['filename'];

    $files = [];
    foreach (Glob::recursive($service_dir_path, '*/*/*.{php}', GLOB_BRACE) as $file) {
      $pi = pathinfo($file);
      $file_without_ext = str_replace('/', '\\', $pi['dirname'] . '/' . $pi['filename']);
      $class = 'App\\' . substr($file_without_ext, strpos($file_without_ext, substr($reflector->getName(), 4)));

      $namespace_start_of_version = str_replace($reflector->getName().'\\', '', $class);
      $namespace_without_version = preg_split('#\\\+#', $namespace_start_of_version, 2)[1];
      $permission_untidy = $piMain['filename'].'.'.$namespace_without_version;
      $permission = str_replace('\\', '.', $permission_untidy);

      if(in_array($class::defaultPermissionFlag(), $flags) && !$role->hasPermissionTo($permission)) {
        $role->givePermissionTo($permission);
      }
    }
  }

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
   * @param int|array $permission  permission | 0 = all | 1 = Public
   * @return array
   * @throws \Exception
   */
  static public function getPermissionFromApi(string $service, string $version_namespace, Array|int $permission = null) : array|bool {
    $pp = ['allow' => [], 'deny' => []];

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
