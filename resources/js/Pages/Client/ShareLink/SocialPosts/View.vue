<template>

    <Head title="Bài viết - AI 3 GỐC - Cộng đồng AI tử tế" />

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
        <div class="w-full overflow-y-auto bg-white text-black rounded-[25px] min-w-2/3 py-2 px-3">
            <div v-html="content" class="whitespace-pre-line"></div>

            <div class="py-2 px-3 grid grid-cols-2 gap-2 my-4">
                <img v-for="(media, k) in medias" :key="k" :src="media" />
            </div>

            <div v-if="video" class="py-2 px-3 flex items-center justify-center my-4 w-full">
                <video class="w-1/2" :src="video" controls></video>
            </div>
        </div>



    </div>


</template>

<script setup>
import Layout from "@/Layouts/Client/ClientLayout.vue";
import { Head } from "@inertiajs/vue3";
defineOptions({ layout: Layout });

defineProps({
    medias: Array,
    content: String,
    video: String,
});
</script>
