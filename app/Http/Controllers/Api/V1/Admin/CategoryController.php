<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CategoryRequest;
use App\Http\Requests\V1\PaginateRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as STATUS;


class CategoryController extends Controller
{
    use Response;
    private CategoryService $category;
    public function __construct(CategoryService $category)
    {
        $this->category = $category;
    }

    public function index(PaginateRequest $request): JsonResponse
    {
        try {
            $data = $this->category->getAllCategory($request);
            return $this
                ->dataResponse(CategoryResource::collection($data), STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        try {
            $this->category->storeCategory($request);
            return $this->successResponse("Category created successfully", STATUS::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $category = $this->category->showCategory($id);

            return $this->dataResponse(new CategoryResource($category), STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|min:3|unique:categories,name',
                'description' => 'required|string|min:3',
                'status' => 'nullable|boolean'
            ]);

            $this->category->updateCategory($id, $request);
            return $this->successResponse("Category updated successfully", STATUS::HTTP_OK);
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

            $this->category->updateCategory($id, $request);
            return $this->successResponse("Category updated successfully", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }

    public function updateImage(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'image' => 'required|file'
            ]);

            $this->category->updateCategoryIMG($id, $request);

            return $this->successResponse("Category updated successfully", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->category->deleteCategory($id);
            return $this->successResponse("Category deleted successfully", STATUS::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }
}
