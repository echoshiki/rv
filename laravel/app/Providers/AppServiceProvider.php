<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\RegionService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 注册 RegionService 单例，多次请求只返回一个实例
        $this->app->singleton(RegionService::class, function ($app) {
            return new RegionService();
        });

        if ($this->app->environment('local')) {
            if (class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
                $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
                $this->app->register(\App\Providers\TelescopeServiceProvider::class);
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
