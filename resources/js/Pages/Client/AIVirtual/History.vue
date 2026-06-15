<template>
    <Head title="Lịch sử tạo video - AI 3 GỐC - Cộng đồng AI tử tế" />

    <Layout :breadcrumbs="breadcrumbsData">
        <div class="flex justify-between items-center my-5">
                        <h2 class="text-orion-primary font-bold text-2xl">
                            Lịch sử tạo MC Ảo
                        </h2>
                        <a
                            :href="route('mc-virtual.index')"
                            class="uppercase px-4 md:px-11 py-2 bg-[#149CBE] text-white font-semibold rounded-[15px] flex justify-center items-center gap-1"
                        >
                            <!-- SVG Icon -->
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M1.93691 5.73778H5.70135V1.63111C5.70135 1.25778 5.8258 0.946666 6.07469 0.697776C6.32358 0.448888 6.63469 0.324444 7.00802 0.324444C7.40209 0.324444 7.71321 0.448888 7.94135 0.697776C8.19024 0.946666 8.31469 1.25778 8.31469 1.63111V5.73778H12.048C12.4214 5.73778 12.7221 5.85185 12.9502 6.08C13.1991 6.30815 13.3236 6.60889 13.3236 6.98222C13.3236 7.35555 13.1991 7.6563 12.9502 7.88444C12.7221 8.11259 12.4214 8.22667 12.048 8.22667H8.31469V12.3333C8.31469 12.7067 8.19024 13.0178 7.94135 13.2667C7.71321 13.5156 7.40209 13.64 7.00802 13.64C6.63469 13.64 6.32358 13.5156 6.07469 13.2667C5.8258 13.0178 5.70135 12.7067 5.70135 12.3333V8.22667H1.93691C1.58432 8.22667 1.28358 8.11259 1.03469 7.88444C0.806539 7.6563 0.692465 7.35555 0.692465 6.98222C0.692465 6.60889 0.806539 6.30815 1.03469 6.08C1.28358 5.85185 1.58432 5.73778 1.93691 5.73778Z"
                                    fill="white"
                                />
                            </svg>
                            Tạo mc ảo mới
                        </a>
                    </div>
                    <div class="flex-col justify-between hidden lg:flex">
                        <div class="columns-4 gap-0.5">
                            <div class="mb-0.5 relative group" v-for="(item, index) in histories" :key="index">
                                <img v-if="item.avatar_url && item.avatar_url !== ''" :src="item.avatar_url" alt="img" class="w-full h-[240px] object-cover border border-gray-400"
                                    @error="handleImageError"
                                />
                                <div v-else class="h-[167px] flex items-center justify-center bg-gray-200">
                                    <p class="text-gray-600">Film đang được tạo</p>
                                </div>
                                <div @click="fetchData(route('mc-virtual.historyDetail', {id: item.id,}))" class="absolute cursor-pointer inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-0"></div>
                                <div class="text-white absolute bottom-2 px-2 hidden group-hover:flex w-full text-left items-center justify-end">
                                    <button class="p-2 rounded-full cursor-pointer hover:bg-white/30"  @click.stop="createPostYoutube(item.id)">
                                        <img src="/assets/svgs/icon-youtube.svg" class="w-5 h-5" />
                                    </button>
                                    <button class="p-2 rounded-full cursor-pointer hover:bg-white/30" @click.stop="createPost(item.result_url)">
                                        <img src="/assets/svgs/edit-icon-white.svg" class="w-5 h-5" />
                                    </button>
                                    <button @click.stop="confirmDelete(item.id)" class="p-2 rounded-full cursor-pointer hover:bg-white/30">
                                        <img src="/assets/svgs/trash-icon-white.svg" class="w-5 h-5" />
                                    </button>
                                    <button class="p-2 rounded-full cursor-pointer hover:bg-white/30" @click.stop="shareAIMcVirtual(item)">
                                        <img src="/assets/svgs/share-icon-white.svg" class="w-5 h-5" />
                                    </button>
                                    <button @click.stop="downloadVideo(item.result_url)" class="p-2 rounded-full cursor-pointer hover:bg-white/30">
                                        <img src="/assets/svgs/download-icon-white.svg" class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lg:hidden flex-col justify-between flex">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-4 w-full h-[70vh] xl:h-[75vh] overflow-y-auto my-3 lg:my-0">
                            <div class="" v-for="(item, index) in histories" :key="index">
                                <div
                                    class="w-full relative h-[252px] pb-2 aspect-video bg-slate-400 overflow-hidden rounded-xl"
                                    @click="
                                        fetchData(
                                            route('mc-virtual.historyDetail', {
                                                id: item.id,
                                            })
                                        )
                                    "
                                >
                                    <div class="rounded-xl h-[200px] overflow-hidden">
                                        <template v-if="item.avatar_url && item.avatar_url !== ''">
                                            <img :src="item.avatar_url" class="cursor-pointer"  @error="handleImageError"/>
                                            <img src="/assets/img/icon_play.png" class="w-20 cursor-pointer absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-50" alt="like" />
                                        </template>
                                        <div v-else class="w-full h-full flex items-center justify-center bg-gray-200">
                                            <p class="text-gray-600">Film đang được tạo</p>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2 mt-2 mr-2">
                                        <button class="p-2 bg-black bg-opacity-50 rounded-full" @click.stop="createPostYoutube(item.id)">
                                            <img src="/assets/svgs/icon-youtube.svg" class="w-5 h-5" />
                                        </button>
                                        <button class="p-2 bg-black bg-opacity-50 rounded-full" @click.stop="createPost(item.s3_url)">
                                            <img src="/assets/svgs/edit-icon-white.svg" class="w-5 h-5" />
                                        </button>
                                        <button @click.stop="confirmDelete(item.id)" class="p-2 bg-black bg-opacity-50 rounded-full">
                                            <img src="/assets/svgs/trash-icon-white.svg" class="w-5 h-5" />
                                        </button>
                                        <button class="p-2 bg-black bg-opacity-50 rounded-full" @click.stop="shareAIMcVirtual(item)">
                                            <img src="/assets/svgs/share-icon-white.svg" class="w-5 h-5" />
                                        </button>
                                        <button @click.stop="downloadVideo(item.s3_url)" class="p-2 bg-black bg-opacity-50 rounded-full">
                                            <img src="/assets/svgs/download-icon-white.svg" class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-center mb-4 lg:mt-2 xl:my-0">
                            <!-- <button
                                v-for="link in paginationLinks"
                                :key="link.label"
                                :disabled="!link.url || link.active"
                                @click="fetchData(link.url)"
                                :class="[
                                    'px-3 py-1 border',
                                    link.active ? 'bg-blue-500 text-white' : 'bg-white text-blue-500',
                                ]"
                            >
                                <span v-html="link.label"></span>
                            </button> -->
                            <Pagination
                                :totalPage="totalPage"
                                :initialPage="currentPage"
                                @updatePage="fetchPage"
                                />
                        </div>

    <!-- Modal Xác Nhận Xóa -->
    <div v-if="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-xl font-bold text-[#092A99]">Xác nhận xóa video</h3>
            <p class="mt-4 text-gray-600">
                Bạn có chắc chắn muốn xóa video này không?
            </p>
            <div class="mt-6 flex justify-end space-x-4">
                <button @click="cancelDelete" class="px-4 py-2 bg-gray-300 text-black rounded">Hủy</button>
                <button @click="confirmDeleteVideo" class="px-4 py-2 bg-red-500 text-white rounded">Xóa</button>
            </div>
        </div>
    </div>

    <div v-if="isLoading" class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
        <div class="loader"></div>
    </div>

    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink" />
    </Modal>
    <div v-if="showConfirmModalVideo" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg" style="width: 600px">
            <h3 class="text-xl font-bold text-[#092A99]">Nhập thông tin Video</h3>
            <p class="mt-4 text-gray-600">Vui lòng nhập thông tin đăng bài</p>
            <div class="mb-4 px-2 w-full">
                <label class="block mb-1 text-sm" for="input1">Tiêu đề:</label>
                <input id="input1" style="color: #6b7280" v-on:keyup="handleInputTitle($event.target.value)" class="w-full border px-4 py-2 rounded focus:border-blue-500 focus:shadow-outline outline-none" type="text" autofocus placeholder="Tiêu đề..." />
            </div>
            <div class="mb-4 px-2 w-full">
                <input style="color: #6b7280" id="input1" v-on:keyup="handleInputDes($event.target.value)" class="w-full border px-4 py-2 rounded focus:border-blue-500 focus:shadow-outline outline-none" type="text" autofocus placeholder="Mô tả..." />
            </div>
            <div class="mt-6 flex justify-end space-x-4">
                <button @click="cancelPostVideo" class="px-4 py-2 bg-gray-300 text-black rounded">Hủy</button>
                <button v-if="!isDisableButton" @click="confirmPostVideo" class="px-4 py-2 bg-blue-500 text-white rounded">Xác nhận</button>
                <button v-else disabled class="px-4 py-2 bg-blue-500 text-white rounded">Xác nhận</button>
            </div>
        </div>
    </div>
    </Layout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Head } from "@inertiajs/vue3";
