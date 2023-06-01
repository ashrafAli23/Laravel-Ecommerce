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
            'name' => ['required', 'string', 'unique:brands,name'],
            'description' => ['nullable', 'string', 'min:1', 'max:300'],
            'order' => ['required', 'integer', 'min:0', 'max:127'],
            'status' => Rule::in(BaseStatusEnum::toArray())
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
