<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Repository\CartRepository;
use App\Services\CouponService;
use App\Traits\Response;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as STATUS;

class CouponController extends Controller
{
    use Response;
    private CartRepository $cart;

    public function __construct(CartRepository $cart)
    {
        $this->cart = $cart;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'coupon' => 'required|string|min:5'
        ]);
        try {
            DB::beginTransaction();

            $apply_coupon = Coupon::where('code', $request->coupon)->first();

            CouponService::applyCoupon($apply_coupon);

            $cartData = $this->cart->checkCart(Cookie::get('e-store_cart_id'));

            if (!$cartData) {
                throw new Exception("Cart not found");
            }

            $cartData->update([
                'coupon' => $request->coupon,
                'discount' => $apply_coupon->percentage_discount
            ]);

            DB::commit();
            return $this->successResponse("Coupon applied successfully", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }
}
