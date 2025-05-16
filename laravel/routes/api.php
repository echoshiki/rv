<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\api\V1\BannerController;
use App\Http\Controllers\api\V1\MenuController;
use App\Http\Controllers\api\V1\ArticleController;
use App\Http\Controllers\api\V1\RegionController;

use App\Models\Article;

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

        // 轮播图
        Route::get('/banners/{channel}', [BannerController::class, 'index'])->name('api.v1.banners');

        // 菜单相关API
        Route::prefix('menus')->group(function () {
            Route::get('/groups', [MenuController::class, 'getAllMenuGroups']);
            Route::get('/group/{code}', [MenuController::class, 'getMenuGroup']);
        });

        // 文章相关API
        Route::get('/articles', [ArticleController::class, 'index']);
        Route::get('/articles/{id}', [ArticleController::class, 'show']);
        Route::get('/articles/code/{code}', [ArticleController::class, 'showByCode']);

        // 区域相关API
        Route::prefix('regions')->group(function () {
            Route::get('/provinces', [RegionController::class, 'provinces']);
            Route::get('/cities/{provinceCode}', [RegionController::class, 'cities']);
            Route::get('/districts/{cityCode}', [RegionController::class, 'districts']);
            Route::get('/name/{code}', [RegionController::class, 'name']);
        });
    });

    Route::prefix('v2')->group(function () {
        // ...
    });
