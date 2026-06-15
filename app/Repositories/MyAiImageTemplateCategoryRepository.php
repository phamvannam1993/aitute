<?php

namespace App\Repositories;

use App\Models\MyAIImageTemplateCategory;

class MyAiImageTemplateCategoryRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return MyAIImageTemplateCategory::class;
    }

    public function paginateImageTemplateCategories(array $params)
    {
        $appends = array_filter([
            'search' => $params['search'] ?? null
        ]);

        $query =  $this->model
            ->select(['id', 'title']);

        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->like('title', $params['search']);
            });
        }

        if (!empty($params['my_ai_image_collection_id'])) {
            $query->where('my_ai_image_collection_id', $params['my_ai_image_collection_id']);
        }

        $query->orderBy('order')
            ->orderByDesc('id');

        return $query
            ->paginate()
            ->appends($appends);
    }
}
