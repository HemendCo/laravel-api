<?php

namespace App\Models;

use Hemend\Api\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticate;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * @property int    $not_deleted
 * @property int    $blocked
 * @property int    $suspended
 * @property int    $activated
 */
class Users extends Authenticate
{
  use HasRoles, HasApiTokens, HasFactory, Notifiable;

  protected $guarded = [];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'not_deleted' => 'integer',
      'blocked' => 'integer',
      'suspended' => 'integer',
      'activated' => 'integer',
      'created_by' => 'integer',
      'mobile_verified_at' => 'datetime',
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  public function userPermissions($service_id): array|\Illuminate\Support\Collection
  {
    if($this->hasRole('super-admin')) {
      $permissions = ['*'];
    } else {
      $permissions = collect($this->getAllPermissions())
        ->where('service_id', $service_id)
        ->pluck('name');
    }

    return $permissions;
  }
}
