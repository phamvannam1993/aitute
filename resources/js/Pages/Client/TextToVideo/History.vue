<template>
    <Head title="Lịch sử tạo video - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData">
        <div class="flex justify-between items-center my-5">
                            <h2 class="text-[#092A99] font-bold text-2xl">
                                Lịch sử tạo video
                            </h2>
                            <a
                                :href="route('text-to-video.index')"
                                class="px-4 md:px-11 py-2 bg-[#2C75E3] text-white font-semibold rounded-[15px] flex justify-center items-center gap-1"
                                ><svg
                                    width="14"
                                    height="14"
                                    viewBox="0 0 14 14"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M1.93691 5.73778H5.70135V1.63111C5.70135 1.25778 5.8258 0.946666 6.07469 0.697776C6.32358 0.448888 6.63469 0.324444 7.00802 0.324444C7.40209 0.324444 7.71321 0.448888 7.94135 0.697776C8.19024 0.946666 8.31469 1.25778 8.31469 1.63111V5.73778H12.048C12.4214 5.73778 12.7221 5.85185 12.9502 6.08C13.1991 6.30815 13.3236 6.60889 13.3236 6.98222C13.3236 7.35555 13.1991 7.6563 12.9502 7.88444C12.7221 8.11259 12.4214 8.22667 12.048 8.22667H8.31469V12.3333C8.31469 12.7067 8.19024 13.0178 7.94135 13.2667C7.71321 13.5156 7.40209 13.64 7.00802 13.64C6.63469 13.64 6.32358 13.5156 6.07469 13.2667C5.8258 13.0178 5.70135 12.7067 5.70135 12.3333V8.22667H1.93691C1.58432 8.22667 1.28358 8.11259 1.03469 7.88444C0.806539 7.6563 0.692465 7.35555 0.692465 6.98222C0.692465 6.60889 0.806539 6.30815 1.03469 6.08C1.28358 5.85185 1.58432 5.73778 1.93691 5.73778Z"
                                        fill="white"
                                    />
                                </svg>
                                Tạo video mới</a
                            >
        </div>
        <div class="flex flex-col justify-between">
                            <div
                            class="columns-1 md:columns-2 xl:columns-4 gap-1"
                        >
                            <div
                                class="mb-1"
                                v-for="(item, index) in histories"
                                :key="index"
                            >
                                <a
                                        :class="`w-full relative aspect-video bg-slate-400 overflow-hidden group z-0`"
                                        href="javascript:void(0)"
                                    >
                                        <div :class="`${item.randomHeight} overflow-hidden break-inside-avoid-column relative bg-gray-200`">
                                            <div loading="lazy" v-if="item.thumbnail" :style="`background-image: url(${item.thumbnail});`"
                                                class="w-full break-inside-avoid-column h-full flex items-center justify-center bg-center bg-no-repeat bg-cover">
                                            </div>
                                            <div v-else
                                                class="w-full h-full flex items-center justify-center bg-gray-200 bg-center bg-no-repeat bg-contain">
                                                <img src="/assets/svgs/logo.svg" alt="" class="h-2/5 px-10">
                                            </div>
                                            <div class="text-white absolute bottom-0 left-0 bg-[#bbb] z-[1] px-2 flex w-full text-left items-center">
                                                <a :href="route('text-to-video.edit', { id: item.id })" class="w-9/12 line-clamp-1">
                                                    {{item.title}}
                                                </a>
                                                <div class="flex w-1/2 md:w-3/12">
                                                    <button
                                                        @click="handleDeleteVideo(item.id, item.title)"
                                                        class="p-2 rounded-full cursor-pointer hover:bg-white/30"
                                                    >
                                                    <img src="/assets/img/orion/icon/history/delete.svg" class="h-[24px] w-auto" />
                                                    </button>
                                                    <button
                                                        class="p-2 rounded-full hover:bg-white/30" @click="shareAIVideo(item)"
                                                    >
                                                    <img src="/assets/img/orion/icon/history/share.svg" class="h-[24px] w-auto" />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <a :href="route('text-to-video.edit', { id: item.id })" class="absolute inset-0 bg-black/30 opacity-100 transition-opacity duration-300 z-0"></a>
    
                                    </a>
                            </div>
                        </div>
                        <div class="flex justify-center my-4">
                            <Pagination
                                :totalPage="totalPage"
                                :initialPage="currentPage"
                                @updatePage="fetchPage"
                                />
                            <!-- <button
                                v-for="link in paginationLinks"
                                :key="link.label"
                                :disabled="!link.url || link.active"
                                @click="fetchData(link.url)"
                                :class="[
                                    'px-3 py-1 border',
                                    link.active
                                        ? 'bg-blue-500 text-white'
                                        : 'bg-white text-blue-500',
                                ]"
                            >
                                <span v-html="link.label"></span>
                            </button> -->
                        </div>
        </div>
    </Layout>
    <div
        v-if="isLoading"
        class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30"
    >
        <div class="loader"></div>
    </div>
    <div v-if="deleteVideo.isShowDelete" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50 text-black">
        <div class="bg-white p-8 rounded-lg shadow-lg m-4">
            <img src="/assets/img/logo-small.png" alt="Icon" class="w-30 h-12 mb-4" />
            <p class="mb-4">Bạn có muốn xóa video [ {{deleteVideo.videoName}} ] không?</p>
            <div class="flex justify-end space-x-4">
                <button @click="deleteVideo.isShowDelete = false" class="px-4 py-2 bg-gray-300 rounded-md">Hủy</button>
                <button @click="prepareDeleteFile(deleteVideo.videoID)" class="px-4 py-2 bg-blue-600 text-white rounded-md">Xác nhận</button>
            </div>
        </div>
    </div>
    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink"/>
    </Modal>
