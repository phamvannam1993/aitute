<template>
    <div :class="mainSectionOpen ? 'flex-col lg:rounded-[35px] items-start' : 'flex-row lg:rounded-full items-center'" class="bg-white shadow-xl md:px-[80px] md:py-[20px] p-3 flex justify-between mt-4">
        <!-- Header -->

        <div @click="toggleMainSection" class="cursor-pointer flex justify-between items-center md:max-w-full line-clamp-1 gap-2 mb-2 text-xs lg:text-sm mt-2 text-black">
            <Step class="flex-shrink-0" :step="6" stepName="Thiết lập Phong cách và Nội dung" />
        </div>
        <!-- Form Content -->
        <div v-if="mainSectionOpen" class="w-full lg:w-4/5 self-center">
            <form @submit.prevent="handleSubmit" class="flex flex-col gap-6">
                <!-- 1. Bối cảnh -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm">1. Bối cảnh</label>
                    <textarea
                        v-model="formData.context"
                        rows="2"
                        class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#2A5F4C] focus:border-[#2A5F4C]"
                    ></textarea>
                </div>

                <!-- 2. Mục tiêu -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm">2. Mục tiêu</label>
                    <select
                        v-model="formData.goal"
                        class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#2A5F4C] focus:border-[#2A5F4C] bg-white"
                    >
                        <option value="" disabled>Tin tưởng</option>
                        <option v-for="goal in goals" :key="goal" :value="goal">
                            {{ goal }}
                        </option>
                    </select>
                </div>

                <!-- 3. Đối tượng khách hàng -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm">3. Đối tượng khách hàng</label>
                    <textarea
                        v-model="formData.targetAudience"
                        rows="2"
                        class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#2A5F4C] focus:border-[#2A5F4C]"
                    ></textarea>
                </div>

                <!-- 4. Thông điệp chính -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm">4. Thông điệp chính</label>
                    <textarea
                        v-model="formData.mainMessage"
                        rows="2"
                        class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#2A5F4C] focus:border-[#2A5F4C]"
                    ></textarea>
                </div>

                <!-- 5. Cảm xúc mong muốn truyền tải -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm">5. Cảm xúc mong muốn truyền tải</label>
                    <select
                        v-model="formData.emotion"
                        class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#2A5F4C] focus:border-[#2A5F4C] bg-white"
                    >
                        <option v-for="emotion in emotions" :key="emotion" :value="emotion">
                            {{ emotion }}
                        </option>
                    </select>
                </div>

                <!-- 6. Phong cách viết -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm">6. Phong cách viết</label>
                    <select
                        v-model="formData.style"
                        class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#2A5F4C] focus:border-[#2A5F4C] bg-white"
                    >
                        <option v-for="style in styles" :key="style" :value="style">
                            {{ style }}
                        </option>
                    </select>
                </div>

                <!-- 7. Định dạng bài viết -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm">7. Định dạng bài viết</label>
                    <select
                        v-model="formData.articleFormat"
                        class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#2A5F4C] focus:border-[#2A5F4C] bg-white"
                    >
                        <option value="" disabled>Tin tưởng</option>
                        <option v-for="articleFormat in articleFormats" :key="articleFormat" :value="articleFormat">
                            {{ articleFormat }}
                        </option>
                    </select>
                </div>

                <!-- 8. Ưu đãi và quà tặng -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm">8. Ưu đãi và quà tặng</label>
                    <textarea
                        v-model="formData.promotion"
                        rows="2"
                        class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#2A5F4C] focus:border-[#2A5F4C]"
                    ></textarea>
                </div>

                <!-- 9. Thêm CTA -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm">9. Thêm CTA (Lời kêu gọi hành động)</label>
                    <select
                        v-model="formData.cta"
                        class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#2A5F4C] focus:border-[#2A5F4C] bg-white"
                    >
                        <option value="" disabled>Đăng ký tham gia ngay</option>
                        <option v-for="cta in ctas" :key="cta" :value="cta">
                            {{ cta }}
                        </option>
                    </select>
                </div>

                <div class="flex justify-center mt-4">
                    <button
                        type="submit"
                        :disabled="loading"
                        class="px-12 py-2 bg-[#2A5F4C] text-white rounded-md hover:bg-[#224939] disabled:opacity-50"
                    >
                        <span v-if="!loading">Xác nhận</span>
                        <LoadingCircle v-else class="w-5 h-5" />
                    </button>
                </div>
            </form>
        </div>

        <div :class="mainSectionOpen ? 'self-end' : ''" class="text-ai3goc-sec mt-2 font-medium flex items-center cursor-pointer text-xs md:text-sm" @click="toggleMainSection">
                  <span class="hidden md:block">{{ mainSectionOpen ? "Thu gọn" : "Hiển thị" }}</span>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': mainSectionOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>

    </div>
