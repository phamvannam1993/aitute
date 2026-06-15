<?php

namespace App\Repositories;

use App\Models\CreatomateVideo;

class CreatomateVideoRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return CreatomateVideo::class;
    }
}
