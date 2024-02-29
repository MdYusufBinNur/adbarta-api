<?php

namespace App\Http\Requests;

use App\Action\HelperAction;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    public function failedValidation(Validator $validator)
    {
        return HelperAction::failedValidation($validator);
    }
}
