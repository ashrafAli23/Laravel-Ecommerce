<?php

namespace Modules\Media\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Media\Database\factories\FolderFactory;
use Modules\Media\Facades\MediaFacade;

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

    protected static function boot()
    {
        parent::boot();
        static::deleting(function (Folder $folder) {
            if ($folder->isForceDeleting()) {
                $files = File::where('folder_id', $folder->id)->onlyTrashed()->get();

                foreach ($files as $file) {
                    MediaFacade::deleteFile($file);
                    $file->forceDelete();
                }
            } else {
                $files = File::where('folder_id', $folder->id)->withTrashed()->get();

                foreach ($files as $file) {
                    $file->delete();
                }
            }
        });

        static::restoring(function ($folder) {
            File::where('folder_id', $folder->id)->restore();
        });
    }

    protected static function newFactory()
    {
        return FolderFactory::new();
    }
}
