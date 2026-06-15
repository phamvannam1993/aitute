<?php

namespace App\Services;

use App\Exceptions\DomainException;
use App\Models\MyAIImageTemplateCategory;
use App\Repositories\MyAiImageTemplateCategoryRepository;
use App\Repositories\MyAIImageTemplateRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class MyAIImageTemplateCategoryService
{
    public function __construct(
        private MyAIImageTemplateService $myAIImageTemplateService,
        private MyAiImageTemplateCategoryRepository $myAiImageTemplateCategoryRepository,
        private MyAIImageTemplateRepository $myAIImageTemplateRepository,
    ) {}

    public function paginateImageTemplateCategories(array $params)
    {
        $paginateImageTemplateCategories = $this->myAiImageTemplateCategoryRepository->paginateImageTemplateCategories($params);
        return $paginateImageTemplateCategories;
    }

    public function store(array $params): MyAIImageTemplateCategory
    {
        try {
            DB::beginTransaction();
            $data = [
                'title' => $params['title'],
                'my_ai_image_collection_id' => $params['my_ai_image_collection_id'],
                'slug' =>  str($params['title'])->slug(),
                'order' => $params['order'],
            ];
            $imageTemplateCategory = $this->myAiImageTemplateCategoryRepository->create($data);

            if (!empty($params['images']) && $imageTemplateCategory) {
                $this->myAIImageTemplateService->insert($params['images'], $imageTemplateCategory->id);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw  $e;
        }

        return $imageTemplateCategory;
    }

    public function getImageTemplateCategoryById(int $id)
    {
        $imageTemplateCategory = $this->myAiImageTemplateCategoryRepository->find($id);
        $imageTemplateCategory->load(['templates' => function ($query) {
            $query->orderBy('id', 'desc');
        }]);
        if (!$imageTemplateCategory) {
            throw new DomainException('Không tìm thấy dữ liệu', Response::HTTP_NOT_FOUND);
        }

        return $imageTemplateCategory;
    }

    public function update(array $params, int $id): MyAIImageTemplateCategory
    {
        $imageTemplateCategory = $this->myAiImageTemplateCategoryRepository->find($id);
        if (!$imageTemplateCategory) {
            throw new DomainException('Không tìm thấy dữ liệu', Response::HTTP_NOT_FOUND);
        }

        try {
            DB::beginTransaction();
            $data = [
                'title' => $params['title'],
                'my_ai_image_collection_id' => $params['my_ai_image_collection_id'],
                'slug' =>  str($params['title'])->slug(),
                'order' => $params['order'],
            ];
            $imageTemplateCategory->update($data);

            if (!empty($params['images']) && $imageTemplateCategory) {
                $this->myAIImageTemplateService->insert($params['images'], $id);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw  $e;
        }

        return $imageTemplateCategory;
    }

    public function destroy(int $id): bool
    {
        $imageTemplateCategory = $this->myAiImageTemplateCategoryRepository->find($id);
        if (!$imageTemplateCategory) {
            throw new DomainException('Không tìm thấy dữ liệu', Response::HTTP_NOT_FOUND);
        }

        return  $imageTemplateCategory->delete();
    }
}
