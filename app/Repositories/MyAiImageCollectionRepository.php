<?php

namespace App\Repositories;

use App\Models\MyAIImageCollection;

class MyAiImageCollectionRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return MyAIImageCollection::class;
    }

    public function paginateCollections(array $params)
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

        $query->orderByDesc('id');

        return $query
            ->paginate()
            ->appends($appends);
    }

    public function all()
    {
        return $this->model->all();
    }
}