import Footer from "../Home/Components/Footer.vue";
import Credit from '@/Components/Credit.vue';
import MainMenu from "@/Components/MainMenu.vue";
import axios from "axios";
import Pagination from '@/Components/Pagination.vue';
import { toast } from "vue3-toastify";
import FormShareLink from "@/Components/FormShareLink.vue";
import Modal from "@/Components/Modal.vue";
import Layout from "@/Layouts/Client/Layout.vue";

const props = defineProps({
    list: Array,
});
const breadcrumbsData = [
    { text: "Lịch sử tạo MC ảo", link: "mc-virtual.history" },
];
const credits = ref(0);

const goBack = () => {
    window.history.back();
};
console.log(props.list)
const histories = ref(props?.list?.data || []);
const showConfirmModal = ref(false);
const paginationLinks = ref(props?.list?.links || []);
const isLoading = ref(false);
const videoIdToDelete = ref(null);
const showConfirmModalVideo = ref(false);

const totalPage = ref(props.list?.last_page || 1);
const currentPage = ref(props.list?.current_page || 1);
const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
const videoPostId = ref(null);
const title = ref("");
const description = ref("");
const isDisableButton = ref(false);


const handleImageError = (event) => {
    // Replace failed image with default thumbnail
    event.target.src = '/assets/img/default-image.webp';
};

