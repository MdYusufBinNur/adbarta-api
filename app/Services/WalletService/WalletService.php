<?php

namespace App\Services\WalletService;

use App\Action\HelperAction;
use App\Http\Resources\User\UserWalletResource;
use App\Models\UserWallet;
use App\Models\WalletHistory;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function wallet(array $data): array
    {
        $wallet = UserWallet::query()->with('history')->where('user_id', '=', auth()->id())->firstOrFail();
        return HelperAction::serviceResponse(false, 'Wallet history', new UserWalletResource($wallet));
    }
    public function addWalletCredit($data, $userId): array
    {
        try {
            DB::beginTransaction();
            $addWallet = UserWallet::query()->where('user_id','=', $userId)->firstOrFail();
            $available = $addWallet->available + floatval($data['point']);
            $addWallet->updateOrFail([
                'available' => $available
            ]);
            WalletHistory::query()->create([
                'user_id' => $userId,
                'user_wallet_id' => $addWallet->id,
                'name' => 'Admin',
                'trxID' => 'ADMIN-'.now(),
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
            return HelperAction::serviceResponse(true,$e->getMessage(), null);
        }
    }

    public function changeStatus($data, $walletID): array
    {
        try {
            DB::beginTransaction();
            $addWallet = UserWallet::query()->findOrFail($walletID);
            $addWallet->updateOrFail([
                'status' => $data['status']
            ]);
            DB::commit();
            return HelperAction::serviceResponse(false, 'Point Added', null);
        } catch (\Throwable $e) {
            DB::rollBack();
            return HelperAction::serviceResponse(true,$e->getMessage(), null);
        }
    }
}
