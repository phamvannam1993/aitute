<?php

namespace App\Services;

use App\Repositories\JobRepository;
use App\Repositories\OccupationRepository;
use App\Repositories\OperationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use function Illuminate\Session\userId;

class OperationService
{
    protected $operationRepository;
    protected $jobRepository;
    // Inject TaskRepository vào service
    public function __construct(OperationRepository $operationRepository , JobRepository $jobRepository)
    {
        $this->operationRepository = $operationRepository;
        $this->jobRepository = $jobRepository;
    }

    /**
     * Xử lý logic tạo nghiep vu moit
     *
     * @param $data
     * @param Request $request
     * @return \App\Models\Operation
     */
    public function create($data, $request)
    {
        $imagePath = null;
        // Kiểm tra xem có file ảnh được upload không
        if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');
                // Lưu ảnh lên S3
                $imagePath = $image->store(config('constants.s3_folder.operations'), 's3');
            } catch (\Exception $e) {
                // Nếu có lỗi xảy ra trong quá trình upload lên S3
                return back()->withErrors(['image' => 'Không thể tải lên ảnh. Vui lòng thử lại.'])->withInput();
            }
        }

        // Thêm đường dẫn ảnh vào mảng $data nếu có ảnh
        $data['image'] = $imagePath;

        // Gán user_id cho người dùng hiện tại
        $data['user_id'] = auth('admin')->id();

        DB::transaction(function () use ($data) {
            try {
                $this->operationRepository->create($data);
            } catch (\Exception $e) {
                \Log::error('Lỗi khi tạo nghiep vu:', ['error' => $e->getMessage()]);
                // Nếu có lỗi xảy ra trong quá trình upload lên S3
                return back()->withErrors(['image' => 'Lỗi khi tạo nghiệp vu'])->withInput();
            }
        });

    }

    public function updateOperation($id, Request $request)
    {
        $operation = $this->operationRepository->findByIdAndUser($id, auth('admin')->id());
        $data = $request->only(['name', 'description', 'occupation_id']);
        if ($request->input('code') !== $operation->code) {
            $request->validate([
                'code' => 'required|string|max:255|unique:occupations,code',
            ], [
                'code.unique' => 'Mã nghiệp vụ này đã tồn tại.',
                'code.required' => 'Mã nghiệp vụ là bắt buộc.',
            ]);

            $data['code'] = $request->input('code');
        }

        // Xử lý ảnh nếu có
        if ($request->hasFile('image')) {
            try {
                // Xóa ảnh cũ từ S3 nếu có
                if ($operation->image) {
                    Storage::disk('s3')->delete($operation->image);
                }

                // Lưu ảnh mới vào S3
                $path = $request->file('image')->store(config('constants.s3_folder.operations'), 's3');

                $data['image'] = $path;

            } catch (\Exception $e) {
                // Nếu có lỗi xảy ra trong quá trình upload lên S3
                return back()->withErrors(['image' => 'Không thể tải lên ảnh. Vui lòng thử lại.'])->withInput();
            }
        }

        DB::transaction(function () use ($id, $data) {
            try {
                return $this->operationRepository->update($id, $data);
            } catch (\Exception $e) {
                \Log::error('Lỗi khi update nghiep vu:', ['error' => $e->getMessage()]);
                // Nếu có lỗi xảy ra trong quá trình upload lên S3
                return back()->withErrors(['image' => 'Lỗi khi update nghiệp vu'])->withInput();
            }
        });
    }

    public function getOperations(array $filters, int $userId)
    {
        return $this->operationRepository->getOperations($filters, $userId);
    }

    public function getOperationById(int $id)
    {
        return $this->operationRepository->findByIdAndUser($id, auth()->id());
    }

    public function getRelatedOperations($occupationId, $id)
    {
        return $this->operationRepository->getRelatedOperations($occupationId, $id);
    }

    public function getAllOperations()
    {
        return $this->operationRepository->getAllOperations();
    }

    public function loadMoreOperations($userId, $offset = 12, $search = '', $type = 1)
    {
        return $this->operationRepository->loadMoreOperations($userId, $offset, $search, $type);
    }

    public function getListSearch($userId, $search, $type) {
        return $this->operationRepository->getListSearch($userId, $search, $type);
    }

    public function getListSearchPb($search) {
        return $this->operationRepository->getListSearchPb($search);
    }

    public function getCount() {
        return $this->operationRepository->getCount(auth('admin')->id());
    }

    public function listOperation($id) {
        return $this->operationRepository->listOperation($id);
    }
}
