<?php

namespace App\Models;

use Hemend\Api\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Users extends Authenticatable
{
  use HasRoles, HasApiTokens, HasFactory, Notifiable;

  protected $guarded = [];

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

  public function userRoles(): \Illuminate\Support\Collection
  {
    return $this->getRoleNames();
  }
}
