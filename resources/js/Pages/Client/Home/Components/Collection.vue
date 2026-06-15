<template>
    <div class="w-full md:w-10/12 flex flex-col gap-6 pt-6">
        <section @click="selectAssistant()" :class="showImageToVideo || showVideoToVideo || isOpenMyAi ? 'hidden' : 'block'">
            <div class="w-full sm:w-10/12 flex gap-2 items-center my-2 px-2 pb-0">
                <p class="text-black font-bold text-xs lg:text-base">HÌNH ẢNH/VIDEO LÀM THƯƠNG HIỆU CÁ NHÂN</p>
            </div>
            <div class="w-full text-xs lg:text-base grid grid-cols-2 lg:grid-cols-4 gap-4 2xl:gap-5 bg-white rounded-3xl drop-shadow-xl px-4 py-6 text-[#092A99] font-bold">
                <div v-for="(collection, index) in listCollection" :key="collection.id" @click="selectCollection(collection)" class="flex flex-col justify-center items-center cursor-pointer max-md:h-[40vh]">
                    <div :class="`slide${index}`" class="relative w-full h-full">
                        <img :src="`/assets/img/slides/${collection?.categories[0]?.slug}_slide_1.jpg` || collection?.s3_url" alt="" :class="`slide-img-0`" class="absolute inset-0 w-full h-full rounded-3xl object-cover" />
                        <img :src="`/assets/img/slides/${collection?.categories[0]?.slug}_slide_2.jpg` || collection?.s3_url" alt="" :class="`slide-img-1`" class="absolute inset-0 w-full h-full rounded-3xl object-cover" />
                        <img :src="`/assets/img/slides/${collection?.categories[0]?.slug}_slide_3.jpg` || collection?.s3_url" alt="" :class="`slide-img-2`" class="absolute inset-0 w-full h-full rounded-3xl object-cover" />
                    </div>
                    <p class="text-center mt-2 uppercase line-clamp-1">{{ collection.title }}</p>
                    <div class="grid grid-cols-3">
                </div>
                </div>
                <a @click.prevent="toggleImgToVideo()" class="flex flex-col justify-center items-center cursor-pointer relative rounded-3xl max-md:h-[40vh]">
                    <img src="/assets/img/aiwow/homepage/thumbnail_04.png" alt="" class="w-full h-full rounded-3xl object-cover" />
                    <p class="text-center mt-2">VIDEO THƯƠNG HIỆU</p>
                    <img src="/assets/img/aiwow/homepage/play_button.png" alt="" class="w-20 h-20 absolute inset-0 m-auto" />
                </a>
            </div>
        </section>

        <div ref="imageToVideoSection" :class="showImageToVideo ? 'block' : 'hidden'">
            <ImageToVideo :scrollToVideoToVideo="scrollToVideoToVideo" />
        </div>
        <div ref="videoToVideoSection" :class="showVideoToVideo ? 'block' : 'hidden'">
            <VideoToVideo />
        </div>

        <!-- <section v-if="isShowCollection" class="bg-white rounded-3xl drop-shadow-xl p-5 text-[#092A99]">
            <div class="flex flex-col lg:flex-row gap-2 justify-between lg:items-center mb-4">
                <div class="flex gap-2 items-center">
                    <img src="/assets/img/aiwow/homepage/logo.png" alt="Logo" class="w-11 h-11" />
                    <p class="font-bold text-xs lg:text-base">Chọn mẫu ảnh: {{ collectionSelected.title }}</p>
                </div>
                <div class="flex gap-2 justify-between">
                    <div class="relative inline-block" ref="dropdownRef">
                        <button @click="isDropdownOpen = !isDropdownOpen" class="flex justify-between text-primary-color gap-2 items-center border-2 border-[#D4D4D4] px-3 h-10 rounded-[20px] outline-none min-w-48">
                            <img src="/assets/img/aiwow/homepage/collection.png" class="h-7 w-6" alt="collection" />
                            <div class="flex gap-2 items-center">
                                <p class="">{{ collectionSelected.title }}</p>
                                <svg :class="isDropdownOpen ? 'rotate-0' : 'rotate-180'" width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 1L5 5L9 1" stroke="#C5C5C5" />
                                </svg>
                            </div>
                        </button>

                        <div v-if="isDropdownOpen" class="absolute mt-2 bg-white border-2 border-[#D4D4D4] rounded-[20px] shadow-lg w-full z-10">
                            <div v-for="collection in collectionActive" :key="collection.id" class="px-4 py-2 hover:bg-gray-100 rounded-[20px] cursor-pointer text-black capitalize" @click="selectCollection(collection)">
                                {{ collection.title }}
                            </div>
                        </div>
                    </div>
                    <button @click="isShowCollection = false" class="flex items-center justify-center text-[#C5C5C5] border-[#D4D4D4] border-2 rounded-full h-10 w-10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="w-full text-xs lg:text-base grid grid-cols-2 lg:grid-cols-4 gap-3 text-[#092A99] font-bold h-[50vh] 2xl:h-[60vh] overflow-y-auto">
                <img v-for="template in listTemplate" :key="template.id" :src="template.s3_url" alt="thumbnail" class="cursor-pointer rounded-xl w-full h-full object-cover" @click="handleRedirect(template.s3_url)" />
            </div>
            <div v-if="!loadingMore && currentPage != lastPage" class="flex justify-center mt-4">
                <button @click="loadMoreTemplates" class="bg-blue-500 text-white px-4 py-2 rounded-full">Xem thêm</button>
            </div>
            <div v-if="loadingMore" class="flex justify-center mt-4">
                <span>Đang tải...</span>
            </div>
        </section> -->

        <section v-if="isOpenMyAi" ref="myAiImageSection">
            <MyAIComponent :listCollection="listCollection" :collectionName="collectionSelected?.title" :collection-selected="collectionSelected" :my_ai_image_models="my_ai_image_models" />
        </section>
    </div>
</template>

<script setup>
import { defineEmits, onBeforeUnmount, onMounted, ref, nextTick } from "vue";
import ImageToVideo from "../../AIPersonal/ImageToVideo.vue";
import VideoToVideo from "../../AIPersonal/VideoToVideo.vue";
import axios from "axios";
import MyAIComponent from "../../AIImage/MyAIComponent.vue";

const props = defineProps({
    my_ai_assistant: Object,
    my_ai_image_models: Array,
});

const emit = defineEmits(["updateListAi", "selectAssistant"]);

const selectAssistant = () => {
    emit("selectAssistant");
};

const listCollection = ref([]);
const isShowCollection = ref(false);
const collectionSelected = ref(null);
const collectionActive = ref(null);
const isDropdownOpen = ref(false);
const dropdownRef = ref(null);
const listTemplate = ref([]);
const currentPage = ref();
const loadingMore = ref(false);
const lastPage = ref();
const showImageToVideo = ref(false);
const showVideoToVideo = ref(false);

const videoToVideoSection = ref(null);
const myAiImageSection = ref(null);
const imageToVideoSection = ref(null);

const scrollToVideoToVideo = async () => {
    showVideoToVideo.value = true;
    await nextTick();
    videoToVideoSection.value?.scrollIntoView({ behavior: "smooth" });
};

const isOpenMyAi = ref(false);
const isShowImageToVideo = ref(false);
// Emit sự kiện
const handleRedirect = (collection) => {
    window.location.href = route("ai-image.my-ai-image", { videoRef: collection });
};
const showCollection = (collection) => {
    const indexCollection = collection.categories.findIndex((item) => item.slug === "noel-nu");
    collectionSelected.value = collection?.categories[indexCollection === -1 ? 0 : indexCollection];
    collectionActive.value = collection.categories;
    isShowCollection.value = true;
    currentPage.value = 1;
    lastPage.value = 1;
    listTemplate.value = [];
    getTemplateByCategory(collectionSelected.value);
};

const selectCollection = async (collection) => {
    await checkPackage(5, "myAi");

    currentPage.value = 1;
    lastPage.value = 1;
    listTemplate.value = [];
    collectionSelected.value = collection;

    await nextTick();
    myAiImageSection.value?.scrollIntoView({ behavior: "smooth" });
};
const toggleImgToVideo = async () => {
    await checkPackage(6, 'imageToVideo');
    await nextTick();
    imageToVideoSection.value?.scrollIntoView({ behavior: "smooth" });
}

const closeDropdown = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isDropdownOpen.value = false;
    }
};

