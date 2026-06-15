<?php

namespace App\Repositories;

use App\Models\AiGeneratedBannerPoster;
use App\Models\PictoryVideo;

class AiGeneratedBannerPosterRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return AiGeneratedBannerPoster::class;
    }
}
