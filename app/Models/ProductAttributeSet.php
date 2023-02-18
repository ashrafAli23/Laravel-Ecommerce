<?php

declare(strict_types=1);

namespace App\Models;

use BaseStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttributeSet extends Model
{
    use HasFactory;

    protected array $fillable = [
        'title',
        'slug',
        'description',
        'display_layout',
        'status'
    ];

    protected array $casts = [
        'status' => BaseStatusEnum::class
    ];

    public function productAttr(): HasMany
    {
        return $this->hasMany(ProductAttribute::class);
    }
}