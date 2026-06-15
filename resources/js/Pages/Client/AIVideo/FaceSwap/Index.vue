<template>
    <Head title="Image to Video - AI 3 GỐC - Cộng đồng AI tử tế" />
    <div class="flex min-h-screen flex-col bg-[url('../../public/assets/img/aiwow/layout_background.png')] bg-cover bg-center">
        <div class="w-full py-[44px] px-[12px] md:px-[36px]">
            <div class="flex flex-col lg:flex-row items-start justify-between lg:items-end mb-5 space-y-4 lg:space-y-0 lg:space-x-4">
                <div class="w-full flex flex-col space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <a :href="route('home.index')">
                                <img src="/assets/img/admin/icon_left_home.png" alt="Trang chủ" class="w-auto h-[19px]" />
                            </a>
                            <span class="font-medium text-[#2C75E3] text-[14px]">/ Hình ảnh thành video </span>
                            <span v-if="ai_assistant?.name" class="font-medium text-[#2C75E3] text-[14px]"> / {{ ai_assistant?.name || "" }}</span>
                        </div>
                        <Credit :credits="credits" />
                    </div>
                    <MainMenu />
                    <div class="flex justify-between gap-4 w-full flex-wrap md:flex-nowrap">
                        <div class="bg-white w-full rounded-[10px] md:rounded-[25px] lg:w-2/5 p-[12px] md:p-[25px]">
                            <div class="flex justify-start items-center gap-2">
                                <img src="/assets/svgs/robot-icon.svg" />
                                <div>
                                    <h2 class="text-black font-bold text-[30px] leading-[32px] line-clamp-1">Orion AI Hình ảnh thành video</h2>
                                    <p class="text-black text-[14px]">Trợ lý tạo video từ hình ảnh</p>
                                </div>
                            </div>
                            <form @submit.prevent="handleSubmit" class="mt-6">
                                <div class="relative">
                                    <div class="mt-4">
                                        <EditImage :model-value="selectedImage" @update:model-value="(val) => (selectedImage = val)" @urlSubmitted="handleUrlSubmit" :active="active" :is-present="isPresent">
                                            <img :src="selectedImage" alt="image" class="w-auto h-[30rem] object-cover rounded-tl-xl rounded-br-xl" />
                                        </EditImage>
                                        <EditVideo class="mt-4" :model-value="selectedVideo" @update:model-value="(val) => (selectedVideo = val)" @urlSubmitted="handleVideoUrlSubmit">
                                            <video id="video" ref="videoRef" v-if="selectedVideo" :src="selectedVideo" class="w-auto h-[30rem] object-cover rounded-tl-xl rounded-br-xl"></video>
                                        </EditVideo>
                                        <span v-if="errors.duration" class="text-red-500 text-sm">{{ errors.duration }}</span>
                                    </div>
                                </div>

                                <div class="mt-5 flex justify-center">
                                    <button type="submit" class="px-4 py-2 bg-[#2C75E3] text-white rounded-[10px] min-w-36 md:w-[180px] text-[15px] font-bold hover:bg-blue-600 transition-colors duration-300" :disabled="isLoading">
                                        {{ isLoading ? "Đang xử lý..." : "Gửi" }}
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="bg-white rounded-[10px] md:rounded-[25px] w-full lg:w-3/5 p-[12px] md:p-[25px]">
                            <div class="flex justify-center items-center mb-4">
                                <video v-if="videoResponse" :src="videoResponse" controls class="w-full h-auto md:max-w-[500px] md:max-h-[500px] object-cover rounded-lg"></video>
                                <div v-else class="bg-gray-300 w-full md:w-[400px] xl:w-[500px] h-[300px] md:h-[400px] xl:h-[500px] rounded-2xl flex items-center justify-center">
                                    <p class="text-gray-500 text-lg">Video sẽ hiển thị tại đây</p>
                                </div>
                            </div>
                            <div class="w-full flex flex-row md:flex-col mt-2 md:w-full xl:w-full lg:pb-[4px]">
                                <div class="flex justify-start md:justify-end gap-4 items-center mt-4 flex-wrap w-full">
                                    <button class="text-[#2C75E3] flex justify-start items-center gap-2 hover:text-blue-600 transition-colors duration-300">
                                        <img src="/assets/svgs/edit-icon-new.svg" class="h-[18px] w-[16px]" />
                                        <span v-if="isShowDes" class="text-[12px] font-medium">Tạo bài đăng</span>
                                    </button>
                                    <button class="text-[#2C75E3] flex justify-start items-center gap-2 hover:text-blue-600 transition-colors duration-300">
                                        <img src="/assets/svgs/trash-icon-new.svg" class="h-[18px] w-[16px]" />
                                        <span v-if="isShowDes" class="text-[12px] font-medium">Xoá</span>
                                    </button>
                                    <button class="text-[#2C75E3] flex justify-start items-center gap-2 hover:text-blue-600 transition-colors duration-300">
                                        <img src="/assets/svgs/share-icon-new.svg" class="h-[18px] w-[16px]" />
                                        <span v-if="isShowDes" class="text-[12px] font-medium">Chia sẻ</span>
                                    </button>
                                    <button class="text-[#2C75E3] flex justify-start items-center gap-2 hover:text-blue-600 transition-colors duration-300" @click="downloadVideo">
                                        <img src="/assets/svgs/download-icon-new.svg" class="h-[18px] w-[16px]" />
                                        <span v-if="isShowDes" class="text-[12px] font-medium">Tải xuống</span>
                                    </button>
                                </div>
                                <div class="flex justify-end mt-4 w-full">
                                    <a :href="route('ai-video.historyFaceSwap')" class="px-4 w-[180px] text-center py-2 bg-[#2C75E3] text-white font-bold text-[15px] rounded-[10px] hover:bg-blue-600 transition-colors duration-300"> Lịch sử </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Footer class="mt-auto"></Footer>
    </div>
    <div v-if="isLoading" class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
        <div class="loader"></div>
    </div>