const getCollections = async () => {
    try {
        const response = await axios.get(route("ai-image.get-list-collections"));
        console.log(response.data.listCollections.data);
        listCollection.value = response.data.listCollections.data;
    } catch (error) {
        console.error("Error fetching collections:", error);
    }
};

const getTemplateByCategory = async (collection, page) => {
    try {
        const response = await axios.get(route("ai-image.get-list-template-by-category", { id: collection.id, page: page }));
        console.log(response.data);
        listTemplate.value.push(...response.data.templates.data);
        currentPage.value = response.data.templates.current_page;
        lastPage.value = response.data.templates.last_page;
    } catch (error) {
        console.error("Error fetching templates:", error);
    }
};

const loadMoreTemplates = () => {
    console.log(currentPage.value);
    console.log(lastPage.value);
    if (currentPage.value < lastPage.value) {
        loadingMore.value = true;
        getTemplateByCategory(collectionSelected.value, currentPage.value + 1).finally(() => {
            loadingMore.value = false;
        });
    }
};

// Hàm kiểm tra gói
const checkPackage = async (idPackage, modal, isShow, redirect = false) => {
    try {
        const response = await axios.get(route("checkPackagePermission"), {
            params: {
                id: idPackage,
            },
        });
        if (response.data.status === "upgrade_required") {
            emit("openModelPackage");
        } else {
            if (modal === "myAi") {
                isOpenMyAi.value = true;
                showImageToVideo.value = false;
                showVideoToVideo.value = false;
            } else {
                showImageToVideo.value = true;
                isOpenMyAi.value = false;
            }
        }
    } catch (error) {
        console.log("🚀 ~ checkPackage ~ error:", error);
    } finally {
    }
};

onMounted(() => {
    document.addEventListener("click", closeDropdown);
    getCollections();
});

onBeforeUnmount(() => {
    document.removeEventListener("click", closeDropdown);
});
</script>
<style>
.slide-img-0,
.slide-img-1,
.slide-img-2 {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    animation: fade 6s infinite;
    transition: opacity 1s ease-in-out;
}

@keyframes fade {
    0% { opacity: 0; }
    25% { opacity: 1; }
    50% { opacity: 0; }
    100% { opacity: 0; }
}

.slide-img-0 { animation-delay: 0s; }
.slide-img-1 { animation-delay: 2s; }
.slide-img-2 { animation-delay: 4s; }
</style>
