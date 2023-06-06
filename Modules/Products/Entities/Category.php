<?php

declare(strict_types=1);

namespace Modules\Products\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Common\Enums\BaseStatusEnum;
use Modules\Products\Database\factories\CategoryFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
        'slug',
        'description',
        'image',
        'order',
        'is_featured',
        'status',
        'meta',
    ];

    protected $casts = [
        'is_featured' => "bool",
        "meta" => "json"
    ];

    protected static function newFactory()
    {
        return CategoryFactory::new();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function activeChildren(): HasMany
    {
        return $this->children()
            ->where("status", BaseStatusEnum::PUBLISHED)
            ->with(['activeChildren']);
    }
}
