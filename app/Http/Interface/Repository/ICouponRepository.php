<?php

declare(strict_types=1);

namespace App\Http\Interface\Repository;

use App\Models\Coupon;

interface ICouponRepository
{
    public function getCoupon(string $coupon): Coupon;
}
