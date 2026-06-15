<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AiAssistant;
use App\Models\Interest;
use App\Models\Operation;
use App\Services\AiAssistantService;
use App\Services\JobService;
use App\Services\OperationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
class OperationController extends Controller
{
    protected $operationService;
    protected $jobService;
    protected $aiAssistantService;
    public function __construct(OperationService $operationService, JobService $jobService, AiAssistantService $aiAssistantService)
    {
        $this->operationService = $operationService;
        $this->jobService = $jobService;
        $this->aiAssistantService = $aiAssistantService;
    }

    public function index(Request $request)
    {
        $user_id = $request->user()->id;
        $operations = $this->operationService->getAllOperations();
        $listAi = $this->aiAssistantService->getListAiTop($user_id);

        return Inertia::render('Client/Operation/Index', [
            'operations' => $operations,
            'listAi' => $listAi,
        ]);
    }

    public function show(Request $request, $id)
    {
        $user_id = $request->user()->id;
        $aiAssistants = AiAssistant::with('operation')
            ->where('operation_id', $id)
            ->where('ai_assistants.is_public', true)
            ->select('id', 'name', 'thumbnail', 'operation_id', 'type')
            ->limit(9)
            ->get();
        $operation = Operation::select('id', 'name', 'occupation_id')
            ->findOrFail($id);

        $relatedOperations = $this->operationService->getRelatedOperations($operation->occupation_id ?? 0, $operation->id ?? 0);

        $listAi = $this->aiAssistantService->getListAiTop($user_id);

        return Inertia::render('Client/Operation/Detail', [
            'operation' => $operation,
            'aiAssistants' => $aiAssistants,
            'relatedOperations' => $relatedOperations ?? [],
            'listAi' => $listAi,
        ]);
    }

    public function search(Request $request)
    {
        $search = $request->search ?? '';
        $type = $request->type ?? 1;
        $userId = auth()->id();
        $operations = $this->operationService->getListSearch($userId, $search, $type);
        return response()->json($operations);
    }

    public function search_pb(Request $request)
    {
        $search = $request->input('search', '');
        $offset = $request->input('offset', 12);
        $type = $request->type ?? 1;
        $operations = $this->operationService->getListSearchPb($search);
        return response()->json($operations);
    }

    public function loadMore(Request $request)
    {
        $search = $request->input('search', '');
        $offset = $request->input('offset', 12);
        $type = $request->type ?? 1;
        $userId = auth()->id();
        $operations = $this->operationService->loadMoreOperations($userId, $offset, $search, $type);
        return response()->json($operations);
    }

    public function toggleInterest(Request $request, $operation_id)
    {
        $operation = Operation::select('id', 'name', 'occupation_id')
            ->findOrFail($operation_id);

        $interest = Interest::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'operation_id' => $operation->id,
                'occupation_id' => $operation->occupation_id
            ],
            ['is_interested' => $request->is_interested ?? false]
        );

        return response()->json(['success' => true, 'is_interested' => $interest->is_interested]);
    }


}
