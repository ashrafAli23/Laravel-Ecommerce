<?php

declare(strict_types=1);

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
            'current_stock' => 'required|numeric',
            'price' => 'required|numeric',
            'retail' => 'required|numeric',
            'status' => 'nullable|boolean',
            'vat' => 'nullable|boolean',
            'category_id' => 'required|numeric|exsites:categories,id',
            'brand_id' => 'required|numeric|exsites:brands,id'
        ];
    }
}
