<?php

namespace App\Services;

use App\Repositories\CreditPackageRepository;

class CreditPackageService
{
    public function __construct(
        private CreditPackageRepository $creditPackageRepository
    ) {}


    public function index()
    {
        return $this->creditPackageRepository->all();
    }
}
