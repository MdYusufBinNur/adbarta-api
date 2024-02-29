<?php

namespace App\Http\Controllers\Api\v1\Web;

use App\Action\HelperAction;
use App\Http\Controllers\Controller;
use App\Services\CategoryService\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebController extends Controller
{
    private CategoryService $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function categories(Request $request): JsonResponse
    {
        $data = $request->toArray();
        $serviceData = $this->service->index($data);
        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }
}
