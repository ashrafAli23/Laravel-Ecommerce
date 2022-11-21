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
        $main_image = $this->uploadFile($request, 'image', 'products');

        // check product_type if physical or digital
        $check_product_type = $request->product_type === 'physical';

        $requestData = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'current_stock' => $check_product_type ? $request->current_stock : 0,
            'images' => json_encode($product_image),
            'main_image' => $main_image,
            'active' => $request->status ?? 1,
            'min_qty' => $check_product_type ? $request->min_qty : 0,
            'product_type' => $request->product_type,
            'unit' => $check_product_type ? $request->unit : null,
            'tax' => $request->tax,
            'tax_type' => $request->tax_type,
            'shipping_cost' => $check_product_type ? $request->shipping_cost : 0,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id ?? null,
        ];

        $product =  $this->repository->store($requestData);

        if (isset($request->variant)) {
            foreach ($request->variant as $key => $value) {
                $product->variant()->create([$key => $value]);
            }
        }
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
