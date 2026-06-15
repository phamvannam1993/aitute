<?php

namespace App\Repositories;

use App\Models\SlideAiTemplate;

class SlideAiTemplateRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return SlideAiTemplate::class;
    }
}
