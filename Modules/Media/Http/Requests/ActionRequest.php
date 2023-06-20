<?php

namespace Modules\Media\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'selected' => ['required', 'array', 'min:1'],
            'selected.*.id' => ['required', 'numeric'],
            'selected.*.is_folder' => ['required', 'boolean'],
            'selected.*.value' => ['nullable', 'string'],
            'action' => ['required', 'string', Rule::in(['rename', 'trash', 'delete'])]
        ];
    }

    public function messages()
    {
        return [
            'action.in' => "The status must be one of the following: rename, trash, delete.",
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