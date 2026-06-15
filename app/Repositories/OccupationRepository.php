<?php

namespace App\Repositories;

use App\Models\Occupation;
use Illuminate\Support\Facades\DB;

class OccupationRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return Occupation::class;
    }

    /**
     * Tìm kiếm, sắp xếp, phân trang ngành nghề theo điều kiện.
     *
     * @param array $filters
     * @param int $userId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getOccupations(array $filters, int $userId)
    {
        $query = Occupation::selectRaw('id, user_id , name, description, image')
            ->where('user_id', $userId);

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        // Xử lý sắp xếp
        $sort = $filters['sort'] ?? 'name';
        $direction = $filters['direction'] ?? 'asc';
        if ($sort) {
            $query->orderBy('name', $direction)
            ->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('name', 'asc');
            $query->orderBy('created_at', 'desc');
        }

        // Xử lý phân trang (limit và offset)
        $page = $filters['page'] ?? 1; // Lấy số trang, mặc định là 1 nếu không có
        $limit = config('constants.pagination.items_per_page'); // Số bản ghi mỗi trang

        // Tính tổng số bản ghi cần lấy (record của trang 1 đến trang hiện tại)
        $totalRecords = $limit * $page; // Lấy cả trang hiện tại và các trang trước đó
        // Lấy tổng số bản ghi phù hợp với điều kiện trước khi áp dụng limit
        $totalOccupationsCount = $query->count();

        // Không sử dụng offset, luôn lấy từ bản ghi đầu tiên
        $query->limit($totalRecords); // Lấy tất cả bản ghi từ trang 1 đến trang hiện tại

        $occupations = $query->get(); // Sử dụng `get()` để lấy tất cả bản ghi mà không có phân trang tự động


       // Lấy query string từ filters
        $queryString = http_build_query(array_filter($filters, function ($key) {
            return $key !== 'page';
        }, ARRAY_FILTER_USE_KEY));


        // Kiểm tra và tạo URL cho next_page_url và previous_page_url
        $nextPageUrl = null;
        if ($totalRecords < $totalOccupationsCount) { // Nếu tổng bản ghi đã lấy nhỏ hơn tổng số bản ghi
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
            'data' => $occupations,
            'current_page' => $page,
            'next_page_url' => $nextPageUrl,
            'previous_page_url' => $previousPageUrl,
        ];
    }

    // Hàm tìm kiếm occupation theo id và user_id
    public function findByIdAndUser($id, $userId)
    {
        return Occupation::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
    }
    // Hàm tìm kiếm occupation theo id
    public function findById($id)
    {
        return Occupation::where('id', $id)
            ->firstOrFail();
    }

    // Phương thức cập nhật occupation
    public function update($id, array $data)
    {
        $occupation = Occupation::findOrFail($id);
        $occupation->update($data);
        return $occupation;
    }

    public function getCount($userId) {
        return Occupation::where('user_id', $userId)->count();
    }

    public function getAllOccupations($userId) {
        return Occupation::where('user_id', $userId)->select('id', 'name')->get();
    }
}
