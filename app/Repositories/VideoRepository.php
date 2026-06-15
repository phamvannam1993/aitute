<?php

namespace App\Repositories;

use App\Models\Video;

class VideoRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return Video::class;
    }
}