</template>

<script setup>
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
import Layout from "@/Layouts/Client/Layout.vue";
import FormShareLink from '@/Components/FormShareLink.vue';
import Modal from '@/Components/Modal.vue';
import { toast } from "vue3-toastify";
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    list: Array,
});
    console.log("🚀 ~ props:", props.list)

defineOptions({ layout: Layout });

const getRandomHeight = () => {
    const heights = ['h-64', 'h-80', 'h-96', 'h-48'];
    return heights[Math.floor(Math.random() * heights.length)];
}


const goBack = () => {
    window.history.back(); // Điều hướng về trang trước đó
};

// Thêm thuộc tính `isPlaying` vào từng video để quản lý việc phát video
const histories = ref(
    props.list?.data.map((item) => ({
        ...item,
        isPlaying: false, // Ban đầu, tất cả video đều không được phát,
        randomHeight: getRandomHeight()
    })) || []
);

const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
const showConfirmModal = ref(false);
const paginationLinks = ref(props.list?.links || []); // Pagination links
const isLoading = ref(false);
const selectedVideo = ref(null);
const videoDeleteId = ref(null);
const deleteVideo = ref({
    isShowDelete: false,
    videoName: "",
    videoID: null
})

const handleDeleteVideo = (id, name) => {
    if(id && name){
        deleteVideo.value = {
            isShowDelete: true,
            videoID: id,
            videoName: name
        }
    }
}


// Lazy load for videos
const openDetail = (item) => {
    selectedVideo.value = item;
};


const closeDetail = () => {
    selectedVideo.value = null;
};

const prepareDeleteFile = async (id) => {
    isLoading.value = true;
    try {
        const response = await axios.get(
            route('text-to-video.delete-video', { id: id })
        );
        deleteVideo.value = {
            isShowDelete: false,
            videoName: "",
            videoID: null
        };
        if (response.status === 200) {
            window.location.reload()
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi khi xóa:", error.message);
    } finally {
        isLoading.value = false;
        videoDeleteId.value = null; // Reset ID video sau khi xóa
    }
};

const cancelDelete = () => {
    showConfirmModal.value = false; // Đóng modal xác nhận xóa
    videoDeleteId.value = null; // Xóa ID video cần xóa
};

const confirmDelete = async () => {
    isLoading.value = true;

    try {
        const response = await axios.post(
            route("ai-video.delete", { id: videoDeleteId.value })
        );
        if (response.status === 200) {
            // Remove the deleted video from the list instead of reloading the page
            histories.value = histories.value.filter(
                (item) => item.id !== videoDeleteId.value
            );
            showConfirmModal.value = false;
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi khi xóa:", error.message);
    } finally {
        isLoading.value = false;
        videoDeleteId.value = null; // Reset ID video sau khi xóa
    }
};

const totalPage = ref(props.list?.last_page || 1);
const currentPage = ref(props.list?.current_page || 1);

const fetchPage = (page) => {
    const url = `/get-histories?page=${page}`;
    window.location.href = url;
    currentPage.value = props.list?.current_page;
};


const fetchData = (url) => {
    window.location.href = url;
};

const downloadVideo = (video) => {
    const link = document.createElement("a");
    link.href = video;
    link.download = "video.mp4";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
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

const shareAIVideo = async (item) => {
    try {
        const response = await axios.post(route("share-link.store"), {
            share_linkable_id: item.id,
            share_linkable_type: 'AIVideo',
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
    width: 60px;
    height: 60px;
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