</template>

<script setup>
    import { ref } from 'vue';
    import Step from '@/Components/Step.vue';
    import LoadingCircle from '@/Components/LoadingCircle.vue';

    const props = defineProps({
        open: {
            type: Boolean,
            required: true
        },
        loading: {
            type: Boolean,
            default: false
        },
        contents: {
            type: Object,
            default: () => ({})
        }
    });

    const formData = ref({
        context: props.contents?.context || '',
        goal: props.contents?.goal || 'Tăng Nhận Diện Thương Hiệu Sản phẩm',
        customerObject: props.contents?.customerObject || '',
        mainMessage: props.contents?.mainMessage || '',
        emotion: props.contents?.emotion || 'Tin tưởng',
        style: props.contents?.style || 'Trang Trọng',
        articleFormat: props.contents?.articleFormat || 'Status ngắn',
        promotion: props.contents?.promotion || '',
        cta: props.contents?.cta || 'Mua Ngay'
    });

    const emotions = [
        'Tin tưởng',
        'Vui vẻ',
        'Cảm động',
        'Hào hứng',
        'Tự hào',
        'Thân thiện'
    ];

    const goals = [
        'Tăng Nhận Diện Thương Hiệu Sản phẩm',
        'Thông Tin Sản Phẩm/Dịch Vụ',
        'Tạo trao đổi về Sản Phẩm / Dịch vụ',
        'Kích Thích Sự Tương Tác (Engagement)',
        'Quảng bá về Tiện ích và Thái độ phục vụ ',
        'Tạo Độ Tin Cậy và Chuyên Môn',
        'Phá bỏ Rào cản và tạo động lực mua',
        'Kích thích hành động mua ngay',
        'Kết Nối và Phản Hồi Khách Hàng',
        'Xây Dựng Cộng Đồng',
        'Hướng Dẫn và Giáo Dục khách hàng',
        'Phân Tích Rào cản Khách Hàng',
        'Tạo Dựng Mối Quan Hệ Với Khách Hàng',
        'Xu Hướng Và Tương Lai',
        'Tăng Cường Hình Ảnh Thương Hiệu',
    ];

    const articleFormats = [
        'Status ngắn',
        'Bài viết dài',
        'Dạng Danh sách',
        'Câu chuyện kể',
        'Kịch bản Video quảng cáo',
        'Câu hỏi thảo luận',
        'Trích dẫn',
        'Hướng dẫn sử dụng',
        'So sánh',
        'Feedback từ khách hàng',
        'Câu chuyện khách hàng',
        'Bài viết dựa trên số liệu',
    ];

    const ctas = [
        'Mua Ngay',
        'Khuyến khích liên kết và tương tác',
        'Đăng Ký tham gia',
        'Xem Chi Tiết',
        'Liên Hệ Chúng Tôi',
        'Thêm Chia Sẻ',
        'Tăng bình luận ',
    ];

    const styles = [
        "Trang Trọng" ,
        "Hấp Dẫn và Sáng Tạo",
        "Thư Giãn và Thân Thiện",
        "Chuyên Nghiệp",
        "Hài hước và vui vẻ",
        "Thể Thao và Năng Động",
        "Hướng Dẫn và Giảng Dạy",
        "Chất Lượng và Tinh Tế",
        "Ngắn Gọn và Súc Tích",
        "Chia Sẻ Kinh Nghiệm Cá Nhân",
        "Truyện Cười và Giải Trí",
        "Đánh Giá và So Sánh",
        "Cảm Xúc và Sâu Sắc",
        "Tone Nghiêm túc",
        "Tone Lạc quan",
        "Tone Hấp Dẫn và Thú vị",
        "Tone Thư Thái, nhẹ nhàng",
    ];


    const emit = defineEmits(['submit', 'toggle']);

    const validateForm = () => {
        // Add validation as needed
        return true;
    };

    const handleSubmit = () => {
        if (validateForm()) {
            console.log("formData ContentStyleSetup", formData.value);
            const data = JSON.parse(JSON.stringify(formData.value));
            emit('submit', data, 'step6');
        }
    };

    const mainSectionOpen = ref(props.open);

    const toggleMainSection = () => {
        mainSectionOpen.value = !mainSectionOpen.value;
    };
</script>
