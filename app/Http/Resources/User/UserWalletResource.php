<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserWalletResource extends JsonResource
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
            'user_id'=> $this->user_id,
            'used'=> $this->used,
            'available'=> $this->available,
            'user_uid' => $this->whenLoaded('user')->uid,
            'user_name' => $this->whenLoaded('user')->full_name,
            'user_email' => $this->whenLoaded('user')->email,
            'history' => UserWalletHistoryResource::collection($this->whenLoaded('history'))
        ];
    }
}
