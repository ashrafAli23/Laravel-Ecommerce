<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\PaginateRequest;
use App\Http\Requests\V1\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as STATUS;

class ProductController extends Controller
{
    use Response;
    private ProductService $product;
    public function __construct(ProductService $product)
    {
        $this->product = $product;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PaginateRequest $request): JsonResponse
    {
        try {
            $data = $this->product->getAllProducts($request);
            return $this
                ->dataResponse(ProductResource::collection($data), STATUS::HTTP_OK);
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
    public function store(ProductRequest $request): JsonResponse
    {
        try {
            $this->product->storeProduct($request);
            return $this->successResponse("Product created successfully", STATUS::HTTP_CREATED);
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
            $product = $this->product->showProduct($id);

            return $this->dataResponse(new ProductResource($product), STATUS::HTTP_OK);
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
