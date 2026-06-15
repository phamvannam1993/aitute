<?php

namespace App\Repositories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return User::class;
    }

    public function paginateUsers(array $params)
    {
        $appends = array_filter([
            'search' => $params['search'] ?? null,
            'time_remaining_until_expiration' => $params['time_remaining_until_expiration'] ?? null,
            'from_date' => $params['from_date'] ?? null,
            'to_date' => $params['to_date'] ?? null,
            'package_id' => $params['package_id'] ?? null,
            'is_gift' => $params['is_gift'] ?? null,
        ]);

        return $this->filterUser($params)
            ->paginate()
            ->appends($appends);
    }
    public function queryExportUsers(array $params)
    {
        return $this->filterUser($params);
    }

    private function filterUser(array $params)
    {
        $query = $this->model
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                'config_aff.name as config_aff_name',
                'config_aff.credit as package_price',
                'users.credit',
                'users.created_at',
                'users.vip_expired_at',
                'users.is_gift',
                'config_aff.price',
            ])
            ->join('aff_keys', function ($join) {
                $join->on(
                    'aff_keys.id',
                    '=',
                    DB::raw('(
                    SELECT aff_keys.id
                    FROM aff_keys
                    WHERE aff_keys.user_id = users.id AND aff_keys.is_used = 1
                    LIMIT 1
                    )')
                );
            })
            ->leftJoin('config_aff', 'aff_keys.config_aff_id', '=', 'config_aff.id');

        // Search
        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('users.name', 'like', '%' . $params['search'] . '%')
                    ->orWhere('users.email', 'like', '%' . $params['search'] . '%')
                    ->orWhere('users.phone', 'like', '%' . $params['search'] . '%');
            });
        }

        // Filter by date range
        if (!empty($params['from_date'])) {
            $fromDate = Carbon::parse($params['from_date'])->startOfDay();
            $query->whereDate('users.created_at', '>=', $fromDate);
        }

        if (!empty($params['to_date'])) {
            $toDate = Carbon::parse($params['to_date'])->endOfDay();
            $query->whereDate('users.created_at', '<=', $toDate);
        }

        // Filter by expiration
        if (!empty($params['time_remaining_until_expiration'])) {
            $now = now();
            $dateExpiration = now()->addDays((int)$params['time_remaining_until_expiration']);
            $query->whereBetween('users.vip_expired_at', [$now, $dateExpiration])
                ->orderBy('users.vip_expired_at', 'asc');
        }

        // Filter by package
        if (!empty($params['package_id'])) {
            $query->where('config_aff.id', $params['package_id']);
        }

        // Filter by gift status
        if (isset($params['is_gift'])) {
            $query->where('users.is_gift', $params['is_gift']);
        }

        $query->orderByDesc('users.id');

        return $query;
    }
    public function calculateTotalRevenue($params)
    {
        $query = $this->filterUser($params);
        return $query->sum('config_aff.price');
    }

    public function findUserByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }
}
