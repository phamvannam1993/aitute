<?php

namespace App\Repositories;

use App\Models\ShortVideo;

class ShortVideoRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return ShortVideo::class;
    }
}
