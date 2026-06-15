<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MyAiImageTemplateCategory\StoreMyAiImageTemplateCategoryRequest;
use App\Http\Requests\MyAiImageTemplateCategory\UpdateMyAiImageTemplateCategoryRequest;
use App\Models\MyAIImageTemplateCategory;
use App\Services\MyAiImageCollectionService;
use App\Services\MyAIImageTemplateCategoryService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminMyAiImageTemplateCategoryController extends Controller
{
    public function __construct(
        private MyAIImageTemplateCategoryService $myAIImageTemplateCategoryService,
        private MyAiImageCollectionService $myAiImageCollectionService,
    ) {}

    public function index(Request $request)
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('my-ai-image-template-categories.index')) {
            return to_route('admin.errors.403');
        }

        $paginateImageTemplateCategories = $this->myAIImageTemplateCategoryService->paginateImageTemplateCategories($request->all());

        $params = [
            'paginateImageTemplateCategories' => $paginateImageTemplateCategories,
        ];

        return Inertia::render('Admin/MyAiImageTemplateCategory/Index', $params);
    }

    public function create()
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('my-ai-image-template-categories.create')) {
            return to_route('admin.errors.403');
        }

        $imageTemplateCategory = new MyAIImageTemplateCategory;
        $imageCollections = $this->myAiImageCollectionService->all();

        $params = [
            'imageTemplateCategory' => $imageTemplateCategory,
            'imageCollections' => $imageCollections,
        ];
        return Inertia::render('Admin/MyAiImageTemplateCategory/Create', $params);
    }

    public function store(StoreMyAiImageTemplateCategoryRequest $request)
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('my-ai-image-template-categories.create')) {
            return to_route('admin.errors.403');
        }

        try {
            $this->myAIImageTemplateCategoryService->store($request->validated());
            return to_route('admin.my-ai-image-template-categories.index');
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->withErrors([
                'message' => 'Lỗi tạo dữ liệu: ' . $e->getMessage(),
            ]);
        }
    }

    public function update(UpdateMyAiImageTemplateCategoryRequest $request, $id)
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('my-ai-image-template-categories.edit')) {
            return to_route('admin.errors.403');
        }

        try {
            $this->myAIImageTemplateCategoryService->update($request->validated(), $id);
            return to_route('admin.my-ai-image-template-categories.edit', ['my_ai_image_template_category' => $id]);
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
        if ($admin->cannot('my-ai-image-template-categories.edit')) {
            return to_route('admin.errors.403');
        }

        $imageTemplateCategory = $this->myAIImageTemplateCategoryService->getImageTemplateCategoryById($id);
        $imageCollections = $this->myAiImageCollectionService->all();

        $params = [
            'imageTemplateCategory' => $imageTemplateCategory,
            'imageCollections' => $imageCollections,
        ];
        return Inertia::render('Admin/MyAiImageTemplateCategory/Edit', $params);
    }

    public function destroy($id)
    {
        $admin = auth('admin')->user();
        if ($admin->cannot('my-ai-image-template-categories.destroy')) {
            return to_route('admin.errors.403');
        }

        try {
            $this->myAIImageTemplateCategoryService->destroy($id);
            return to_route('admin.my-ai-image-template-categories.index');
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->withErrors([
                'message' => 'Lỗi xóa dữ liệu: ' . $e->getMessage(),
            ]);
        }
    }
}
