<?php

namespace App\Repositories;

use App\Models\Occupation;
use App\Models\Operation;
use Illuminate\Support\Facades\DB;

class JobRepository
{
    public function getAllOccupations()
    {
        $occupations = Occupation::select('occupations.id', 'occupations.name')
            ->leftJoin('operations', function($join) {
                $join->on('occupations.id', '=', 'operations.occupation_id')
                    ->whereNull('operations.deleted_at');
            })
            ->leftJoin('ai_assistants', function($join) {
                $join->on('occupations.id', '=', 'ai_assistants.occupation_id')
                    ->whereNull('ai_assistants.deleted_at');
            })
            ->whereNull('occupations.deleted_at')
            ->groupBy('occupations.id', 'occupations.name')
            ->selectRaw('COUNT(DISTINCT operations.id) as operations_count')
            ->selectRaw('COUNT(DISTINCT CASE WHEN ai_assistants.is_public = true THEN ai_assistants.id END) as ais_count')
            ->limit(12)
            ->get();
        return $occupations;
    }

    public function getAllOccupationsMore($offset = 0, $search = '')
    {
        $occupations = Occupation::select('occupations.id', 'occupations.name')
            ->leftJoin('operations', function($join) {
                $join->on('occupations.id', '=', 'operations.occupation_id')
                    ->whereNull('operations.deleted_at');
            })
            ->leftJoin('ai_assistants', function($join) {
                $join->on('occupations.id', '=', 'ai_assistants.occupation_id')
                    ->whereNull('ai_assistants.deleted_at');
            })
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->whereNull('occupations.deleted_at')
            ->groupBy('occupations.id', 'occupations.name')
            ->selectRaw('COUNT(DISTINCT operations.id) as operations_count')
            ->selectRaw('COUNT(DISTINCT CASE WHEN ai_assistants.is_public = true THEN ai_assistants.id END) as ais_count')
            ->offset($offset)
            ->limit(12)
            ->get();
        return $occupations;
    }

    public function getOccupationById($id)
    {
        return Occupation::select('id', 'name', 'description', 'image')
            ->withCount('operations')
            ->withCount(['ais' => function($query) {
                $query->where('is_public', true);
            }])
            ->findOrFail($id);
    }

    public function getOperationsByOccupationId($occupationId, $offset = 0, $search = '')
    {
        return Operation::where('occupation_id', $occupationId)
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->select('id', 'name')
            ->withCount(['ais' => function($query) {
                $query->where('is_public', true);
            }])
            ->offset($offset)
            ->orderBy('ais_count', 'desc') // Sắp xếp theo số lượng ais trước
            ->orderBy('created_at', 'desc') // Sau đó sắp xếp theo created_at
            ->limit(12)
            ->get();
    }

    public function getRelatedOccupations($occupationId, $limit = 12)
    {
        $occupation = Occupation::findOrFail($occupationId);
        return Occupation::where('category_id', $occupation->category_id)
            ->where('id', '!=', $occupationId)
            ->withCount('operations')
            ->withCount(['ais' => function($query) {
                $query->where('is_public', true);
            }])
            ->limit($limit)
            ->get(['id', 'name', 'image']);
    }

    public function searchOccupations($search, $limit = 12)
    {
        return Occupation::select('occupations.id', 'occupations.name')
            ->leftJoin('operations', function($join) {
                $join->on('occupations.id', '=', 'operations.occupation_id')
                    ->whereNull('operations.deleted_at');
            })
            ->leftJoin('ai_assistants', function($join) {
                $join->on('occupations.id', '=', 'ai_assistants.occupation_id')
                    ->whereNull('ai_assistants.deleted_at');
            })
            ->whereNull('occupations.deleted_at')
            ->when($search, function ($query, $search) {
                return $query->where('occupations.name', 'like', "%{$search}%");
            })
            ->groupBy('occupations.id', 'occupations.name')
            ->selectRaw('COUNT(DISTINCT operations.id) as operations_count')
            ->selectRaw('COUNT(DISTINCT ai_assistants.id) as ais_count')
            ->orderBy('occupations.created_at', 'desc')
            ->paginate($limit);
    }
}
