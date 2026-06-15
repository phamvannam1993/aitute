<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiAssistant;
use App\Models\Occupation;
use App\Models\Operation;
use App\Services\AiAssistantService;
use App\Services\OccupationService;
use App\Services\OperationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OperationController extends Controller
{
    protected $operationService;
    protected $AiAssistantService;
    public function __construct(OperationService $operationService, AiAssistantService $AiAssistantService)
    {
        $this->operationService = $operationService;
        $this->AiAssistantService = $AiAssistantService;
    }

    public function index(Request $request)
    {
        $userId = auth('admin')->id();
        $operations = $this->operationService->getOperations($request->all(), $userId);
        return Inertia::render('Admin/Operation/Index', [
            'operations' => $operations,
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Operation/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'occupation_id' => 'required|integer|exists:occupations,id',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB
        ], [
            'occupation_id.required' => 'Ngành nghề  là bắt buộc.',
            'occupation_id.exists' => 'Ngành nghề không tồn tại.',
            'name.required' => 'Tên là bắt buộc.',
            'image.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif.',
            'image.max' => 'Ảnh không được lớn hơn 10MB.',
        ]);

        try {
            $this->operationService->create($request->only(['name', 'description', 'occupation_id']), $request);
            return redirect()->route('admin.operation.index')->withSuccess("Nghiệp vụ đã được tạo thành công.");
        } catch (\Exception $e) {
            \Log::error('Lỗi khi tạo Nghiệp vụ:', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors('Đã có lỗi xảy ra khi tạo Nghiệp vụ.');
        }
    }

    public function update(Request $request, $id)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'occupation_id' => 'required|integer|exists:occupations,id',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',  // 10MB
        ], [
            'occupation_id.required' => 'Occupation ID là bắt buộc.',
            'occupation_id.exists' => 'Occupation ID không tồn tại.',
            'name.required' => 'Tên là bắt buộc.',
            'image.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif.',
            'image.max' => 'Ảnh không được lớn hơn 10MB.',
            'image.image' => 'Trường hình ảnh phải là file hình ảnh.',
        ]);
        try {
            $this->operationService->updateOperation($id, $request);
            return redirect()->route('admin.operation.index')->with('success', 'Nghiệp vụ ngành nghề thành công!');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.operation.index')->withErrors('Nghiệp vụ không tồn tại.');
        }
    }

    public function edit($id)
    {
        $operation = $this->operationService->getOperationById($id);
        return response()->json([
            'operation' => $operation
        ]);
    }


    public function getOperations(Request $request)
    {
        $query = Operation::selectRaw('id, name, description, image')
            ->where('user_id',  auth('admin')->id());

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->has('occupation_id')) {
            $query->where('occupation_id', $request->input('occupation_id'));
        }

        return $query->paginate(10); // Trả về 10 mục mỗi lần
    }


    public function listAis($id)
    {
        $listAis = $this->AiAssistantService->listAis($id);
        return response()->json([
            'listAis' => $listAis
        ]);
    }

    public function destroy($id)
    {
        $operation = Operation::findOrFail($id);
        $operation->ais()->delete();
        $operation->delete();
        return response()->json([
            'success' => true,
            'message' => 'Xóa thành công!!'
        ]);
    }
}
