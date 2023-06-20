<?php

namespace Modules\Media\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Media\Database\factories\FileFactory;
use Modules\Media\Facades\MediaFacade;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'size',
        'url',
        'mime_type',
        'user_id',
        'folder_id',
        'options',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'options' => 'json',
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }

    public function canGenerateThumbnails(): bool
    {
        return MediaFacade::canGenerateThumbnails($this->mime_type);
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function (File $file) {
            if ($file->isForceDeleting()) {
                MediaFacade::deleteFile($file);
            }
        });
    }

    protected static function newFactory()
    {
        return FileFactory::new();
    }
}
