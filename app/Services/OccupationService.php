<?php

namespace App\Services;

use App\Repositories\OccupationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use function Illuminate\Session\userId;

class OccupationService
{
    protected $occupationRepository;

    // Inject OccupationRepository vào service
    public function __construct(OccupationRepository $occupationRepository)
    {
        $this->occupationRepository = $occupationRepository;
    }

    /**
     * Xử lý logic tạo ngành nghề mới
     *
     * @param $data
     * @param Request $request
     * @return \App\Models\Occupation
     */
    public function createOccupation($data, $request)
    {
        $imagePath = null;
        // Kiểm tra xem có file ảnh được upload không
        if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');
                // Lưu ảnh lên S3
                $imagePath = $image->store(config('constants.s3_folder.occupations'), 's3');
            } catch (\Exception $e) {
                // Nếu có lỗi xảy ra trong quá trình upload lên S3
                return back()->withErrors(['image' => 'Không thể tải lên ảnh. Vui lòng thử lại.'])->withInput();
            }
        }

        // Thêm đường dẫn ảnh vào mảng $data nếu có ảnh
        $data['image'] = $imagePath;

        // Gán user_id cho người dùng hiện tại
        $data['user_id'] = auth('admin')->id();

        // Lưu occupation vào repository
        return $this->occupationRepository->create($data);
    }

    // Hàm cập nhật occupation
    public function updateOccupation($id, Request $request)
    {
        $occupation = $this->occupationRepository->findByIdAndUser($id, auth('admin')->id());
        $data = $request->only(['name', 'category_id', 'description']);
        // Xử lý ảnh nếu có
        if ($request->hasFile('image')) {
            try {
                // Xóa ảnh cũ từ S3 nếu có
                if ($occupation->image) {
                    Storage::disk('s3')->delete($occupation->image);
                }

                // Lưu ảnh mới vào S3
                $path = $request->file('image')->store(config('constants.s3_folder.occupations'), 's3');

                // Thêm đường dẫn ảnh mới vào dữ liệu cập nhật
                $data['image'] = $path;

            } catch (\Exception $e) {
                // Nếu có lỗi xảy ra trong quá trình upload lên S3
                return back()->withErrors(['image' => 'Không thể tải lên ảnh. Vui lòng thử lại.'])->withInput();
            }
        }
        return $this->occupationRepository->update($id, $data);
    }

    /**
     * Xử lý logic nghiệp vụ cho việc lấy danh sách ngành nghề.
     *
     * @param array $filters
     * @param int $userId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getOccupations(array $filters, int $userId)
    {
        return $this->occupationRepository->getOccupations($filters, $userId);
    }

    public function getOccupationById(int $id)
    {
        return $this->occupationRepository->findByIdAndUser($id, auth('admin')->id());
    }

    public function getCount() {
        return $this->occupationRepository->getCount(auth('admin')->id());
    }

    public function getAllOccupations() {
        return $this->occupationRepository->getAllOccupations(auth('admin')->id());
    }


}
