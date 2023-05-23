<?php

declare(strict_types=1);

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\User\Database\factories\RoleFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'permissions',
        'description',
        'is_default',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'permissions' => 'json',
    ];

    protected static function newFactory()
    {
        return RoleFactory::new();
    }

    public function user(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id');
    }
}
