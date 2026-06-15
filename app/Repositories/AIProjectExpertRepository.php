<?php

namespace App\Repositories;

use App\Models\AIProjectExpert;

class AIProjectExpertRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return AIProjectExpert::class;
    }
}
