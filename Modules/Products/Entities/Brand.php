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
        'description',
        'logo',
        'status',
        'order',
        'is_feature',
    ];
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    protected static function newFactory()
    {
        return BrandFactory::new();
    }
}
