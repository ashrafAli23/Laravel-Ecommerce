<?php

declare(strict_types=1);

namespace App\Http\Interface\Repository;

use Illuminate\Database\Eloquent\Model;

interface ICartRepository
{
    public function createCart(array $data): Model;
    public function getUserCart(string $id): Model|null;
    public function checkCart(string|null $cooke_id): Model | null;
    public function updateCart(array $data, int $id): void;
    public function deleteCart(string|null $id): void;
    public function subTotal(int $id): float | null;
}
