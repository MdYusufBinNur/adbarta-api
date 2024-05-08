<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uid' => $this->uid,
            'email' => $this->email,
            'photo' => $this->photo,
            'name' => $this->full_name,
            'phone' => $this->phone,
            'website' => $this->website,
            'about' => $this->about,
            'role' => $this->role,
            'nid_one' => $this->nid_one,
            'nid_two' => $this->nid_two,
            'status' => $this->status,
            'street' => $this->street,
            'dob' => $this->dob,
            'company' => $this->company,
            'active' => $this->active,
            'is_public' => $this->is_public,
            'email_verified_at' => $this->email_verified_at,
            'district' => $this?->district?->name,
            'district_id' => $this?->district_id,
            'sub_district' => $this?->sub_district?->name,
            'sub_district_id' => $this?->sub_district_id,
            'type' => $this?->type,
            'wallet' => $this->whenLoaded('wallet'),
        ];
    }
}
