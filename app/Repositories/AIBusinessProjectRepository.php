<?php

namespace App\Repositories;

use App\Models\AIBusinessProject;

class AIBusinessProjectRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return AIBusinessProject::class;
    }

    public function getCount($userId)
    {
        $record = $this->model->where('user_id', $userId)
            ->selectRaw("
                SUM(count) as total
            ")
            ->groupBy('user_id')
            ->first();

        return empty($record->total) ? 0 : $record->total;
    }
}
