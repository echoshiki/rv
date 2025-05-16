<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\RegionService;
use Illuminate\Http\JsonResponse;

class RegionController extends Controller
{
    protected $regionService;
    
    public function __construct(RegionService $regionService)
    {
        $this->regionService = $regionService;
    }

    public function provinces(): JsonResponse
    {
        return $this->successResponse($this->regionService->getProvinces());
    }

    public function cities(string $provinceCode): JsonResponse
    {
        return $this->successResponse($this->regionService->getCities($provinceCode));
    }

    public function districts(string $cityCode): JsonResponse
    {
        return $this->successResponse($this->regionService->getDistricts($cityCode));
    }

    public function name(string $code): JsonResponse
    {
        return $this->successResponse($this->regionService->getRegionNameByCode($code));
    }
}
