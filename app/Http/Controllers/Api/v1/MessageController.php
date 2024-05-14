<?php

namespace App\Http\Controllers\Api\v1;

use App\Action\HelperAction;
use App\Http\Controllers\Controller;
use App\Services\MessageService\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    private MessageService $service;

    public function __construct(MessageService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $serviceData = $this->service->index();
        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }

    public function store(Request $request): JsonResponse
    {
        $serviceData = $this->service->store($request);
        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }

    public function getMessagesByReceiverId($receiverID): JsonResponse
    {
        $serviceData = $this->service->getMessagesByReceiverId($receiverID);
        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }
}
