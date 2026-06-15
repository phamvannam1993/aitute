<?php

namespace App\Repositories;

use App\Models\UserNoteGoogleSearch;

class UserNoteGoogleSearchRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return UserNoteGoogleSearch::class;
    }
}
