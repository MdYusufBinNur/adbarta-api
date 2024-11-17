<?php

namespace App\Services\AdminService;

use App\Action\HelperAction;
use App\Helper\Helper;
use App\Http\Resources\Api\District\DistrictResource;
use App\Models\Category;
use App\Models\District;
use App\Models\Product;
use App\Models\SubDistrict;
use App\Models\User;
use App\Models\WalletHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class AdminService
{
    public function homeData($data)
    {
        $categories = Category::query()->count();
        $users = User::query()->where('role', '=', 'seller')->count();
        $ads = Product::query()->count();
        $date = $data['date'] ?? Carbon::today()->toDateString();
        $transactionCountsForDate = WalletHistory::getDailyTransactionAmounts($date);
        $saleCountLast7Days = Product::getLast7DaysSaleCount();

        return HelperAction::serviceResponse(false, 'Historical data', compact('categories', 'users', 'ads', 'transactionCountsForDate', 'saleCountLast7Days'));

    }

    /**
     * District Service
     */
    public function districtIndex(): JsonResponse
    {
        $districts = District::query()->get();
        return HelperAction::successResponse('District', $districts);
    }

    public function districtUpdate(Request $request, District $district): JsonResponse
    {
        if (!$district)
            return HelperAction::errorResponse('No District Found');

        $update = $district->update($request->all());
        if ($update)
            return HelperAction::successResponse('District', $district->refresh());

        return HelperAction::errorResponse('Something went wrong!');
    }
    public function districtStore(Request $request): JsonResponse
    {
        $update = District::query()->create($request->all());
        if ($update)
            return HelperAction::successResponse('District', $update->refresh());

        return HelperAction::errorResponse('Something went wrong!');
    }

    public function districtDestroy(District $district): JsonResponse
    {
        if (!$district)
            return HelperAction::errorResponse('No District Found');

        $update = $district->delete();
        if ($update)
            return HelperAction::successResponse('District Deleted', null);
        return HelperAction::errorResponse('Something went wrong!');
    }

    public function districtGetSubs($id): JsonResponse
    {
        $subs = SubDistrict::query()->where('district_id', '=', $id)->get();
        return HelperAction::successResponse('Sub Dist', $subs);
    }

    public function districtUpdateStatus(Request $request): JsonResponse
    {
        $district = District::query()->find($request->id);
        $data = $request->active != 'Enable' ? 0 : 1;
        if ($district && $district->update(['active' => $data]))
            return HelperAction::successResponse('List', $district);
        return HelperAction::successResponse('Sub Dist', $subs);

    }

}
