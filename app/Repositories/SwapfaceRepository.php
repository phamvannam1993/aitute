<?php

namespace App\Repositories;

use App\Models\SwapFace;

class SwapfaceRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return SwapFace::class;
    }
}
