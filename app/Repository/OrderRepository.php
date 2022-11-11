<?php

declare(strict_types=1);

namespace App\Repository;

use App\Http\Interface\Repository\IOrderRepository;
use App\Models\Order;

class OrderRepository implements IOrderRepository
{
    private Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function createOrder(array $data): Order
    {
        $orderData = $this->order->create($data);
        return $orderData;
    }

    public function updateOrder(array $data, int $id): void
    {
        $orderData = $this->order->find($id);
        $orderData->update($data);
    }

    public function getUserOrder(int $id, string $number): Order|null
    {
        $orderData = $this->order->with(['order_address'])
            ->where(['user_id' => $id, 'number' => $number])->first();
        return $orderData;
    }

    // public function cancleOrder(): void
    // {

    // }
}
