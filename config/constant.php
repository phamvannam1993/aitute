<?php


return [
    // Định nghĩa các loại trợ lý ảo
    'assistant_types' => [
        'text' => 'text',
        'image' => 'image',
        'audio' => 'audio',
        'mc' => 'mc',
        'video' => 'video',
    ],

    'package' => [
        'VIP' => 1,
        'TRAIL' => 2,
        'Standard' => 3,
        'Premium' => 4,
        'SELF' => 5,
    ],

    'STORAGE_DISK' => env('STORAGE_DISK', 's3'),
    'APP_ENV' => env('APP_ENV'),
    'MESSENGER_TIME_TO_NEW_CONVERSATION' => env('MESSENGER_TIME_TO_NEW_CONVERSATION'),
];
