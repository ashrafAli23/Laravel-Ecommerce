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
            'selected.*.value' => ['required_if:action,rename', 'string'],
            'action' => ['required', 'string', Rule::in(['rename', 'download', 'trash', 'delete', 'restore', 'empty_trash'])]
        ];
    }

    public function messages()
    {
        return [
            'action.in' => "The status must be one of the following: rename, trash, delete, restore, empty_trash, download.",
            'selected.*.value.required_if' => 'Value field is required'
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