<?php

namespace App\Repositories;

use App\Constants\Facebook as ConstantsFacebook;
use App\Models\Facebook;
use Carbon\Carbon;

class FacebookRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return Facebook::class;
    }

    public function getFacebookExpiredInNext30Days()
    {
        return $this->model
            ->where('user_access_token_expires_at', '<', Carbon::now()->addDays(ConstantsFacebook::DAYS_REMAINING_TO_EXPIRE))
            ->where('user_access_token_expires_at', '>', Carbon::now())
            ->orderBy('user_access_token_expires_at')
            ->get();
    }
}
