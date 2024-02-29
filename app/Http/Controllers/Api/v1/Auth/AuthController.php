<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CheckResetPasswordCodeRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ResetRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Services\AuthService\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthService $service;
    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->service->login($request);
    }

    public function register(UserStoreRequest $request): JsonResponse
    {
        return $this->service->register($request);
    }

    /**
     * Reset User Password
     */
    public function sendResetPasswordCodeToMail(ResetPasswordRequest $request): JsonResponse
    {
        return $this->service->sendResetPasswordCodeToMail($request);
    }

    public function checkResetCodeValidity(CheckResetPasswordCodeRequest $request): JsonResponse
    {
        return $this->service->checkResetPasswordCodeValidity($request);
    }

    public function resetPassword(ResetRequest $request): JsonResponse
    {
        return $this->service->resetPassword($request);
    }

    public function checkVerificationCodeValidity(Request $request): JsonResponse
    {
        return $this->service->checkVerificationCodeValidity($request);
    }
}
