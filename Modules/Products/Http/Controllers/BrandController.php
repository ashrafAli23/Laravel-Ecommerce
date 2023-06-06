<?php

namespace Modules\Products\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;
use Modules\Common\Dto\SelectedList;
use Modules\Common\Http\Requests\PaginationRequest;
use Modules\Common\Http\Requests\SelectedListRequest;
use Modules\Common\Http\Response\BaseResponse;
use Modules\Products\Dto\CreateBrandDto;
use Modules\Products\Http\Requests\V1\Brand\UpdateBrandRequest;
use Modules\Products\Http\Requests\V1\BrandRequest;
use Modules\Products\Service\BrandService;
use Modules\Products\Transformers\BrandTransformers;
use Symfony\Component\HttpFoundation\Response;

class BrandController extends Controller
{
    /**
     * @param BrandService $brandService
     */
    public function __construct(
        private readonly BrandService $brandService
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param BaseResponse $response
     * @return JsonResponse|JsonResource
     */
    public function index(PaginationRequest $request, BaseResponse $response): JsonResponse|JsonResource
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
                ->setMessage($th->getMessage())
                ->setCode(Response::HTTP_BAD_REQUEST)
                ->toApiResponse();
        }
    }

    /**
     * Show the specified resource.
     *
     * @param integer $id
     * @param BaseResponse $response
     * @return JsonResponse|JsonResource
     */
    public function show(int $id, BaseResponse $response): JsonResponse|JsonResource
    {
        try {
            $brand = $this->brandService->findOne($id);
            return $response
                ->setMessage("Success")
                ->setData(new BrandTransformers($brand))
                ->toApiResponse();
        } catch (\Throwable $th) {
            return $response
                ->setSuccess(false)
                ->setCode(Response::HTTP_NOT_FOUND)
                ->setMessage($th->getMessage())
                ->toApiResponse();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param BrandRequest $request
     * @param int $id
     * @param BaseResponse $response
     * @return JsonResponse|JsonResource
     */
    public function update(BrandRequest $request, int $id, BaseResponse $response): JsonResponse|JsonResource
    {
        try {
            $brand = $this->brandService->update(CreateBrandDto::create($request), $id);
            return $response
                ->setMessage("Updated successfully")
                ->setData(new BrandTransformers($brand))
                ->toApiResponse();
        } catch (\Throwable $th) {
            return $response
                ->setMessage($th->getMessage())
                ->setSuccess(false)
                ->setCode(Response::HTTP_BAD_REQUEST)
                ->toApiResponse();
        }
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

    /**
     * Remove lists of specified resources from storage.
     *
     * @param SelectedListRequest $request
     * @param BaseResponse $response
     * @return JsonResponse
     */
    public function deletes(SelectedListRequest $request, BaseResponse $response): JsonResponse
    {
        try {
            $this->brandService->deletes(SelectedList::create($request));
            return $response->setMessage("deletes successfully")->toApiResponse();
        } catch (\Throwable $th) {
            return $response
                ->setSuccess(false)
                ->setCode($th->getCode())
                ->setMessage($th->getMessage())
                ->toApiResponse();
        }
    }
}
