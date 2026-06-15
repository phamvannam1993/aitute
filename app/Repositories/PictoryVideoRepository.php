<?php

namespace App\Repositories;

use App\Models\PictoryVideo;

class PictoryVideoRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return PictoryVideo::class;
    }
}
