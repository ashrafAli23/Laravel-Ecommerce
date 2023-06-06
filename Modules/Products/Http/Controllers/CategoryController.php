<?php

namespace Modules\Products\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;
use Modules\Common\Dto\SelectedList;
use Modules\Common\Http\Requests\SelectedListRequest;
use Modules\Common\Http\Response\BaseResponse;
use Modules\Products\Dto\Category\CreateCategoryDto;
use Modules\Products\Http\Requests\V1\CategoryRequest;
use Modules\Products\Service\CategoryService;
use Modules\Products\Transformers\CategoryTransformer;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CategoryRequest $request
     * @param BaseResponse $response
     * @return JsonResponse|JsonResource
     */
    public function store(CategoryRequest $request, BaseResponse $response): JsonResponse|JsonResource
    {
        try {
            $category = $this->categoryService->create(CreateCategoryDto::create($request));
            return $response
                ->setMessage("Created successfully")
                ->setCode(Response::HTTP_CREATED)
                ->setData(new CategoryTransformer($category))
                ->toApiResponse();
        } catch (\Throwable $th) {
            return $response
                ->setSuccess(false)
                ->setCode(Response::HTTP_BAD_REQUEST)
                ->setMessage("Failed to create")
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
            $category = $this->categoryService->findOne($id);
            return $response
                ->setMessage("Success")
                ->setData(new CategoryTransformer($category))
                ->toApiResponse();
        } catch (\Throwable $th) {
            return $response
                ->setSuccess(false)
                ->setMessage($th->getMessage())
                ->setCode($th->getCode())
                ->toApiResponse();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CategoryRequest $request
     * @param integer $id
     * @param BaseResponse $response
     * @return JsonResponse|JsonResource
     */
    public function update(CategoryRequest $request, int $id, BaseResponse $response): JsonResponse|JsonResource
    {
        try {
            $category = $this->categoryService->update($id, CreateCategoryDto::create($request));
            return $response
                ->setMessage("Updated successfully")
                ->setData(new CategoryTransformer($category))
                ->toApiResponse();
        } catch (\Throwable $th) {
            return $response
                ->setSuccess(false)
                ->setMessage($th->getMessage())
                ->setCode($th->getCode())
                ->toApiResponse();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param integer $id
     * @param BaseResponse $response
     * @return JsonResponse
     */
    public function destroy(int $id, BaseResponse $response): JsonResponse
    {
        try {
            $this->categoryService->destroy($id);
            return $response
                ->setMessage("Deleted successfully")
                ->toApiResponse();
        } catch (\Throwable $th) {
            return $response
                ->setSuccess(false)
                ->setMessage($th->getMessage())
                ->setCode($th->getCode())
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
            $this->categoryService->deletes(SelectedList::create($request));
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
