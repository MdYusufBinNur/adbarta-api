<?php

namespace App\Http\Requests\Auth;

use App\Action\HelperAction;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|exists:users,email|max:50',
            'code' => ['nullable|min:6|required_if:type,==,check_code|exists:remember_token,users'],
            'password' => 'nullable|required_if:type,==,check_secret|min:6',
        ];
    }
    public function messages(): array
    {
        return [

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        return HelperAction::failedValidation($validator);
    }
}
