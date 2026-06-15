<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT'),
    ],
    'facebook' => [
        'client_id' => env('FACEBOOK_LOGIN_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_LOGIN_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_LOGIN_REDIRECT'),
    ],
    'pdf_to_text' => [
        'binary_path' => env('PDF_TO_TEXT_BINARY_PATH', '/bin/pdftotext'),
    ],
    'google_text_to_speech' => [
        'api_key' => env('GOOGLE_TEXT_TO_SPEECH_API_KEY'),
        'voice' => [
            'languageCode' => 'vi-VN',
        ],
        'folder' => 'ai_audio',
        'url_v1_beta' => env('GOOGLE_TEXT_TO_SPEECH_URL_V1_BETA', 'https://texttospeech.googleapis.com/v1beta1/text:synthesize'),
        'url' => env('GOOGLE_TEXT_TO_SPEECH_API_URL', 'https://texttospeech.googleapis.com/v1/text:synthesize'),
    ],
    'google_speech_to_text' => [
        'api_key' => env('GOOGLE_TEXT_TO_SPEECH_API_KEY'),
        'voice' => [
            'languageCode' => 'vi-VN',
        ],
        'folder' => 'ai_audio',
        'url' => env('GOOGLE_SPEECH_TO_TEXT_API_URL', 'https://speech.googleapis.com/v1/speech:recognize'),
    ],
    'kling' => [
        'endpoint' => env('KLING_ENDPOINT'),
        'api_key' => env('KLING_API_KEY'),
    ],
    'segmind' => [
        'endpoint' => env('SEGMIND_ENDPOINT'),
        'api_key' => env('SEGMIND_API_KEY'),
    ],
    'messenger' => [
        'page_access_token' => env('MESSENGER_VERIFY_TOKEN', '25430931b77e37f13bcf4a3ffa92e43f'),
        'verify_token' => env('MESSENGER_VERIFY_TOKEN', '25430931b77e37f13bcf4a3ffa92e43f'),
    ],
    'dify' => [
        'api_key' => env('DIFY_API_KEY'),
        'api_key_v2' => env('DIFY_API_KEY_V2'),
        'base_url' => env('DIFY_BASE_URL', 'https://api.dify.ai'),
        'api_access_key' => env('DIFY_API_ACCESS_KEY'),
    ],
    'chatdocs' => [
        'endpoint' => env('CHATDOCS_ENDPOINT', 'https://baoson.aiwow.com.vn'),
    ],
    'dify_video_to_text' => [
        'api_key' => env('DIFY_API_KEY_VIDEO_TO_TEXT'),
        'base_url' => env('DIFY_BASE_URL', 'https://api.dify.ai'),
    ],
    'self_host_dify' => [
        'api_key' => env('SELF_HOST_DIFY_API_KEY'),
        'api_key_v2' => env('SELF_HOST_DIFY_API_KEY_V2'),
        'base_url' => env('SELF_HOST_DIFY_BASE_URL', 'https://dify.myg.vn'),
    ],
    'messenger' => [
        'app_id' => env('MESSENGER_APP_ID', '1234567890'),
        'app_secret' => env('MESSENGER_APP_SECRET', '1234567890'),
        'verify_token' => env('MESSENGER_VERIFY_TOKEN', '25430931b77e37f13bcf4a3ffa92e43f')
    ],
    'dify_chat' => [
        'api_key' => env('CHAT_DIFY_API_KEY'),
        'api_key_v2' => env('CHAT_DIFY_API_KEY_V2'),
        'base_url' => env('CHAT_DIFY_BASE_URL', 'https://api.dify.ai'),
    ],
];
