<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'sometimes|required|exists:categories,id',
            'sub_category_id' => 'sometimes|required|exists:sub_categories,id',
            'title' => 'sometimes|required|min:3',
            'division_id' => 'sometimes|required|exists:divisions,id',
            'district_id' => 'sometimes|required|exists:districts,id',
            'sub_district_id' => 'sometimes|required|exists:sub_districts,id',
            'location' => 'sometimes|required',
            'price' => 'sometimes|required',
            'image' => 'sometimes|required|array',
            'image.*' => 'sometimes|mimes|jpg,png',
            'authenticity' => '',
            'edition' => '',
            'brand' => '',
            'condition' => '',
        ];
    }
}
