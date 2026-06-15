<template>
    <div class="w-full text-black px-2 relative">
        <!-- Loading overlay when submitting -->
        <div v-if="fanpageData.submitting" class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
            <div class="bg-white p-4 rounded-md shadow-lg flex flex-col items-center">
                <svg aria-hidden="true" class="w-10 h-10 text-gray-200 animate-spin dark:text-gray-600 fill-purple-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"></path>
                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"></path>
                </svg>
                <span class="mt-3 text-gray-800 font-medium">Đang xử lý...</span>
            </div>
        </div>

        <div class="flex items-center gap-2 mb-4">
            <img src="/assets/img/ai3goc/logo/circle.svg" alt="Facebook" class="w-[24px] lg:w-[32px] h-auto" />
            <span class="text-[14px] lg:text-[20px] font-bold">Liên kết tài khoản Fan page Facebook</span>
        </div>

        <div v-for="(step, index) in steps" :key="index" class="mb-4">
            <div class="flex flex-col gap-3">
                <Step :step="index + 1" :stepName="step.name" />
                <a v-if="step?.link" :href="step.link" class="text-[#FF7011]" target="_blank">{{ step.link }}</a>
                <div v-if="step.image" class="ml-4">
                    <img :src="step.image" alt="Step image" class="w-full h-auto" />
                </div>
            </div>

            <div v-if="step.substeps && step.substeps.length > 0" class="mt-2">
                <div v-for="(substep, subindex) in step.substeps" :key="subindex" class="mt-2">
                    <div class="flex flex-col gap-2">
                        <div class="flex items-start">
                            <div class="text-[12px] lg:text-[16px] font-bold mr-2">
                                {{ subindex + 1 }}.
                            </div>
                            <div class="text-[12px] lg:text-[16px]">
                                <span v-html="substep.content"></span>
                            </div>
                        </div>
                        <img v-if="substep.image" :src="substep.image" alt="Substep Image" class="w-full h-auto" />

                        <div v-if="substep.substeps && substep.substeps.length > 0" class="pl-3.5">
                            <div v-for="(subsubstep, subsubindex) in substep.substeps" :key="subsubindex" class="mt-2">
                                <div class="flex items-start gap-2">
                                    <div class="text-[12px] lg:text-[16px]">
                                        <span v-html="subsubstep.content"></span>
                                    </div>
                                </div>
                                <img v-if="subsubstep.image" :src="subsubstep.image" alt="Subsubstep Image" class="w-full h-auto mt-1" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="index === 8" class="mt-2">
                <div v-if="fanpageData.loading" class="text-center p-4 flex justify-center items-center">
                    <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-purple-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"></path>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"></path>
                    </svg>
                    <span class="ml-3 text-gray-700">Đang tải...</span>
                </div>
                <div v-else>
                    <div v-if="fanpageData.hasConfig" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-md">
                        <p class="text-green-700 font-medium">Bạn đã cấu hình Facebook Fanpage</p>
                    </div>

                    <!-- Success Message -->
                    <div v-if="fanpageData.successMessage" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-md">
                        <div class="flex">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <p class="ml-3 text-sm font-medium text-green-800">{{ fanpageData.successMessage }}</p>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div v-if="fanpageData.errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                        <div class="flex">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <p class="ml-3 text-sm font-medium text-red-800">{{ fanpageData.errorMessage }}</p>
                        </div>
                    </div>

                    <form @submit.prevent="saveFanpageData" class="flex flex-col gap-2">
                        <div>
                            <label for="fanpageId" class="text-[12px]">ID Fanpage</label>
                            <input
                                type="text"
                                id="fanpageId"
                                v-model="fanpageData.id"
                                class="mt-1 p-2 border rounded-md w-full"
                            />
                        </div>

                        <div>
                            <label for="fanpageCode" class="text-[12px]">Mã Fanpage</label>
                            <input
                                type="text"
                                id="fanpageCode"
                                v-model="fanpageData.code"
                                class="mt-1 p-2 border rounded-md w-full"
                            />
                        </div>

                        <button type="submit" class="bg-ai3goc-sec text-white p-2 mt-4 rounded-md hover:opacity-80 w-[120px] mx-auto flex justify-center items-center">
                            <svg v-if="fanpageData.submitting" class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ fanpageData.hasConfig ? 'Cập nhật' : 'Lưu' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, onMounted } from 'vue';
