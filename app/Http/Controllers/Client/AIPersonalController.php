<?php

namespace App\Http\Controllers\Client;

use App\Exceptions\AjaxException;
use App\Exceptions\FacebookApiServiceException;
use App\Http\Controllers\Controller;
use App\Models\AIGeneratedMedia;
use App\Services\CalendarService;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class AIPersonalController extends Controller
{

    public function __construct()
    {
    }
    public function index()
    {
        return Inertia::render('Client/AIPersonal/Index');
    }

    public function getListMedia(Request $request) {
        $params = $request->all();
        $validator = Validator::make($params, [
            'type' => 'required|string',
            'model' => 'required|string',
        ], [
            'type.required' => 'Loại dữ liệu phải là ảnh hoặc video',
            'model.required' => 'Mô hình là bắt buộc',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
    
        $query = AIGeneratedMedia::where('user_id', auth('web')->id());
    
        if ($params['type'] === '*' || $params['type'] === 'all') {
            $query->whereIn('type', ['image', 'video']);
        } else {
            $query->where('type', $params['type']);
        }
    
        if (!($params['model'] === '*' || $params['model'] === 'all')) {
            $query->where('model', $params['model']);
        }
    
        $perPage = $request->input('per_page', 1000);
        $mediaList = $query->paginate($perPage);
    
        return response()->json([
            'success' => true,
            'data' => $mediaList,
        ]);
    }    
}
