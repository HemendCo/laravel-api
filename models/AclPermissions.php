<?php

namespace App\Models;

use Hemend\Api\Traits\AclHandler;
use Hemend\Library\Laravel\Traits\PositionModel;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Permission as SpatiePermission;

class AclPermissions extends SpatiePermission
{
  use AclHandler, PositionModel;

  protected $hidden = ['guard_name', 'position'];

  /**
   * Find or create permission by its name (and optionally guardName).
   *
   * @param string $name
   * @param string $title
   * @param int   $package_id
   * @param string|null $guard_name
   *
   * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|PermissionContract
   */
  public static function upsert(string $name, string $title, int $service_id, int $package_id, string $guard_name = null): PermissionContract
  {
    $guard_name = $guard_name ?? Guard::getDefaultName(static::class);
    $permission = static::getPermission([
      'service_id' => $service_id,
      'package_id' => $package_id,
      'guard_name' => $guard_name,
      'name' => $name,
    ]);

    if (! $permission) {
      return static::query()->create([
        'service_id' => $service_id,
        'package_id' => $package_id,
        'name' => $name,
        'title' => $title,
        'guard_name' => $guard_name,
        'position' => self::newPosition($service_id, $package_id, $guard_name)
      ]);
    } else {
      $permission->update([
        'service_id' => $service_id,
        'package_id' => $package_id,
        'name' => $name,
        'title' => $title,
        'guard_name' => $guard_name,
        ...(  $package_id === $permission->package_id ? [] : ['position' => self::newPosition($service_id, $package_id, $guard_name)])
      ]);
    }

    return $permission;
  }

  static public function updatePosition(int $from, int $to, int $service_id, int $package_id, string $guard_name = null)
  {
    $guard_name = $guard_name ?? Guard::getDefaultName(static::class);
    return static::changePosition($from, $to, function ($q) use ($service_id, $package_id, $guard_name) {
      $q->where('service_id', '=', $service_id)
        ->where('package_id', '=', $package_id)
        ->where('guard_name', '=', $guard_name);
    });
  }

  static public function lastPosition(int $service_id, int $package_id, string $guard_name = null)
  {
    $guard_name = $guard_name ?? Guard::getDefaultName(static::class);
    return static::getLastPosition(function ($q) use ($service_id, $package_id, $guard_name) {
      $q->where('service_id', '=', $service_id)
        ->where('package_id', '=', $package_id)
        ->where('guard_name', '=', $guard_name);
    });
  }

  static public function newPosition(int $service_id, int $package_id, string $guard_name = null)
  {
    return (static::lastPosition($service_id, $package_id, $guard_name) ?? 0) + 1;
  }

  public function package() {
    return $this->hasOne(AclPackages::class, 'id', 'package_id');
  }

  public function service() {
    return $this->hasOne(AclServices::class, 'id', 'package_id');
  }

}
