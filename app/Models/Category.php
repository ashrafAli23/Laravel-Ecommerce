<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'sub_category',
        'image',
        'status',
        'featured',
        'total_sale'
    ];

    protected $casts = [
        'featured' => 'boolean'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function meta(): HasMany
    {
        return $this->hasMany(Meta::class);
    }
}