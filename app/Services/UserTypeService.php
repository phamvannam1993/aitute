<?php

namespace App\Services;

use App\Helper\ApiErrorsMessageBag;
use Illuminate\Support\Facades\DB;
use App\Repositories\UserTypeRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserTypeService
{
    public function __construct(
        protected UserTypeRepository $userRepository,
    ) {
    }

    public function getList() {
        $list = $this->userRepository->getList();
        return $list;
    }
}
