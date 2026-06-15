<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AiAssistant;
use App\Models\AssistantFavorites;
use App\Services\AiAssistantService;
use App\Services\JobService;
use App\Services\OperationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FavoriteController extends Controller
{
    protected $aiAssistantService;
    public function __construct(AiAssistantService $aiAssistantService)
    {
        $this->aiAssistantService = $aiAssistantService;
    }

    public function index(Request $request)
    {
        $user_id = $request->user()->id;

        $favoriteAssistants = AiAssistant::select('ai_assistants.id', 'ai_assistants.name', 'ai_assistants.description', 'ai_assistants.type', 'ai_assistants.thumbnail', 'ai_assistants.operation_id', 'ai_assistants.occupation_id')
            ->with('favorites')
            ->withExists(['favorites as is_favorited_by_user' => function($query) use ($user_id) {
                $query->where('user_id', $user_id);
            }])
            ->join('assistant_favorites', 'ai_assistants.id', '=', 'assistant_favorites.ai_assistant_id')
            ->where('assistant_favorites.user_id', $user_id)
            ->where('ai_assistants.is_public', true)
            ->with(['operation:id,name', 'occupation:id,name'])
            ->limit(18)
            ->orderBy('ai_assistants.id', 'desc')
            ->get();

            $listAi = $this->aiAssistantService->getListAiTop($user_id);
        return Inertia::render('Client/Favorite/Index', [
            'favoriteAssistants' => $favoriteAssistants,
            'listAi' => $listAi,
        ]);
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $user_id = $request->user()->id || null;

        $favoriteAssistants = AiAssistant::select('ai_assistants.id', 'ai_assistants.name', 'ai_assistants.description', 'ai_assistants.thumbnail', 'ai_assistants.operation_id', 'ai_assistants.occupation_id')
            ->with('favorites')
            ->join('assistant_favorites', 'ai_assistants.id', '=', 'assistant_favorites.ai_assistant_id')
            ->where('assistant_favorites.user_id', $user_id)
            ->where('ai_assistants.is_public', true)
            ->with(['operation:id,name', 'occupation:id,name'])
            ->where(function ($query) use ($search) {
                $query->where('ai_assistants.name', 'like', "%$search%")
                    ->orWhere('ai_assistants.description', 'like', "%$search%")
                    ->orWhereHas('occupation', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('operation', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
            })
            ->limit(18)
            ->orderBy('ai_assistants.id', 'desc')
            ->get();

        return response()->json($favoriteAssistants);
    }


    public function loadMore(Request $request)
    {
        $user_id = $request->user()->id;
        $search = $request->input('search', '');
        $limit = 18;
        $offset = $request->input('offset', 0);

        $favoriteAssistants = AiAssistant::select('ai_assistants.id', 'ai_assistants.name', 'ai_assistants.description', 'ai_assistants.thumbnail', 'ai_assistants.operation_id', 'ai_assistants.occupation_id')
            ->with('favorites')
            ->join('assistant_favorites', 'ai_assistants.id', '=', 'assistant_favorites.ai_assistant_id')
            ->where('assistant_favorites.user_id', $user_id)
            ->where('ai_assistants.is_public', true)
            ->with(['operation:id,name', 'occupation:id,name'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('ai_assistants.name', 'like', "%$search%")
                        ->orWhere('ai_assistants.description', 'like', "%$search%")
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
            ->orderBy('ai_assistants.id', 'desc')
            ->get();

        return response()->json($favoriteAssistants);
    }

}
