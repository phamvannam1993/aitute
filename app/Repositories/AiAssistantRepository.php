<?php

namespace App\Repositories;

use App\Models\AiAssistant;
use App\Models\Operation;
use Illuminate\Support\Facades\DB;

class AiAssistantRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return AiAssistant::class;
    }

    public function getAssistants(array $filters, int $userId)
    {
        $query = AiAssistant::selectRaw('ai_assistants.id, ai_assistants.admin_id, ai_assistants.name,ai_assistants.type, ai_assistants.description,ai_assistants.is_public,
            ai_assistants.thumbnail, occupations.name as occupation_name, operations.name as operation_name')
            ->join('occupations', 'occupations.id', '=', 'ai_assistants.occupation_id')
            ->join('operations', 'operations.id', '=', 'ai_assistants.operation_id')
            ->where('ai_assistants.admin_id', $userId);

        // Nếu có tìm kiếm theo tên
        if (!empty($filters['search'])) {
            $query->where('ai_assistants.name', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['public'])) {
            $public = filter_var($filters['public'], FILTER_VALIDATE_BOOLEAN);
            $query->where('ai_assistants.is_public', $public);
        }

        if (!empty($filters['occupation_ids'])) {
            $query->whereIn('ai_assistants.occupation_id', $filters['occupation_ids']);
        }


        // Xử lý sắp xếp
        $sort = $filters['sort'] ?? 'name';
        $direction = $filters['direction'] ?? 'asc';
        if ($sort) {
            $query->orderBy('ai_assistants.name', $direction)
                ->orderBy('ai_assistants.created_at', 'desc');
        } else {
            $query->orderBy('ai_assistants.created_at', 'desc');
            $query->orderBy('ai_assistants.name', 'asc');
        }

        $page = $filters['page'] ?? 1;
        $limit = config('constants.pagination.items_per_page');

        $totalRecords = $limit * $page;
        $totalOperationCount = $query->count();
        $query->limit($totalRecords);
        $ai_assistants = $query->get();

        $ai_assistants = $ai_assistants->map(function ($assistant) {
            $assistant->thumbnail_url = !empty($assistant->thumbnail) ? (new Operation)->getUrl($assistant->thumbnail) : null;
            return $assistant;
        });
        // Lấy query string từ filters
        $queryString = http_build_query(array_filter($filters, function ($key) {
            return $key !== 'page';
        }, ARRAY_FILTER_USE_KEY));

        // Kiểm tra và tạo URL cho next_page_url và previous_page_url
        $nextPageUrl = null;
        if ($totalRecords < $totalOperationCount) { // Nếu tổng bản ghi đã lấy nhỏ hơn tổng số bản ghi
            $nextPage = $page + 1;
            $nextPageUrl = url()->current() . '?' . $queryString . '&page=' . $nextPage;
        }

        $previousPageUrl = null;
        if ($page > 1) { // Kiểm tra nếu có trang trước
            $prevPage = $page - 1;
            $previousPageUrl = url()->current() . '?' . $queryString . '&page=' . $prevPage;
        }

        // Debug query log để kiểm tra truy vấn
        return [
            'data' => $ai_assistants,
            'current_page' => $page,
            'next_page_url' => $nextPageUrl,
            'previous_page_url' => $previousPageUrl,
        ];
    }

    public function findByIdAndUser($id, $userId)
    {
        return AiAssistant::with(['occupation', 'operation', 'criteria'])
            ->where('id', $id)
            ->where('admin_id', $userId)
            ->firstOrFail();
    }

    public function update($id, array $data)
    {
        $assistant = AiAssistant::findOrFail($id);
        $assistant->update($data);
        return $assistant;
    }

    public function getListAiTop($userId) {
        // $topAiAssistants = AiAssistant::leftJoin('assistant_favorites', 'ai_assistants.id', '=', 'assistant_favorites.ai_assistant_id')
        //     ->leftJoin('operations', 'operations.id', '=', 'ai_assistants.operation_id')
        //     ->leftJoin('occupations', 'occupations.id', '=', 'operations.occupation_id')
        //     ->leftJoin('categories', 'categories.id', '=', 'occupations.category_id')
        //     ->select(
        //         'ai_assistants.id',
        //         'ai_assistants.name',
        //         'ai_assistants.type',
        //         'ai_assistants.description',
        //         'ai_assistants.thumbnail',
        //         'ai_assistants.operation_id',
        //         'categories.name as category_name',
        //         DB::raw('COUNT(assistant_favorites.ai_assistant_id) as like_count')
        //     )
        //     ->whereNull('ai_assistants.deleted_at')
        //     ->groupBy('ai_assistants.id', 'categories.name')
        //     ->orderBy('like_count', 'desc')
        //     ->limit(10)
        //     ->get();


        $topAiAssistants = AiAssistant::select('id', 'name', 'description', 'thumbnail', 'type', 'operation_id', 'occupation_id')
            ->with(['operation:id,name', 'occupation:id,name'])
            ->withCount('favorites')
            ->withExists(['favorites as is_favorited_by_user' => function($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->where('ai_assistants.is_public', true)
            ->orderBy('favorites_count', 'desc')
            ->limit(5)
            ->get();

        return $topAiAssistants;
    }

    public function getCount($userId) {
        return AiAssistant::where('admin_id', $userId)->count();
    }

    public function getListTimeLine() {
        $timeline = AiAssistant::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('ai_assistants.is_public', true)
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Lấy tất cả các bản ghi AI theo các mốc thời gian đã lấy
        $years = $timeline->pluck('year');
        $months = $timeline->pluck('month');
        $ais = AiAssistant::select(
                'id', 'name', 'description',
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month')
            )
            ->where('ai_assistants.is_public', true)
            ->whereIn(DB::raw('YEAR(created_at)'), $years)
            ->whereIn(DB::raw('MONTH(created_at)'), $months)
            ->orderBy('created_at', 'desc')
            ->get();
        $groupedAis = $ais->groupBy(function ($item) {
            return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
        });

        // Kết hợp lại timeline với các AI đã lấy theo từng mốc thời gian
        $finalTimeline = $timeline->map(function ($time) use ($groupedAis) {
            $key = $time->year . '-' . str_pad($time->month, 2, '0', STR_PAD_LEFT);
            return [
                'year' => $time->year,
                'month' => $time->month,
                'total' => $time->total,
                'ais' => $groupedAis->get($key, collect())
            ];
        });
        return $finalTimeline;
    }

    public function listAis($id)
    {
        return AiAssistant::select('id', 'name')
            ->where('ai_assistants.is_public', true)
            ->where('operation_id', $id)->get();
    }

    public function listAisByOccupationId($id)
    {
        return AiAssistant::select('id', 'name')
            ->where('ai_assistants.is_public', true)
            ->where('occupation_id', $id)->get();
    }


}
