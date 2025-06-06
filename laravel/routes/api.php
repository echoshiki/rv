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

// v1
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

    // 区域相关API
    Route::middleware('auth:sanctum')->prefix('regions')->group(function () {
        Route::get('/provinces', [RegionController::class, 'provinces']);
        Route::get('/cities/{provinceCode}', [RegionController::class, 'cities']);
        Route::get('/districts/{cityCode}', [RegionController::class, 'districts']);
        Route::get('/name/{code}', [RegionController::class, 'name']);
    });

    // 活动相关API
    Route::prefix('activities')->group(function () {
        Route::get('/categories', [ArtivityController::class, 'categories']);
        Route::get('/{id}', [ArtivityController::class, 'show']);
        Route::get('/', [ArtivityController::class, 'index']);
    });

    // 报名相关API
    Route::middleware('auth:sanctum')->prefix('registrations')->group(function () {
        Route::post('/', [RegistrationController::class, 'store']);
        // Route::post('/{id}/cancel', [RegistrationController::class, 'cancel']);
        Route::get('/my', [RegistrationController::class, 'index']);
        Route::get('/{activityId}/status', [RegistrationController::class, 'status']);
        Route::get('/{id}', [RegistrationController::class, 'show']);
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

    // 我的爱车相关API
    // GET	/my-cars	my-cars.index	index	获取所有资源
    // POST	/my-cars	my-cars.store	store	创建新资源
    // GET	/my-cars/{my_car}	my-cars.show	show	获取指定资源
    // PUT/PATCH	/my-cars/{my_car}	my-cars.update	update	更新指定资源
    // DELETE	/my-cars/{my_car}	my-cars.destroy	destroy	删除指定资源
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('my-cars', MyCarController::class);
    });

    // 积分记录
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user/point-logs/consumption', [PointLogController::class, 'consumptionLogs']);
    });

    // 维保预约
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('maintenances', MaintenanceController::class);
    });
    
});

// 测试API
Route::prefix('v2')->group(function () {
    Route::get('/test', function () {
        return response()->json(['message' => 'Hello World!']);
    });
});
