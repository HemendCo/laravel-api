<?php

namespace Hemend\Api\Traits;

use Spatie\Permission\Contracts\Role as RoleContract;

trait AclHandler
{
  protected string $guard_name = '*';

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
    $query->where('not_deleted', '1');

    foreach ($params as $key => $value) {
      $query->where($key, $value);
    }

    return $query->first();
  }
}
