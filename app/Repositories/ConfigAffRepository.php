<?php

namespace App\Repositories;

use App\Models\ConfigAff;

class ConfigAffRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return ConfigAff::class;
    }

    public function all()
    {
        return $this->model->all();
    }
}
