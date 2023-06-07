<?php

namespace Modules\Media\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Media\Database\factories\FolderFactory;

class Folder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'user_id',
        'parent_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'folder_id');
    }

    public function parentFolder(): HasOne
    {
        return $this->hasOne(Folder::class, 'parent_id');
    }

    protected static function newFactory()
    {
        return FolderFactory::new();
    }
}