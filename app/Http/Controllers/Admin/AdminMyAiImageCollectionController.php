<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MyAiImageCollection\StoreMyAiImageCollectionRequest;
use App\Http\Requests\MyAiImageCollection\UpdateMyAiImageCollectionRequest;
use App\Models\MyAIImageCollection;
use App\Services\MyAiImageCollectionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminMyAiImageCollectionController extends Controller
{
    public function __construct(
        private MyAiImageCollectionService $myAiImageCollectionService,
    ) {}

    public function index(Request $request)
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('my-ai-image-collections.index')) {
            return to_route('admin.errors.403');
        }

        $paginateCollections = $this->myAiImageCollectionService->paginateCollections($request->all());

        $params = [
            'paginateCollections' => $paginateCollections,
        ];

        return Inertia::render('Admin/MyAiImageCollection/Index', $params);
    }

    public function create()
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('my-ai-image-collections.create')) {
            return to_route('admin.errors.403');
        }

        $imageCollection = new MyAIImageCollection;
        $params = [
            'imageCollection' => $imageCollection,
        ];
        return Inertia::render('Admin/MyAiImageCollection/Create', $params);
    }

    public function store(StoreMyAiImageCollectionRequest $request)
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('my-ai-image-collections.create')) {
            return to_route('admin.errors.403');
        }

        try {
            $imageCollection = $this->myAiImageCollectionService->store($request->validated());
            return to_route('admin.my-ai-image-collections.show', ['my_ai_image_collection' => $imageCollection->id]);
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->withErrors([
                'message' => 'Lỗi tạo người dùng: ' . $e->getMessage(),
            ]);
        }
    }

    public function update(UpdateMyAiImageCollectionRequest $request, $id)
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('my-ai-image-collections.edit')) {
            return to_route('admin.errors.403');
        }

        try {
            $this->myAiImageCollectionService->update($request->validated(), $id);
            return to_route('admin.my-ai-image-collections.edit', $id);
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->withErrors([
                'message' => 'Lỗi cập nhật: ' . $e->getMessage(),
            ]);
        }
    }

    public function edit($id)
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('my-ai-image-collections.edit')) {
            return to_route('admin.errors.403');
        }

        $imageCollection = $this->myAiImageCollectionService->getImageCollectionById([], $id);

        $params = [
            'imageCollection' => $imageCollection['imageCollection'],
        ];

        return Inertia::render('Admin/MyAiImageCollection/Edit', $params);
    }

    public function destroy($id)
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('my-ai-image-collections.destroy')) {
            return to_route('admin.errors.403');
        }

        try {
            $this->myAiImageCollectionService->destroy($id);
            return to_route('admin.my-ai-image-collections.index');
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->withErrors([
                'message' => 'Lỗi xóa dữ liệu: ' . $e->getMessage(),
            ]);
        }
    }

    public function show(Request $request, int $id)
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('my-ai-image-collections.show')) {
            return to_route('admin.errors.403');
        }

        $imageCollection = $this->myAiImageCollectionService->getImageCollectionById($request->all(), $id);

        $params = [
            'imageCollection' => $imageCollection['imageCollection'],
        ];
        return Inertia::render('Admin/MyAiImageCollection/Show', $params);
    }
}
