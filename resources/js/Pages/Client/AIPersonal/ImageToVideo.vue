<template>
    <section class="flex flex-col gap-5 p-4 w-full text-black bg-white drop-shadow-xl rounded-3xl">
        <div class="w-full flex flex-col lg:flex-row justify-between lg:items-center">
            <div>
                <div class="flex justify-start items-center gap-4 mb-1">
                    <img class="bg-[#D4D4D4] p-2 rounded-2xl" src="/assets/img/aiwow/robot.png" />
                    <div>
                        <h2 class="text-black font-bold text-base lg:text-[30px] leading-[32px] line-clamp-1">Tạo ảnh thành video</h2>
                    </div>
                </div>
                <p class="text-sm text-[#092A99] font-bold my-2">Thực hiện theo các bước sau:</p>
            </div>

            <button @click="props.scrollToVideoToVideo()" class="flex justify-center gap-3 rounded-[10px] bg-[#1FCDF3] text-white py-2 px-7 items-center hover:scale-105 transform transition-transform">
                <img class="size-7" src="/assets/svgs/aiwow/merge_video.svg" alt="merge video" />
                <p class="font-bold text-base text-center">Ghép video</p>
            </button>
        </div>

        <div class="flex flex-col gap-7 w-full">
            <label for="select-image" class="text-xs lg:text-sm flex gap-1 items-center mb-1 font-semibold">
                <div class="flex items-center py-1 px-2 rounded-2xl text-white bg-primary-color">
                    <img class="h-2 w-4" src="/assets/img/aiwow/right_arrow.png" />
                    <p>Bước 1</p>
                </div>
                <p>Chọn ảnh AI của bạn</p>
            </label>
            <p v-if="imagesHistory.length < 1" class="text-black">Hiện tại chưa có ảnh trong kho dữ liệu</p>

            <div class="max-h-[40vh] lg:max-h-[50vh] grid grid-cols-2 lg:grid-cols-4 gap-2 lg:gap-5 overflow-y-auto w-full">
                <button @click="imageSelected = collection" v-for="collection in imagesHistory" :key="collection.id" class="relative w-full">
                    <img :src="collection.s3_url" alt="img" class="w-full object-cover rounded-2xl" />
                    <input type="radio" :value="collection" v-model="imageSelected" class="absolute top-2 lg:top-4 right-2 lg:right-4 cursor-pointer outline-none size-6" />
                </button>
            </div>
            <div v-if="!loadingMore && currentPage != lastPage" class="flex justify-center mt-4">
                <button @click="loadMoreHistories" class="bg-blue-500 text-white px-4 py-2 rounded-full">Xem thêm</button>
            </div>
            <div v-if="loadingMore" class="flex justify-center mt-4">
                <span>Đang tải...</span>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-10 justify-between mt-10">
            <section>
                <form @submit.prevent="handleSubmit" class="flex flex-col gap-5 items-start">
                    <div v-if="imageSelected" class="flex gap-2 font-semibold justify-center items-center">
                        <img src="/assets/img/aiwow/homepage/collection.png" class="h-7 w-6" alt="collection" />
                        <p class="text-sm">Ảnh đã chọn:</p>
                    </div>
                    <img v-if="imageSelected" class="w-1/2 rounded-2xl" :src="imageSelected.s3_url" alt="image selected" />
                    <div class="grid grid-cols-2 w-full">
                        <label for="video-artist" class="text-xs lg:text-sm flex gap-1 items-center mb-1 font-semibold">
                            <div class="flex items-center py-1 px-2 rounded-2xl text-white bg-primary-color">
                                <img class="h-2 w-4" src="/assets/img/aiwow/right_arrow.png" />
                                <p>Bước 2</p>
                            </div>
                            <p>Thời lượng</p>
                        </label>
                        <select
                            v-model="videoDuration"
                            :class="{
                                'bg-gray-200 border-gray-200': !videoDuration,
                                'bg-white border-[#2C75E3]': videoDuration,
                            }"
                            class="block mt-1 text-[14px] appearance-none w-full text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                            id="video-artist"
                        >
                            <option v-for="duration in durations" :value="duration" :key="duration">{{ duration }} giây</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 w-full">
                        <label for="video-dimensions" class="text-xs lg:text-sm flex gap-1 items-center mb-1 font-semibold">
                            <div class="flex items-center py-1 px-2 rounded-2xl text-white bg-primary-color">
                                <img class="h-2 w-4" src="/assets/img/aiwow/right_arrow.png" />
                                <p>Bước 3</p>
                            </div>
                            <p>Kích thước</p>
                        </label>
                        <select
                            v-model="videoDimensions"
                            id="video-dimensions"
                            :class="{
                                'bg-gray-200 border-gray-200': !videoDimensions,
                                'bg-white border-[#2C75E3]': videoDimensions,
                            }"
                            class="block mt-1 text-[14px] appearance-none text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500 w-full"
                        >
                            <option v-for="dimension in validDimensions" :value="dimension" :key="dimension">
                                {{ dimension }}
                            </option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 w-full">
                        <label for="submit-button" class="text-xs lg:text-sm flex gap-1 items-center mb-1 font-semibold">
                            <div class="flex items-center py-1 px-2 rounded-2xl text-white bg-primary-color">
                                <img class="h-2 w-4" src="/assets/img/aiwow/right_arrow.png" />
                                <p>Bước 4</p>
                            </div>
                            <p>Bấm vào đây</p>
                        </label>
                        <button :disabled="isLoading" type="submit" class="w-full bg-primary-color text-white font-bold rounded-xl py-2 hover:scale-105 transform transition-transform">Tạo video</button>
                    </div>
                </form>
            </section>

            <section class="flex flex-col gap-5">
                <p class="italic font-thin text-primary-color text-sm">Hiển thị kết quả</p>
                <div class="flex justify-center items-center mb-4">
                    <video v-if="videoRef && !isLoading" :src="videoRef" alt="video-generate-by-AI" controls="" preload="auto" class="w-full h-auto md:max-w-[500px] md:max-h-[500px] object-cover" />
                    <div v-else class="bg-[#E6E6E6] w-full h-[50vh] rounded-2xl showLoaderVideo items-center flex justify-center">
                        <div v-if="isLoading">
                            <div class="loaderVideo"></div>
                            <p class="text-loading">Video đang được tạo. Vui lòng chờ !</p>
                        </div>
                        <img v-else src="\assets\img\aiwow\ai_image\placeholder.png" alt="" class="w-20" />
                    </div>
                </div>
                <TaskBar :isActive="resultValue" :selectedImage="resultValue" :shareLinkableType="'AIGeneratedMedia'" :features="['lipsync']" />
                <div class="w-full flex justify-end">
                    <a :href="route('ai-video.history')" class="px-4 w-[180px] text-center py-2 bg-[#2C75E3] text-white font-bold text-[15px] rounded-[10px] h-fit hover:scale-105 transform transition-transform">Lịch sử</a>
                </div>
            </section>
        </div>
    </section>
