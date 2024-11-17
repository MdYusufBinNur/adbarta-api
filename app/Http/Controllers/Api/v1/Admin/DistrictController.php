<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Services\AdminService\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    protected AdminService $services;

    public function __construct(AdminService $services)
    {
        $this->services = $services;
    }

    public function index(): JsonResponse
    {
        return $this->services->districtIndex();
    }

    public function store(Request $request): JsonResponse
    {
        return $this->services->districtStore($request);
    }
    public function update(Request $request, District $district): JsonResponse
    {
        return $this->services->districtUpdate($request, $district);
    }

    public function destroy(District $district): JsonResponse
    {
        return $this->services->districtDestroy($district);
    }

    public function getSubs($id): JsonResponse
    {
        return $this->services->districtGetSubs($id);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        return $this->services->districtUpdateStatus($request);
    }
}
