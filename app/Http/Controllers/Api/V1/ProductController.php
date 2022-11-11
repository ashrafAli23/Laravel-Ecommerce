<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as STATUS;

class ProductController extends Controller
{
    use Response;

    public function index(): JsonResponse
    {
        $products = Product::with(['category', 'variant'])
            ->where('active', true)
            ->paginate();


        return $this->dataResponse(
            ProductResource::collection($products),
            STATUS::HTTP_OK
        );
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $product = Product::with(['category', 'variant'])
            ->where('slug', $slug)->firstOrFail();


        return $this->dataResponse(
            new ProductResource($product),
            STATUS::HTTP_OK
        );
    }
}
