<?php

namespace App\Http\Controllers\Client;



use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\AiAssistant;
use App\Services\AiAssistantService;

class TimeLineController extends Controller
{
    protected $aiAssistantService;
    public function __construct(AiAssistantService $aiAssistantService)
    {
        $this->aiAssistantService = $aiAssistantService;
    }

    public function index(Request $request)
    {
        $popularAssistants = AiAssistant::select('id', 'name', 'description', 'thumbnail', 'operation_id', 'occupation_id')
            ->with(['operation:id,name', 'occupation:id,name'])
            ->with('favorites')
            ->withCount('favorites')
            ->where('ai_assistants.is_public', true)
            ->orderBy('favorites_count', 'desc')
            ->limit(18)
            ->get();

        $user = $request->user(); // Lấy thông tin người dùng
        $user_id = $user ? $user->id : null; // Lấy user_id nếu có người dùng

        $listAi = $this->aiAssistantService->getListAiTop($user_id);
        // get mốc thời gian timeline
        $timeline = $this->aiAssistantService->getListTimeLine();
        return Inertia::render('Client/TimeLine/Index', [
            'popularAssistants' => $popularAssistants,
            'listAi' => $listAi,
            'timeline' => $timeline
        ]);
    }
}
