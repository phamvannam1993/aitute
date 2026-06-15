<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

class ActivityLogRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return Activity::class;
    }

    public function paginateActivityLogs(array $params)
    {
        $appends = [
            'search' => $params['search'] ?? '',
        ];

        $list =  $this->model->select([
            'activity_log.*',
            'users.email as subject_email',
            'admins.email as causer_email'
        ])
            ->leftJoin('users', function ($join) {
                $join->on('activity_log.subject_id', '=', 'users.id')
                    ->where('activity_log.subject_type', '=', User::class);
            })
            ->leftJoin('admins', function ($join) {
                $join->on('activity_log.causer_id', '=', 'admins.id')
                    ->where('activity_log.causer_type', '=', Admin::class);
            });

        if (isset($params['subject_id'])) {
            $list =  $list->where('subject_id', $params['subject_id']);
        }

        $list =  $list
            ->orderBy('activity_log.id', 'desc')
            ->paginate()
            ->appends($appends);

        return $list;
    }
}
