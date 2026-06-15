<?php

namespace App\Repositories;

use App\Models\AIVideo;

class AiVideoRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return AIVideo::class;
    }
}
