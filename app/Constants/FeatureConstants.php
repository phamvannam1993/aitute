<?php

namespace App\Constants;

class FeatureConstants
{
    // Danh sách feature codes
    public const AI_TEXT = 'ai_text';
    public const AI_IMAGE = 'ai_image';
    public const AI_IMAGE_ROOT = 'ai_image_root';
    public const AI_MOVIE = 'ai_movie';
    public const AI_VIDEO = 'ai_video';
    public const AI_VIRTUAL = 'ai_virtual';
    public const AI_SWAP_FACE = 'ai_swap_face';
    public const AI_IMAGE_TO_VIDEO = 'ai_image_to_video';
    public const AI_TEXT_TO_AUDIO_MUSIC = 'ai_text_to_audio_music';
    public const AI_LYRIC_TO_MUSIC = 'ai_lyric_to_music';
    public const AI_VIDEO_VOICE_OVERLAY = 'ai_video_voice_overlay';
    public const AI_CHAT_BOT = 'ai_chat_bot';
    public const AI_AUDIO = 'ai_audio';
    public const AI_BACKGROUND = 'ai_background';
    public const AI_SLIDE = 'ai_slide';
    public const AI_BANNER_POSTER = 'ai_banner_poster';
    public const MY_AI_IMAGE = 'my_ai_image';
    public const AI_LINK_TO_VIDEO = 'ai_link_to_video';
    public const AI_MERGE_VIDEO = 'ai_merge_video';
    public const AI_PERSONAL = 'ai_personal';
    public const AI_CREATE_VIDEO = 'ai_create_video';
    public const AI_GOOGLE_SEARCH = 'ai_google_search';
    public const AI_CUSTOMER_CARE = 'ai_customer_care';

    // Map màn hình với feature codes
    public const SCREENS = [
        'ai_text' => self::AI_TEXT,
        'ai_image' => self::AI_IMAGE,
        'ai_background' => self::AI_BACKGROUND,
        'ai_swap_face' => self::AI_SWAP_FACE,
        'ai_image_to_video' => self::AI_IMAGE_TO_VIDEO,
        'ai_image_root' => self::AI_IMAGE_ROOT,
        'ai_movie' => self::AI_MOVIE,
        'ai_video' => self::AI_VIDEO,
        'ai_virtual' => self::AI_VIRTUAL,
        'ai_video_voice_overlay' => self::AI_VIDEO_VOICE_OVERLAY,
        'ai_chat_bot' => self::AI_CHAT_BOT,
        'ai_audio' => self::AI_AUDIO,
        'ai_text_to_audio_music' => self::AI_TEXT_TO_AUDIO_MUSIC,
        'ai_lyric_to_music' => self::AI_LYRIC_TO_MUSIC,
        'ai_slide' => self::AI_SLIDE,
        'ai_banner_poster' => self::AI_BANNER_POSTER,
        'my_ai_image' => self::MY_AI_IMAGE,
        'ai_link_to_video' => self::AI_LINK_TO_VIDEO,
        'ai_merge_video' => self::AI_MERGE_VIDEO,
        'ai_personal' => self::AI_PERSONAL,
        'ai_create_video' => self::AI_CREATE_VIDEO,
        'ai_google_search' => self::AI_GOOGLE_SEARCH,
        'ai_customer_care' => self::AI_CUSTOMER_CARE
    ];

    public const FEATURE_DESCRIPTIONS = [
        self::AI_TEXT => 'Các trợ lý giải quyết mọi công việc',
        self::AI_IMAGE => ' Tạo hình ảnh từ văn bản',
        self::AI_BACKGROUND => ' Hình ảnh phối cảnh',
        self::AI_SWAP_FACE => 'Đổi khuôn mặt',
        self::AI_IMAGE_TO_VIDEO => 'Tạo hình ảnh thành video ',
        self::AI_IMAGE_ROOT => 'Tạo hình ảnh từ ảnh gốc',
        self::AI_MOVIE => 'Tạo Flim từ văn bản',
        self::AI_VIDEO => 'Tạo Video từ ảnh và văn bản',
        self::AI_VIRTUAL => 'Làm MC ảo',
        self::AI_VIDEO_VOICE_OVERLAY => 'lipsync Trợ lí tạo video TÔI LÀ AI',
        self::AI_CHAT_BOT => 'Chatbot',
        self::AI_AUDIO => 'Giọng nói',
        self::AI_TEXT_TO_AUDIO_MUSIC => 'Tạo âm nhạc từ văn bản',
        self::AI_LYRIC_TO_MUSIC => 'Tạo bài hát từ văn bản',
        self::AI_SLIDE => 'Tạo slide',
        self::AI_BANNER_POSTER => 'Tạo banner hoặc poster',
        self::MY_AI_IMAGE => 'Tạo ảnh từ mô hình AI của bạn',
        self::AI_LINK_TO_VIDEO => 'Tạo video từ link',
        self::AI_MERGE_VIDEO => 'Tạo ghép video',
        self::AI_PERSONAL => 'Tạo AI_PERSONAL',
        self::AI_CREATE_VIDEO => 'Tạo video cá nhân hóa',
        self::AI_GOOGLE_SEARCH => 'Tìm kiếm khách hàng',
        self::AI_CUSTOMER_CARE => 'Chăm sóc khách hàng',
    ];
}
