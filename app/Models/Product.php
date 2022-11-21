<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'current_stock',
        'images',
        'active',
        'min_qty',
        'featured',
        'tax',
        'tax_type',
        'category_id',
        'shipping_cost',
        'free_shipping',
        'brand_id',
        'unit',
        'product_type'
    ];

    protected $casts = [
        'active' => 'boolean',
        'free_shipping' => 'boolean'
    ];


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variant(): HasMany
    {
        return $this->hasMany(Variant::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brands::class, 'brand_id');
    }
}
