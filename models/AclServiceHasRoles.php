<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AclServiceHasRoles extends Model
{
  protected $guarded = [];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'service_id' => 'integer',
      'role_id' => 'integer',
    ];
  }

  public function service(): BelongsTo
  {
    return $this->belongsTo(AclServices::class, 'service_id');
  }

  public function role(): BelongsTo
  {
    return $this->belongsTo(AclRoles::class, 'role_id');
  }
}
