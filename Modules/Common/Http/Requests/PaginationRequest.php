<?php

namespace Modules\Common\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaginationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'page' => ['required', 'numeric', 'min:0'],
            'per_page' => ['required', 'numeric', 'min:10', 'max:100'],
            'search' => ['nullable', 'string', 'min:2', 'max:30'],
            'order_by' => ['nullable', Rule::in(['desc', 'asc'])]
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