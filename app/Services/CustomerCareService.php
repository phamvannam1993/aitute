<?php

namespace App\Services;

use App\Repositories\CustomerCareRepository;

class CustomerCareService
{
    protected $customerCareRepository;

    // Inject CustomerCareRepository vào Service
    public function __construct(CustomerCareRepository $customerCareRepository)
    {
        $this->customerCareRepository = $customerCareRepository;
    }

    // Kiểm tra người dùng có lịch sử tin nhắn hay không
    public function checkUserMessageHistory($userId)
    {
        return $this->customerCareRepository->checkUserMessageHistory($userId);
    }

    // Lấy lịch sử tin nhắn của người dùng
    public function getMessageHistories($userId, $perPage = 8)
    {
        return $this->customerCareRepository->getMessageHistories($userId, $perPage);
    }

    // Kiểm tra tất cả trạng thái tin nhắn của người dùng
    public function checkAllMessagesStatus($userPageIds)
    {
        return $this->customerCareRepository->checkAllMessagesStatus($userPageIds);
    }
}