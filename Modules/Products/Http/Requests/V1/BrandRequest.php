<?php

namespace Modules\Products\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Common\Enums\BaseStatusEnum;

class BrandRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', Rule::unique('brands')->ignore($this->id)],
            'description' => ['nullable', 'string', 'min:3', 'max:300'],
            'order' => ['required', 'numeric', 'min:0', 'max:127'],
            'status' => ['required', Rule::in(BaseStatusEnum::toArray())],
            'is_feature' => ['nullable', 'bool'],
        ];
    }

    public function messages()
    {
        return [
            'status.in' => "The status must be one of the following: published, pending, draft.",
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}