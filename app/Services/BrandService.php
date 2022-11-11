<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Interface\Repository\IRepository;
use App\Traits\UploadFile;
use Exception;
use Illuminate\Http\Request;

class BrandService
{
    use UploadFile;

    private IRepository $repository;

    public function __construct(IRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllBrands(Request $request): object
    {
        $entries = $request->entries ?? 10;
        $brand = $this->repository->index()
            ->query()->paginate($entries);

        if (!$brand) {
            throw new Exception("Brands is empty", 400);
        }

        return $brand;
    }

    public function storeBrand(Request $request): void
    {
        $this->repository->store([
            'name' => $request->name,
            'image' => $this->uploadFile($request, 'brand'),
            'active' => $request->status ?? 1,
        ]);
    }

    public function showBrand(int $id): object
    {
        $brand = $this->repository->show($id);

        if (!$brand) {
            throw new Exception("Brand not found", 404);
        }

        return $brand;
    }

    public function updateBrand(int $id, Request $request): void
    {

        $brand = $this->repository->update($id);

        if (!$brand) {
            throw new Exception("Brand not found", 404);
        }

        if (isset($request->status) && !isset($request->name)) {
            $brand->update(['active' => $request->status]);
        } else {

            $data = [
                'name' => $request->name,
                'active' => $request->status ?? 1
            ];

            $brand->update($data);
        }
    }

    public function updateBrandIMG(int $id, Request $request): void
    {
        $brand = $this->repository->update($id);

        if (!$brand) {
            throw new Exception("Brand not found", 404);
        }

        $this->deleteFile($brand->image);

        $data = [
            'image' => $this->uploadFile($request, 'brand')
        ];

        $brand->update($data);
    }

    public function deleteBrand(int $id): void
    {
        $brand = $this->repository->delete($id);
        if (!$brand) {
            throw new Exception("Brand not found", 404);
        }
        $this->deleteFile($brand->image);
        $brand->delete();
    }
}
