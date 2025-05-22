<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\api\V1\BannerController;
use App\Http\Controllers\api\V1\MenuController;
use App\Http\Controllers\api\V1\ArticleController;
use App\Http\Controllers\api\V1\RegionController;
use App\Http\Controllers\api\V1\ArtivityController;
use App\Http\Controllers\api\V1\ActivityRegistrationController;
use App\Http\Controllers\api\V1\RvController;
use App\Http\Controllers\api\V1\UsedRvController;

// v1
Route::prefix('v1')->group(function () {
    // 小程序静默登录
    Route::post('/login-silence', [AuthController::class, 'miniLoginInSilence'])->name('api.v1.mini.mini-login-silence');
    // 小程序登录（未绑定手机号）
    Route::post('/login-bound', [AuthController::class, 'miniLoginOnBound'])->name('api.v1.mini.mini-login-bound');
    // 小程序登录（绑定手机号）
    Route::post('/login', [AuthController::class, 'miniLogin'])->name('api.v1.mini.mini-login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.v1.mini.logout')->middleware('auth:sanctum');

    Route::post('/user', function (Request $request) {
        return new UserResource($request->user());
    })->middleware('auth:sanctum');

    // 轮播图相关API
    Route::get('/banners/{channel}', [BannerController::class, 'index'])->name('api.v1.banners');

    // 菜单相关API
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
        Route::get('/', [ArtivityController::class, 'index']);
        Route::get('/{id}', [ArtivityController::class, 'show']);
    });

    // 报名相关API
    Route::middleware('auth:sanctum')->prefix('registrations')->group(function () {
        Route::post('/', [ActivityRegistrationController::class, 'store']);
        // Route::post('/{id}/cancel', [ActivityRegistrationController::class, 'cancel']);
        Route::get('/my', [ActivityRegistrationController::class, 'index']);
        Route::get('/{id}', [ActivityRegistrationController::class, 'show']);
    });

    // 房车相关API
    Route::prefix('rvs')->group(function () {
        Route::get('/', [RvController::class, 'index']);
        Route::get('/{id}', [RvController::class, 'show']);
    });

    // 二手车相关API
    Route::prefix('used-rvs')->group(function () {
        Route::get('/', [UsedRvController::class, 'index']);
        Route::get('/{id}', [UsedRvController::class, 'show']);
    });
});

Route::prefix('v2')->group(function () {
    // ...
});
