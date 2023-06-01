<?php

namespace Modules\Products\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;
use Modules\Common\Enums\BaseStatusEnum;
use Modules\Common\Http\Response\BaseResponse;
use Modules\Products\Dto\CreateBrandDto;
use Modules\Products\Http\Requests\V1\BrandRequest;
use Modules\Products\Service\BrandService;
use Modules\Products\Transformers\BrandTransformers;
use Symfony\Component\HttpFoundation\Response;

class BrandController extends Controller
{
    /**
     * @param BrandService $brandService
     */
    public function __construct(private readonly BrandService $brandService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param BaseResponse $response
     * @return JsonResponse|JsonResource
     */
    public function index(Request $request, BaseResponse $response): JsonResponse|JsonResource
    {
        try {
            $brand = $this->brandService->findAll($request);
            return $response
                ->setMessage("success")
                ->setData(BrandTransformers::collection($brand))
                ->toApiResponse();
        } catch (\Throwable $th) {
            return $response
                ->setSuccess(false)
                ->setMessage($th->getMessage())
                ->setCode(Response::HTTP_BAD_REQUEST)
                ->toApiResponse();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BrandRequest $request
     * @param BaseResponse $response
     * @return JsonResponse|JsonResource
     */
    public function store(BrandRequest $request, BaseResponse $response): JsonResponse|JsonResource
    {
        try {
            $brand = $this->brandService->create(CreateBrandDto::create($request));
            return $response
                ->setCode(Response::HTTP_CREATED)
                ->setData(new BrandTransformers($brand))
                ->setMessage("Created successfully")
                ->toApiResponse();
        } catch (\Throwable $th) {
            return $response
                ->setSuccess(false)
                ->setMessage("Failed to create")
                ->setCode(Response::HTTP_BAD_REQUEST)
                ->toApiResponse();
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param integer $id
     * @return JsonResponse|JsonResource
     */
    public function destroy(int $id, BaseResponse $response): JsonResponse|JsonResource
    {
        try {
            $this->brandService->destroy($id);
            return $response
                ->setMessage("Deleted successfully")
                ->toApiResponse();
        } catch (\Throwable $th) {
            return $response
                ->setSuccess(false)
                ->setMessage($th->getMessage())
                ->setCode(Response::HTTP_BAD_REQUEST)
                ->toApiResponse();
        }
    }
}
