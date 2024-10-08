<?php

namespace App\Http\Controllers\Api\v1\Admin\Product;

use App\Action\HelperAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommonCategory\CategoryStoreRequest;
use App\Http\Requests\Product\StoreRequest;
use App\Services\ProductService\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    private ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $data = $request->toArray();
        $serviceData = $this->service->index($data);

        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $data = collect($request)->toArray();
        $serviceData = $this->service->store($data);

        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);

    }

    /**
     * Update the specified resource in storage.
     * @throws \Throwable
     */
    public function update(Request $request, string $id)
    {
        $data = collect($request)->except(['_method', '/' . $request->path()])->toArray();
        $serviceData = $this->service->update($data, $id);

        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $serviceData = $this->service->destroy($id);

        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }

    public function searchProduct(Request $request)
    {
        $data = collect($request)->toArray();
        $serviceData = $this->service->searchProduct($data);

        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }

    public function uploadImage(Request $request)
    {
        $validate = Validator::make($request->all(),
            [
                'image' => 'required|mimes:jpg,png',
            ]);
        if ($validate->fails())
            return HelperAction::errorResponse($validate->errors()->first());
        $data = collect($request)->toArray();
        $serviceData = $this->service->uploadImage($data);

        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }
}
