<?php

namespace App\Http\Resources\Categories;

use App\Http\Resources\SubCategory\SubCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image' => $this->image,
            'name' => $this->name,
            'ads' => 0,
            'sub' => SubCategoryResource::collection($this->sub_category)
        ];
    }
}
