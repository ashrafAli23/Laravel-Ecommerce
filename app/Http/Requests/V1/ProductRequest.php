<?php

declare(strict_types=1);

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Http\Enum\ProductType;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:2|unique:products,name',
            'description' => 'required|string|min:6',
            'images' => 'required|image',
            'main_image' => 'required|image',
            'current_stock' => 'required|numeric',
            'price' => 'required|numeric',
            'min_qty' => 'required|numeric|min:0',
            'status' => 'nullable|boolean',
            'tax' => 'required|numeric|min:0',
            'tax_type' => 'required',
            'unit' => 'required_if:product_type,==,physical',
            'shipping_cost' => 'required_if:product_type,==,physical|min:0|numeric',
            'product_type' => [new Enum(ProductType::class)],
            'category_id' => 'required|numeric|exsites:categories,id',
            'brand_id' => 'nullable|numeric|exsites:brands,id'
        ];
    }
}
