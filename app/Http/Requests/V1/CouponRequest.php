<?php

declare(strict_types=1);

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
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
            'code' => 'required|min:5|max:10|unique:coupons,code',
            'usage_limit' => 'required|numeric|min:1|max:500',
            'percentage_discount' => 'required|numeric|min:1|max:100',
            'expire_at' => 'required|date',
            'status' => 'nullable|boolean'
        ];
    }
}
