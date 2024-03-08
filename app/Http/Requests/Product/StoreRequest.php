<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'title' => 'required|min: 3',
            'division_id' => 'required|exists:divisions,id',
            'district_id' => 'required|exists:districts,id',
            'sub_district_id' => 'required|exists:sub_districts,id',
            'location' => 'required',
            'price' => 'required',
            'image' => 'required|array',
            'image.*' => 'mimes:jpg,png',
            'authenticity' => '',
            'edition' => '',
            'brand' => '',
            'condition' => '',
        ];
    }
}
