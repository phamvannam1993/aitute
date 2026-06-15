<template>
    <Head title="Hợp tác với AI 3 Gốc - AI 3 GỐC - Cộng đồng AI tử tế" />
    <div class="flex flex-col min-h-screen">
        <div class="flex flex-col flex-1 mt-[60px] lg:mt-0 p-4 lg:p-[60px] h-full bg-white" >
            <header class="flex items-center justify-between">
            <div class="flex items-center gap-1">
                <a href="/">
                    <img
                        src="/assets/img/orion/icon/header/home.svg"
                        alt="Trang chủ"
                        class="w-auto h-[19px] md:h-[24px] min-h-[19px] min-w-[18px]"
                    />
                </a>
                <a :href="route('home.index')" class="font-medium text-black text-[16px] md:text-[20px] line-clamp-1">
                        / Hợp tác với AI 3 Gốc
                </a>
            </div>
            </header>

            <div class="h-screen flex flex-col items-center gap-6 mt-2">
                <img
                    src="/assets/img/ai3goc/logo/contact.svg"
                    alt="logo-aiwow"
                    class="w-[180px] md:w-[400px] h-auto"
                />
                <div class="">
                    <p
                        class="text-[10px] md:text-[16px] md:max-w-[600px] text-orion-primary flex justify-between text-center">
                        Ứng dụng này giúp bạn tạo ra các Nội dung để Truyền thông và Bán hàng một cách đơn giản nhất chỉ bằng vài nút bấm.
                    </p>
                    <p
                        class="text-[10px] md:text-[16px] md:max-w-[600px] text-orion-primary flex justify-between text-center">
                        Kể cả bạn không phải Chuyên gia Truyền thông cũng có thể có được kết quả ưng nhất.
                    </p>
                </div>
                <div class="font-semibold w-full text-center flex flex-col items-center gap-2">
                    <a href="https://aff.ai3goc.vn/" target="_blank" class="w-[300px] md:w-[600px] h-[80px] md:h-[224px] bg-contain bg-center bg-no-repeat" style="background-image: url('/assets/img/ai3goc/aff-button.svg');">
                        
                    </a>
                </div>
                <div
                    class="my-4 w-full flex flex-col justify-center items-center gap-4 md:gap-0 md:flex-row"
                >
                    <div
                        class="px-8 md:px-3 rounded-[10px] md:rounded-[20px] cursor-pointer"
                    >
                        <img
                            src="/assets/img/logo_of_youtube.png"
                            alt=""
                            class="w-28 md:w-[150px]"
                        />
                    </div>
                    <div
                        class="px-8 md:px-3 rounded-[10px] md:rounded-[20px] cursor-pointer"
                    >
                        <img
                            src="/assets/img/logo_of_facebook.png"
                            alt=""
                            class="w-28 md:w-[150px]"
                        />
                    </div>
                    <div
                        class="px-8 md:px-3 rounded-[10px] md:rounded-[20px] cursor-pointer"
                    >
                        <img
                            src="/assets/img/logo_of_tiktok.png"
                            alt=""
                            class="w-28 md:w-[150px]"
                        />
                    </div>
                    <div
                        class="px-8 md:px-3 rounded-[10px] md:rounded-[20px] cursor-pointer"
                    >
                        <img
                            src="/assets/img/logo_of_zalo.png"
                            alt=""
                            class="w-28 md:w-[150px]"
                        />
                    </div>
                </div>
            </div>
        </div>
        <Footer class="mt-auto"/>
    </div>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import Layout from "@/Layouts/Client/ClientLayout.vue";
import Footer from "../Home/Components/Footer.vue";

defineOptions({ layout: Layout });
</script>