<?php

namespace App\Services;

use App\Repositories\JobRepository;

class JobService
{
    protected $jobRepository;

    public function __construct(JobRepository $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

    public function getAllOccupations()
    {
        return $this->jobRepository->getAllOccupations();
    }

    public function getOccupationDetail($id)
    {
        $occupation = $this->jobRepository->getOccupationById($id);
        $operations = $this->jobRepository->getOperationsByOccupationId($id);
        $relatedOccupations = $this->jobRepository->getRelatedOccupations($id);

        return [
            'occupation' => $occupation,
            'operations' => $operations,
            'relatedOccupations' => $relatedOccupations
        ];
    }

    public function loadAllMore($offset = 12, $search = '')
    {
        return $this->jobRepository->getAllOccupationsMore($offset, $search);
    }

    public function loadMoreOperations($occupationId, $offset = 12, $search = '')
    {
        return $this->jobRepository->getOperationsByOccupationId($occupationId, $offset, $search);
    }

    public function searchOccupations($search, $limit = 12)
    {
        return $this->jobRepository->searchOccupations($search, $limit);
    }
}
