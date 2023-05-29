<?php

declare(strict_types=1);

namespace Modules\User\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'username' => ['string', 'min:3', 'max:20', 'required', 'unique:users,username'],
            'email' => ['string', 'email', 'required', 'unique:users,email'],
            'password' => ['required', 'min:6', 'required_with:confirme_password', 'same:confirme_password'],
            'confirme_password' => ['required', 'min:6'],
            'roleId' => ['nullable', 'numeric', 'exists:roles,id']
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
