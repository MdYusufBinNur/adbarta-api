<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Action\HelperAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wallet\AddWalletPointFromAdminRequest;
use App\Http\Requests\Wallet\SaveTransactionStoreRequest;
use App\Services\WalletService\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    private WalletService $service;

    public function __construct(WalletService $service)
    {
        $this->service = $service;
    }

    public function wallet(Request $request): JsonResponse
    {
        $data = collect($request)->except(['_method', '/' . $request->path()])->toArray();
        $serviceData = $this->service->wallet($data);

        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }

    public function addWalletCredit(AddWalletPointFromAdminRequest $request, $userId): JsonResponse
    {
        $data = collect($request)->except(['_method', '/' . $request->path()])->toArray();
        $serviceData = $this->service->addWalletCredit($data, $userId);

        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }

    public function getWalletHistory(Request $request): JsonResponse
    {
        $data = collect($request)->except(['_method', '/' . $request->path()])->toArray();
        $serviceData = $this->service->getWalletHistory($data);

        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }

    public function changeStatus(Request $request, $historyId): JsonResponse
    {
        $data = collect($request)->except(['_method', '/' . $request->path()])->toArray();
        $serviceData = $this->service->changeStatus($data, $historyId);

        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }
    public function saveTransactionId(SaveTransactionStoreRequest $request): JsonResponse
    {
        $data = collect($request)->except(['_method', '/' . $request->path()])->toArray();
        $serviceData = $this->service->saveTransactionId($data);

        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }

}
