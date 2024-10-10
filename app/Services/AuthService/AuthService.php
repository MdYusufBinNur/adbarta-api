<?php

namespace App\Services\AuthService;

use App\Action\HelperAction;
use App\Http\Resources\User\UserResource;
use App\Jobs\Auth\ResendVerificationCodeJob;
use App\Jobs\Auth\SendResetPasswordCodeJob;
use App\Models\PasswordResetToken;
use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

class AuthService
{
    public function login(Request $request): JsonResponse
    {
        try {
            $user = User::query()->where('email', '=', $request->email)->firstOrFail();
            $user->updateOrFail(['last_activity' => \date('Y-m-d H:i:s', strtotime(now()))]);
            if (!Hash::check($request->password, $user->password)) {
                return HelperAction::validationResponse('The provided password is incorrect.');
            }
            $token = $user->createToken(HelperAction::APP_NME)->plainTextToken;
            $responseData['is_verified'] = $user->email_verified_at ? 1 : 0;
            $responseData['user'] = new UserResource($user);
            $responseData['token'] = $token;
            return HelperAction::successResponse('Login successfully', $responseData);
        } catch (\Throwable $e) {
            return HelperAction::validationResponse($e->getMessage());
        }
    }


    public function register(Request $request): JsonResponse
    {
        $data['full_name'] = $request->name;
        $data['email'] = $request->email;
        $data['password'] = bcrypt($request->password);
        $data['role'] = 'seller';
        $data['remember_token'] = rand(1111, 9999);
        $data['status'] = 'approved';
        try {
            DB::beginTransaction();
            $user = User::query()->create($data);
            $userData['token'] = $user->createToken('authToken')->plainTextToken;
            $userData['user'] = new UserResource(User::query()->find($user->id));
            dispatch(new ResendVerificationCodeJob($user->id));
            UserWallet::query()->create([
                'user_id' => $user->id,
                'available' => 0
            ]);
            DB::commit();
            return HelperAction::successResponse('Verification code has been send', $userData);
        } catch (\Exception $exception) {
            DB::rollBack();
            return HelperAction::errorResponse($exception->getMessage());
        }
    }

    public function checkVerificationCodeValidity(Request $request): JsonResponse
    {
        if (!$request->code)
            return HelperAction::errorResponse('Code is required');
        $user = User::query()->findOrFail(auth()->id());
        if ($user->email_verified_at)
            return HelperAction::errorResponse('Account already verified');
        $code = auth()->user()->getRememberToken();
        if ($code != $request->code) {
            return HelperAction::errorResponse('Invalid Code');
        }
        try {
            $user->updateOrFail([
                'remember_token' => null,
                'email_verified_at' => \date('Y-m-d H:i:s', strtotime(now()))
            ]);
            return HelperAction::successResponse('Verified', null);
        } catch (Throwable $e) {
            return HelperAction::errorResponse($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * Reset Password Request
     */
    public function sendResetPasswordCodeToMail(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $user = User::query()->where('email', '=', $request->email)->first();
            PasswordResetToken::query()->firstOrCreate(
                [
                    'email' => $request->email
                ],
                [
                    'email' => $request->email,
                    'token' => rand(1111, 9999)
                ]);
            dispatch(new SendResetPasswordCodeJob($user->id));
            DB::commit();
            return HelperAction::successResponse('Reset password code sent to your email', null);
        } catch (\Exception $e) {
            DB::rollBack();
            return HelperAction::errorResponse($e->getMessage());
        }
    }

    public function checkResetPasswordCodeValidity(Request $request): JsonResponse
    {
        $email = $request->email;
        $code = $request->code;

        $tableExistsEmail =
        PasswordResetToken::query()
            ->where('email', '=', $email)
            ->first();
        if (!$tableExistsEmail)
            return HelperAction::errorResponse('Invalid Resource');
        $tableExistsEmailCode = PasswordResetToken::query()
            ->where('email', '=', $email)
            ->where('token', '=', $code)
            ->first();
        if (!$tableExistsEmailCode)
            return HelperAction::errorResponse('Invalid Code');
        try {
            DB::beginTransaction();
            PasswordResetToken::query()
                ->where('email', '=', $email)
                ->where('token', '=', $code)
                ->delete();
            $user = User::query()->where('email', '=', $email)->firstOrFail();
            $rand = Str::uuid()->toString();
            $user->updateOrFail([
                'remember_token' => $rand
            ]);
            $data['secret'] = $rand;
            DB::commit();
            return HelperAction::successResponse('Code Verified', $data);
        } catch (Throwable $e) {
            DB::rollBack();
            return HelperAction::errorResponse($e->getMessage());
        }
    }

    public function resetPassword(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $user = User::query()
                ->whereNotNull('remember_token')
                ->where('email', '=', $request->email)
                ->where('remember_token', '=', $request->secret)
                ->first();
            if (!$user)
                return HelperAction::errorResponse('Invalid Resource');
            $user->updateOrFail([
                'password' => bcrypt($request->password),
                'remember_token' => null,
            ]);
            DB::commit();
            return HelperAction::successResponse('Password updated', null);
        } catch (\Throwable $e) {
            DB::rollBack();
            return HelperAction::errorResponse($e->getMessage());
        }
    }

    public function logout(): JsonResponse
    {
        \auth()->user()->currentAccessToken()->delete();
        return HelperAction::successResponse('Successfully Logout', null);
    }


}
