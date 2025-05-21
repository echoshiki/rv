<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\RvService;
use Illuminate\Http\Request;
use App\Http\Resources\RvResourceCollection;
use App\Http\Resources\RvDetailResource;

class RvController extends Controller
{
    protected $rvService;

    public function __construct(RvService $rvService)
    {
        $this->rvService = $rvService;
    }

    /**
     * 获取房车列表
     */
    public function index(Request $request)
    {
        try {
            $rvs = $this->rvService->getRvList();
            return $this->successResponse(new RvResourceCollection($rvs));
        } catch (\Throwable $e) {
            return $this->errorResponse('房车列表获取失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 获取房车详情
     */
    public function show(Request $request, int $id)
    {
        try {
            $rv = $this->rvService->getRvDetail($id);
            return $this->successResponse(new RvDetailResource($rv));
        } catch (\Throwable $e) {
            return $this->errorResponse('房车详情获取失败：' . $e->getMessage(), 500);
        }
    }
}
