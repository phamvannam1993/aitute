<?php

namespace App\Repositories;

use App\Models\AIAssistantHistories;

class AIAssistantHistoriesRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return AIAssistantHistories::class;
    }

    public function update($id, array $data)
    {
        $assistant = AIAssistantHistories::findOrFail($id);
        $assistant->update($data);
        return $assistant;
    }

    
}
