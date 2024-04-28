<?php

namespace App\Services\UserService;

use App\Action\HelperAction;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function index($data)
    {
        $count = $data['per_page'] ?? 10;
        $query = User::query()->with('district','sub_district','wallet')->where('role', '=', HelperAction::ROLE_SELLER)->latest();
        if (isset($data['text'])) {
            $query->where('full_name', 'like', '%' . $data['text'] . '%')
            ->orWhere('email', 'like', '%' . $data['text'] . '%');
        }
        $users = $query->paginate($count);
        $d['count'] = $users->count();
        $d['current_page'] = $users->currentPage();
        $d['next_page_url'] = $users->nextPageUrl();
        $d['last_page'] = $users->lastPage();
        $d['prev_page_url'] = $users->previousPageUrl();
        $d['per_page'] = $users->perPage();
        $d['total'] = $users->total();
        $d['total_page'] = ceil($users->total() / $users->perPage());
        $d['next_page'] = $users->currentPage() + 1 <= $users->lastPage() ? $users->currentPage() + 1 : $users->lastPage();
        $d['prev_page'] = $users->currentPage() - 1 < 0 ? $users->currentPage() : $users->currentPage() - 1;
        $finalDataset['users'] = UserResource::collection($users->items());
        $finalDataset['pagination'] = $d;
        return HelperAction::serviceResponse(false, 'User list', $finalDataset);
    }

    public function profile(): JsonResponse
    {
        $userData = new UserResource(User::query()
            ->with('wallet', )
            ->find(auth()->id()));
        return HelperAction::successResponse('Profile info', $userData);
    }

    /**
     * @throws \Throwable
     */
    public function update(array $data, string $id)
    {
        if ($id != 0) {
            $user = User::query()->findOrFail($id);
        } else {
            $user = auth()->user();
        }

        try {
            DB::beginTransaction();
            $password = null;
            if (isset($data['email']) && $user->email !== $data['email']) {
                $checkEmail  = User::query()->where('email','=', $data['email'])->exists();
                if ($checkEmail) {
                    return HelperAction::serviceResponse(true,'Email already taken', null);
                }
            }
            if (isset($data['password'])) {
                $password = bcrypt($data['password']);
                $data['password'] = $password;
            }
            if (isset($data['image'])) {
                $data['photo'] = HelperAction::saveVendorImage($data['image'], 'Vendors');
            }
            if (isset($data['nid_one'])) {
                $data['nid_one'] = HelperAction::saveVendorImage($data['nid_one'], 'Vendors');
            }
            if (isset($data['nid_two'])) {
                $data['nid_two'] = HelperAction::saveVendorImage($data['nid_two'], 'Vendors');
            }


            $finalDataset = collect($data)->except('image')->toArray();
            $create = $user->updateOrFail($finalDataset);
            DB::commit();
            return HelperAction::serviceResponse(false, 'User has been updated', new UserResource($user->fresh('district','sub_district', 'wallet')));
        } catch (\Exception $exception) {
            DB::rollBack();
            return HelperAction::serviceResponse(true, $exception->getMessage(), null);

        }

    }

    public function destroy(string $id)
    {

    }

    public function store(array $data): array
    {
        try {
            DB::beginTransaction();
            $password = null;
            if ($data['password']) {
                $password = bcrypt($data['password']);
            }
            if (isset($data['image'])) {
                $data['photo'] = HelperAction::saveVendorImage($data['image'], 'Vendors');
            }
            $data['password'] = $password;

            $finalDataset = collect($data)->except('image')->toArray();
            $create = User::query()->create($finalDataset);
            DB::commit();
            return HelperAction::serviceResponse(false, 'User has been created', $create->fresh());
        } catch (\Exception $exception) {
            DB::rollBack();
            return HelperAction::serviceResponse(true, $exception->getMessage(), null);

        }


    }

    public function addPoint(array $data, string $id)
    {
        $user = User::query()->findOrFail($id);
        $wallet = UserWallet::query()->where('user_id','=', $id)->firstOrFail();
        $available = floatval($wallet->available) + floatval($data['points']);
        $wallet->updateOrFail([
            'available' => $available
        ]);
        return HelperAction::serviceResponse(false,'Wallet point Added', new UserResource($user->fresh('wallet','district', 'sub_district')));
    }
}
