<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends Model
{
    use HasFactory;


    protected array $fillable = [
        'product_id',
        'qty'
    ];

    public function product(): HasMany
    {
        return $this->hasMany(Product::class, 'product_id');
    }
}