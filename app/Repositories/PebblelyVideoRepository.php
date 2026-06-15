<?php

namespace App\Repositories;

use App\Models\PebblelyVideo;

class PebblelyVideoRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return PebblelyVideo::class;
    }
}
