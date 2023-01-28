<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'slug',
        'description',
        'featured',
        'status',
        'link',
        'total_sale'
    ];

    protected $cast = [
        'featured' => 'boolean',
    ];


    public function products(): HasMany
    {
        return $this->HasMany(Product::class);
    }
}