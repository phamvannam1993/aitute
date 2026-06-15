<?php

namespace App\Repositories;

use App\Models\Music;

class AIMusicRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return Music::class;
    }
}
