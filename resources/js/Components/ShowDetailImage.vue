<template>
    <div v-if="props.selectedImage" class="fixed inset-0 w-screen bg-black/90 z-[102]">
        <div class="flex min-h-screen h-full flex-col md:flex-row">
            <section class="w-10/12 md:w-9/12 lg:py-20 relative flex items-center mx-auto flex-1 justify-center px-4 lg:px-10" >
                <img
                    :src="props.selectedImage.s3_url"
                    alt="Selected Image"
                    class="rounded-lg object-contain h-full"
                />
                <button
                    @click="props.closeDetail"
                    class="absolute right-4 top-4 text-white text-2xl hover:bg-white/20 rounded-full w-10 h-10"
                >
                    &times;
                </button>
            </section>
            <section class="w-full md:w-4/12 bg-white/85 px-4 py-6 lg:py-10 border-l border-white/10 flex flex-col gap-2 lg:gap-4 max-h-full overflow-y-auto text-orion-primary">
                <div class="flex justify-end lg:hidden justify gap-6">
                    <button
                        class=" flex flex-col justify-start items-center gap-2 hover:text-blue-600 transition-colors duration-300" @click="createPost(props.selectedImage.s3_url)">
                        <img  src="/assets/img/orion/icon/taskbar/create_post.svg" class="h-[26px] w-[26px]" alt="Create Post" />
                        <span class=" text-[12px] font-medium">Tạo bài đăng</span>
                    </button>
                    <button
                        class=" flex flex-col items-center gap-2 "
                        @click="prepareDeleteFile(props.selectedImage.id)"
                    >
                        <img src="/assets/img/orion/icon/taskbar/delete.svg" class="h-[26px] w-[26px]" alt="Delete" />
                        <span class=" text-[12px] font-medium">Xoá</span>
                    </button>
                    <button
                        class=" flex flex-col items-center gap-2 "
                        @click="shareAIGeneratedMedia(props.selectedImage)"
                    >
                        <img src="/assets/img/orion/icon/taskbar/share.svg" class="h-[26px] w-[26px]" alt="Share" />
                        <span class=" text-[12px] font-medium">Chia sẻ</span>
                    </button>
                    <button
                        class=" flex flex-col items-center gap-2 "
                        @click="downloadImage(props.selectedImage.s3_url)"
                    >
                        <img src="/assets/img/orion/icon/taskbar/download.svg" class="h-[26px] w-[26px]" alt="Download" />
                        <span class="text-[12px] font-medium">Tải xuống</span>
                    </button>
                    
                </div>
                <div class="mb-2 max-h-[300px] overflow-y-auto lg:flex lg:flex-col">
                    <strong class="w-fit mr-2">Mô tả:</strong>
                    <span>{{
                        props.selectedImage.description != "null"
                            ? props.selectedImage.description
                            : "Không có"
                    }}</span>
                </div>
                <div
                    class="mb-2 lg:flex lg:flex-col"
                    v-if="
                        props.selectedImage.width != null &&
                        props.selectedImage.height != null
                    "
                >
                    <strong class="w-fit mr-2">Kích thước:</strong>
                    <span>{{
                        props.selectedImage.width + "x" + props.selectedImage.height
                    }}</span>
                </div>
                <div class="mb-2 lg:flex lg:flex-col">
                    <strong class="w-fit mr-2">Phong cách:</strong>
                    <span>{{ props.selectedImage.style || "Tự động" }}</span>
                </div>
                <div class="mb-2 lg:flex lg:flex-col">
                    <strong class="w-fit mr-2">Nghệ sĩ:</strong>
                    <span>{{ props.selectedImage.artist || "Tự động" }}</span>
                </div>

                <div class="lg:flex justify-evenly gap-6 mt-6 hidden">
                    <button
                        class="flex flex-col justify-start items-center gap-2 transition-colors duration-300" @click="createPost(props.selectedImage.s3_url)">
                        <img  src="/assets/img/orion/icon/taskbar/create_post.svg" class="h-[26px] w-[26px]" alt="Create Post" />
                        <span class="hidden xl:block text-[12px] font-medium">Tạo bài đăng</span>
                    </button>
                    <button
                        class="flex flex-col items-center gap-2"
                        @click="prepareDeleteFile(props.selectedImage.id)"
                    >
                        <img src="/assets/img/orion/icon/taskbar/delete.svg" alt="Delete" class="h-[26px] w-[26px]" />
                        <span class="hidden xl:block text-[12px] font-medium">Xoá</span>
                    </button>
                    <button
                        v-if="!props?.hiddenFeature?.includes('share')"
                        class="flex flex-col items-center gap-2"
                        @click="shareAIGeneratedMedia(props.selectedImage)"
                    >
                        <img src="/assets/img/orion/icon/taskbar/share.svg" alt="Share" class="h-[26px] w-[26px]" />
                        <span class="hidden xl:block text-[12px] font-medium">Chia sẻ</span>
                    </button>
                    <button
                        class="flex flex-col items-center gap-2"
                        @click="downloadImage(props.selectedImage.s3_url)"
                    >
                        <img src="/assets/img/orion/icon/taskbar/download.svg" alt="Download" class="h-[26px] w-[26px]" />
                        <span class="hidden xl:block text-[12px] font-medium">Tải xuống</span>
                    </button>
                </div>
                <h3 v-if="!props?.hiddenFeature?.includes('all')" class="mt-6" >Các tính năng khác:</h3>
                <div v-if="!props?.hiddenFeature?.includes('all')" class="rounded-xl flex  gap-16 lg:gap-4 flex-wrap justify-evenly">
                    <a v-if="!props?.hiddenFeature?.includes('face-swap')" :href="route('ai-image.generate-swap-face', {imageUrl: props.selectedImage.s3_url})" class="relative group flex items-center gap-2">
                        <img src="/assets/img/orion/icon/taskbar/change-face.svg" alt="AI Ảnh" class="min-w-[20px] w-[28px] md:w-[32px] xl:w-[28px] h-auto" />
                        <span class=" font-medium">Đổi mặt</span>
                    </a>
                    <!-- <a :href="route('text-to-video.index', {imageUrl: props.selectedImage.s3_url})" class="relative group flex flex-col lg:flex-row items-center gap-2">
                        <img src="/assets/img/text-to-video.png" alt="AI Chat" class="min-w-[20px] w-[28px] md:w-[32px] xl:w-[28px] h-auto" />
                        <span class=" font-medium">Văn bản thành video</span>
                    </a> -->
                    <a :href="route('ai-video.img2Video', {imageUrl: props.selectedImage.s3_url})" class="relative group flex items-center gap-2">
                        <img src="/assets/img/orion/icon/taskbar/video.svg" alt="AI Video" class="min-w-[20px] w-[28px] md:w-[32px] xl:w-[28px] h-auto" />
                        <span class=" font-medium">Video</span>
                    </a>
                </div>
            </section>
        </div>
    </div>
    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink"/>
    </Modal>
    <Popup 
        v-if="showConfirmModal"
        title="Cảnh báo !"
        message="Bạn có chắc chắn muốn xóa ảnh này không?"
        cancelButtonText="Huỷ"
        acceptButtonText="Xoá"
        @cancel="cancelDelete"
        @accept="confirmDelete"
    />
    <div
        v-if="isLoading"
        class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30"
    >
        <div class="loader"></div>
    </div>
