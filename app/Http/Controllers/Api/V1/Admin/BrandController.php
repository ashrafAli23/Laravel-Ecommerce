<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\BrandRequest;
use App\Http\Requests\V1\PaginateRequest;
use App\Http\Resources\BrandResource;
use App\Services\BrandService;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as STATUS;

class BrandController extends Controller
{
    use Response;

    private BrandService $brand;
    public function __construct(BrandService $brand)
    {
        $this->brand = $brand;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PaginateRequest $request): JsonResponse
    {
        try {
            $data = $this->brand->getAllBrands($request);
            return $this
                ->dataResponse(BrandResource::collection($data), STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BrandRequest $request): JsonResponse
    {
        try {
            $this->brand->storeBrand($request);
            return $this->successResponse("Brand created successfully", STATUS::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id): JsonResponse
    {
        try {
            $brand = $this->brand->showBrand($id);

            return $this->dataResponse(new BrandResource($brand), STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|min:2|unique:brands,name',
                'status' => 'nullable|boolean'
            ]);

            $this->brand->updateBrand($id, $request);
            return $this->successResponse("Brand updated successfully", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|boolean'
            ]);

            $this->brand->updateBrand($id, $request);
            return $this->successResponse("Brand updated successfully", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }

    public function updateImage(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'image' => 'required|image'
            ]);

            $this->brand->updateBrandIMG($id, $request);

            return $this->successResponse("Brand updated successfully", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->brand->deleteBrand($id);
            return $this->successResponse("Brand deleted successfully", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }
}
