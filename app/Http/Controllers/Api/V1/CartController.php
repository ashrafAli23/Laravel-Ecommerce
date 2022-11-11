<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItems;
use App\Repository\CartRepository;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as STATUS;



class CartController extends Controller
{
    use Response;

    private CartRepository $cart;
    private string|null $cooke_id;

    public function __construct(CartRepository $cart)
    {
        $this->cart = $cart;
        $this->cooke_id = Cookie::get('e-store_cart_id');
    }

    public function addToCart(CartRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $cart = $this->cart->checkCart($this->cooke_id);

            // check cart if created
            if (isset($cart)) {
                $cartItem = $cart->cartItems();

                $itemsData = $cartItem->where(
                    ['variant_id' => $request->product_id, 'cart_id' => $cart->id]
                )->first();

                /**
                 * check product if exist in cart_item,
                 * if not,
                 * add new product to cart_item
                 */
                if (!$itemsData) {

                    $cartItem->create([
                        'variant_id' => $request->product_id,
                        'quantaty' => 1
                    ]);
                } else {
                    // update exist product quantaty
                    $itemsData->update([
                        'quantaty' => $itemsData->quantaty + 1
                    ]);
                }

                db::commit();

                return $this->successResponse("Cart updated", STATUS::HTTP_OK);
            }

            $cartData =  $this->cart->createCart([
                'variant_id' => $request->product_id,
                'quantaty' => 1
            ]);
            $cooke = cookie('e-store_cart_id', $cartData->cooke_id, 60 * 24 * 30);
            db::commit();

            return $this->successResponse("Cart created", STATUS::HTTP_CREATED)
                ->withCookie($cooke);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }

    public function showCartDetails(Request $request): JsonResponse
    {
        try {
            $cartData = $this->cart->getUserCart($this->cooke_id);

            if (!isset($cartData)) {
                return $this->errorResponse("Cart is empty", STATUS::HTTP_BAD_REQUEST);
            }



            return $this->dataResponse(
                new CartResource($cartData),
                STATUS::HTTP_OK
            );
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }

    public function updateCart(CartRequest $request): JsonResponse
    {
        $request->validate([
            'quantaty' => 'required|numeric'
        ]);

        $checkCart = $this->cart->checkCart($this->cooke_id);

        // check cart if exsits 
        if (!isset($checkCart)) {
            return $this->errorResponse("Cart not found", STATUS::HTTP_NOT_FOUND);
        }

        $itemsData = $checkCart->cartItems()->where(
            ['variant_id' => $request->product_id, 'cart_id' => $checkCart->id]
        )->first();

        // check product if exists
        if (isset($itemsData)) {
            /**
             * check if product qty >=1
             * if true update qty
             * else delete product
             */
            if ($request->quantaty >= 1) {
                $itemsData->update([
                    'quantaty' => $request->quantaty
                ]);
            } else {

                $itemsData->delete();
            }
        } else {
            return $this->errorResponse("This product not in cart", STATUS::HTTP_NOT_FOUND);
        }

        // get user cart
        $cartData = $this->cart->getUserCart($this->cooke_id);
        return $this->dataResponse(
            new CartResource($cartData),
            STATUS::HTTP_OK
        );
    }

    public function destroyCart(Request $request): JsonResponse
    {
        try {
            $cartData = $this->cart->checkCart($this->cooke_id);

            if (!isset($cartData)) {
                return $this->errorResponse("Cart is empty", STATUS::HTTP_NOT_FOUND);
            }

            $this->cart->deleteCart($this->cooke_id);

            return $this->successResponse("cart deleted", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }
}