</template>
<script setup>
import { ref, watch, computed, onMounted } from "vue";
import Layout from "@/Layouts/Client/ClientLayout.vue";
import { Head } from "@inertiajs/vue3";
import Footer from "../../Home/Components/Footer.vue";

import MainMenu from "@/Components/MainMenu.vue";
import Credit from "@/Components/Credit.vue";

import EditImage from "@/Pages/Client/AIVideo/EditImage/Index.vue";
import EditVideo from "@/Pages/Client/AIVideo/EditVideo/Index.vue";
import axios from "axios";

defineOptions({ layout: Layout });

const props = defineProps({
    ai_assistant: Object,
    images: {
        type: Array,
        default: () => [],
    },
});

const selectedImage = ref("/assets/img/img2Vid-default.jpg");
const selectedVideo = ref("https://segmind-sd-models.s3.amazonaws.com/display_images/videoFaceSwap/faceswap-input.mp4");
const errors = ref({});
const isLoading = ref(false);

const handleVideoUrlSubmit = (url) => {
    selectedVideo.value = url;
    console.log(url)
};

const videoRef = ref(null);
const videoResponse = ref();

watch(selectedVideo, (newVal) => {
    if (videoRef.value) {
        videoRef.value.load();
    }
});

onMounted(() => {
    videoRef.value = document.querySelector('#video');
});

const handleSubmit = async () => {
    await generateVideo();
};

const generateVideo = async () => {
    console.log('Generating video...');
    isLoading.value = true;

    try {
        const payload = {
            img: selectedImage.value,
            video: selectedVideo.value,
        };

        console.log('Sending payload:', payload);

        const response = await axios.post(
            route("ai-video.generate-face-swap-video"),
            payload
        );

        console.log('Response:', response);

        if (response.status === 200) {
            videoResponse.value = response?.data?.url || "";
        } else {
            videoResponse.value = "";
            alert("Có lỗi xảy ra khi tạo video. Vui lòng thử lại.");
        }
    } catch (error) {
        console.error("Có lỗi xảy ra:", error);
        videoResponse.value = "";
        alert("Có lỗi xảy ra khi tạo video. Vui lòng thử lại.");
    } finally {
        isLoading.value = false;
    }
};

</script>
<style scoped>
.loader {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #24AA69;
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}
</style>
