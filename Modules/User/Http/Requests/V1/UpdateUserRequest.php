<?php

namespace Modules\User\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => ['string', 'min:3', 'max:20', 'required'],
            'last_name' => ['string', 'min:3', 'max:20', 'required'],
            'username' => ['string', 'min:3', 'max:20', Rule::unique('users')->ignore($this->id), 'required'],
            'email' => ['string', 'email', Rule::unique('users')->ignore($this->id), 'required'],
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