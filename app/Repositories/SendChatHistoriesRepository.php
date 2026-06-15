<?php

namespace App\Repositories;

use App\Models\SendChatHistories;

class SendChatHistoriesRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return SendChatHistories::class;
    }
}
