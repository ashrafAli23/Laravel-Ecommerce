<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Interface\Repository\IRepository;
use App\Traits\UploadFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductService
{
    use UploadFile;

    private IRepository $repository;
    public function __construct(IRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllProducts(Request $request): object
    {
        $entries = $request->entries ?? 10;
        $product = $this->repository->index()
            ->query()->paginate($entries);

        if (!$product) {
            throw new Exception("Products is empty", 400);
        }

        return $product;
    }

    public function storeProduct(Request $request): void
    {
        $product_image = $this->uploadMultipleFile($request, 'images', 'products');
        dd($product_image);

        $requestData = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'retail' => $request->retail,
            'current_stock' => $request->current_stock,
            'images' => $request->images,
            'active' => $request->status,
            'vat' => $request->vat,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
        ];
        // $this->repository->store([
        //     'name' => $request->name,
        //     'description' => $request->description,
        //     'image' => $this->uploadFile($request, 'category'),
        // ]);
    }

    public function showProduct(int $id): object
    {
        $product = $this->repository->show($id);

        if (!$product) {
            throw new Exception("Category not found", 404);
        }

        return $product;
    }

    public function deleteProduct(int $id): void
    {
        $product = $this->repository->delete($id);
        if (!$product) {
            throw new Exception("Category not found", 404);
        }
        $this->deleteFile($product->image);
        $product->delete();
    }
}
