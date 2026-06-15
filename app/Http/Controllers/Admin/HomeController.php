<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AiAssistantService;
use App\Services\OccupationService;
use App\Services\OperationService;
use Inertia\Inertia;

class HomeController extends Controller
{
    protected $AiAssistantService;
    protected $occupationService;
    protected $operationService;

    public function __construct(OccupationService $occupationService, OperationService $operationService, AiAssistantService $AiAssistantService)
    {
        $this->occupationService = $occupationService;
        $this->operationService = $operationService;
        $this->AiAssistantService = $AiAssistantService;
    }

    public function index()
    {
        // get Count Ngành nghề
        $countOccupation = $this->occupationService->getCount();
        // get Count Nghiep vu
        $countOperation = $this->operationService->getCount();
        // get Count Ai
        $countAi = $this->AiAssistantService->getCount();
        return Inertia::render('Admin/Index', [
            'countOccupation' => $countOccupation,
            'countOperation' => $countOperation,
            'countAi' => $countAi
        ]);
    }
}
