<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Interface\Repository\IRepository;
use App\Traits\UploadFile;
use Exception;
use Illuminate\Http\Request;

class CategoryService
{
    use UploadFile;

    private IRepository $repository;
    public function __construct(IRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllCategory(Request $request): object
    {
        $entries = $request->entries ?? 10;
        $category = $this->repository->index()
            ->query()->paginate($entries);

        if (!$category) {
            throw new Exception("Categories is empty", 400);
        }

        return $category;
    }

    public function storeCategory(Request $request): void
    {
        $this->repository->store([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $this->uploadFile($request, 'category'),
        ]);
    }

    public function showCategory(int $id): object
    {
        $category = $this->repository->show($id);

        if (!$category) {
            throw new Exception("Category not found", 404);
        }

        return $category;
    }

    public function updateCategory(int $id, Request $request): void
    {
        $category = $this->repository->update($id);

        if (!$category) {
            throw new Exception("Category not found", 404);
        }

        if (isset($request->status) && !isset($request->name)) {
            $category->update(['active' => $request->status]);
        } else {
            $data = [
                'name' => $request->name,
                'description' => $request->description,
                'active' => $request->status ?? 1
            ];

            $category->update($data);
        }
    }

    public function updateCategoryIMG(int $id, Request $request): void
    {
        $category = $this->repository->update($id);

        if (!$category) {
            throw new Exception("Category not found", 404);
        }

        $this->deleteFile($category->image);
        $data = [
            'image' => $this->uploadFile($request, 'category')
        ];
        $category->update($data);
    }

    public function deleteCategory(int $id): void
    {
        $category = $this->repository->delete($id);
        if (!$category) {
            throw new Exception("Category not found", 404);
        }
        $this->deleteFile($category->image);
        $category->delete();
    }
}
