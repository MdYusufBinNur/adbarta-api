<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UserUpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'sometimes|email:regex:/^.+@.+$/i|max:50',
            'password' => 'sometimes|min:6',
            'role' => 'sometimes|required|in:admin,vendor,staff',
            'full_name' => 'sometimes|required|max:30',
            'company' => '',
            'phone' => '',
        ];
    }
}
