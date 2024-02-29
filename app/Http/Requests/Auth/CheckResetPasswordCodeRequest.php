<?php

namespace App\Http\Requests\Auth;

use App\Action\HelperAction;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CheckResetPasswordCodeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|exists:password_reset_tokens,email',
            'code' => 'required|exists:password_reset_tokens,token',
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