</template>

<script setup>
import { computed, ref, onMounted } from "vue";
import {toast} from "vue3-toastify";
import {usePage} from "@inertiajs/vue3";

import TaskBar from "@/Components/TaskBar.vue";

const props = defineProps({
    scrollToVideoToVideo: {
        type: Function,
    },
});
const page = usePage()

const imagesHistory = ref([]);

const imageSelected = ref(imagesHistory.value[0]);
const videoModel = ref("Runway/gen3-alphaturbo");
const videoDuration = ref(5);
const videoDimensions = ref("16:9");
const resultValue = ref(null);
const isLoading = ref(false);
const videoRef = ref("");

const durations = computed(() => {
    if (videoModel.value === "Kling") {
        videoDuration.value = 5;
        return [5, 10];
    }
    return [5, 10];
});

const validDimensions = computed(() => {
    if (videoModel.value === "Kling") {
        videoDimensions.value = "16:9";
        return ["16:9", "9:16", "1:1"];
    }
    if (videoModel.value === "Runway/gen3-alphaturbo") {
        videoDimensions.value = "16:9";
        return ["16:9", "9:16"];
    }
});

const checkCredit = async () => {
    try {
        const formData = new FormData();
        formData.append("type", "image_to_video");

        // Gọi API để kiểm tra credit của user
        const response = await axios.post(route("credit.check_credit"), formData);
        if (response.data.success) {
            // Đủ credit để tiếp tục
            return true;
        } else if (response.data.is_vip_expired) {
            showAffKeyPopup.value = true;
            if (response.data.affCode == "aff_trial") {
                popupMessage.value = " Gói của bạn đã hết hạn. Xin vui lòng gia hạn tài khoản để tiếp tục sử dụng tính năng này.";
                acceptUpgrade.value = false;
            }
            return false;
        } else {
            additionalCredit.value = response?.data?.data?.total_price - pageProps.auth.user.credit;
            // Không đủ credit, hiển thị modal yêu cầu nạp thêm
            showBuyCreditModal();
            return false;
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi khi kiểm tra credit:", error);
        toast.error("Không thể kiểm tra credit. Vui lòng thử lại.");
        if (error.response && error.response.status === 422) {
            const errors = error.response.data.errors;
            Object.values(errors).forEach((errorMessages) => {
                if (Array.isArray(errorMessages)) {
                    errorMessages.forEach((error) => {
                        toast.error(error);
                    });
                } else {
                    toast.error(errorMessages);
                }
            });
        } else {
            if (error.response) {
                toast.error(error.response.data.message || "Đã xảy ra lỗi.");
            } else if (error.request) {
                toast.error("Không thể kết nối đến server.");
            } else {
                toast.error("Lỗi: " + error.message);
            }
        }
        return false;
    }
};
const handleSubmit = async () => {
    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        isLoading.value = false;
        return; // Dừng nếu không đủ credit
    }

    await generateVideo();
};

