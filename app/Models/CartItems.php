<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItems extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'quantaty', 'cart_id', 'variant_id'
    ];

    protected $casts = [
        'quantaty' => 'int'
    ];

    public function variant(): BelongsTo
    {
        return $this->BelongsTo(Variant::class, 'variant_id');
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }
}
