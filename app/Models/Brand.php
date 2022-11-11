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
        'active',
    ];

    protected $cast = [
        'active' => 'boolean',
    ];


    public function products(): HasMany
    {
        return $this->HasMany(Product::class);
    }
}
