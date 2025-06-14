<?php

return [
    // 小程序相关配置
    'mini' => [
        'app_id' => env('WECHAT_MINI_APP_ID'),
        'secret' => env('WECHAT_MINI_APP_SECRET'),
        'token' => env('WECHAT_MINI_APP_TOKEN'),
        'aes_key' => env('WECHAT_MINI_APP_AES_KEY'),
        'use_stable_access_token' => false,
        'http' => [
            'throw'  => true, // 状态码非 200、300 时是否抛出异常，默认为开启
            'timeout' => 5.0,
            'retry' => true, // 使用默认重试配置
        ],
    ],
    
    // 微信支付相关配置
    'pay' => [
        'app_id'     => env('WECHAT_MINI_APP_ID', ''),
        'secret'     => env('WECHAT_MINI_APP_SECRET', ''),
        'mch_id'     => env('WECHAT_PAY_MCH_ID', ''),
        'secret_key' => env('WECHAT_PAY_API_V3_KEY', ''),
        'private_key' => env('WECHAT_PAY_PRIVATE_KEY', ''),
        'certificate' => env('WECHAT_PAY_CERTIFICATE', ''),
        'serial_no'   => env('WECHAT_PAY_SERIAL_NO', ''),
        'platform_certs' => env('WECHAT_PAY_PLATFORM_CERTS', ''),
        'platform_public_key' => env('WECHAT_PAY_PUBLIC_KEY', ''),
        'notify_url' => env('WECHAT_PAY_NOTIFY_URL', '/api/v1/payments/notify/wechat'),
        'http' => [
            'throw'  => true,
            'timeout' => 5.0
        ]
    ],
];