<?php

namespace App\Models;

use Hemend\Api\Traits\AclHandler;
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
      'not_deleted' => 'integer',
      'is_protected' => 'integer',
      'activated' => 'integer',
      'is_default' => 'integer',
    ];
  }
}
