<?php

namespace App\Services\CategoryService;

use App\Action\HelperAction;
use App\Http\Resources\Categories\CategoryResource;
use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class CategoryService
{
    public function index($data): array
    {
        if (auth()->user() && auth()->user()->role === 'super_admin') {
            $count = $data['per_page'] ?? 20;
            $query = Category::query();

            if (isset($data['name'])) {
                $query->where('name', 'like', '%' . $data['name'] . '%');
            }
            if (isset($data['sort'])) {
                $query->orderBy('id', $data['sort']);
            } else {
                $query->latest();
            }
            $responseData = $query->paginate($count);
            $paginationData = [
                'count' => $responseData->count(),
                'current_page' => $responseData->currentPage(),
                'next_page_url' => $responseData->nextPageUrl(),
                'last_page' => $responseData->lastPage(),
                'prev_page_url' => $responseData->previousPageUrl(),
                'per_page' => $responseData->perPage(),
                'total' => $responseData->total(),
                'total_page' => ceil($responseData->total() / $responseData->perPage()),
                'next_page' => $responseData->currentPage() + 1 <= $responseData->lastPage() ? $responseData->currentPage() + 1 : $responseData->lastPage(),
                'prev_page' => $responseData->currentPage() - 1 < 0 ? $responseData->currentPage() : $responseData->currentPage() - 1,
            ];
            $finalDataset = [
                'categories' => $responseData->items(),
                'pagination' => $paginationData,
            ];
            return HelperAction::serviceResponse(false, 'Category list', $finalDataset);
        }

        $responseData = Category::with('sub_category')->latest()->get();
        return HelperAction::serviceResponse(false, 'Category list', CategoryResource::collection($responseData));
    }

    public function store($data): array
    {
        try {
            DB::beginTransaction();
            if (array_key_exists('image', $data)) {
                $data['image'] = HelperAction::save_image($data['image'], 'Category');
            }
            $createCategory = Category::query()->create($data);
            DB::commit();
            return HelperAction::serviceResponse(false, 'Category added', $createCategory->fresh());
        } catch (Exception $e) {
            DB::rollBack();
            return HelperAction::serviceResponse(true, $e->getMessage(), null);
        }
    }

    /**
     * @throws Throwable
     */
    public function update($data, $id): array
    {
        try {
            DB::beginTransaction();
            $Category = Category::query()->findOrFail($id);
            if (array_key_exists('image', $data)) {
                $data['image'] = HelperAction::save_image($data['image'], 'Category');

            }
            $update = $Category->updateOrFail($data);
            DB::commit();
            return HelperAction::serviceResponse(false, 'Category updated', $Category->fresh());
        } catch (Exception $e) {
            DB::rollBack();
            return HelperAction::serviceResponse(true, $e->getMessage(), null);
        }
    }

    public function destroy($id): array
    {
        try {
            $check = Category::query()->findOrFail($id);
            $check->deleteOrFail();
            return HelperAction::serviceResponse(false, 'Category deleted', null);
        } catch (Throwable $exception) {
            return HelperAction::serviceResponse(true, $exception->getMessage(), null);
        }
    }

}