const fetchPage = (page) => {
  const url = `/mc-virtual/history?page=${page}`;
  window.location.href = url;
  currentPage.value = props.list?.current_page;
};

const fetchData = (url) => {
    window.location.href = url;
};
const mergeVideo = async (item) => {
    isLoading.value = true;
    try {
        const response = await axios.post(route("mc-virtual.merge-voice-with-audio"), {
            id: item.id,
            ai_generated_media_id: item.ai_generated_media_id,
        });
        if (response.status === 200) {
            alert("Giọng thật đã được áp dụng thành công!");
            window.location.reload();
        }
    } catch (error) {
        console.error("Error applying real voice:", error.message);
        alert("Có lỗi xảy ra khi áp dụng giọng thật.");
    } finally {
        isLoading.value = false;
    }
};

// Delete confirmation
const confirmDelete = (id) => {
    showConfirmModal.value = true;
    videoIdToDelete.value = id;
};

const cancelDelete = () => {
    showConfirmModal.value = false;
    videoIdToDelete.value = null;
};
const downloadVideo = async (video) => {
    if (!video) {
        alert("Chưa có video");
        return;
    }
    var url = route(("ai-video.downloadFile"), {url:video, name:"video.mp4"})
    window.location.href = url
};
const createPost = (imageUrl) => {
    if (imageUrl) {
        let image = {
            s3_url: imageUrl,
            type: 'video',
        };
        localStorage.setItem("aiImage",JSON.stringify(image) );
        window.location.href = route('calendar');
    }
};
const cancelPostVideo = () => {
    videoPostId.value = null;
    showConfirmModalVideo.value = false;
};

const handleInputTitle = (value) => {
    title.value = value;
};
const handleInputDes = (value) => {
    description.value = value;
};
const confirmPostVideo = async () => {
    if (!title.value) {
        alert("Tiêu đề không được để trống");
        return;
    }
    if (!description.value) {
        alert("Mô tả không được để trống");
        return;
    }
    if(isDisableButton.value) {
        return
    }
    var url = "/youtube/upload?title=" + title.value + "&description=" + description.value + "&id=" + videoPostId.value;
    try {
        isDisableButton.value = true
        const response = await fetch(url);
        isDisableButton.value = false
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }

        const json = await response.json();
        if (json["success"] == false && json["code"] == 400) {
            window.location.href = "/youtube/login";
        } else {
            alert(json["message"]);
            videoPostId.value = null;
            showConfirmModalVideo.value = false;
        }
    } catch (error) {
        isDisableButton.value = false
        console.error(error.message);
    }
};
const openShareLink = () => {
    isShowShareLinkModal.value = true;
};

const closeShareLink = () => {
    isShowShareLinkModal.value = false;
};
const shareAIMcVirtual = async (item) => {
    try {
        const response = await axios.post(route("share-link.store"), {
            share_linkable_id: item.id,
            share_linkable_type: 'McVirtual',
        });

        shareLink.value = route("share-link.show", { 'token': response.data.data.link_token })
        openShareLink();

    } catch (error) {
        console.log("Error sharing AI Mc Virtual:", error.message);
        toast.error("Chia sẻ tin thất bại");
    }
};
const confirmDeleteVideo = async () => {
    isLoading.value = true;
    try {
        const response = await axios.post(route("mc-virtual.delete", { id: videoIdToDelete.value }));
        if (response.status === 200) {
            histories.value = histories.value.filter((item) => item.id !== videoIdToDelete.value);
            alert("Video đã được xóa thành công.");
        }
    } catch (error) {
        console.error("Error deleting video:", error.message);
        alert("Có lỗi xảy ra khi xóa video.");
    } finally {
        isLoading.value = false;
        showConfirmModal.value = false;
        videoIdToDelete.value = null;
    }
};
const createPostYoutube = (id) => {
    videoPostId.value = id;
    showConfirmModalVideo.value = true;
};
</script>

<style scoped>
.loader {
    border: 5px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top: 5px solid #24AA69;
    width: 60px;
    height: 60px;
    -webkit-animation: spin 2s linear infinite;
    animation: spin 2s linear infinite;
}

@-webkit-keyframes spin {
    0% {
        -webkit-transform: rotate(0deg);
    }
    100% {
        -webkit-transform: rotate(360deg);
    }
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
