<?php

namespace App\Repositories;

use App\Models\AIGeneratedMedia;

class AIGeneratedMediaRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return AIGeneratedMedia::class;
    }
}
