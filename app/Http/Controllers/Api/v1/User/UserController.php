<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Action\HelperAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\AdminUserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Services\UserService\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function profile(): JsonResponse
    {
        return $this->service->profile();
    }


    public function index(Request $request): JsonResponse
    {
        $data = $request->toArray();
        $serviceData = $this->service->index($data);

        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminUserStoreRequest $request)
    {
        $data = collect($request)->toArray();
        $serviceData = $this->service->store($data);

        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);

    }

    /**
     * Update the specified resource in storage.
     * @throws \Throwable
     */
    public function update(UserUpdateRequest $request, string $id)
    {
        $data = collect($request)->except(['_method', '/' . $request->path()])->toArray();
        $serviceData = $this->service->update($data, $id);

        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }
    /**
     * Update the specified resource in storage.
     * @throws \Throwable
     */
    public function addPoint(Request $request, string $id)
    {
        $data = collect($request)->except(['_method', '/' . $request->path()])->toArray();
        $serviceData = $this->service->addPoint($data, $id);

        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $serviceData = $this->service->destroy($id);

        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }

    }

    /**
     * @throws \Throwable
     */
    public function profileUpdate(UserUpdateRequest $request): JsonResponse
    {
        $data = collect($request)->except(['_method', '/' . $request->path()])->toArray();
        $serviceData = $this->service->update($data, 0);

        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }

}
