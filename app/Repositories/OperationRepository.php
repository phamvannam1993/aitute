<?php

namespace App\Repositories;

use App\Models\AiAssistant;
use App\Models\Interest;
use App\Models\Occupation;
use App\Models\Operation;
use Illuminate\Support\Facades\DB;

class OperationRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return Operation::class;
    }

    /**
     * Tìm kiếm, sắp xếp, phân trang ngành nghề theo điều kiện.
     *
     * @param array $filters
     * @param int $userId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getOperations(array $filters, int $userId)
    {
        $query = Operation::selectRaw('operations.id, operations.user_id , operations.name, operations.description,
            operations.image, occupations.name as occupation_name')
            ->join('occupations', 'operations.occupation_id', '=', 'occupations.id')
            ->where('operations.user_id', $userId);

        // Nếu có tìm kiếm theo tên
        if (!empty($filters['search'])) {
            $query->where('operations.name', 'like', '%' . $filters['search'] . '%');
        }

        // Xử lý sắp xếp
        $sort = $filters['sort'] ?? 'name';
        $direction = $filters['direction'] ?? 'asc';
        if ($sort) {
            $query->orderBy('operations.name', $direction)
                ->orderBy('operations.created_at', 'desc');
        } else {
            $query->orderBy('operations.name', 'asc');
            $query->orderBy('operations.created_at', 'desc');
        }

        // Xử lý phân trang (limit và offset)
        $page = $filters['page'] ?? 1; // Lấy số trang, mặc định là 1 nếu không có
        $limit = config('constants.pagination.items_per_page'); // Số bản ghi mỗi trang

        // Tính tổng số bản ghi cần lấy (record của trang 1 đến trang hiện tại)
        $totalRecords = $limit * $page; // Lấy cả trang hiện tại và các trang trước đó
        // Lấy tổng số bản ghi phù hợp với điều kiện trước khi áp dụng limit
        $totalOperationCount = $query->count();

        // Không sử dụng offset, luôn lấy từ bản ghi đầu tiên
        $query->limit($totalRecords); // Lấy tất cả bản ghi từ trang 1 đến trang hiện tại
        $operations = $query->get(); // Sử dụng `get()` để lấy tất cả bản ghi mà không có phân trang tự động

        // Sau khi lấy dữ liệu từ database, thêm thuộc tính `image_url` thủ công
        $operations = $operations->map(function ($operation) {
            $operation->image_url = !empty($operation->image) ? (new Operation)->getUrl($operation->image) : null;
            return $operation;
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
            'data' => $operations,
            'current_page' => $page,
            'next_page_url' => $nextPageUrl,
            'previous_page_url' => $previousPageUrl,
        ];
    }

    // Hàm tìm kiếm occupation theo id và user_id
    public function findByIdAndUser($id, $userId)
    {
        return Operation::with('occupation')->where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    public function findById($id)
    {
        return Operation::with('occupation')->where('id', $id)
            ->firstOrFail();
    }

    // Phương thức cập nhật Operation
    public function update($id, array $data)
    {
        $operation = Operation::findOrFail($id);
        $operation->update($data);
        return $operation;
    }

    public function getRelatedOperations($occupationId, $id, $limit = 12)
    {
        return Operation::with('occupation')->where('occupation_id', $occupationId)
            ->where('id', '!=', $id)
            ->limit($limit)
            ->get(['id', 'name', 'image', 'occupation_id']);
    }

    public function getAllOperations()
    {
        $userId = auth()->id();
        $operations = Operation::select('operations.id', 'operations.name', 'interests.is_interested')
            ->leftJoin('interests', function ($join) use ($userId) {
                $join->on('operations.id', '=', 'interests.operation_id')
                    ->where('interests.user_id', '=', $userId);
            })
            ->orderBy('operations.created_at', 'desc')
            ->limit(12)
            ->get();

        $operationIds = $operations->pluck('id');
        $interestsCount = Interest::select('operation_id', DB::raw('COUNT(*) as num_of_interests'))
            ->whereIn('operation_id', $operationIds)
            ->where('is_interested', 1)
            ->groupBy('operation_id')
            ->get();

        $operations = $operations->map(function ($operation) use ($interestsCount) {
            // Gắn số người quan tâm
            $operation->num_of_interests = $interestsCount->where('operation_id', $operation->id)->first()->num_of_interests ?? 0;
            return $operation;
        });
        return $operations;
    }


    public function loadMoreOperations($userId, $offset = 0, $search = '', $type = 1)
    {
        $operations = Operation::leftJoin('interests', function ($join) use ($userId) {
            $join->on('operations.id', '=', 'interests.operation_id')
                ->where('interests.user_id', '=', $userId);
        })
            ->when($search, function ($query, $search) {
                return $query->where('operations.name', 'like', "%{$search}%");
            })
            ->when($type == 3, function ($query) {
                $query->where('interests.is_interested', true);
            })
            ->select('operations.id', 'operations.name', 'interests.is_interested')
            ->offset($offset)
            ->limit(12)
            ->get();
        $operationIds = $operations->pluck('id');
        $interestsCount = Interest::select('operation_id', DB::raw('COUNT(*) as num_of_interests'))
            ->whereIn('operation_id', $operationIds)
            ->where('is_interested', 1)
            ->groupBy('operation_id')
            ->get();

        $operations = $operations->map(function ($operation) use ($interestsCount) {
            $operation->num_of_interests = $interestsCount->where('operation_id', $operation->id)->first()->num_of_interests ?? 0;
            return $operation;
        });
        return $operations;
    }

    public function getListSearch($userId, $search, $type)
    {
        $operations = Operation::select('operations.id', 'operations.name', 'interests.is_interested')
            ->leftJoin('interests', function ($join) use ($userId) {
                $join->on('operations.id', '=', 'interests.operation_id')
                    ->where('interests.user_id', '=', $userId);
            })
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('operations.name', 'like', "%$search%");
            })
            ->when($type == 3, function ($query) {
                $query->where('interests.is_interested', true);
            })
            ->orderBy('operations.created_at', 'desc')
            ->paginate(12);

        $operationIds = $operations->pluck('id');
        $interestsCount = Interest::select('operation_id', DB::raw('COUNT(*) as num_of_interests'))
            ->whereIn('operation_id', $operationIds)
            ->where('is_interested', 1)
            ->groupBy('operation_id')
            ->get();

        $operations->getCollection()->transform(function ($operation) use ($interestsCount) {
            $operation->num_of_interests = $interestsCount->where('operation_id', $operation->id)->first()->num_of_interests ?? 0;
            return $operation;
        });

        return $operations;
    }

    public function getListSearchPb($search) {
        $userId = auth()->id();
        $popularOperations = Operation::select('operations.id', 'operations.name', 'current_user_interest.is_interested')
            ->leftJoin('interests as all_user_interests', function ($join) {
                $join->on('operations.id', '=', 'all_user_interests.operation_id')
                    ->where('all_user_interests.is_interested', 1);
            })
            ->leftJoin('interests as current_user_interest', function ($join) use ($userId) {
                $join->on('operations.id', '=', 'current_user_interest.operation_id')
                    ->where('current_user_interest.user_id', '=', $userId);
            })
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('operations.name', 'like', "%$search%");
            })
            ->groupBy('operations.id', 'operations.name', 'current_user_interest.is_interested')
            ->selectRaw('COUNT(all_user_interests.id) as num_of_interests')
            ->orderByDesc('num_of_interests') // Sắp xếp theo số lượng người quan tâm giảm dần
            ->paginate(12);
        return $popularOperations;

    }

    public function getCount($userId) {
        return Operation::where('user_id', $userId)->count();
    }

    public function listOperation($id) {
        return Operation::select('id', 'name')
            ->where('occupation_id', $id)->get();
    }
}

