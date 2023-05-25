<?php

declare(strict_types=1);

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\User\Database\factories\UserFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\User\traits\Permission;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable, Permission;

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'last_login',
        'super_user'
    ];

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public function isSuperUser(): bool
    {
        return $this->super_user;
    }

    public function hasPermission(array|string $permission): bool
    {
        if ($this->isSuperUser()) {
            return true;
        }

        if (is_string($permission)) {
            $permissions[] = $permission;
            return $this->hasAccess($permissions);
        }

        return $this->hasAccess($permission);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->isSuperUser()) {
            return true;
        }

        return $this->hasAnyAccess($permissions);
    }
}
