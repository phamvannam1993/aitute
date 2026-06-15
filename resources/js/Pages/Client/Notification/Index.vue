<template>
    <Head title="Thông báo nâng cấp tài khoản - Đơn giản thôi mà !" />
    <div class="relative hidden md:block h-52 w-full">
        <div
            class="absolute inset-0"
            style="
                background: linear-gradient(
                    180deg,
                    #ffffff 59.48%,
                    rgba(255, 255, 255, 0) 93.67%
                );
            "
        ></div>
    </div>
    <div
        class="Wapper-Layout w-full h-screen overflow-hidden"
    >
        <div
            class="flex gap-3 text-[#2C75E3] mt-20 mb-5 ml-6 md:mt-7 md:ml-10 md:mb-12 md:hidden"
        >
        <a :href="route('home.index')">
                            <img
                                src="/assets/img/admin/icon_left_home.png"
                                alt="Trang chủ"
                                class="w-auto h-[19px]"
                            />
                       </a>
            <span>/</span>
            <span>Liên kết với chúng tôi</span>
        </div>
        <div class="flex flex-col min-h-screen overflow-y-auto">
            <div class="flex flex-col items-center justify-center">
                <div class="font-semibold w-full text-center">
                    <h1 class="text-[#003380] text-2xl mt-10 mb-1">
                        Bạn cần nâng cấp tài khoản <span class="text-[#FFB800]">{{appName}}</span> để sử dụng hệ thống
                    </h1>
                    <a :href="urlUpgrade" target="_blank" class="text-[#FF7A00] underline">
                        Bấm vào đây để nâng cấp tài khoản
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import Layout from "@/Layouts/Client/ClientLayout.vue";
import Footer from "../Home/Components/Footer.vue";

defineOptions({ layout: Layout });
const urlUpgrade = import.meta.env.VITE_UPGRADE_AFF_URL
const appName = import.meta.env.VITE_APP_NAME
</script>

<style scoped>
.Wapper-Layout {
    background-image: url("/assets/img/login_icon/dec.png");
    background-size: contain;
    background-repeat: repeat-y;
}
</style>
