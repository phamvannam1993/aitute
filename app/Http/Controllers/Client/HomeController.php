<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AffKey;
use App\Models\AiAssistant;
use App\Models\Category;
use App\Models\Occupation;
use App\Services\AiAssistantService;
use App\Services\AIAssistantsService;
use App\Services\AIBusinessProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    protected $aiAssistantService;
    protected $aIAssistantsService;
    public function __construct(AiAssistantService $aiAssistantService, AIAssistantsService $aIAssistantsService)
    {
        $this->aiAssistantService = $aiAssistantService;
        $this->aIAssistantsService = $aIAssistantsService;
    }

    public function index(Request $request, AIBusinessProjectService $aIBusinessProjectService)
    {
        $user = Auth::user();
        $user_id = $user ? $user->id : null;
        $aiAssistants = ($user) ?
            AiAssistant::select('id', 'name', 'description', 'type', 'thumbnail', 'operation_id', 'occupation_id', 'created_at', 'step_ais')
            ->with(['operation:id,name', 'occupation:id,name'])
            ->with([
                'favorites' => function ($q) use ($request) {
                    $q->where('user_id', $request->user()->id);
                }
            ])
            ->withExists(['favorites as is_favorited_by_user' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            }])
            ->where('ai_assistants.is_public', true)
            ->orderBy('id', 'desc')
            ->paginate(9)
            :
            AiAssistant::select('id', 'name', 'description', 'type', 'thumbnail', 'operation_id', 'occupation_id', 'created_at')
            ->with(['operation:id,name', 'occupation:id,name'])
            ->where('ai_assistants.is_public', true)
            ->orderBy('id', 'desc')
            ->paginate(15);


        $isFirstLogin = 0;
        $fields = [];

        if ($user) {
            $isFirstLogin = $user->first_login;

            if ($isFirstLogin) {
                $fields = Category::select('id', 'name', 'image')->get();
            }
            $my_ai_assistant = $this->aIAssistantsService->getAiAssistantById($request->id);
        }

        $listAi = $this->aiAssistantService->getListAiTop($user_id);

        $msg = $request->session()->pull('messageError', '');
        $msg_pictory = $request->session()->pull('messageErrorPictory', '');

        $existsInAffKeys = $user ? AffKey::where('user_id', $user_id)->exists() : false;

        $affCode = null;
        if (!empty($user->package->package_code)) {
            $affCode = $user->package->package_code;
        }

        $view = $request->get('view');
        $canshowProject = true;
        if ($view == 'project' && !empty($user)) {
            if (strtotime($user->vip_expired_at) <= time()) {
                $msg = 'Tài khoản của bạn đã hết hạn!';
                $canshowProject = false;
            } else {
                $mode = $request->get('mode');

                if (empty($affCode)) {
                    $msg = 'Bạn phải mua gói trả phí!';
                    $canshowProject = false;
                } elseif ($affCode == 'aff_trial') {
                    $checkTrialCount = $aIBusinessProjectService->checkTrialOverCount();
                    if ($checkTrialCount) {
                        $msg = "Tài khoản thử nghiệm chỉ được thao tác 3 lần. Vui lòng nâng cấp tài khoản để sử dụng!";
                        $canshowProject = false;
                    }
                } elseif (!empty($mode)) {
                    $modes = [
                        'basic' => 1,
                        'advanced' => 2,
                        'expert' => 3,
                    ];

                    $packages = [
                        'aff_basic' => 1,
                        'aff_account' => 1,
                        'aff_xdthcn' => 1,
                        'aff_advanced' => 2,
                        'aff_expert' => 3,
                        'aff_trial' => 4,
                        'aff_business' => 2,
                        'aff_vip' => 2,
                    ];

                    if ($modes[$mode] > $packages[$affCode]) {
                        $msg = 'Rất tiếc, bạn không có quyền truy cập tính năng này. Vui lòng nâng cấp tài khoản để sử dụng đầy đủ các tính năng.';
                        $canshowProject = false;
                    }
                }
            }
        }

        $data = [
            'message_alert' => $msg,
            'message_alert_pictory' => $msg_pictory,
            'aiAssistants' => $aiAssistants,
            'isFirstLogin' => $isFirstLogin,
            'fields' => $fields,
            'listAi' => $listAi,
            'aff_key_missing' => !$existsInAffKeys,
            'affCode' => $affCode,
            'my_ai_assistant' => $my_ai_assistant ?? null,
            'my_ai_image_models' => $user->myAiImages ?? null,
            'canShowProject' => $canshowProject
        ];

        return Inertia::render('Client/Home/Index', $data);
    }
    public function othersAi(Request $request, AIBusinessProjectService $aIBusinessProjectService)
    {
        $user = Auth::user();
        $user_id = $user ? $user->id : null;
        $aiAssistants = ($user) ?
            AiAssistant::select('id', 'name', 'description', 'type', 'thumbnail', 'operation_id', 'occupation_id', 'created_at', 'step_ais')
            ->with(['operation:id,name', 'occupation:id,name'])
            ->with([
                'favorites' => function ($q) use ($request) {
                    $q->where('user_id', $request->user()->id);
                }
            ])
            ->withExists(['favorites as is_favorited_by_user' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            }])
            ->where('ai_assistants.is_public', true)
            ->orderBy('id', 'desc')
            ->paginate(9)
            :
            AiAssistant::select('id', 'name', 'description', 'type', 'thumbnail', 'operation_id', 'occupation_id', 'created_at')
            ->with(['operation:id,name', 'occupation:id,name'])
            ->where('ai_assistants.is_public', true)
            ->orderBy('id', 'desc')
            ->paginate(15);


        $isFirstLogin = 0;
        $fields = [];

        if ($user) {
            $isFirstLogin = $user->first_login;

            if ($isFirstLogin) {
                $fields = Category::select('id', 'name', 'image')->get();
            }
            $my_ai_assistant = $this->aIAssistantsService->getAiAssistantById($request->id);
        }

        $listAi = $this->aiAssistantService->getListAiTop($user_id);

        $msg = $request->session()->pull('messageError', '');
        $msg_pictory = $request->session()->pull('messageErrorPictory', '');

        $existsInAffKeys = $user ? AffKey::where('user_id', $user_id)->exists() : false;

        $affCode = null;
        if (!empty($user->package->package_code)) {
            $affCode = $user->package->package_code;
        }

        $view = $request->get('view');
        $canshowProject = true;
        if ($view == 'project' && !empty($user)) {
            if (strtotime($user->vip_expired_at) <= time()) {
                $msg = 'Tài khoản của bạn đã hết hạn!';
                $canshowProject = false;
            } else {
                $mode = $request->get('mode');

                if (empty($affCode)) {
                    $msg = 'Bạn phải mua gói trả phí!';
                    $canshowProject = false;
                } elseif ($affCode == 'aff_trial') {
                    $checkTrialCount = $aIBusinessProjectService->checkTrialOverCount();
                    if ($checkTrialCount) {
                        $msg = "Tài khoản thử nghiệm chỉ được thao tác 3 lần. Vui lòng nâng cấp tài khoản để sử dụng!";
                        $canshowProject = false;
                    }
                } elseif (!empty($mode)) {
                    $modes = [
                        'basic' => 1,
                        'advanced' => 2,
                        'expert' => 3,
                    ];

                    $packages = [
                        'aff_basic' => 1,
                        'aff_advanced' => 2,
                        'aff_expert' => 3,
                        'aff_trial' => 4
                    ];

                    if ($modes[$mode] > $packages[$affCode]) {
                        $msg = 'Rất tiếc, bạn không có quyền truy cập tính năng này. Vui lòng nâng cấp tài khoản để sử dụng đầy đủ các tính năng.';
                        $canshowProject = false;
                    }
                }
            }
        }

        $data = [
            'message_alert' => $msg,
            'message_alert_pictory' => $msg_pictory,
            'aiAssistants' => $aiAssistants,
            'isFirstLogin' => $isFirstLogin,
            'fields' => $fields,
            'listAi' => $listAi,
            'aff_key_missing' => !$existsInAffKeys,
            'affCode' => $affCode,
            'my_ai_assistant' => $my_ai_assistant ?? null,
            'my_ai_image_models' => $user->myAiImages ?? null,
            'canShowProject' => $canshowProject
        ];

        return Inertia::render('Client/Home/OthersAi', $data);
    }

    public function search(Request $request)
    {
        $search = $request->search ?? '';
        $type = $request->type ?? null;
        $user = $request->user();
        $activeTab = $request->activeTab ?? 'new';
        $limit = is_numeric($request->limitPage) ? (int)$request->limitPage : 9;

        $query = AiAssistant::select('occupations.category_id', 'ai_assistants.id', 'ai_assistants.step_ais', 'ai_assistants.name', 'ai_assistants.description', 'ai_assistants.type', 'ai_assistants.thumbnail', 'ai_assistants.operation_id', 'ai_assistants.occupation_id', 'ai_assistants.created_at')
            ->join('operations', 'operations.id', '=', 'ai_assistants.operation_id')
            ->join('occupations', 'occupations.id', '=', 'operations.occupation_id')
            ->join('categories', 'categories.id', '=', 'occupations.category_id')
            ->where('ai_assistants.is_public', true)
            ->where(function ($q) use ($search) {
                $q->where('ai_assistants.name', 'like', "%$search%");
            });
        $query->with(['operation:id,name', 'occupation:id,name']);
        if ($user) {
            $query->with(['favorites' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }])
                ->withExists(['favorites as is_favorited_by_user' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }])
            ;
        }

        if ($activeTab == 'operation') {
            if ($user) {
                $query->whereExists(function ($subQuery) use ($user) {
                    $subQuery->select(DB::raw(1))
                        ->from('assistant_favorites')
                        ->whereRaw('assistant_favorites.ai_assistant_id = ai_assistants.id')
                        ->where('assistant_favorites.user_id', $user->id);
                });
            }
        }

        if ($activeTab == 'occupation') {
            $query->orderBy('ai_assistants.name', 'desc');
        }

        $aiAssistants = $query->orderBy('ai_assistants.id', 'desc')
            ->paginate($limit)
            ->appends(['activeTab' => $activeTab]);

        return response()->json($aiAssistants);
    }

    public function loadMore(Request $request)
    {
        $search = $request->input('search', '');
        $offset = $request->input('offset', 18);
        $type = $request->input('type');
        $user = $request->user();

        $query = AiAssistant::select('id', 'name', 'description', 'type', 'thumbnail', 'operation_id', 'occupation_id', 'created_at');
        $query->with(['operation:id,name', 'occupation:id,name'])->where('ai_assistants.is_public', true);
        if ($user) {
            $query->with(['favorites' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }]);

            if ($type == 1) {
                $query->whereHas('operation', function ($q) use ($user) {
                    $q->whereIn('id', function ($subquery) use ($user) {
                        $subquery->select('operation_id')
                            ->from('user_focus_operations')
                            ->where('user_id', $user->id);
                    });
                });
            }
        }

        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%");
        });
        $aiAssistants = $query->orderBy('id', 'desc')
            ->offset($offset)
            ->limit(18)
            ->get();

        return response()->json($aiAssistants);
    }

    public function favorite(Request $request)
    {
        $aiAssistantId = $request->aiAssistantId;

        $favorite = $request->user()->favorites()->where('ai_assistant_id', $aiAssistantId)->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['message' => 'Removed from favorites']);
        }

        $request->user()->favorites()->create([
            'ai_assistant_id' => $aiAssistantId,
        ]);

        return response()->json(['message' => 'Added to favorites']);
    }

    public function updateEnterpriseField(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'field_ids' => 'required|array',
            'first_login' => 'required',
        ]);

        if ($user) {
            if ($user instanceof \App\Models\User) {
                $user->categories()->sync($request->field_ids);
                $user->first_login = $request->first_login;
                $user->save();
            } else {
                return response()->json(['message' => 'Không tìm thấy người dùng.'], 404);
            }
        } else {
            return response()->json(['message' => 'Người dùng chưa được xác thực.'], 401);
        }

        return response()->json(['message' => 'Cập nhật thành công!']);
    }

    public function searchAssistants(Request $request)
    {
        $search = $request->search;
        $occupation_id = $request->occupation_id ?? '';
        $query = AiAssistant::select('id', 'name', 'description', 'type', 'thumbnail')->where('ai_assistants.is_public', true);

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }
        if (!empty($occupation_id)) {
            $query->where('occupation_id', $occupation_id);
        }

        $assistants = $query->limit(3)->get();

        return response()->json($assistants);
    }

    public function searchOccupations(Request $request)
    {
        $search = $request->search;

        $query = Occupation::select('id', 'name', 'description', 'image');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $occupation = $query->limit(3)->get();

        return response()->json($occupation);
    }
}
