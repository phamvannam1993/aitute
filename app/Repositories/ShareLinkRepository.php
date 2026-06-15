<?php

namespace App\Repositories;

use App\Models\ShareLink;

class ShareLinkRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return ShareLink::class;
    }
}
