<?php

namespace App\Http\Resources\Chat;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $message = $this->message;

        if (strlen($message) > 150) {
            $message = substr($message, 0, 150) . '...';
        }

        if ($this->user_id == auth()->id()) {
            $message = 'You: ' . $message;
        }
        return [

            'id' => $this->user->id,
            'full_name' => $this->user->full_name,
            'photo' => $this->user->photo,
            'room_id' => $this->room_id,
            'message' => [
                'id' => $this->id,
                'message' => $message,
                'seen' => $this->seen,
                'created_at' => $this->created_at->format('Y-m-d h:i A'),
            ],
        ];
    }
}
