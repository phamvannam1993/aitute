<?php

namespace App\Enums;

use App\Models\AIAssistantHistories;
use App\Models\AiGeneratedBannerPoster;
use App\Models\AIGeneratedMedia;
use App\Models\AIVideo;
use App\Models\CreatomateVideo;
use App\Models\McVirtual;
use App\Models\PictoryVideo;
use App\Models\SwapFace;
use App\Models\Lipsync;
use App\Models\PebblelyVideo;
use App\Models\Music;
use App\Models\SocialPost;
use App\Models\Video;
use App\Models\ProductImage;

enum ShareLinkableTypeEnum: string
{
    case AIAssistantHistories = AIAssistantHistories::class;
    case AIGeneratedMedia = AIGeneratedMedia::class;
    case AIVideo = AIVideo::class;
    case SwapFace = SwapFace::class;
    case Lipsync = Lipsync::class;
    case Video = Video::class;
    case McVirtual = McVirtual::class;
    case PebblelyVideo = PebblelyVideo::class;
    case PictoryVideo = PictoryVideo::class;
    case AiGeneratedBannerPoster = AiGeneratedBannerPoster::class;
    case CreatomateVideo = CreatomateVideo::class;
    case Music = Music::class;
    case SocialPost = SocialPost::class;
    case ProductImage = ProductImage::class;
    
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function array(): array
    {
        return array_combine(self::names(), self::values());
    }
}
