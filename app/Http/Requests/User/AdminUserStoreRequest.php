<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
class AdminUserStoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|unique:users,email|email:regex:/^.+@.+$/i|max:50',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,manager,seller',
            'full_name' => 'required|max:30',
            'phone' => '',
        ];
    }
}
