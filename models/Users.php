<?php

namespace App\Models;

use Hemend\Api\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticate;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * @property int       $not_deleted
 * @property int       $blocked
 * @property int       $suspended
 * @property int       $activated
 * @property string    $name
 * @property string    $first_name
 * @property string    $last_name
 * @property string    $username
 * @property string    $national_code
 * @property string    $mobile
 * @property string    $email
 */
class Users extends Authenticate
{
  use HasRoles, HasApiTokens, HasFactory, Notifiable;

  protected $guarded = [];

  protected $appends = ['name'];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'username',
    'created_by',
    'national_code',
    'mobile',
    'email',
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

  public function getNameAttribute(): string
  {
      return trim($this->first_name . ' ' . $this->last_name);
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

  /**
   * Check if the user has any roles in a specific service.
   *
   * @param string $serviceName
   * @return bool
   */
  public function hasRoleInService(string $serviceName): bool
  {
    return $this->hasRole('super-admin') || $this->roles()
      ->whereHas('services', function ($query) use ($serviceName) {
        $query->where('name', $serviceName);
      })->exists();
  }

  public function creator(): BelongsTo
  {
      return $this->belongsTo(self::class, 'created_by');
  }
}
