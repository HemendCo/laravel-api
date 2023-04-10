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
}
