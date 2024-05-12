<?php

namespace App\Http\Resources\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserWalletHistoryResource extends JsonResource
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
            'user_uid' => $this->whenLoaded('user')->uid,
            'points' => $this->points,
            'points_type' => $this->points_type,
            'gateway' => $this->gateway,
            'status' => $this->status === 'approved' ? "Approve" : 'Not Approve',
            'trxID' => $this->trxID,
            'name' => $this->name,
            'phone' => $this->phone,
            'date' => Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('Y-m-d'),
        ];
    }
}
