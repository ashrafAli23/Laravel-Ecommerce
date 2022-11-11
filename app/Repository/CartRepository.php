<?php

declare(strict_types=1);

namespace App\Repository;

use App\Http\Interface\Repository\ICartRepository;
use App\Models\Cart;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class CartRepository implements ICartRepository
{
    private Cart $model;

    public function __construct(Cart $model)
    {
        $this->model = $model;
    }

    public function createCart(array $data): Cart
    {
        $cart = $this->model->create([
            'cooke_id' => Str::uuid()
        ]);

        $cart->cartItems()->create($data);

        return $cart;
    }


    public function getUserCart(string|null $id): Cart|null
    {
        $cart = $this->model->with(['cartItems' => [
            'variant'
        ]])->where('cooke_id', $id)->first();
        return $cart;
    }

    public function checkCart(string|null $cooke_id): Cart | null
    {
        $cart = $this->model->where('cooke_id', $cooke_id)
            ->first();

        return $cart;
    }

    public function updateCart(array $data, int $id): void
    {
        $cart = $this->model->findOrFail($id, function () {
            return "Cart not found";
        });

        $cart->update($data);
    }

    public function deleteCart(string|null $id): void
    {
        $this->model->where('cooke_id', $id)->first()->delete();
    }

    public function subTotal(int $id): float | null
    {
        $total = $this->model->cartItems()->where('cart_id', $id)
            ->join('variants', 'variants.id', '=', 'cart_items.variant_id')
            ->selectRaw('SUM(variants.price * cart_items.quantaty) as total')
            ->value('total');

        return floatval($total);
    }
}
