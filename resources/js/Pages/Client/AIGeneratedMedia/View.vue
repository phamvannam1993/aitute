<template>
    <Head>
        <title>Chia sẻ hình ảnh</title>
        <meta head-key="og-title" property="og:title" content="AI 3 GỐC - Cộng đồng AI tử tế" />
        <meta head-key="og-image" property="og:image" :content="url" />
        <meta head-key="og-description" property="og:description" content="AI 3 GỐC - Cộng đồng AI tử tế" />
        <meta head-key="og-site_name" property="og:site_name" content="Orion AI" />
        <meta head-key="og-type" property="og:type" content="website" />
    </Head>

    <div
        class="relative w-full mx-auto bg-[url('../../public/assets/img/aiwow/layout_background.png')] bg-cover bg-center min-h-screen p-5 lg:p-10 text-xs lg:text-sm lg:px-[15%] mb-20">
        <div class="flex gap-3 text-[#2C75E3] mt-14 ml-2 mb-6 text-sm">
            <a :href="route('home.index')">
                <img src="/assets/img/admin/icon_left_home.png" alt="Trang chủ" class="w-auto h-[19px]" />
            </a>
            <span>/</span>
            <span>
                Share Link
            </span>
        </div>
        <div class="w-full overflow-y-auto text-center bg-white text-black rounded-[25px] min-w-2/3 py-2 px-3">
            <div data-v-064b5a97="" class="aspect-video w-full">
                   <img v-if="url.includes('images')" :src="url"
                                                    class="w-full rounded-[20px] bg-black" />
                   <video v-else :src="url" controls  preload="auto"
                                                    class="w-full h-full rounded-[20px] bg-black"></video>
            </div>
        </div>
    </div>


</template>

<script setup>
import Layout from "@/Layouts/Client/ClientLayout.vue";
import { Head } from "@inertiajs/vue3";
defineOptions({ layout: Layout });

defineProps({
    url: String,
});
</script>
