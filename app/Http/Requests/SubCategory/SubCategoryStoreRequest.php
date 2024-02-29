<?php

namespace App\Http\Requests\SubCategory;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class SubCategoryStoreRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required',
            'active' =>'',
            'image' => ''
        ];
    }
}
