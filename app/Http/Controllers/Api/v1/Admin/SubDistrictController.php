<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Action\HelperAction;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\District\DistrictResource;
use App\Models\District;
use App\Models\SubDistrict;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubDistrictController extends Controller
{
    public function activeDistrict(): JsonResponse
    {
        $locations = District::query()
            ->where('active', '=', 1)
            ->with('sub_districts')
            ->get();
        return HelperAction::successResponse('location', $locations);
    }

    public function activeSubDistrict(): JsonResponse
    {
        $locations =  SubDistrict::query()->with('district')->get();
        return HelperAction::successResponse('location', $locations);
    }
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),
            [
                'district_id' => 'required|exists:districts,id',
                'name' => 'required|unique:sub_districts,name'
            ]);
        if ($validator->fails())
            return HelperAction::validationResponse($validator->errors()->first());
        try {
            SubDistrict::query()->insert($request->all());
            return HelperAction::successResponse('List', SubDistrict::query()->latest()->first());
        } catch (Exception $exception) {
            return HelperAction::errorResponse($exception->getMessage());
        }

    }
}
