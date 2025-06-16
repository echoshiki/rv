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

    // [
    //     "app_id" => "wxe3d1ce1694982eb5",
    //     "secret" => "a6f530cc3a742e8c2ce1b37e3f50b762",
    //     "mch_id" => "1719428406",
    //     "secret_key" => "DczDr8GENESqMxSaB2Ub5dhMk4DkYILz",
    //     "private_key" => "/www/rv/laravel/storage/certs/wechat_pay/apiclient_key.pem",
    //     "certificate" => "/www/rv/laravel/storage/certs/wechat_pay/apiclient_cert.pem",
    //     "serial_no" => "",
    //     "platform_certs" => "",
    //     "platform_public_key" => "",
    //     "notify_url" => "",
    //     "http" => [
    //       "throw" => true,
    //       "timeout" => 5.0,
    //     ],
    // ]
    
    // 微信支付相关配置
    // https://github.com/wechatpay-apiv3/wechatpay-php#%E5%A6%82%E4%BD%95%E4%B8%8B%E8%BD%BD%E5%B9%B3%E5%8F%B0%E8%AF%81%E4%B9%A6
    'pay' => [
        'app_id'     => env('WECHAT_MINI_APP_ID', ''),
        'secret'     => env('WECHAT_MINI_APP_SECRET', ''),
        'mch_id'     => env('WECHAT_PAY_MCH_ID', ''),
        'secret_key' => env('WECHAT_PAY_API_V3_KEY', ''),
        'private_key' => storage_path('certs/wechat_pay/apiclient_key.pem'),
        'certificate' => storage_path('certs/wechat_pay/apiclient_cert.pem'),
        'serial_no'   => env('WECHAT_PAY_SERIAL_NO', ''),
        'platform_certs' => [
            "PUB_KEY_ID_0117194284062025061600332347001803" => storage_path('certs/wechat_pay/pub_key.pem'),
        ],
        'notify_url' => env('WECHAT_PAY_NOTIFY_URL', '/api/v1/payments/notify/wechat'),
        'http' => [
            'throw'  => true,
            'timeout' => 5.0
        ]
    ],
];