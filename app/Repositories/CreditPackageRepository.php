<?php

namespace App\Repositories;

use App\Models\CreditPackage;

class CreditPackageRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return CreditPackage::class;
    }


    public function all()
    {
        return $this->model->all();
    }
}