import Step from '@/Components/MiniStep/Index.vue';
import axios from 'axios';

const chatbotTrainingURL = import.meta.env.VITE_CHATBOT_TRAINING_URL || 'https://chatbot-admin.ai3goc.vn/login';
const callbackFacebookVerificationURL = import.meta.env.VITE_CALLBACK_FACEBOOK_VERIFICATION_URL || 'https://aitute.vn/api/messenger-webhook';

const steps = [
    {
        name: '',
        substeps: [
            {
                content: 'Sau khi hoàn tất đăng ký, bạn sẽ được chuyển hướng đến trang tạo ứng dụng mới </br> hoặc truy cập vào <a href="https://developers.facebook.com/apps/" class="text-[#FF7011]" target="_blank">https://developers.facebook.com/apps/</a>.',
            },
            {
                content: 'Nhấn nút <strong>"Tạo ứng dụng"</strong> ở góc trên bên phải. </br> <strong>Lưu ý:</strong> Các ứng dụng đã được tạo trước đây hoặc các ứng dụng cũ sẽ không còn hoạt động.',
                image: '/assets/img/orion/link_fb_fanpage/step-1.png',
            },
        ],
    },
    {
        name: 'Điền thông tin và nhấn "Tiếp"',
        image: '/assets/img/orion/link_fb_fanpage/step-2.png',
        substeps: [],
    },
    {
        name: 'Chọn "Khác" và nhấn "Tiếp"',
        image: '/assets/img/orion/link_fb_fanpage/step-3.png',
        substeps: [],
    },
    {
        name: 'Chọn "Kinh doanh" và nhấn "Tiếp"',
        image: '/assets/img/orion/link_fb_fanpage/step-4.png',
        substeps: [],
    },
    {
        name: 'Xác nhận thông tin và nhấn "Tạo ứng dụng"',
        image: '/assets/img/orion/link_fb_fanpage/step-5.png',
        substeps: [],
    },
    {
        name: 'Xác nhận mật khẩu và nhấn "Gửi"',
        image: '/assets/img/orion/link_fb_fanpage/step-6.png',
        substeps: [],
    },
    {
        name: 'Thêm sản phẩm vào ứng dụng',
        substeps: [
            {
                content: 'Chọn <strong>"Messenger"</strong>',
            },
            {
                content: 'Chọn <strong>"Thiết lập"</strong>',
                image: '/assets/img/orion/link_fb_fanpage/step-7.png',
            },
        ],
    },
    {
        name: 'Cài đặt webhook',
        substeps: [
            {
                content: `<strong>Đặt cấu hình webhook. Nhập mã:</strong> <ul class="list-disc">
                            <li>URL gọi lại: <a href="${callbackFacebookVerificationURL}" class="text-[#FF7011]" target="_blank">${callbackFacebookVerificationURL}</a></li>
                            <li>Xác minh mã: <strong class="text-[#FF7011]">25430931b77e37f13bcf4a3ffa92e43f</strong></li>
                            <li>Nhấn <strong>"Xác minh và lưu"</strong></li>
                            </ul>`,
                image: '/assets/img/orion/link_fb_fanpage/step-8_substep-1.png',
                substeps: [],
            },
            {
                content: `<strong>Tạo mã truy cập</strong>`,
                substeps: [
                    {
                        content: `<ul class="list-disc">
                            <li>Chọn <strong>"Kết nối"</strong></li>
                            </ul>`,
                        image: '/assets/img/orion/link_fb_fanpage/step-8_substep-2_1.png',
                    },
                    {
                        content: `<ul class="list-disc">
                            <li>Xác nhận <strong>"Tiếp tục dưới tên.."</strong> Facebook của bạn</li>
                            <li>Chọn Fanpage bạn muốn liên kết và nhấn <strong>"Tiếp tục"</strong></li>
                            <li>Nhấn <strong>"Lưu"</strong></li>
                            <li>Nhấn <strong>"Đã hiểu"</strong></li>
                            <li>Sau khi hoàn thành tạo mã truy cập sẽ hiển thị <strong class="text-[#FF7011]">ID fanpage</strong></li>
                            </ul>`,
                        image: '/assets/img/orion/link_fb_fanpage/step-8_substep-2_2.png',
                    },
                    {
                        content: `<ul class="list-disc">
                            <li>Nhấn vào <strong>"Tạo"</strong> để tạo Mã fanpage</li>
                            </ul>`,
                        image: '/assets/img/orion/link_fb_fanpage/step-8_substep-2_3.png',
                    },
                    {
                        content: `<ul class="list-disc">
                            <li>Nhấn vào <strong>"Tạo"</strong> để tạo <strong class="text-[#FF7011]">Mã fanpage</strong>.</li>
                            <li>Sao chép mã fan page và nhấn <strong>"Xong"</strong></li>
                            </ul>`,
                        image: '/assets/img/orion/link_fb_fanpage/step-8_substep-2_4.png',
                    },
                ],
            },
            {
                content: `<strong>Hoàn tất quy trình Xét duyệt ứng dụng</strong>`,
                substeps: [
                    {
                        content: `<ul class="list-disc">
                            <li>Chọn <strong>"Yêu cầu cấp quyền"</strong></li>
                            </ul>`,
                        image: '/assets/img/orion/link_fb_fanpage/step-8_substep-3_1.png',
                    },
                    {
                        content: `<ul class="list-disc">
                            <li>Tích chọn các yêu cầu cấp quyền và nhấn <strong>"Yêu cầu cấp quyền"</strong></li>
                            </ul>`,
                        image: '/assets/img/orion/link_fb_fanpage/step-8_substep-3_2.png',
                    },
                ],
            },
        ],
    },
    {
        name: 'Nhập ID Fanpage và Mã Fanpage',
        substeps: [],
    },
    {
        name: 'Đăng nhập vào link sau để tải tài liệu huấn luyện Chatbot',
        link: chatbotTrainingURL,
        substeps: [
            {
                content: 'Đăng nhập bằng tài khoản được cấp khi mua aitute',
            },
            {
                content: 'Đăng nhập thành công, nhấn "Tải lên" để tải tài liệu',
            },
            {
                content: 'Ghi chú: Sau khi tải tài liệu, cần 10-15 phút để Chatbot huấn luyện',
                image: '/assets/img/orion/link_fb_fanpage/step-10.png',
            },
        ],
    },
];

