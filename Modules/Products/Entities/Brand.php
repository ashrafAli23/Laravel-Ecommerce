<?php

declare(strict_types=1);

namespace Modules\Products\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Common\Enums\BaseStatusEnum;
use Modules\Products\Database\factories\BrandFactory;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'status',
        'order',
        'is_featured',
        'meta'
    ];

    protected $casts = [
        'is_featured' => "bool",
        "meta" => "json"
    ];

    protected static function newFactory()
    {
        return BrandFactory::new();
    }
}
