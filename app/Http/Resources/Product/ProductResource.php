<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\Categories\CategoryResource;
use App\Http\Resources\SubCategory\SubCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $thumbnailImage = $this->image->isEmpty() ? null : $this->image->first()->image;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'category_id' => $this->category_id,
            'sub_category_id' => $this->sub_category_id,
            'slug' => $this->slug,
            'title' => $this->title,
            'size' => $this->size,
            'ad_type' => $this->product_type === 'Premium' ? 'Top Ad' : "Normal Ad",
            'color' => $this->color,
            'location' => $this->location,
            'condition' => $this->condition,
            'brand' => $this->brand,
            'edition' => $this->edition,
            'authenticity' => $this->authenticity,
            'features' => $this->features,
            'division_id' => $this->division_id,
            'district_id' => $this->district_id,
            'sub_district_id' => $this->sub_district_id,
            'view' => $this->view,
            'status' => $this->status,
            'points' => $this->points,
            'price' => $this->price,
            'contact_name' => $this->contact_name,
            'contact_email' => $this->contact_email,
            'contact_number' => $this->contact_number,
            'additional_contact_number' => $this->additional_contact_number,
            'show_contact_number' => $this->show_contact_number,
            'accept_terms' => $this->accept_terms,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'category_name' => $this?->category?->name,
            'sub_category_name' => $this?->sub_category?->name,
            'sub_category' => new SubCategoryResource($this->sub_category),
            'category' => new CategoryResource($this->category),
            'images' => ProductImagesResource::collection($this->whenLoaded('image')),
            'image' => $thumbnailImage,
            'date' => Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('Y-m-d')
            // Assuming 'image' is a relationship

        ];
    }
}
