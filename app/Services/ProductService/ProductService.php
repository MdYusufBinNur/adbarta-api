<?php

namespace App\Services\ProductService;

use App\Action\HelperAction;
use App\Http\Resources\Categories\CategoryResource;
use App\Http\Resources\Product\ProductDetailsResource;
use App\Http\Resources\Product\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\UserWallet;
use App\Models\WalletHistory;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductService
{
    public function index($data): array
    {
        if (auth()->user() && auth()->user()->role === 'super_admin') {
            $count = $data['per_page'] ?? 20;
            $query = Product::query()->with('category', 'sub_category', 'user', 'image');

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
                'products' => ProductResource::collection($responseData->items()),
                'pagination' => $paginationData,
            ];
            return HelperAction::serviceResponse(false, 'Product list', $finalDataset);
        }
        $responseData = Product::with('sub_category', 'category', 'image')
            ->where('user_id', '=', auth()->id())
            ->latest()
            ->get();
        return HelperAction::serviceResponse(false, 'Product list', ProductResource::collection($responseData));
    }

    public function store($data): array
    {
        try {
            DB::beginTransaction();
            $checkWallet = UserWallet::query()->where('user_id', '=', auth()->id())->firstOrFail();


            $productData = collect($data)->except('image')->toArray();
            if ($checkWallet->available >= 2) {
                if ($data['product_type'] === 'premium') {
                    if ($checkWallet->available < 100) {
                        return HelperAction::serviceResponse(true, 'Insufficient wallet points for Top Ad', null);
                    }
                    $productData['points'] = 100;
                } else {
                    $productData['points'] = 2;
                }
            } else {
                return HelperAction::serviceResponse(true, 'Insufficient wallet points', null);

            }
            $productData['user_id'] = auth()->id();
            $createCategory = Product::query()->create($productData);
            if (array_key_exists('image', $data)) {
                foreach ($data['image'] as $item) {
                    ProductImage::query()->create([
                        'image' => $item,
                        'product_id' => $createCategory->id
                    ]);
                }
            }
            $this->walletHistory($createCategory->id, auth()->id());
            DB::commit();
            return HelperAction::serviceResponse(false, 'Product added', $createCategory->fresh());
        } catch (Exception $e) {
            DB::rollBack();
            Log::info('Product Store Service : ' . $e->getMessage());
            return HelperAction::serviceResponse(true, 'Something went wrong!', null);
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
                        'image' => $item,
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
            if (isset($data['category_id'])) {
                $query->where('category_id', '=', $data['category_id']);
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
            $respondedData = new ProductResource($details);
            return HelperAction::serviceResponse(false, 'Product Details', $respondedData);
        } catch (Throwable $exception) {
            return HelperAction::serviceResponse(true, $exception->getMessage(), null);
        }
    }

    public function walletHistory($productId, $userID)
    {
        $check = UserWallet::query()->where('user_id', '=', $userID)->firstOrFail();
        $product = Product::query()->findOrFail($productId);
        $availablePoints = $check->available - $product->points;
        $totalUsedPoint = $check->used + $product->points;
        try {
            $check->updateOrFail([
                'available' => $availablePoints,
                'used' => $totalUsedPoint,
            ]);
            WalletHistory::query()->create([
                'user_id' => $product->user_id,
                'user_wallet_id' => $check->id,
                'points' => $product->points,
                'points_type' => 'debit',
                'gateway' => 'system',
                'status' => 'approved',
                'trxID' => 'P-' . $productId,
            ]);
        } catch (Throwable $e) {
        }
    }

    public function searchProduct($data)
    {
        // Initialize the query builder for the Product model
        $query = Product::query();

        // Check if there is a filter for the category and join the categories table
        if (isset($data['category']) && !empty($data['category'])) {
            $query->whereHas('category', function ($q) use ($data) {
                $q->where('name', $data['category']);
            });
        }

        // Check if there is a filter for the subcategory and join the subcategories table
        if (isset($data['sub_category']) && !empty($data['sub_category'])) {
            $query->whereHas('sub_category', function ($q) use ($data) {
                $q->where('name', $data['sub_category']);
            });
        }

        // Apply other filters if they exist
        if (isset($data['title']) && !empty($data['title'])) {
            $query->where('title', 'like', '%' . $data['title'] . '%');
        }

        if (isset($data['color']) && !empty($data['color'])) {
            $query->where('color', $data['color']);
        }

        if (isset($data['condition']) && !empty($data['condition'])) {
            $query->where('condition', $data['condition']);
        }

        if (isset($data['brand']) && !empty($data['brand'])) {
            $query->where('brand', $data['brand']);
        }

        if (isset($data['price_min']) && !empty($data['price_min'])) {
            $query->where('price', '>=', $data['price_min']);
        }

        if (isset($data['price_max']) && !empty($data['price_max'])) {
            $query->where('price', '<=', $data['price_max']);
        }

        if (isset($data['status']) && !empty($data['status'])) {
            $query->where('status', $data['status']);
        }

        if (isset($data['location']) && !empty($data['location'])) {
            $query->where('location', 'like', '%' . $data['location'] . '%');
        }

        // Execute the query and get the results
        $products = $query->get();

        $products->transform(function ($product) {
            return [
                'id' => $product->id,
                'slug' => $product->slug,
                'title' => $product->title,
            ];
        });
        return HelperAction::serviceResponse(false, 'Search result', $products);
    }

    public function uploadImage(array $data)
    {
        if (array_key_exists('image', $data)) {
            $image = HelperAction::saveImage($data, 'Products');
            return HelperAction::serviceResponse(false,'Image uploaded', $image);
        }
        return HelperAction::errorResponse('Something went wrong! Please try again');
    }

}
