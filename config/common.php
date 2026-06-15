<?php

return [
    'google_search_engine_id' => env('GOOGLE_SEARCH_ENGINE_ID'),
    'google_search_api_key' => env('GOOGLE_SEARCH_API_KEY'),
    'pixabay_api_key' => env('PIXABAY_API_KEY', '45024643-032dc0bc2f699737ea84a5bff'),
    'pexels_api_key' => env('PEXELS_API_KEY', 'FICdqF4nOwH3RRgQffHdN12KDSecpsUkSIkUPXDFIBwgXrUsZWkT5kda'),
    'iconfinder_api_key' => env('ICONFINDER_API_KEY', 'X0vjEUN6KRlxbp2DoUkyHeM0VOmxY91rA6BbU5j3Xu6wDodwS0McmilLPBWDUcJ1'),

    'app_url' => env('APP_URL', 'https://japanese.t4code.xyz/'),

    'payment' => [
        'alepay' => [
            'url' => env('ALEPAY_URL', 'https://alepay-v3.nganluong.vn/api/v3/checkout'),
            'token' => env('ALEPAY_TOKEN', 'Kt44MUOGbBUn7aShgsBWy4t56ws9aX'),
            'checksum' => env('ALEPAY_CHECKSUM', 'pQ1LPRuTz6FVkCWm9rkdVqFNltCEzn'),
            'encrypt_key' => env('ALEPAY_ENCRYPT_KEY', 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQChgYYFQTaEJ9BxqT+1KEHYKJ/rgKGpMLS1/OhskU5a7fDDKJr+8i5QIyFyHT/lGchLmOhbk5rUDFSXvz2lG+pnkE4V6HoNGuaY58RD/Jt+xEjp7t/PUqCvMwfGosrYMKzuHzuHu2lpuWOD7lZQR/7gbuEF9sfD2RgkRGW9ZDVBJwIDAQAB'),
        ],
    ],

    'max_allowed_prizes_per_day' => env('MAX_ALLOWED_PRIZES_PER_DAY', 1),

    'encrypted_key' => 'polypi',

    'payment_type' => env('PAYMENT_TYPE', 'QRCode'),
    'duration_send_remind_buy_product' => env('DURATION_SEND_REMIND_BUY_PRODUCT', 24),
    'trial_max_count' => env('TRIAL_MAX_COUNT', 3),
];
