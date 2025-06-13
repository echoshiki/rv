<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BannerController;
use App\Http\Controllers\Api\V1\MenuController;
use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\Api\V1\RegionController;
use App\Http\Controllers\Api\V1\ArtivityController;
use App\Http\Controllers\Api\V1\RegistrationController;
use App\Http\Controllers\Api\V1\RvController;
use App\Http\Controllers\Api\V1\UsedRvController;
use App\Http\Controllers\Api\V1\MyCarController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\PointLogController;
use App\Http\Controllers\Api\V1\MaintenanceController;
use App\Http\Controllers\Api\V1\SuggestController;
use App\Http\Controllers\Api\V1\WebhookController;
use App\Http\Controllers\Api\V1\RvOrderController;
use App\Http\Controllers\Api\V1\PaymentController;

// 服务接口 v1 版本
Route::prefix('v1')->group(function () {
    // 小程序静默登录
    Route::post('/login-silence', [AuthController::class, 'miniLoginInSilence']);
    // 小程序登录（未绑定手机号）
    Route::post('/login-bound', [AuthController::class, 'miniLoginOnBound']);
    // 小程序登录（绑定手机号）
    Route::post('/login', [AuthController::class, 'miniLogin']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    // 用户相关API
    Route::middleware('auth:sanctum')->prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::put('/', [UserController::class, 'update']);
        // 积分记录 ✅
        Route::get('/point-logs/consumption', [PointLogController::class, 'consumptionLogs']);
    });

    // 轮播图API
    Route::get('/banners/{channel}', [BannerController::class, 'index']);

    // 菜单API
    Route::prefix('menus')->group(function () {
        Route::get('/groups', [MenuController::class, 'getAllMenuGroups']);
        Route::get('/group/{code}', [MenuController::class, 'getMenuGroup']);
    });

    // 文章相关API
    Route::prefix('articles')->group(function () {
        Route::get('/code/{code}', [ArticleController::class, 'showByCode']);
        Route::get('/', [ArticleController::class, 'index']);
        Route::get('/{id}', [ArticleController::class, 'show']);
    });

    // 活动相关API
    Route::prefix('activities')->group(function () {
        Route::get('/categories', [ArtivityController::class, 'categories']);
        Route::get('/{id}', [ArtivityController::class, 'show']);
        Route::get('/', [ArtivityController::class, 'index']);
    });

    // 房车相关API
    Route::prefix('rvs')->group(function () {
        Route::get('/all', [RvController::class, 'all']);
        Route::get('/categories', [RvController::class, 'categories']);
        Route::get('/', [RvController::class, 'index']);
        Route::get('/{id}', [RvController::class, 'show']);
    });

    // 二手车相关API
    Route::prefix('used-rvs')->group(function () {
        Route::get('/', [UsedRvController::class, 'index']);
        Route::get('/{id}', [UsedRvController::class, 'show']);
    });

    // 区域相关API
    Route::middleware('auth:sanctum')->prefix('regions')->group(function () {
        Route::get('/provinces', [RegionController::class, 'provinces']);
        Route::get('/cities/{provinceCode}', [RegionController::class, 'cities']);
        Route::get('/districts/{cityCode}', [RegionController::class, 'districts']);
        Route::get('/name/{code}', [RegionController::class, 'name']);
    });

    // 报名相关API
    Route::middleware('auth:sanctum')->prefix('registrations')->group(function () {
        // 创建一个新的报名 ✅
        Route::post('/', [RegistrationController::class, 'store']);
        // 我的报名列表 ✅
        Route::get('/my', [RegistrationController::class, 'index']);
        // 获取报名状态 ✅
        Route::get('/{activityId}/status', [RegistrationController::class, 'status']);
        // 获取报名详情 ✅
        Route::get('/{registration}', [RegistrationController::class, 'show']);
        // 取消报名 ✅
        // Route::post('/{registration}/cancel', [RegistrationController::class, 'cancel']);
    });

    // 房车订单相关
    Route::middleware('auth:sanctum')->prefix('rv-orders')->group(function () {
        // 房车订单列表 ✅
        Route::get('/', [RvOrderController::class, 'index']);
        // 房车订单详情 ✅
        Route::get('/{id}', [RvOrderController::class, 'show']);
        // 创建一个新的房车订单 ✅
        Route::post('/', [RvOrderController::class, 'store']);
    });

    // 支付相关
    Route::middleware('auth:sanctum')->prefix('payments')->group(function () {
        // 查询支付状态 ✅
        Route::get('/status', [PaymentController::class, 'pollPaymentStatus']);
        // 获取支付单详情 ✅
        Route::get('/{payment}', [PaymentController::class, 'getPaymentDetail']);
        // 为指定的房车订单发起支付 （线上测试）
        Route::post('/rv-orders/{rvOrder}/pay', [PaymentController::class, 'createForRvOrder']);
        // 为指定的活动报名发起支付 （线上测试）
        Route::post('/registrations/{registration}/pay', [PaymentController::class, 'createForRegistration']);
    });

    // 微信回调处理 （线上测试）
    Route::post('/payments/notify/wechat', [WebhookController::class, 'handlePaymentNotify']);

    Route::middleware('auth:sanctum')->group(function () {
        // 用户建议 ✅
        Route::apiResource('suggests', SuggestController::class);
        // 维保预约 ✅
        Route::apiResource('maintenances', MaintenanceController::class);
        // 我的爱车 ✅
        Route::apiResource('my-cars', MyCarController::class);
    });

});

// 测试API
Route::prefix('v2')->group(function () {
    Route::get('/test', function () {
        return response()->json(['message' => 'Hello World!']);
    });
});
