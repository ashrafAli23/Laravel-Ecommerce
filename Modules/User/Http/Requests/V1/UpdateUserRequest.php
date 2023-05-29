<?php

namespace Modules\User\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

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
            'username' => ['string', 'min:3', 'max:20', 'required'],
            'email' => ['string', 'email', 'required'],
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
