<template>

    <Head title="Chăm sóc khách hàng - Aitute" />
    <Layout :breadcrumbs="breadcrumbs">
        <div class="w-full sm:px-[12px] md:px-[36px]">
            <div
                class="flex flex-col lg:flex-row items-start justify-between lg:items-end mb-5 space-y-4 lg:space-y-0 lg:space-x-4">
                <div class="w-full flex flex-col gap-4">
                    <!-- No Facebook config - show initial screen -->
                    <div v-if="!hasFacebookConfig && !showGuide"
                        class="w-full flex items-center justify-center mt-4">
                        <div class="flex flex-col items-center text-black font-bold px-2">
                            <img src="/assets/img/orion/connect-facebook.svg" alt="Empty"
                                class="w-[100px] lg:w-[240px] h-auto mb-4" />
                            <p class="text-center mt-2 text-[12px] lg:text-[16px]">
                                Đây là giải pháp tích hợp với Fan page để tự động tương tác, trả lời, tư vấn, định
                                hướng, điều hướng khách hàng đến chốt sale.
                            </p>
                            <p class="text-center mt-2 text-[12px] lg:text-[16px]">
                                Để sử dụng giải pháp này, bạn cần phải liên kết tài khoản Facebook
                            </p>
                            <p v-if="message" class="text-center mt-2 text-red-500 text-[14px] lg:text-[16px]">
                                {{ message }}
                            </p>
                            <button class="bg-ai3goc-sec py-2 px-8 text-white rounded-full mt-8 text-[16px] lg:text-[20px] hover:scale-105 hover:bg-ai3goc-sec/80 cursor-pointer"
                            @click="handleShowGuide">
                                Bấm vào đây để kết nối
                            </button>
                        </div>
                    </div>

                    <!-- Show Facebook setup guide -->
                    <div v-else-if="!hasFacebookConfig && showGuide" class="px-2 lg:w-[640px] mx-auto">
                        <FacebookAccountVerification />
                        <LinkFacebookFanpage />
                    </div>

                    <!-- Show chat history table regardless of whether there's data or not -->
                    <div v-else-if="hasFacebookConfig">
                        <HistoriesChat
                            :data="data || { conversations: {}, pagination: { current_page: 1, last_page: 1 } }"
                            :has-data="hasHistory" :message="message" />
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { ref } from 'vue';
import Layout from "@/Layouts/Client/Layout.vue";
import { Head } from "@inertiajs/vue3";

import HistoriesChat from "./HistoriesChat.vue";
import FacebookAccountVerification from "./Components/FacebookAccountVerification.vue";
import LinkFacebookFanpage from "./Components/LinkFacebookFanpage.vue";

const props = defineProps({
    data: Object,
    hasHistory: Boolean,
    hasFacebookConfig: Boolean,
    message: String,
});

const breadcrumbs = [
    { text: 'Chăm sóc khách hàng' },
];

const showGuide = ref(false);

const handleShowGuide = () => {
    window.location.href = route('embed-config.facebook.index');
};
</script>
