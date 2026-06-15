<?php

namespace App\Services;

use App\Repositories\ActivityLogRepository;
use Carbon\CarbonInterval;
use DateInterval;
use Illuminate\Database\Eloquent\Model;


class ActivityLogService
{
    public function __construct(
        private ActivityLogRepository $activityLogRepository
    ) {}

    public function paginateActivityLogs(array $params)
    {
        $activityLogs = $this->activityLogRepository->paginateActivityLogs($params);

        return $activityLogs;
    }

    public function activityLog(Model  $model, array $changes, string $event)
    {
        $changes = $this->differenceBetweenTheOldAndNew($changes);
        
        if ($this->isLogEmpty($changes)) {
            return;
        }

        activity()
            ->causedBy(auth('admin')->user())
            ->performedOn($model)
            ->event($event)
            ->withProperties($changes)
            ->log($event);
    }

    private function differenceBetweenTheOldAndNew(array $changes): array
    {
        // Get difference between the old and new attributes.
        $changes['attributes'] = array_udiff_assoc(
            $changes['attributes'],
            $changes['old'],
            function ($new, $old) {
                // Strict check for php's weird behaviors
                if ($old === null || $new === null) {
                    return $new === $old ? 0 : 1;
                }

                // Handles Date interval comparisons since php cannot use spaceship
                // Operator to compare them and will throw ErrorException.
                if ($old instanceof DateInterval) {
                    return CarbonInterval::make($old)->equalTo($new) ? 0 : 1;
                } elseif ($new instanceof DateInterval) {
                    return CarbonInterval::make($new)->equalTo($old) ? 0 : 1;
                }

                return $new <=> $old;
            }
        );

        $changes['old'] = collect($changes['old'])
            ->only(array_keys($changes['attributes']))
            ->all();

        return $changes;
    }

    private function isLogEmpty(array $changes): bool
    {
        return empty($changes['attributes'] ?? []) && empty($changes['old'] ?? []);
    }
}
