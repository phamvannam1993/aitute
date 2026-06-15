<?php

namespace App\Repositories;

use App\Models\SlideAiComponent;

class SlideAiComponentRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return SlideAiComponent::class;
    }
}
