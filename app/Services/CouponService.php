<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Coupon;
use Exception;

class CouponService
{
    public static function applyCoupon(Coupon|null $apply_coupon): void
    {

        if (
            !$apply_coupon ||
            !$apply_coupon->active ||
            !(strtotime('now') > strtotime($apply_coupon->expire_at)) ||
            $apply_coupon->usage_limit < 1
        ) {
            throw new Exception("Invalid coupon");
        }

        $apply_coupon->update([
            'usage_limit' => $apply_coupon->usage_limit - 1
        ]);
    }
}
