<?php

namespace App\Http\Requests\User;

use App\Action\HelperAction;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|unique:users,email|email:regex:/^.+@.+$/i|max:50',
            'password' => 'required|min:6',
        ];
    }
    public function failedValidation(Validator $validator)
    {
        return HelperAction::failedValidation($validator);
    }
}
