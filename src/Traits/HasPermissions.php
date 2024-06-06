<?php

namespace Hemend\Api\Traits;

use Spatie\Permission\Traits\HasPermissions as SpatieHasPermissions;

trait HasPermissions
{
  use SpatieHasPermissions;

  protected function getDefaultGuardName(): string {
    return '*';
  }

  public function hasPermissionTo($permission, $guardName = '*'): bool
  {
    if ($this->getWildcardClass()) {
      return $this->hasWildcardPermission($permission, $guardName);
    }

    $permission = $this->filterPermission($permission, $guardName);

    return $this->hasDirectPermission($permission) || $this->hasPermissionViaRole($permission);
  }
}