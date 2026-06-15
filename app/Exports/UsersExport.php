<?php

namespace App\Exports;

use App\Repositories\UserRepository;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class UsersExport implements FromQuery
{
    use Exportable;
    private $params;
    private $userRepository;

    public function __construct(
        array $params = []
    ) {
        $this->params =  $params;
        $this->userRepository =  app(UserRepository::class);
    }

    public function query()
    {
       
        return $this->userRepository->queryExportUsers($this->params);
    }
}
