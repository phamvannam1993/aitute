<?php

namespace App\Repositories;

use App\Models\FacebookFanpage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FacebookFanpageRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return FacebookFanpage::class;
    }

    public function getFacebookFanpageOfUser(int $facebookFanpageId, int $userId): FacebookFanpage|null
    {
        $facebookFanpage =  $this->getModel()->select(
            DB::raw("facebook_fanpages.*"),
        )
            ->join('facebooks', function ($join) {
                $join->on('facebooks.id', '=', 'facebook_fanpages.facebook_id');
            })
            ->join('facebookables', function ($join) {
                $join->on('facebookables.facebook_id', '=', 'facebooks.id')
                    ->where('facebookables.facebookable_type', '=', User::class);
            })
            ->where(
                [
                    'facebook_fanpages.id' => $facebookFanpageId,
                    'facebookables.facebookable_id' => $userId,
                ]
            )
            ->first();

        return $facebookFanpage;
    }
}
