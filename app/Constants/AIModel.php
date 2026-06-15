<?php

namespace App\Constants;

class AIModel
{
    const GPT_35 = 'gpt-3.5-turbo';
    const GPT_4_MINI = 'gpt-4o-mini';
    const GPT_4 = 'gpt-4o';
    const CLAUDE = 'claude-3-5-sonnet-20240620';
    const Flux_Schnell = 'flux-schnell';
    const Latent = 'latent-consistency';
    const Kling = 'kling';
    const Lucataco = 'animate-diff';
    const HEY_GEN = 'heygen';
    const O3_MINI = 'o3-mini';
    const Aesthetic = 'aesthetic';
    const Runway = 'gen3-alphaturbo';
    const ElevenLab = 'elevenlab';
    const Pebblely = 'pebblely';
    const HeygenVideoAvatar = 'heygen_video_avatar';
    const HeygenPhotoAvatar = 'heygen_photo_avatar';
    const FLUX = 'flux-1.1-pro';
    const PictoryCreateContent = 'pictory_create_content';
    const PictoryCreateVideo = 'pictory_create_video';
    public static function getModel($key)
    {
        $models = [
            'GPT-3.5' => self::GPT_35,
            'GPT-4o mini' => self::GPT_4_MINI,
            'GPT-4o' => self::GPT_4,
            'Claude 3.5 Sonnet' => self::CLAUDE,
            'o3-mini' => self::O3_MINI,
            'Flux Schnell' => self::Flux_Schnell,
            'Latent Consistency' => self::Latent,
            'Kling' => self::Kling,
            'Lucataco/animate-diff' => self::Lucataco,
            'Heygen' => self::HEY_GEN,
            'Aesthetic' => self::Aesthetic,
            'Runway/gen3-alphaturbo' => self::Runway,
            'Elevenlab' => self::ElevenLab,
            'Pebblely' => self::Pebblely,
            'HeygenVideoAvatar' =>self::HeygenVideoAvatar,
            'HeygenPhotoAvatar' => self::HeygenPhotoAvatar,
            'flux-1.1-pro' => self::FLUX,
            'pictory_create_content' => self::PictoryCreateContent,
            'pictory_create_video' => self::PictoryCreateVideo,
        ];

        return $models[$key] ?? null;
    }
}
