<template>
    <Head title="Lịch sử tạo video - AI 3 GỐC - Cộng đồng AI tử tế" />
    <div
        class="w-full lg:h-screen bg-[url('../../public/assets/img/aiwow/layout_background.png')] bg-cover bg-center flex-col lg:flex-row pt-[45px] lg:pt-[0px] items-center"
    >
        <div
            class="flex lg:flex-row flex-col w-[100%] lg:w-[100%] px-5 justify-between py-[32px]"
        >
            <div
                class="w-full lg:items-end mb-5 space-y-4 lg:space-y-0 lg:space-x-4 lg:py-5 gap-8"
            >
                <!-- Left Section (Breadcrumb and Search Bar) -->
                <div class="md-[50%] lg:w-[100%]">
                    <!-- Breadcrumb -->
                    <div class="flex items-center justify-between">
                       <div class="flex items-center space-x-2">
                            <a :href="route('home.index')">
                                <img
                                    src="/assets/img/admin/icon_left_home.png"
                                    alt="Trang chủ"
                                    class="w-auto h-[19px]"
                                />
                           </a>
                            <span class="font-medium text-[#2C75E3]"
                                >/ Hình ảnh thành video / Lịch sử
                            </span>
                       </div>
                       <Credit />
                    </div>
                    <MainMenu/>
                    <h1 class="text-black text-2xl font-bold my-3 lg:pl-[30px]">
                        Orion AI Hình ảnh thành video
                    </h1>
                    <div
                        class="flex w-full flex-col lg:flex-row justify-around gap-10 lg:gap-0"
                    >
                        <!-- Video Section -->
                        <div class="relative w-full lg:w-[60%] overflow-hidden">
                            <div class="aspect-video w-full">
                                <video
                                    :src="record.s3_url"
                                    controls
                                    preload="auto"
                                    class="w-full h-full rounded-[20px] bg-black"
                                ></video>
                            </div>
                        </div>
                        <!-- Right Section History List -->
                        <div
                            class="flex flex-col w-full lg:gap-1 max-h-[700px] h-[300px] md:h-[500px] lg:h-[350px] xl:h-[500px] lg:w-[25%] overflow-y-auto xl:items-end"
                        >
                            <div
                                class="flex items-center justify-center"
                                v-for="(item, index) in histories"
                                :key="index"
                            >
                                <div
                                    class="w-[90%] md:w-[60%] lg:w-full md:flex md:justify-center md:items-center aspect-video relative"
                                    @click="
                                        fetchData(
                                            route('ai-video.ImgToVideoHistoryDetail', {
                                                id: item.id,
                                            })
                                        )
                                    "
                                >
                                    <!--  -->

                                    <div
                                        class="flex justify-center items-center rounded-xl lazy-src overflow-hidden relative mb-4"
                                    >
                                        <div
                                            class="flex justify-center items-center"
                                            v-if="
                                                item.thumbnail &&
                                                item.thumbnail !== ''
                                            "
                                        >
                                            <div
                                                class="aspect-video w-full md:w-[75%] lg:w-[75%] h-[150px] sm:h-[180px] md:h-[240px] lg:h-[180px]"
                                            >
                                                <!-- Giảm chiều cao cho box thumbnail -->
                                                <img
                                                    :src="item.thumbnail"
                                                    class="cursor-pointer image w-full h-full object-cover rounded-xl"
                                                />
                                            </div>
                                            <img
                                                src="/assets/img/icon_play.png"
                                                class="w-14 md:w-20 sm:w-16 lg:w-20 cursor-pointer absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-[65%] sm:-translate-y-[70%] lg:-translate-y-[50%] opacity-50"
                                                alt="like"
                                            />
                                        </div>
                                        <div
                                            v-else
                                            class="flex justify-center items-center"
                                        >
                                            <!-- Chiều cao cho box "Đang tạo video" -->
                                            <div
                                                class="aspect-video w-full md:w-[75%] lg:w-[75%] h-[150px] sm:h-[180px] md:h-[240px] lg:h-[180px]"
                                            >
                                                <!-- Giảm chiều cao cho box "Đang tạo video" -->
                                                <img
                                                    src="/assets/img/no-image.png"
                                                    class="cursor-pointer image w-full h-full object-cover rounded-xl"
                                                />
                                            </div>
                                            <img
                                                src="/assets/img/icon_play.png"
                                                class="w-14 md:w-20 sm:w-16 lg:w-20 cursor-pointer absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-[65%] sm:-translate-y-[70%] lg:-translate-y-[50%] opacity-50"
                                                alt="like"
                                            />
                                        </div>
                                    </div>

                                    <!--  -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
import Layout from "@/Layouts/Client/ClientLayout.vue";
import Footer from "../Home/Components/Footer.vue";
import Credit from '@/Components/Credit.vue';
import MainMenu from "@/Components/MainMenu.vue";

const props = defineProps({
    record: Object,
    list: Array,
});

defineOptions({ layout: Layout });

const listHistory = ref([]);
const showConfirmModal = ref(false);
const isLoading = ref(false);
const selectedVideo = ref(null);

console.log("list : ", props.list);
console.log("record : ", props.Object);

const goBack = () => {
    window.history.back();
};

// Pagination fetch without full page reload
const fetchData = (url) => {
    window.location.href = url;
};

// Thêm thuộc tính `isPlaying` vào từng video để quản lý việc phát video
const histories = ref(
    props.list?.map((item) => ({
        ...item,
        isPlaying: false,
    })) || []
);

// Lazy load for videos
const openDetail = (item) => {
    selectedVideo.value = item;
};

const closeDetail = () => {
    selectedVideo.value = null;
};
</script>

<style scoped>
/* CSS for loader and other styles */
</style>
