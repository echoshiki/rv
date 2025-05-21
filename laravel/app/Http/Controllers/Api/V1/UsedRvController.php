<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\UsedRvService;
use Illuminate\Http\Request;
use App\Http\Resources\UsedRvResourceCollection;
use App\Http\Resources\UsedRvDetailResource;

class UsedRvController extends Controller
{
    protected $usedRvService;

    public function __construct(UsedRvService $usedRvService)
    {
        $this->usedRvService = $usedRvService;
    }

    /**
     * 获取二手车列表
     */
    public function index(Request $request)
    {
        try {
            $usedRvs = $this->usedRvService->getUsedRvList();
            return $this->successResponse(new UsedRvResourceCollection($usedRvs));
        } catch (\Throwable $e) {
            return $this->errorResponse('二手车列表获取失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 获取二手车详情
     */
    public function show(Request $request, int $id)
    {
        try {
            $usedRv = $this->usedRvService->getUsedRvDetail($id);
            return $this->successResponse(new UsedRvDetailResource($usedRv));
        } catch (\Throwable $e) {
            return $this->errorResponse('二手车详情获取失败：' . $e->getMessage(), 500);
        }
    }
}
