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
            'category_id' => 'exists:categories,id',
            'sub_category_id' => 'exists:sub_categories,id',
            'title' => 'min: 3',
            'product_type' => 'in:normal,premium',
            'division_id' => 'exists:divisions,id',
            'district_id' => 'exists:districts,id',
            'sub_district_id' => 'exists:sub_districts,id',
            'location' => 'required',
            'price' => 'required',
            'image' => 'array',
//            'image.*' => 'mimes:jpg,png',
            'authenticity' => '',
            'edition' => '',
            'brand' => '',
            'condition' => '',
            'features' => '',
        ];
    }

    public function messages()
    {
        return [
//            'product_type.required' => "Ad type is required",
            'product_type.in' => "Ad type must be Top Ad or Normal Ad",
        ];
    }
}
