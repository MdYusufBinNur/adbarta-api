<?php

namespace App\Services\MessageService;

use App\Action\HelperAction;
use App\Events\Chat\MessageSentEvent;
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

        // Retrieve distinct users whom the authenticated user has communicated with
        $users = Message::query()
            ->where('user_id', $user_id)
            ->orWhere('receiver_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('room_id')
            ->map(function ($messages) use ($user_id) {
                $message = $messages->first();
                if ($message->user_id == $user_id) {
                    $message->user = $message->receiver;
                } else {
                    $message->user = $message->sender;
                }
                return $message;
            })
            ->values();


        return HelperAction::serviceResponse(false, 'User list with messages', ChatResource::collection($users));
        // Initialize an array to store user details with their latest message
        $userMessages = [];

        // Iterate over each user
        foreach ($users as $user) {
            // Determine the ID of the other user in the conversation
            $other_user_id = ($user->user_id == $user_id) ? $user->receiver_id : $user->user_id;

            // Retrieve the latest message exchanged between the authenticated user and this user
            $latestMessage = Message::where(function ($query) use ($user_id, $other_user_id) {
                $query->where('user_id', $user_id)
                    ->where('receiver_id', $other_user_id);
            })
                ->orWhere(function ($query) use ($user_id, $other_user_id) {
                    $query->where('receiver_id', $user_id)
                        ->where('user_id', $other_user_id);
                })
                ->orderBy('created_at', 'desc')
                ->first();

            // Add user details with their latest message and receiver information to the array
            $userMessages[] = [

                'receiver_info' => $latestMessage->receiver,
                'latest_message' => $latestMessage,
            ];
        }

        // Sort the userMessages array by the timestamp of the latest message in descending order
        usort($userMessages, function ($a, $b) {
            return $b['latest_message']->created_at <=> $a['latest_message']->created_at;
        });

        return HelperAction::serviceResponse(false, 'User list with messages', ChatResource::collection($userMessages));
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

        $message = Message::create($data);
//        broadcast(new MessageSentEvent($message));
        return HelperAction::serviceResponse(false, 'Message sent', $message->fresh('sender', 'receiver'));
    }

    public function save_message_files($file, $user_id, $directory)
    {
        $path = 'files/' . $user_id;
        if (!File::exists($path)) {
            mkdir($path, 0755, false);
        }

        $fileType = $file->getClientOriginalExtension();
        $imageName = rand() . '.' . $fileType;
        $path_info = pathinfo($imageName, PATHINFO_EXTENSION);
        $directory = $path . '/';

        if (in_array($path_info, ['pdf', 'docx', 'zip', 'pptx'])) {
            $imageUrl = $directory . $imageName;
            $file->move($directory, $imageName);
        } elseif (in_array($path_info, ['png', 'jpeg', 'jpg'])) {
            $imageUrl = $directory . $imageName;
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

    public function getMessagesByRoomId($room_id)
    {
        // Retrieve messages where the receiver is the specified ID and the sender is the authenticated user
        $messages = Message::query()->where('room_id', $room_id)
            ->get();

        return HelperAction::serviceResponse(false, 'Messages retrieved', $messages);
    }

    public function checkExistingChat($userId): array
    {
        $authUserId = auth()->id();
        $userInfo = User::query()->select('id','full_name','photo')->findOrFail($userId);
        $check = Message::query()
            ->where(function ($query) use ($authUserId, $userId) {
                $query->where('user_id', '=', $authUserId)
                    ->where('receiver_id', '=', $userId);
            })
            ->orWhere(function ($query) use ($authUserId, $userId) {
                $query->where('user_id', '=', $userId)
                    ->where('receiver_id', '=', $authUserId);
            });
        if ($check->exists()) {
            $checkData = $check->first();
            $userInfo->message = $checkData;
            $userInfo->room_id = $checkData->room_id;
            $data['receiver'] = $userInfo;
        } else {
            $data['receiver'] = $userInfo;
        }

        return HelperAction::serviceResponse(false, 'Messages retrieved', $data);
    }


}
