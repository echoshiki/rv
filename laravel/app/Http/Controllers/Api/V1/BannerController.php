<?php

namespace App\Http\Controllers\api\V1;

use App\Http\Controllers\Controller;
use App\Services\BannerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    //声明服务属性，仅声明
    protected BannerService $bannerService;

    /**
     * 构造函数注入 BannerService
     *
     * @param BannerService $bannerService
     */
    public function __construct(BannerService $bannerService)
    {
        // 自动化注入了服务类，不需要每次调用再 new 创建
        $this->bannerService = $bannerService;
    }

    /**
     * 获取指定频道的轮播图列表
     *
     * @param string $channel
     * @return JsonResponse
     */
    public function index(string $channel): JsonResponse
    {
        try {
            // 调用服务层方法获取数据
            $banners = $this->bannerService->getActiveBannersByChannel($channel);
            // 服务层已经处理了错误和空数据情况，直接返回即可
            return $this->successResponse($banners);
        } catch (\Throwable $e) {
            return $this->errorResponse('轮播图列表获取失败：' . $e->getMessage(), 500);
        }
    }
}