<?php

namespace App\Services\AdminService;

use App\Action\HelperAction;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\WalletHistory;
use Illuminate\Support\Carbon;

class AdminService
{
    public function homeData($data)
    {
        $categories = Category::query()->count();
        $users = User::query()->where('role','=','seller')->count();
        $ads = Product::query()->count();
        $date = $data['date'] ?? Carbon::today()->toDateString();
        $transactionCountsForDate = WalletHistory::getDailyTransactionAmounts($date);
        $saleCountLast7Days = Product::getLast7DaysSaleCount();

        return HelperAction::serviceResponse(false,'Historical data', compact('categories','users','ads','transactionCountsForDate','saleCountLast7Days'));

    }
}
