<?php

namespace App\Models;

use Hemend\Api\Traits\AclHandler;
use Hemend\Library\Laravel\Traits\PositionModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Guard;

class AclServices extends Model
{
  use AclHandler, HasFactory, PositionModel;

  protected $guarded = [];
  protected $hidden = ['guard_name', 'position'];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'activated' => 'integer',
      'position' => 'integer',
    ];
  }

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

  public function packages(): HasMany {
    return $this->hasMany(AclPackages::class, 'service_id', 'id');
  }

  public function roles(): BelongsToMany {
    return $this->belongsToMany(AclRoles::class, 'acl_service_has_roles', 'service_id', 'role_id');
  }
}
