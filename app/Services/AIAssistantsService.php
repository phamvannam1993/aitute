<?php

namespace App\Services;

use App\Helper\ApiErrorsMessageBag;
use App\Models\AiAssistant;
use App\Repositories\OccupationRepository;
use App\Repositories\OperationRepository;
use Exception;
use Illuminate\Support\Facades\Log;

class AIAssistantsService
{
    protected $occupationRepository;
    protected $operationRepository;

    // Inject OccupationRepository vào service
    public function __construct(OccupationRepository $occupationRepository, OperationRepository $operationRepository)
    {
        $this->occupationRepository = $occupationRepository;
        $this->operationRepository = $operationRepository;
    }

    function getAiAssistantById($id)
    {
        try {
            $ai_assistants = AiAssistant::with('criteria')
                ->where('id', '=', $id)
                ->where('ai_assistants.is_public', true)
                ->first();
            return $ai_assistants;
        } catch (\Throwable $e) {
            Log::error('Error deleting message:', ['error' => $e->getMessage()]);
        }
    }

    public function getOccupationById(int $id)
    {
        return $this->occupationRepository->findById($id);
    }

    public function getOperationById(int $id)
    {
        return $this->operationRepository->findById($id);
    }
}
