<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 在这里配置 CSRF 排除项
        $middleware->validateCsrfTokens(except: [
            // 将你的微信回调路由加到这里
            'api/v1/payments/notify/wechat',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
