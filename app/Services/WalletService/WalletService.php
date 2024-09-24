<?php

namespace App\Services\WalletService;

use App\Action\HelperAction;
use App\Http\Resources\User\UserWalletHistoryResource;
use App\Http\Resources\User\UserWalletResource;
use App\Models\UserWallet;
use App\Models\WalletHistory;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function wallet(array $data): array
    {
        $wallet = UserWallet::query()->with('history.user','user')->where('user_id', '=', auth()->id())->firstOrFail();
        return HelperAction::serviceResponse(false, 'Wallet history', new UserWalletResource($wallet));
    }

    public function addWalletCredit($data, $userId): array
    {
        try {
            DB::beginTransaction();
            $addWallet = UserWallet::query()->where('user_id', '=', $userId)->firstOrFail();
            $available = $addWallet->available + floatval($data['point']);
            $addWallet->updateOrFail([
                'available' => $available
            ]);
            WalletHistory::query()->create([
                'user_id' => $userId,
                'user_wallet_id' => $addWallet->id,
                'name' => 'Admin',
                'trxID' => 'ADMIN-' . now(),
                'points' => $data['point'],
                'phone' => '-',
                'gateway' => 'system',
                'status' => 'approved',
                'points_type' => 'credit',
            ]);
            DB::commit();
            return HelperAction::serviceResponse(false, 'Point Added', null);
        } catch (\Throwable $e) {
            DB::rollBack();
            return HelperAction::serviceResponse(true, $e->getMessage(), null);
        }
    }

    public function changeStatus($data, $walletID): array
    {
        try {
            DB::beginTransaction();
            $addWallet = WalletHistory::query()->where('points_type','=','credit')->findOrFail($walletID);
            $checkWallet = UserWallet::query()->findOrFail($addWallet->user_wallet_id);
            if ($data['status'] === 'Approve') {
                $data['status'] = 'approved';
            } else {
                $data['status'] = 'not_approved';
            }
            $available = $checkWallet->available + floatval($addWallet->points);
            $addWallet->updateOrFail([
                'status' => $data['status']
            ]);
            $checkWallet->updateOrFail([
                'available' => $available
            ]);
            DB::commit();
            return HelperAction::serviceResponse(false, 'Point Added', new UserWalletHistoryResource($addWallet->fresh('user')));
        } catch (\Throwable $e) {
            DB::rollBack();
            return HelperAction::serviceResponse(true, $e->getMessage(), null);
        }
    }

    public function getWalletHistory($data): array
    {
        $wallet = WalletHistory::query()->with('user')->where('status','!=', 'cancelled')->latest()->get();
        return HelperAction::serviceResponse(false, 'Wallet history', UserWalletHistoryResource::collection($wallet));

    }

    public function saveTransactionId(array $data): array
    {
        try {
            DB::beginTransaction();
            $addWallet = UserWallet::query()->where('user_id', '=', auth()->id())->firstOrFail();

            $create = WalletHistory::query()->create([
                'user_id' => auth()->id(),
                'user_wallet_id' => $addWallet->id,
                'name' => auth()?->user()?->full_name,
                'trxID' => $data['trxID'],
                'points' => 0,
                'phone' => auth()?->user()?->phone,
                'gateway' => $data['gateway'],
                'status' => 'pending',
                'points_type' => 'credit',
            ]);
            DB::commit();
            return HelperAction::serviceResponse(false, 'trxID submitted successfully',  new UserWalletResource($create->fresh('user')));
        } catch (\Throwable $e) {
            DB::rollBack();
            return HelperAction::serviceResponse(true, $e->getMessage(), null);
        }
    }
}
