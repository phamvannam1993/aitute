<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AiAssistant;
use App\Services\AiAssistantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PopularController extends Controller
{
    protected $aiAssistantService;
    public function __construct(AiAssistantService $aiAssistantService)
    {
        $this->aiAssistantService = $aiAssistantService;
    }

    public function index(Request $request)
    {
        $user_id = $request->user()->id;
        $user = Auth::user();
        $credits = $user->credit ?? 0;

        $popularAssistants = AiAssistant::select('id', 'name', 'description', 'thumbnail', 'type', 'operation_id', 'occupation_id')
            ->with(['operation:id,name', 'occupation:id,name'])
            ->with('favorites')
            ->withCount('favorites')
            ->withExists(['favorites as is_favorited_by_user' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            }])
            ->where('ai_assistants.is_public', true)
            ->orderBy('favorites_count', 'desc')
            ->limit(18)
            ->get();

        $listAi = $this->aiAssistantService->getListAiTop($user_id);

        return Inertia::render('Client/Popular/Index', [
            'popularAssistants' => $popularAssistants,
            'listAi' => $listAi,
            'credits' => $credits
        ]);
    }

    public function search(Request $request)
    {
        $search = $request->input('search', '');
        $user = $request->user();

        $popularAssistants = AiAssistant::select('id', 'name', 'description', 'thumbnail', 'operation_id', 'occupation_id', 'type', 'step_ais')
            ->with(['operation:id,name', 'occupation:id,name'])
            ->withCount('favorites')
            ->where('ai_assistants.is_public', true)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhereHas('occupation', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('operation', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
            })
            ->orderBy('favorites_count', 'desc')
            ->limit(18)
            ->get();

        return response()->json($popularAssistants);
    }

    public function loadMore(Request $request)
    {
        $user = $request->user();
        $search = $request->input('search', '');
        $limit = 18;
        $offset = $request->input('offset', 0);

        $popularAssistants = AiAssistant::select('id', 'name', 'description', 'thumbnail', 'operation_id', 'occupation_id')
            ->with(['operation:id,name', 'occupation:id,name'])
            ->with('favorites')
            ->withCount('favorites')
            ->where('ai_assistants.is_public', true)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('description', 'like', "%$search%")
                        ->orWhereHas('occupation', function ($q) use ($search) {
                            $q->where('name', 'like', "%$search%");
                        })
                        ->orWhereHas('operation', function ($q) use ($search) {
                            $q->where('name', 'like', "%$search%");
                        });
                });
            })
            ->offset($offset)
            ->limit($limit)
            ->orderBy('favorites_count', 'desc')
            ->get();

        return response()->json($popularAssistants);
    }

    public function getAllAssistants(Request $request)
    {
    $user_id = $request->user()->id;
    $perPage = 18; 
    $page = $request->input('page', 1); 

    $popularAssistants = AiAssistant::select('id', 'name', 'description', 'thumbnail', 'operation_id', 'occupation_id', 'type', 'step_ais')
        ->with(['operation:id,name', 'occupation:id,name'])
        ->withCount('favorites')
        ->where('ai_assistants.is_public', true)
        ->orderBy('favorites_count', 'desc')
        ->paginate($perPage, ['*'], 'page', $page); 

    return response()->json([
        'popularAssistants' => $popularAssistants
    ]);
    }
}
