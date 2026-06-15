<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Occupation;
use App\Services\AiAssistantService;
use App\Services\JobService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JobsController extends Controller
{
    protected $jobService;
    protected $aiAssistantService;
    public function __construct(JobService $jobService, AiAssistantService $aiAssistantService)
    {
        $this->jobService = $jobService;
        $this->aiAssistantService = $aiAssistantService;
    }

    public function index(Request $request)
    {
        $user_id = $request->user()->id;

        $occupations = $this->jobService->getAllOccupations();
        $listAi = $this->aiAssistantService->getListAiTop($user_id);
        
        return Inertia::render('Client/Job/Index', [
            'occupations' => $occupations,
            'listAi' => $listAi,
        ]);
    }

    public function show(Request $request, $id)
    {
        $user_id = $request->user()->id;
        $data = $this->jobService->getOccupationDetail($id);
        $listAi = $this->aiAssistantService->getListAiTop($user_id);        
        return Inertia::render('Client/Job/Detail', [
            'occupation' => $data['occupation'],
            'operations' => $data['operations'],
            'relatedOccupations' => $data['relatedOccupations'],
            'listAi' => $listAi,
        ]);
    }

    public function search(Request $request)
    {
        $search = $request->input('search', '');

        $occupations = $this->jobService->searchOccupations($search, 12);

        return response()->json($occupations);
    }

    public function loadAllMore(Request $request)
    {
        $search = $request->input('search', '');
        $offset = $request->input('offset', 12);

        $operations = $this->jobService->loadAllMore($offset, $search);

        return response()->json($operations);
    }

    public function loadMore(Request $request)
    {
        $occupationId = $request->input('occupation_id');
        $search = $request->input('search', '');
        $offset = $request->input('offset', 12);

        $operations = $this->jobService->loadMoreOperations($occupationId, $offset, $search);

        return response()->json($operations);
    }
}
