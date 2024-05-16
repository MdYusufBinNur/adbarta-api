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
        $message = $this['latest_message']['message'];

        if (strlen($message) > 150) {
            $message = substr($message, 0, 150) . '...';
        }
        return [

            'id' => $this['receiver_info']['id'],
            'full_name' => $this['receiver_info']['full_name'],
            'photo' => $this['receiver_info']['photo'],
            'message' => [
                'id' => $this['latest_message']['id'],
                'message' => $message,
                'seen' => $this['latest_message']['seen'],
                'created_at' => $this['latest_message']['created_at']->format('Y-m-d h:i A'),
                // Add other latest_message fields as needed
            ],
        ];
    }
}
