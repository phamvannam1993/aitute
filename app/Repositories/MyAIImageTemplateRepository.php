<?php

namespace App\Repositories;

use App\Models\MyAIImageTemplate;

class MyAIImageTemplateRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return MyAIImageTemplate::class;
    }

    public function insert(array $data)
    {
        return $this->model->insert($data);
    }
}
