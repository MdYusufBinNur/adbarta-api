<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Action\HelperAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wallet\AddWalletPointFromAdminRequest;
use App\Models\UserWallet;
use App\Services\UserService\UserService;
use App\Services\WalletService\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    private WalletService $service;

    public function __construct(WalletService $service)
    {
        $this->service = $service;
    }

    public function wallet(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = collect($request)->except(['_method', '/' . $request->path()])->toArray();
        $serviceData = $this->service->wallet($data);

        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }

    public function addWalletCredit(AddWalletPointFromAdminRequest $request, $userId): \Illuminate\Http\JsonResponse
    {
        $data = collect($request)->except(['_method', '/' . $request->path()])->toArray();
        $serviceData = $this->service->addWalletCredit($data, $userId);

        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }

}
