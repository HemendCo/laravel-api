<?php

namespace App\Models;

use Hemend\Api\Traits\AclHandler;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Models\Role as SpatieRole;

class AclRoles extends SpatieRole
{
  use AclHandler;

  protected $hidden = ['guard_name', 'pivot'];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'activated' => 'integer',
      'is_default' => 'integer',
    ];
  }

  protected static function findByParam(array $params = []): ?RoleContract
  {
    $query = static::query();

    $teams = config('permission.teams', false);
    $teamsKey = config('permission.column_names.team_foreign_key', 'team_id');

    if ($teams) {
      $query->where(function ($q) use ($params, $teamsKey) {
        $q->whereNull($teamsKey)
          ->orWhere($teamsKey, $params[$teamsKey] ?? getPermissionsTeamId());
      });
      unset($params[$teamsKey]);
    }

    $query->where('activated', '1');

    foreach ($params as $key => $value) {
      $query->where($key, $value);
    }

    return $query->first();
  }
}
