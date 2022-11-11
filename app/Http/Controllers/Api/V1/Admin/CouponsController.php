<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CouponRequest;
use App\Http\Requests\V1\PaginateRequest;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use App\Repository\Repository;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as STATUS;

class CouponsController extends Controller
{
    use Response;
    private Repository $coupon;
    public function __construct(Coupon $coupon)
    {
        $this->coupon = new Repository($coupon);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(PaginateRequest $request): JsonResponse
    {
        try {
            $entries = $request->entries ?? 10;
            $data = $this->coupon
                ->index()->query()->paginate($entries);

            return $this
                ->dataResponse(CouponResource::collection($data), STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CouponRequest $request): JsonResponse
    {
        try {

            $this->coupon->store([
                'code' => $request->code,
                'usage_limit' => $request->usage_limit,
                'percentage_discount' => $request->percentage_discount,
                'expire_at' => $request->expire_at,
                'active' => $request->status ?? 1
            ]);

            return $this->successResponse("Coupon created successfully", STATUS::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $coupon = $this->coupon->show($id);

            if (!$coupon) {
                return $this->errorResponse("Coupon not found", STATUS::HTTP_NOT_FOUND);
            }

            return $this->dataResponse(new CouponResource($coupon), STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CouponRequest $request, int $id): JsonResponse
    {
        try {
            $coupon = $this->coupon->update($id);

            if (!$coupon) {
                return $this->errorResponse("Coupon not found", STATUS::HTTP_NOT_FOUND);
            }

            $data = [
                'code' => $request->code,
                'usage_limit' => $request->usage_limit,
                'percentage_discount' => $request->percentage_discount,
                'expire_at' => $request->expire_at,
                'active' => $request->status
            ];

            $coupon->update($data);

            return $this->successResponse("Coupon updated successfully", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $data = $this->coupon->delete($id);
            if (!$data) {
                return $this->errorResponse("Coupon not found", STATUS::HTTP_NOT_FOUND);
            }
            $data->delete();
            return $this->successResponse("Coupon deleted successfully", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), STATUS::HTTP_CONFLICT);
        }
    }
}
