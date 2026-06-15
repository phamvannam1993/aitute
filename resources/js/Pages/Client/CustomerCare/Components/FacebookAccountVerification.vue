<template>
    <div class="w-full text-black px-2">
        <div class="flex items-center gap-2 mb-4">
            <img src="/assets/img/ai3goc/logo/circle.svg" alt="Facebook" class="w-[24px] lg:w-[32px] h-auto" />
            <span class="text-[14px] lg:text-[20px] font-bold">Xác minh tài khoản Facebook</span>
        </div>

        <div v-for="(step, index) in steps" :key="index" class="mb-4">
            <div class="flex items-start">
                <div class="text-[12px] lg:text-[16px] font-bold mr-2">
                    {{ index + 1 }}.
                </div>
                <div class="text-[12px] lg:text-[16px]">
                    <span v-html="step.description"></span>
                    <img v-if="step.image" :src="step.image" alt="Step image" class="mt-2 w-full h-auto rounded-[12px]" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const steps = [
    {
        description: `Truy cập: <a href="https://developers.facebook.com/async/registration/" class="text-[#FF7011] underline" target="_blank">https://developers.facebook.com/async/registration/</a>`,
    },
    {
        description: `Xác nhận tài khoản Facebook của bạn và nhấn <strong>“Tiếp tục”</strong>.`,
        image: '/assets/img/orion/facebook_verification/step-2.png', 
    },
    {
        description: `Điền số di động và nhấn <strong>“Gửi SMS xác minh”</strong>.`,
        image: '/assets/img/orion/facebook_verification/step-3.png', 
    },
    {
        description: `Hệ thống tự động gửi mã xác minh về số di động của bạn \nNhập mã xác minh và nhấn <strong>“Tiếp tục”</strong>`,
        image: '/assets/img/orion/facebook_verification/step-4.png', 
    },
    {
        description: `Kiểm tra thông tin email và nhấn <strong>“Xác nhận”</strong>.`,
        image: '/assets/img/orion/facebook_verification/step-5.png', 
    },
    {
        description: `Lựa chọn vai trò mô tả đúng về bạn và nhấn <strong>“Hoàn tất đăng ký”</strong>`,
        image: '/assets/img/orion/facebook_verification/step-6.png', 
    },
];
</script>