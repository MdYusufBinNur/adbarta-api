<?php

namespace App\Services\MessageService;

use App\Action\HelperAction;
use App\Http\Resources\Chat\ChatResource;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class MessageService
{
    public function index()
    {
        // Get the authenticated user's ID
        $user_id = auth()->user()->id;

        // Retrieve distinct room IDs where the authenticated user is a participant
        $rooms = Message::where('user_id', $user_id)
            ->orWhere('receiver_id', $user_id)
            ->select('room_id')
            ->distinct()
            ->get();

        // Initialize an array to store user details with their latest message
        $userMessages = [];

        // Iterate over each room
        foreach ($rooms as $room) {
            // Retrieve the latest message in this room
            $latestMessage = Message::where('room_id', $room->room_id)
                ->orderBy('created_at', 'desc')
                ->first();

            // Determine the ID of the other user in the conversation
            $other_user_id = ($latestMessage->user_id == $user_id) ? $latestMessage->receiver_id : $latestMessage->user_id;

            // Get the receiver's information
            $receiver = User::find($other_user_id);

            // Add user details with their latest message and receiver information to the array
            $userMessages[] = [
                'receiver_info' => $receiver,
                'latest_message' => $latestMessage,
            ];
        }

        // Sort the userMessages array by the timestamp of the latest message in descending order
        usort($userMessages, function ($a, $b) {
            return $b['latest_message']->created_at <=> $a['latest_message']->created_at;
        });

        return HelperAction::serviceResponse(false, 'User list with messages', $userMessages);
    }



    public function store(Request $request)
    {
        $user_id = auth()->id();
        $receiver_id = $request->receiver_id;
        $text_message = $request->message;

        if ($request->hasFile('file_name')) {
            $data['file_type'] = $request->file_name->getClientOriginalExtension();
            $data['file_name'] = $this->save_message_files($request->file_name, $user_id, 'Message');
        }

        $room_id = $this->getRoomId($user_id, $receiver_id);

        $data['user_id'] = $user_id;
        $data['receiver_id'] = $receiver_id;
        $data['message'] = $text_message;
        $data['room_id'] = $room_id;

        $message = Message::query()->create($data);

        return HelperAction::serviceResponse(false, 'Message sent', $message->fresh('sender','receiver'));
    }

    public function save_message_files($file, $user_id, $directory)
    {
        $path = 'files/'.$user_id;
        if (!File::exists($path)) {
            mkdir($path, 0755, false);
        }

        $fileType = $file->getClientOriginalExtension();
        $imageName = rand().'.'.$fileType;
        $path_info = pathinfo($imageName, PATHINFO_EXTENSION);
        $directory = $path.'/';

        if (in_array($path_info, ['pdf', 'docx', 'zip', 'pptx'])) {
            $imageUrl = $directory.$imageName;
            $file->move($directory, $imageName);
        } elseif (in_array($path_info, ['png', 'jpeg', 'jpg'])) {
            $imageUrl = $directory.$imageName;
            Image::make($file)->save($imageUrl);
        } else {
            $imageUrl = "No Valid File";
        }

        return $imageUrl;
    }

    public function getRoomId($user_id, $receiver_id)
    {
        $check_room_one = Message::where('user_id', $user_id)->where('receiver_id', $receiver_id)->first();
        $check_room_two = Message::where('receiver_id', $user_id)->where('user_id', $receiver_id)->first();

        if (!$check_room_one && !$check_room_two) {
            $max_room_no = Message::orderBy('room_id', 'desc')->value('room_id');
            return $max_room_no ? ++$max_room_no : 1;
        }

        return $check_room_one ? $check_room_one->room_id : $check_room_two->room_id;
    }

    public function getMessagesByReceiverId($receiver_id)
    {
        // Get the authenticated user's ID
        $user_id = auth()->id();

        // Retrieve messages where the receiver is the specified ID and the sender is the authenticated user
        $messages = Message::query()->where('receiver_id', $receiver_id)
            ->where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return HelperAction::serviceResponse(false, 'Messages retrieved', $messages);
    }


}
