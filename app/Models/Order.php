<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'sub_total',
        'number',
        'paymant_method',
        'coupon',
        'status',
        'user_id',
        'completed_at',
        'cancelled_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order_address(): HasMany
    {
        return $this->hasMany(OrderAddress::class);
    }

    public function shipping_address(): HasOne
    {
        return $this->HasOne(OrderAddress::class)->where('type', 'shipping');
    }

    public function billing_address(): HasOne
    {
        return $this->HasOne(OrderAddress::class)->where('type', 'billing');
    }

    protected static function booted()
    {
        static::creating(function (Order $order) {
            $order->number = Order::getNextOrderNum();
        });
    }

    public static function getNextOrderNum(): string
    {
        $year = Carbon::now()->year;
        $number = Order::whereYear('created_at', $year)->max('number');
        if ($number) {

            return strval($number + 1);
        }
        return $year . '0001';
    }
}
