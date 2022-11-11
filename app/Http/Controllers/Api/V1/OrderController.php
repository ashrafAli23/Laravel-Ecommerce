<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Repository\CartRepository;
use App\Repository\CouponRepository;
use App\Repository\OrderRepository;
use App\Services\CouponService;
use App\Services\OrderService;
use App\Traits\Response;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cookie;
// use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as STATUS;

class OrderController extends Controller
{
    use Response;
    private CartRepository $cart;

    public function __construct(CartRepository $cart)
    {
        $this->cart = $cart;
    }

    public function createOrder(OrderRequest $request): JsonResponse
    {
        try {

            DB::beginTransaction();

            $cartData = $this->cart->getUserCart(Cookie::get('e-store_cart_id'));

            if (!isset($cartData) || !$cartData->cartItems->count()) {
                return $this->errorResponse("Cart is empty",  STATUS::HTTP_NOT_FOUND);
            }

            $sub_total = $this->cart->subTotal($cartData->id);

            if (isset($cartData->coupon)) {
                $sub_total = OrderService::calcSubTotal($sub_total, $cartData->discount);
            }

            $orderData = Order::create([
                'user_id' => auth()->user()->id,
                'paymant_method' => $request->paymant_method,
                'sub_total' => $sub_total
            ]);

            OrderAddress::create([
                'order_id' => $orderData->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'street_address' => $request->street_address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'post_code' => $request->post_code
            ]);

            $cartData->delete();

            DB::commit();

            return $this->successResponse("Success create order", STATUS::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_BAD_REQUEST);
        }
    }

    public function getOrderDetails($id): JsonResponse
    {
        $orderData = Order::find((int)$id);

        if (!$orderData || $orderData->user_id !== auth()->user()->id) {

            return $this->errorResponse("Order not found", STATUS::HTTP_NOT_FOUND);
        }

        return $this->dataResponse(new OrderResource($orderData), STATUS::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateOrderStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:cancelled,completed,refunded',
        ]);

        try {
            $order = Order::find($id);
            if (!$order || $order->user_id !== auth()->user()->id) {
                return $this->errorResponse("Order not found", STATUS::HTTP_NOT_FOUND);
            }

            if (isset($request->status)) {
                switch ($request->status) {
                    case 'cancelled':
                        $order->update([
                            'status' => $request->status,
                            'cancelled_at' => Carbon::now()
                        ]);
                        break;
                    case 'completed':
                        $order->update([
                            'status' => $request->status,
                            'completed_at' => Carbon::now()
                        ]);
                        break;
                    default:
                        $order->update([
                            'status' => $request->status,
                        ]);
                        break;
                }
            }

            return $this->successResponse("Order updated", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }
}
