<?php

namespace App\Repositories;

use App\Models\AffKey;

class AffKeyRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return AffKey::class;
    }

    public function exists($filters)
    {
        return $this->model->where($filters)->exists();
    }

    public function updateConfigAffId($filters)
    {
        $this->model->where('active', 1)
            ->where('destination', 'San Diego')
            ->update(['delayed' => 1]);

        return $this->model->where($filters)->exists();
    }

    public function getKeyById($id) {
        return  $this->model
            ->leftjoin('users', 'users.id', '=', 'aff_keys.user_id')
            ->selectRaw('
                aff_keys.id as key_id,
                aff_keys.key,
                aff_keys.config_aff_id,
                aff_keys.is_used,
                DATE_FORMAT(aff_keys.created_at, "%d/%m/%Y %H:%i:%s") as created_at_formatted,
                users.email,
                users.name as user_name
            ')
            ->where('aff_keys.id', $id)->first();

    }


    public function getKeyQuery(array $params = [])
    {
        $email = $params['email'] ?? '';
        $key = $params['key'] ?? '';
        $package = $params['package'] ?? '';
        $status = $params['status'] ?? '';
        $agent = $params['agent'] ?? '';
        $startDate = $params['startDate'] ?? '';
        $endDate = $params['endDate'] ?? '';

        // Xây dựng query chung để tái sử dụng
        $query = $this->model
            ->leftJoin('users', 'users.id', '=', 'aff_keys.user_id')
            ->leftJoin('config_aff', 'config_aff.id', '=', 'aff_keys.config_aff_id')
            ->leftJoin('agents', 'agents.id', '=', 'aff_keys.agent_id')
            ->selectRaw('
            aff_keys.id as key_id,
            aff_keys.key,
            aff_keys.is_used,
            aff_keys.config_aff_id,
            DATE_FORMAT(aff_keys.created_at, "%d/%m/%Y %H:%i:%s") as created_at_formatted,
            users.email,
            users.name as user_name,
            config_aff.name as config_aff_name,
            agents.coupon_parent,
            agents.code as agent_code,
            agents.name as agent_name
        ')
            ->when($email, function ($query) use ($email) {
                $query->where('users.email', 'like', "%{$email}%");
            })
            ->when($key, function ($query) use ($key) {
                $query->where('aff_keys.key', 'like', "%{$key}%");
            })
            ->when($package, function ($query) use ($package) {
                $query->where('config_aff.id', $package);
            })
            ->when($agent, function ($query) use ($agent) {
                $query->where('aff_keys.agent_id', $agent);
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('aff_keys.is_used', $status);
            })
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('aff_keys.created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereDate('aff_keys.created_at', '<=', $endDate);
            })
            ->whereNotNull('aff_keys.key')
            ->where('aff_keys.key', '!=', '');

        return $query;
    }

    public function paginateKeys(array $params = [])
    {
        $query = $this->getKeyQuery($params);
        $appends = [
            'email' => $params['email'] ?? '',
            'key' => $params['key'] ?? '',
            'package' => $params['package'] ?? '',
            'status' => $params['status'] ?? '',
            'agent' => $params['agent'] ?? '',
            'startDate' => $params['startDate'] ?? '',
            'endDate' => $params['endDate'] ?? '',
        ];

        return $query
            ->orderBy('aff_keys.is_used', 'asc')
            ->orderBy('aff_keys.id', 'desc')
            ->paginate()
            ->appends($appends);
    }

    public function getFilteredKeys(array $params = [])
    {
        $query = $this->getKeyQuery($params);
        return $query->orderBy('aff_keys.id', 'desc')->get();
    }



}
