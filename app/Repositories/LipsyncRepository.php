<?php

namespace App\Repositories;

use App\Models\Lipsync;

class LipsyncRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return Lipsync::class;
    }
}