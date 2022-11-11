<?php

declare(strict_types=1);

namespace App\Services;

class OrderService
{
    public static function calcSubTotal(float $sub_total, int $percentage_discount): float
    {
        $total = $sub_total - ($sub_total * ($percentage_discount / 100));

        return $total;
    }
}
