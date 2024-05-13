<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Action\HelperAction;
use App\Http\Controllers\Controller;
use App\Services\AdminService\AdminService;
use Illuminate\Http\Request;

class AdminHomeController extends Controller
{
    protected AdminService $service;

    public function __construct(AdminService $service)
    {
        $this->service = $service;
    }

    public function homeData(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->toArray();
        $serviceData = $this->service->homeData($data);

        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }

}
