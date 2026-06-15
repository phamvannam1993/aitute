<?php

namespace App\Repositories;

use App\Models\Config;

class ConfigsRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return Config::class;
    }

    public function updateConfigByKey($value)
    {
        $configInstance = new Config;
    
        $index = 'key';
        batch()->update($configInstance, $value, $index);
    }
}
