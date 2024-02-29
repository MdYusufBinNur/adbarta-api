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
            'status' => $this->status,
            'company' => $this->company,
            'active' => $this->active,
            'is_public' => $this->is_public,
            'email_verified_at' => $this->email_verified_at,
        ];
    }
}
