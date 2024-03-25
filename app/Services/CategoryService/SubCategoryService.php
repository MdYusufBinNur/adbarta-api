<?php

namespace App\Services\CategoryService;

use App\Action\HelperAction;
use App\Http\Resources\Categories\CategoryResource;
use App\Http\Resources\SubCategory\SubCategoryResource;
use App\Models\SubCategory;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class SubCategoryService
{
    public function index($data): array
    {
        if (auth()->user() && auth()->user()->role === 'super_admin') {
            $count = $data['per_page'] ?? 20;
            $query = SubCategory::query();
            if (isset($data['name'])) {
                $query->where('name', 'like', '%' . $data['name'] . '%');
            }
            if (isset($data['sort'])) {
                $query->orderBy('id', $data['sort']);
            } else {
                $query->latest();
            }
            $responseData = $query->with('category')->paginate($count);
            $paginationData = HelperAction::paginationMetaData($responseData);
            $finalDataset = [
                'sub_categories' => $responseData->items(),
                'pagination' => $paginationData,
            ];
            return HelperAction::serviceResponse(false, 'Category list', $finalDataset);
        }
        $responseData = SubCategory::with('category')->latest()->get();

        return HelperAction::serviceResponse(false, 'Category list', SubCategoryResource::collection($responseData));
    }

    public function store($data): array
    {
        try {
            DB::beginTransaction();
            if (array_key_exists('image', $data)) {
                $data['image'] = HelperAction::save_image($data['image'], 'Category');
            }
            $createCategory = SubCategory::query()->create($data);
            DB::commit();
            return HelperAction::serviceResponse(false, 'Category added', $createCategory->fresh('category'));
        } catch (Exception $e) {
            DB::rollBack();
            return HelperAction::serviceResponse(true, $e->getMessage(), null);
        }
    }

    /**
     */
    public function update($data, $id): array
    {
        try {
            DB::beginTransaction();
            $Category = SubCategory::query()->findOrFail($id);
            if (array_key_exists('image', $data)) {
                $data['image'] = HelperAction::save_image($data['image'], 'Category');

            }
            $update = $Category->updateOrFail($data);
            DB::commit();
            return HelperAction::serviceResponse(false, 'Sub Category updated', $Category->fresh('category'));
        } catch (Exception $e) {
            DB::rollBack();
            return HelperAction::serviceResponse(true, $e->getMessage(), null);
        } catch (\Throwable $e) {
        }
    }


    /**
     * @throws Throwable
     */
    public function destroy($id): array
    {
        $check = SubCategory::query()->findOrFail($id);
        $check->deleteOrFail();
        return HelperAction::serviceResponse(false, 'Category deleted', null);

    }

}
