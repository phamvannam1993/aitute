<?php

namespace App\Repositories;

use App\Models\CreatomateTemplate;

class CreatomateTemplateRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return CreatomateTemplate::class;
    }
}
