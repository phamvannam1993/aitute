<?php

return [
    's3_folder' => [
        "occupations" => "occupations",
        "ai_chat" => "ai_chat",
        "operations" => "operations",
        "ai_assistants" => "ai_assistants"
    ],
    'pagination' => [
        'items_per_page' => 15,  // Số bản ghi mặc định trên mỗi trang
    ],
    'app_settings' => [
        'max_upload_size' => 1024 * 1024 * 5,  // 5MB
        'default_locale' => 'en',
    ],
    'app_purchase_redirect' => env('APP_PURCHASE_REDIRECT', 'https://aiwow.com.vn/'),
];
