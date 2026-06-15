<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Occupation;
use App\Models\Operation;
use App\Services\AiAssistantService;
use App\Services\OccupationService;
use App\Services\OperationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OccupationController extends Controller
{
    protected $occupationService;
    protected $operationService;
    protected $AiAssistantService;
    public function __construct(OccupationService $occupationService, OperationService $operationService, AiAssistantService $AiAssistantService)
    {
        $this->occupationService = $occupationService;
        $this->operationService = $operationService;
        $this->AiAssistantService = $AiAssistantService;
    }

    public function index(Request $request)
    {
        $userId = auth('admin')->id();
        $occupations = $this->occupationService->getOccupations($request->all(), $userId);
        $categories = Category::selectRaw('id, name')->get();
        return Inertia::render('Admin/Job/Index', [
            'occupations' => $occupations,
            'categories' => $categories,
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }

    public function create()
    {
        $categories = Category::selectRaw('id, name')->get();
        return Inertia::render('Admin/Job/Create', [
                'categories' => $categories
            ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|integer|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB
        ], [
            'category_id.required' => 'Lĩnh vực ngành nghề là bắt buộc.',
            'category_id.exists' => 'Lĩnh vực ngành nghề không tồn tại.',
            'name.required' => 'Tên ngành nghề là bắt buộc.',
            'image.image' => 'Trường hình ảnh phải là file hình ảnh.',
            'image.mimes' => 'File ảnh phải có định dạng: jpeg, png, jpg, gif.',
            'image.max' => 'File ảnh không được vượt quá 10MB.',
        ]);
        try {
            // Truyền request với các trường cần thiết
            $this->occupationService->createOccupation($request->only(['category_id', 'name', 'description']), $request);
            return redirect()->route('admin.job.index')->withSuccess("Ngành nghề đã được tạo thành công.");
        } catch (\Exception $e) {
            \Log::error('Lỗi khi tạo ngành nghề:', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors('Đã có lỗi xảy ra khi tạo ngành nghề.');
        }
    }

    public function edit($id)
    {
        $occupation = $this->occupationService->getOccupationById($id);
        return response()->json([
            'occupation' => $occupation
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|integer|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',  // 10MB
        ], [
            'name.required' => 'Tên ngành nghề là bắt buộc.',
            'category_id.required' => 'Lĩnh vực ngành nghề là bắt buộc.',
            'category_id.exists' => 'Lĩnh vực ngành nghề không tồn tại.',
            'image.image' => 'Trường hình ảnh phải là file hình ảnh.',
            'image.mimes' => 'File ảnh phải có định dạng: jpeg, png, jpg, gif.',
            'image.max' => 'File ảnh không được vượt quá 10MB.',
        ]);
        try {
            $this->occupationService->updateOccupation($id, $request);
            return redirect()->route('admin.job.index')->with('success', 'Cập nhật ngành nghề thành công!');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.job.index')->withErrors('Ngành nghề không tồn tại.');
        }
    }

    public function getOccupations(Request $request)
    {
        $query = Occupation::selectRaw('occupations.id, occupations.user_id ,occupations.category_id, occupations.name, occupations.description, occupations.image')
            ->leftjoin('operations', 'operations.occupation_id', '=', 'occupations.id')
            ->where('occupations.user_id',  auth('admin')->id());

        if ($request->has('search')) {
            $query->where('occupations.name', 'like', '%' . $request->input('search') . '%');
        }
        if ($request->has('operation_id')) {
            $query->where('operations.id', $request->input('operation_id'));
        }
        $query->groupBy('occupations.id');
        return $query->paginate(10);
    }

    public function getAllOccupations() {
        return $this->occupationService->getAllOccupations();
    }

    public function listOperationsAndAis($id) {
        $listOperation = $this->operationService->listOperation($id);
        $listAis = $this->AiAssistantService->listAisByOccupationId($id);
        return response()->json([
            'listOperation' => $listOperation,
            'listAis' => $listAis
        ]);
    }

    public function destroy($id)
    {
        $occupation = Occupation::findOrFail($id);
        $occupation->operations()->delete();
        $occupation->ais()->delete();
        $occupation->delete();
        return response()->json([
            'success' => true,
            'message' => 'Xóa thành công!!'
        ]);
    }
}
