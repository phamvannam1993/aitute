<template>
    <!-- <div :class="!props.isActive ? 'pointer-events-none opacity-70' : ''" -->
    <div :class="!props.isActive ? 'pointer-events-none opacity-70' : ''" class="grid grid-cols-2 gap-2 justify-between w-full mt-2">
        <!-- <button type="button" @click="props.handleSharpenClick && props.handleSharpenClick()"
            v-if="props?.features?.includes('sharpen')" class="relative group md:min-w-[120px] justify-center rounded-full bg-primary-color flex gap-1 px-2 items-center">
            <img src="/assets/img/aiwow/studio.png" alt="AI Video" class="hidden md:block max-lg:w-4"
                class="min-w-[20px] w-[32px] h-[32px] lg:w-[28px] lg:h-[28px]" />
            <span
                class="font-medium hover:scale-105 text-white lg:px-1 xl:px-2 py-1.5 text-[10px] text-nowrap lg:text-sm">Làm
                nét</span>
        </button> -->
        <a v-if="props?.features?.includes('background')"
            :href="route('ai-background.indexV2', { imageUrl: props.selectedImage?.s3_url })"
            class="relative group md:min-w-[120px] justify-center rounded-full bg-[#149CBE] flex gap-1 px-2 items-center">
            <img src="/assets/svgs/aiwow/taskbar/background.svg" alt="AI Video" class="hidden md:block max-lg:w-4"/>
            <span
                class="font-medium hover:scale-105 text-white lg:px-1 xl:px-1 py-1.5 text-nowrap text-sm">Phối cảnh</span>
        </a>
                <a v-if="props?.features?.includes('swap_face')"
            :href="route('ai-image.generate-swap-face', { imageUrl: props.selectedImage?.s3_url })"
            class="relative group md:min-w-[120px] justify-center rounded-full bg-[#149CBE] flex gap-1 px-2 items-center">
            <img src="/assets/svgs/aiwow/taskbar/faceswap.svg" alt="AI Video" class="hidden md:block max-lg:w-4" />
            <span
                class="font-medium hover:scale-105 text-white lg:px-1 xl:px-1 py-1.5 text-nowrap text-sm">Đổi
                mặt</span>
        </a>
        <a v-if="props?.features?.includes('video')"
            :href="route('ai-video.img2Video', { imageUrl: props.selectedImage?.s3_url })"
            class="relative group md:min-w-[120px] justify-center rounded-full bg-[#149CBE] flex gap-1 px-2 items-center">
            <img src="/assets/svgs/aiwow/taskbar/video.svg" alt="AI Video" class="hidden md:block max-lg:w-4" />
            <span
                class="font-medium hover:scale-105 text-white lg:px-1 xl:px-1 py-1.5 text-nowrap text-sm">Video</span>
        </a>
        <a v-if="props?.features?.includes('lipsync')"
            :href="route('lipsync.lipsync', { videoUrl: props.selectedImage?.s3_url })"
            class="relative group md:min-w-[120px] justify-center rounded-full bg-[#149CBE] flex gap-1 px-2 items-center">
            <img src="/assets/svgs/aiwow/taskbar/lipsync.svg" alt="AI Video" class="hidden md:block max-lg:w-4" />
            <span
                class="font-medium hover:scale-105 text-white lg:px-1 xl:px-1 py-1.5 text-nowrap text-sm">Lồng
                tiếng</span>
        </a>
        <a v-if="props?.features?.includes('background_audio')" @click="$refs.fileInputAudio.click()"
            href="javascript:void(0)" class="relative group md:min-w-[120px] justify-center rounded-full bg-[#149CBE] flex gap-1 px-2 items-center">
            <div v-if="isCallUploadAudio" class="loaderAudio"></div>
            <img v-if="!isCallUploadAudio" src="/assets/svgs/aiwow/taskbar/music.svg" alt="AI Video" class="hidden md:block max-lg:w-4"/>
            <span
                class="font-medium hover:scale-105 text-white lg:px-1 xl:px-1 py-1.5 text-nowrap text-sm">Nhạc
                nền</span>
            <input type="file" id="audio-file" @change="handleFileChange" accept="audio/*" class="hidden"
                ref="fileInputAudio" :disabled="isDisabled" />
        </a>
        <button type="button" v-if="props?.features?.includes('background_music')"
            class="relative group md:min-w-[120px] justify-center rounded-full bg-[#149CBE] flex gap-1 px-2 items-center">
            <img src="/assets/svgs/aiwow/taskbar/music.svg" alt="AI Video" class="hidden md:block max-lg:w-4" />
            <span
                class="font-medium hover:scale-105 text-white lg:px-1 xl:px-1 py-1.5 text-nowrap text-sm">Nhạc
                nền</span>
        </button>
        <div class="relative text-nowrap lg:min-w-[120px]">
            <button type="button" @click="toggleShowMore"
                class="relative group md:min-w-[120px] justify-center flex bg-[#149CBE] w-full py-1.5 min-h-8 px-2 text-nowrap lg:px-2.5 rounded-full gap-1 items-center">
                <img src="/assets/svgs/aiwow/taskbar/more.svg" alt="AI Video" class="w-4 h-4" />
                <span class="font-medium max-lg:text-xs text-nowrap lg:text-sm hover:scale-105 rounded-md text-white">Khác</span>
            </button>
            <div v-if="isShowMore"
                class="bg-white text-black menu-container absolute top-0 w-[10rem] right-20 z-[100] border-2 border-[#E6E6E6] gap-4 flex flex-col p-3 rounded-[20px]">
                <button type="button" @click="createPost(props.selectedImage.s3_url)"
                    class="flex text-nowrap lg:min-w-[20%] py-1.5 px-2 lg:px-2.5 rounded-full hover:bg-[#FFFBE2] gap-1 items-center transition-colors duration-300">
                    <img src="/assets/svgs/aiwow/taskbar/post.svg" class="max-sm:w-5 w-6"
                        alt="Create Post" />
                    <span class="font-medium max-lg:text-xs lg:text-sm hover:scale-105 rounded-md text-nowrap">Đăng bài</span>
                </button>
                <button type="button" @click="shareAIGeneratedMedia(props.selectedImage)"
                    class="flex text-nowrap lg:min-w-[20%] py-1.5 px-2 lg:px-2.5 rounded-full hover:bg-[#FFFBE2] gap-1 items-center">
                    <img src="/assets/svgs/aiwow/taskbar/share2.svg" class="max-sm:w-5 w-6" />
                    <span class="font-medium max-lg:text-xs lg:text-sm hover:scale-105 rounded-md text-nowrap">Chia sẻ</span>
                </button>
                <button type="button"
                    class="flex text-nowrap lg:min-w-[20%] py-1.5 px-2 lg:px-2.5 rounded-full hover:bg-[#FFFBE2] gap-1 items-center"
                    @click="downloadContent(props.selectedImage.s3_url)">
                    <img src="/assets/svgs/aiwow/taskbar/download2.svg" alt="Download" class="max-sm:w-5 w-6" />
                    <span class="font-medium max-lg:text-xs lg:text-sm hover:scale-105 rounded-md text-nowrap">Tải xuống</span>
                </button>
            </div>
        </div>
    </div>
    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink" align-items="items-center">
        <FormShareLink :shareLink="shareLink" />
    </Modal>
    <div v-if="showConfirmModal" class="fixed inset-0 flex items-center justify-center z-[103] bg-black bg-opacity-30">
        <div
            class="bg-[url('../../public/assets/img/bg-modal.png')] bg-cover bg-center rounded-lg p-5 shadow-lg absolute z-[51] md:w-[500px] md:h-[346px] md:rounded-[41px] flex flex-col justify-start items-center">
            <img src="/assets/img/model-delete-icon.png" />
            <h3 class="text-lg font-semibold text-black">Cảnh báo !</h3>
            <p class="text-black">Bạn có chắc chắn muốn xóa nội dung này không?</p>
            <div class="flex justify-center mt-4">
                <button type="button" @click="cancelDelete"
                    class="px-4 md:px-11 py-2 border-[1px] border-[#2C75E3] text-black font-semibold rounded-[15px] mr-2">Huỷ</button>
                <button type="button" @click="confirmDelete"
                    class="px-4 md:px-11 py-2 bg-[#2C75E3] text-white font-semibold rounded-[15px]">Xoá</button>
            </div>
        </div>
        <div class="fixed inset-0 bg-black opacity-50"></div>
    </div>
    <div v-if="isLoading" class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
        <div class="loader"></div>
    </div>
