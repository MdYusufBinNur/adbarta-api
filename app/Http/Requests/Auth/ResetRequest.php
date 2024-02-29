<?php

namespace App\Http\Requests\Auth;

use App\Action\HelperAction;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ResetRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'password' => 'required|min:6',
            'secret' => 'required|exists:users,remember_token',
        ];
    }
    public function messages(): array
    {
        return [

        ];
    }

    protected function failedValidation(Validator $validator)
    {
//        $response = new JsonResponse([
//            'error' => true,
//            'message' => $validator->errors()->first(),
//            'data' => null,
//        ], 422);
//
//        throw new ValidationException($validator, $response);
        return HelperAction::failedValidation($validator);
    }
}
