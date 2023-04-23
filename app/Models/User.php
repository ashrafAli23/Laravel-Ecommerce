<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;


    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'username',
        'password',
        'avatar_id',
        'permissions',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'permissions' => 'json',
    ];

    public function isSuper(): bool
    {
        return $this->is_super;
    }
}
