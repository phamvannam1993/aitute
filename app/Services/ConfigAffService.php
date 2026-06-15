<?php

namespace App\Services;

use App\Repositories\ConfigAffRepository;

class ConfigAffService
{
    public function __construct(
        private ConfigAffRepository $configAffRepository,
    ) {}

    public function all()
    {
        return $this->configAffRepository->all();
    }
}
