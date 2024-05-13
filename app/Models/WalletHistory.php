<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class WalletHistory extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(UserWallet::class, 'user_wallet_id');
    }

    public static function getDailyTransactionAmounts($date = null)
    {
        $query = self::query();

        if ($date) {
            $query->whereDate('created_at', $date);
        } else {
            $date = Carbon::today()->toDateString();
            $query->whereDate('created_at', $date);
        }

        $transactions = $query->get();

        $dailyTransactionAmounts = [];

        foreach ($transactions as $transaction) {
            $date = Carbon::createFromFormat('Y-m-d H:i:s', $transaction->created_at)->format('Y-m-d');

            if (!isset($dailyTransactionAmounts[$date])) {
                $dailyTransactionAmounts[$date] = $transaction->points;
            } else {
                $dailyTransactionAmounts[$date] += $transaction->points;
            }
        }

        return $dailyTransactionAmounts;
    }
}
