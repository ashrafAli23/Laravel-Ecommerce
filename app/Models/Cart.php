<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'cooke_id',
        'coupon',
        'discount',
    ];

    protected $casts = [
        'discount' => 'int'
    ];

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItems::class);
    }
}
