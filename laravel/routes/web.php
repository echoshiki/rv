<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/debug-request', function () {
    return [
        'uri' => request()->getRequestUri(),
        'query' => request()->query(),
        'all' => request()->all(),
        'has_valid_signature' => request()->hasValidSignature()
    ];
});
