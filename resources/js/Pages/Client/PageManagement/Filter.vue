<template>
    <div  class="fixed top-28 right-64 w-1/2 lg:w-1/4 rounded-[8px] my-2 bg-white shadow-lg z-50 text-black px-2" ref="dropdownContainer">
      <div class="p-4 flex items-center justify-between ">
        <h2 class="text-lg font-semibold text-[#333] ">Lọc & sắp xếp</h2>
        <button @click="closeFilter" class="">X</button>
      </div>
      <div class="max-h-[614px] overflow-y-auto">
        <div class="mb-4 pt-4 pb-4 border-t-[1px] border-b-[1px]">
            <h3 class="px-4 uppercase text-[#999]">Sắp xếp theo :</h3>
            <FilterOption title="Lần tạo cuối cùng" inputType="radio" :optionItems="lastCreated"/>
        </div>
        <div class="mb-4">
            <h3 class="px-4 uppercase text-[#999]">Lọc theo :</h3>
            <FilterOption title="Trạng thái phê duyệt" inputType="checkbox" :optionItems="approvalStatus"/>
            <FilterOption title="Trạng thái bài viết" inputType="checkbox" :optionItems="postStatuses"/>
            <FilterOption title="Phản hồi & Hoạt động" inputType="checkbox" :optionItems="feedbackActivities"/>

            <FilterOption title="Nhãn" inputType="checkbox" :optionItems="tags"/>

            <FilterOption title="Giai đoạn" inputType="radio" :optionItems="stages"/>

            <FilterOption title="Loại bài đăng" inputType="checkbox" :optionItems="postTypes"/>

            <FilterOption title="Nội dung" inputType="checkbox" :optionItems="contentTypes"/>

            <FilterOption title="Tác giả" inputType="checkbox" :optionItems="author"/>
        </div>
        <div class="p-4 border-t-[1px] border-b-[1px]">
          <button class="text-[#232426] text-[12px]">
            Tìm kiếm trong bài viết
          </button>
        </div>
        <div class="p-4 flex flex-col">
          <button class="text-[#232426] text-[12px] w-full text-left py-2">Ẩn bài viết chỉ của nhóm</button>
          <button class="text-[#232426] text-[12px] w-full text-left py-2">Hiển thị bài viết đã lưu trữ</button>
        </div>
      </div>
    </div>
</template>

<script setup>
import FilterOption from './FilterOption.vue';
import { defineEmits } from 'vue';

const emit = defineEmits();

const lastCreated = [
    { id: 1, name: 'Ngày dự kiến' },
    { id: 2, name: 'Ngày dự kiến cuối cùng' },
    { id: 3, name: 'Lần tạo cuối cùng' },
    { id: 4, name: 'Đầu tiên được tạo ra' },
    { id: 5, name: 'Bình luận gần đây' },
    { id: 6, name: 'Hoạt động cuối cùng' },
    { id: 7, name: 'Đặt hàng theo yêu cầu' },
]

const approvalStatus = [
    { id: 1, name: 'Tán thành' },
    { id: 2, name: 'Không được chấp thuận' },
    { id: 3, name: 'Đã được chấp thuận một phần' },
    { id: 4, name: 'Đang chờ sự chấp thuận của tôi' },
    { id: 5, name: 'Được yêu cầu bởi tôi' },
];

const postStatuses = [
    { id: 1, name: 'Đã lên lịch' },
    { id: 2, name: 'Không có lịch trình' },
    { id: 3, name: 'Đã xuất bản' },
    { id: 4, name: 'Chưa công bố' },
    { id: 5, name: 'Không có ngày nào được thiết lập' },
    { id: 6, name: 'Đã đánh dấu là đã xuất bản' },
    { id: 7, name: 'Đã xuất bản dưới dạng Quảng cáo' },
    { id: 8, name: 'Đã xuất bản dưới dạng Bản thảo' },
    { id: 9, name: 'Không thể xuất bản' },
    { id: 10, name: 'Nội dung không tương thích' },
    { id: 11, name: 'Nhập khẩu' }
];

const feedbackActivities = [
    { id: 1, name: 'Bình luận' },
    { id: 2, name: 'Gợi ý' },
    { id: 3, name: 'Ghi chú nội bộ' },
    { id: 4, name: 'Bình luận đã giải quyết' }
];

const tags = []

const stages = [
    { id: 1, name: 'Hôm nay' },
    { id: 2, name: 'Tuần này' },
    { id: 3, name: 'Tháng này' },
    { id: 4, name: 'Tháng tới' },
    { id: 5, name: '14 ngày tiếp theo' },
    { id: 6, name: 'Phong tục'}
];

const postTypes = [
    { id: 1, name: 'Bài viết' },
    { id: 2, name: 'Những câu chuyện' },
    { id: 3, name: 'Cuộn phim' }
];

const contentTypes = [
    { id: 1, name: 'Hình ảnh' },
    { id: 2, name: 'Băng hình' },
    { id: 3, name: 'Ảnh động GIF' },
    { id: 4, name: 'Liên kết hình thu nhỏ' },
    { id: 5, name: 'Vòng xoay ngựa gỗ' },
    { id: 6, name: 'Phương tiện truyền thông hỗn hợp' },
    { id: 7, name: 'Không trung bình' },
    { id: 8, name: 'Không có văn bản' }
];

const author = [
];

const closeFilter = () => {
    emit('close');
}
</script>