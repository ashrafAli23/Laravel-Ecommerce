<?php

declare(strict_types=1);

namespace App\Http\Interface\Repository;

use App\Models\Order;

interface IOrderRepository
{
    public function createOrder(array $data): Order;
    public function updateOrder(array $data, int $id): void;
    public function getUserOrder(int $id, string $number): Order|null;
    // public function cancleOrder(int $id): void;
}
