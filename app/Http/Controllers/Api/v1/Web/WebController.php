<?php

namespace App\Http\Controllers\Api\v1\Web;

use App\Action\HelperAction;
use App\Http\Controllers\Controller;
use App\Services\CategoryService\CategoryService;
use App\Services\ProductService\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebController extends Controller
{
    private CategoryService $service;
    private ProductService $productService;

    public function __construct(CategoryService $service, ProductService $productService)
    {
        $this->service = $service;
        $this->productService = $productService;
    }

    public function categories(Request $request): JsonResponse
    {
        $data = $request->toArray();
        $serviceData = $this->service->index($data);
        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }
    public function getSubCategories($id): JsonResponse
    {
        $serviceData = $this->service->getSubCategories($id);
        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }
    public function getDivisions(): JsonResponse
    {
        $serviceData = $this->service->getDivisions();
        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }
    public function getDistricts($id): JsonResponse
    {
        $serviceData = $this->service->getDistricts($id);
        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }
    public function getDistrict(): JsonResponse
    {
        $serviceData = $this->service->getDistrict();
        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }
    public function getSubDistricts($id): JsonResponse
    {
        $serviceData = $this->service->getSubDistricts($id);
        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }

    public function allAds(Request $request): JsonResponse
    {
        $data = $request->toArray();
        $serviceData = $this->productService->getAllProducts($data);
        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }

    public function adDetails($slug): JsonResponse
    {
        $serviceData = $this->productService->details($slug);
        if ($serviceData['error']) {
            return HelperAction::errorResponse($serviceData['message']);
        }
        return HelperAction::jsonResponse($serviceData);
    }
}
