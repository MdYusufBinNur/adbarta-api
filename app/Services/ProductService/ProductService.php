<?php

namespace App\Services\ProductService;

use App\Action\HelperAction;
use App\Http\Resources\Categories\CategoryResource;
use App\Http\Resources\Product\ProductDetailsResource;
use App\Http\Resources\Product\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProductService
{
    public function index($data): array
    {
        if (auth()->user() && auth()->user()->role === 'super_admin') {
            $count = $data['per_page'] ?? 20;
            $query = Product::query()->with('category', 'sub_category', 'user');

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
                'products' => $responseData->items(),
                'pagination' => $paginationData,
            ];
            return HelperAction::serviceResponse(false, 'Product list', $finalDataset);
        }
        $responseData = Product::with('sub_category', 'category', 'image')->latest()->get();
        return HelperAction::serviceResponse(false, 'Product list', ProductResource::collection($responseData));
    }

    public function store($data): array
    {
        try {
            DB::beginTransaction();
            $productData = collect($data)->except('image')->toArray();
            $createCategory = Product::query()->create($productData);
            if (array_key_exists('image', $data)) {
                foreach ($data['image'] as $item) {
                    ProductImage::query()->create([
                        'image' => HelperAction::saveImage($item, 'Products'),
                        'product_id' => $createCategory->id
                    ]);
                }
            }
            DB::commit();
            return HelperAction::serviceResponse(false, 'Product added', $createCategory->fresh());
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
            $check = Product::query()->findOrFail($id);
            $productData = collect($data)->except('image')->toArray();
            $update = $check->updateOrFail($data);
            if (array_key_exists('image', $data)) {
                foreach ($data['image'] as $item) {
                    ProductImage::query()->create([
                        'image' => HelperAction::saveImage($item, 'Products'),
                        'product_id' => $id
                    ]);
                }
            }

            DB::commit();
            return HelperAction::serviceResponse(false, 'Product updated', $check->fresh('image'));
        } catch (Exception $e) {
            DB::rollBack();
            return HelperAction::serviceResponse(true, $e->getMessage(), null);
        }
    }

    public function destroy($id): array
    {
        try {
            $check = Product::query()->findOrFail($id);
            $check->deleteOrFail();
            return HelperAction::serviceResponse(false, 'Product deleted', null);
        } catch (Throwable $exception) {
            return HelperAction::serviceResponse(true, $exception->getMessage(), null);
        }
    }

    public function getAllProducts($data): array
    {
        try {
            $count = $data['per_page'] ?? 20;
            $query = Product::query()
                ->with('image')
                ->where('status', '=', HelperAction::PRODUCT_STATUS_APPROVED)
                ->where('status', '!=', HelperAction::PRODUCT_STATUS_SOLD);
            if (isset($data['text'])) {
                $query->where('name', 'like', '%' . $data['name'] . '%')
                    ->orWhereHas('category', function ($q) use ($data) {
                        $q->where('name', 'like', '%' . $data['name'] . '%');
                    })
                    ->orWhereHas('sub_category', function ($q) use ($data) {
                        $q->where('name', 'like', '%' . $data['name'] . '%');
                    });
            }
            if (isset($data['sub_category_id'])) {
                $query->where('sub_category_id', '=', $data['sub_category_id']);
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
                'products' => ProductResource::collection($responseData),
                'pagination' => $paginationData,
            ];
            return HelperAction::serviceResponse(false, 'Product list', $finalDataset);
        } catch (Throwable $exception) {
            return HelperAction::serviceResponse(true, $exception->getMessage(), null);
        }
    }

    public function details($slug)
    {
        try {
            $details = Product::query()->where('slug', '=', $slug)->firstOrFail();
            $respondedData = new ProductDetailsResource($details);
        } catch (Throwable $exception) {
            return HelperAction::serviceResponse(true, $exception->getMessage(), null);
        }
    }
}