const generateVideo = async () => {
    isLoading.value = true;

    try {
        const payload = {
            duration: videoDuration.value,
            resolution: videoDimensions.value,
            imageUrl: imageSelected.value.s3_url,
        };

        console.log(payload)

        const response = await axios.post(route("ai-video.generate-img-to-video"), payload);

        if (response.status === 200) {
            videoRef.value = response?.data?.url || "";

            if (!!response.data.thumbnail) {
                saveData(response.data.thumbnail, response.data.path);
            } else {
                saveData(useSearchImage.value ? imagePreview.value : imageUrl.value, response.data.path);
            }
        } else {
            videoRef.value = "";
            toast.error("Có lỗi xảy ra khi tạo video. Vui lòng thử lại.");
        }
    } catch (error) {
        console.error("Có lỗi xảy ra:", error);
        videoRef.value = "";
        toast.error("Có lỗi xảy ra khi tạo video. Vui lòng thử lại.");
    } finally {
        isLoading.value = false;
    }
};

const saveData = async (thumbnail, path) => {
    const response = await axios.post(route("ai-video.store"), {
        type: "video",
        s3_url: path,
        duration: videoDuration.value,
        model: "img-video",
        thumbnail: thumbnail ? thumbnail : DEFAULT_SEARCH_IMAGE,
    });
    if (response.status === 200) {
        resultValue.value = response.data.response;
    }
};

const currentPage = ref(1);
const loadingMore = ref(false);
const lastPage = ref();

const getMyAIImageHistory = async (page) => {
    try {
        const response = await axios.get(route("ai-image.get-history-my-ai-image", { page: page }));
        console.log(response.data);
        imagesHistory.value.push(...response.data.histories.data);
        if(!page){
            imageSelected.value = imagesHistory.value[0];
        }
        currentPage.value = response.data.histories.current_page;
        lastPage.value = response.data.histories.last_page;
    } catch (error) {
        console.error("Error fetching templates:", error);
    }
};

const loadMoreHistories = () => {
    if (currentPage.value < lastPage.value) {
        loadingMore.value = true;
        getMyAIImageHistory(currentPage.value + 1).finally(() => {
            loadingMore.value = false;
        });
    }
};

onMounted(() => {
    if (page.props.auth.user) {
        getMyAIImageHistory()
    }
});

</script>

<style>
.loaderVideo {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #24AA69;
    border-radius: 50%;
    width: 120px;
    height: 120px;
    margin: auto;
    animation: spin 2s linear infinite;
}

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