</template>

<script setup>
import { onMounted, ref, onBeforeMount, watch, onBeforeUnmount } from "vue";
import FormShareLink from "@/Components/FormShareLink.vue";
import Modal from "@/Components/Modal.vue";
import { toast } from "vue3-toastify";

const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
const showConfirmModal = ref(false);
const selectedImage = ref(null);
const imageDeleteId = ref(null);
const isLoading = ref(false);
const isShowMore = ref(false);

const props = defineProps({
    selectedImage: Object,
    closeDetail: Function,
    features: Array,
    routerDelete: String,
    shareLinkableType: String,
    handleSharpenClick: Function,
    type: {
        type: String,
        default: "image"
    },
    isActive: Boolean,
    isCallUploadAudio: {
        type: Boolean,
        required: true,
    },
    isDisabled: {
        type: Boolean,
        required: true,
    },
});

const emit = defineEmits(['handle-audio-file-change']);
const handleFileChange = (event) => {
    emit('handle-audio-file-change', event);
};

const toggleShowMore = (event) => {
    event.stopPropagation(); // Ngăn chặn sự kiện lan ra ngoài
    isShowMore.value = !isShowMore.value;
};

const closeMenu = (event) => {
    const menu = document.querySelector('.menu-container'); // Đặt class cho menu container
    if (menu && !menu.contains(event.target)) {
        isShowMore.value = false;
        console.log('eeee')
    }
};