</template>

<script setup>
import { onMounted, ref, onBeforeMount, watch  } from "vue";
import FormShareLink from '@/Components/FormShareLink.vue';
import Modal from '@/Components/Modal.vue';
import { toast } from "vue3-toastify";
import Popup from '@/Components/Popup/Index.vue'

const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
const showConfirmModal = ref(false);
const selectedImage = ref(null);
const imageDeleteId = ref(null);
const isLoading = ref(false);

const props = defineProps({
    selectedImage: Object,
    closeDetail: Function,
    hiddenFeature: Array,
    routerDelete: String,
    shareLinkableType: String
});

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

        const response = await axios.post(
            route(routeName, { id: imageDeleteId.value })
        );

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

const downloadImage = (image) => {
    var url = route(("ai-video.downloadFile"), {url:image, name:"image.png"})
    window.open(url, '_blank');
};

const createPost = (imageUrl) => {
    if (imageUrl) {
        let image = {
            s3_url: imageUrl,
            type: 'image',
        };
        localStorage.setItem("aiImage",JSON.stringify(image) );
        window.location.href = route('calendar');
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
            share_linkable_type: props.shareLinkableType || 'AIGeneratedMedia',
        });

        shareLink.value = route("share-link.show", { 'token': response.data.data.link_token })
        openShareLink();

    } catch (error) {
        toast.error("Chia sẻ tin thất bại");
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

