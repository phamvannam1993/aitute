<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\VideoToTextHistory;use App\Models\VoiceType;

class VideoToTextHistoryRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return VideoToTextHistory::class;
    }

    public function getListByUserId($userId)
    {
        return VideoToTextHistory::where('user_id', $userId)->latest()->paginate(15);
    }
}