const openDetail = (item) => {
    currentImage.value = item;
};

const prepareDeleteFile = (id) => {
    imageDeleteId.value = id;
    showConfirmModal.value = true;
};

const confirmDelete = async () => {
    isLoading.value = true;

    try {
        const routeName = props.routerDelete || "ai-image.delete";

        const response = await axios.post(route(routeName, { id: imageDeleteId.value }));

        if (response.status === 200) {
            window.location.reload();
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi khi xóa:", error.message);
    } finally {
        isLoading.value = false;
    }
};

const cancelDelete = () => {
    showConfirmModal.value = false;
};

const downloadContent = (url) => {
    if (props.type === 'image') {
        downloadImage(url);
    } else if (props.type === 'video') {
        downloadVideo(url);
    } else {
        downloadAudio(url)
    }
}
const downloadImage = (image) => {
    var url = route("ai-video.downloadFile", { url: image, name: "image.png" });
    window.open(url, "_blank");
};
const downloadVideo = async (video) => {
    var url = route(("ai-video.downloadFile"), { url: video, name: "video.mp4" })
    window.location.href = url
};
const createPost = (imageUrl) => {
    if (imageUrl) {
        let image = {
            s3_url: imageUrl,
            type: props.type || "image",
        };
        localStorage.setItem("aiImage", JSON.stringify(image));
        window.location.href = route("calendar");
    }
};

const openShareLink = () => {
    isShowShareLinkModal.value = true;
};

const closeShareLink = () => {
    isShowShareLinkModal.value = false;
};

const shareAIGeneratedMedia = async (item) => {
    try {
        const response = await axios.post(route("share-link.store"), {
            share_linkable_id: item.id,
            share_linkable_type: props.shareLinkableType || "AIGeneratedMedia",
        });

        shareLink.value = route("share-link.show", { token: response.data.data.link_token });
        openShareLink();
    } catch (error) {
        toast.error("Chia sẻ tin thất bại");
    }
};

onMounted(() => {
    document.addEventListener('click', closeMenu);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', closeMenu);
});
</script>

<style scoped>
.loader {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #3498db;
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

.loaderAudio {
    width: 20px !important;
    height: 20px !important;
    border-radius: 50%;
    margin-left: 0px !important;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #3498db;
    animation: spin 2s linear infinite;
}
</style>