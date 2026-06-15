<?php

namespace App\Repositories;

use App\Models\CreditHistory;

class CreditHistoryRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return CreditHistory::class;
    }

    public function getListCreditHistory($per_page, $userId) {
        $result = CreditHistory::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($per_page);
        return $result;
    }

    public function create($data = []) {
        return CreditHistory::create($data);
    }
}