const fanpageData = reactive({
    id: '',
    code: '',
    hasConfig: false,
    loading: false,
    submitting: false,
    errorMessage: '',
    successMessage: ''
});

// Hàm để tải cấu hình Facebook Fanpage
const loadFacebookConfig = () => {
    fanpageData.loading = true;
    fanpageData.errorMessage = '';

    axios.get('/facebook-fanpage/get-config')
        .then(response => {
            fanpageData.loading = false;
            if (response.data.success && response.data.data && response.data.data.length > 0) {
                // Lấy cấu hình đầu tiên nếu có nhiều
                const config = response.data.data[0];
                fanpageData.id = config.page_id;
                fanpageData.code = config.page_access_token;
                fanpageData.hasConfig = true;
            }
        })
        .catch(error => {
            fanpageData.loading = false;
            fanpageData.errorMessage = error.response && error.response.data && error.response.data.message
                ? error.response.data.message
                : 'Có lỗi xảy ra khi tải cấu hình Facebook Fanpage.';
            console.error('Error loading Facebook config:', error);
        });
};

const saveFanpageData = () => {
    fanpageData.submitting = true;
    fanpageData.errorMessage = '';
    fanpageData.successMessage = '';

    axios.post('/facebook-fanpage/save-config', {
        page_id: fanpageData.id,
        page_access_token: fanpageData.code
    })
    .then(response => {
        fanpageData.submitting = false;
        if (response.data.success) {
            // Show success message
            fanpageData.successMessage = response.data.message;
            fanpageData.hasConfig = true;
        } else {
            fanpageData.errorMessage = 'Có lỗi xảy ra khi lưu cấu hình Facebook Fanpage.';
        }
    })
    .catch(error => {
        fanpageData.submitting = false;
        fanpageData.errorMessage = error.response && error.response.data && error.response.data.message
            ? error.response.data.message
            : 'Có lỗi xảy ra khi lưu cấu hình Facebook Fanpage.';
    });
};

// Tải cấu hình khi component được mount
onMounted(() => {
    loadFacebookConfig();
});
</script>
