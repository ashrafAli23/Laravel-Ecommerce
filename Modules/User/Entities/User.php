<?php

declare(strict_types=1);

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\User\Database\factories\UserFactory;

class User extends Model
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = [];

    protected static function newFactory()
    {
        return UserFactory::new();
    }
}