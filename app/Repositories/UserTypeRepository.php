<?php

namespace App\Repositories;

use App\Models\UserType;

class UserTypeRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return UserType::class;
    }

    public function getList(){
        $list = UserType::select('type_name')->get();
        return $list;
    }
    
}
