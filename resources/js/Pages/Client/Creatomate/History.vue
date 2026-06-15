<template>
    <Head title="Âm Nhạc - AI 3 GỐC - Cộng đồng AI tử tế" />
    <div class="relative w-full mx-auto bg-[url('../../public/assets/img/aiwow/layout_background.png')] bg-cover bg-center min-h-screen p-5 lg:p-10 text-xs lg:text-sm mb-20">
        <div class="flex items-center justify-between mt-8 px-[10px]">
            <div class="flex items-center space-x-2">
                <a :href="route('home.index')">
                    <img src="/assets/img/admin/icon_left_home.png" alt="Trang chủ" class="w-auto h-[19px]" />
                </a>
                <span class="font-medium text-[#2C75E3]"> / Lịch sử video đã tạo</span>
            </div>
            <Credit :credits="credits" />
        </div>
        <MainMenu />
        <div class="w-full text-black px-3 grid lg:grid-cols-3 xl:grid-cols-4 gap-1">
            <div class="mb-0.5 relative group" v-for="(item, index) in listVideos" :key="index">
                <video :src="item.result" controls class="w-full h-auto rounded-tl-md rounded-tr-md aspect-square bg-black" :poster="item.snapshot_url" preload="none"></video>
                <div class="bg-white rounded-bl-md rounded-br-md shadow-sm p-4 flex lg:justify-start md:justify-end gap-4 items-center flex-wrap">
                    <button @click="editVideo(item.id)" class="text-[#2C75E3] flex justify-start items-center gap-2 hover:text-blue-600 transition-colors duration-300 mx-auto lg:mx-0">
                        <img src="/assets/svgs/edit-icon.svg" class="h-[26px] w-[26px]" />
                        <!-- <span class="font-medium hidden lg:inline-block">Chỉnh sửa  </span> -->
                    </button>
                    <button @click="createPost(item.result)" class="text-[#2C75E3] flex justify-start items-center gap-2 hover:text-blue-600 transition-colors duration-300 mx-auto lg:mx-0">
                        <img src="/assets/svgs/edit-icon-new.svg" class="h-[26px] w-[26px]" />
                        <!-- <span class="font-medium hidden lg:inline-block">Tạo bài đăng</span> -->
                    </button>
                    <button @click="prepareDeleteFile(item?.id)" class="text-[#2C75E3] flex items-center gap-2 hover:text-[#1f5bb5] mx-auto lg:mx-0">
                        <img src="/assets/svgs/trash-icon-new.svg" class="h-[26px] w-[26px]" />
                        <!-- <span class="font-medium hidden lg:inline-block">Xoá</span> -->
                    </button>
                    <!-- <button @click="shareAIGeneratedMedia(resultValue)" class="text-[#2C75E3] flex items-center gap-2 hover:text-[#1f5bb5] mx-auto lg:mx-0">
                                <img src="/assets/svgs/share-icon-new-2.svg" class="h-[26px] w-[26px]" />
                                <span class="font-medium">Chia sẻ</span>
                            </button> -->
                    <button @click="downloadVideo(item.result)" class="text-[#2C75E3] flex items-center gap-2 hover:text-[#1f5bb5] mx-auto lg:mx-0">
                        <img src="/assets/svgs/download-icon-new.svg" class="h-[26px] w-[26px]" />
                        <!-- <span class="font-medium hidden lg:inline-block">Tải xuống</span> -->
                    </button>
                </div>
            </div>
        </div>
        <div class="flex justify-center my-4 text-black">
            <Pagination :totalPage="totalPage" :initialPage="currentPage" @updatePage="fetchData" />
        </div>
    </div>
    <Footer />
    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink"/>
    </Modal>
    <Popup 
        v-if="showConfirmModal"
        title="Cảnh báo !"
        message="Bạn có chắc chắn muốn xóa video này không?"
        cancelButtonText="Huỷ"
        acceptButtonText="Xoá"
        @cancel="cancelDelete"
        @accept="confirmDelete"
    />
    <div v-if="isLoading" class="fixed inset-0 flex items-center justify-center z-[104] bg-black bg-opacity-30">
        <div class="loader"></div>
    </div>
</template>

<script setup>
import Layout from "@/Layouts/Client/ClientLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import axios from "axios";
import "suneditor/dist/css/suneditor.min.css"; // Import CSS của Sun Editor
import MainMenu from "@/Components/MainMenu.vue";
import { computed, onBeforeMount, onMounted, reactive, ref, watch } from "vue";
import { toast } from "vue3-toastify";
import Credit from "@/Components/Credit.vue";
import Footer from "../Home/Components/Footer.vue";
import "1llest-waveform-vue/lib/style.css";
import Pagination from "@/Components/Pagination.vue";
import Popup from '@/Components/Popup/Index.vue'

defineOptions({ layout: Layout });

const props = defineProps({
    credits: Number,
    list: Array,
});
const credits = ref(props.credits);

const listVideos = ref(props.list.data || []);

const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
const showConfirmModal = ref(false);
const videoDeleteId = ref(null);
const isLoading = ref(false);

const totalPage = ref(props.list?.last_page || 1);
const currentPage = ref(props.list?.current_page || 1);

const fetchData = (page) => {
    const url = `/creatomate/history?page=${page}`;
    window.location.href = url;
    currentPage.value = props.list?.current_page;
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

const editVideo = (itemId) => {
    window.location.href=route('creatomate.edit', {id: itemId})
};

const prepareDeleteFile = (id) => {
    videoDeleteId.value = id;
    showConfirmModal.value = true;
};
const confirmDelete = async () => {
    isLoading.value = true;

    try {
        const response = await axios.post(
            route("creatomate.delete", { id: videoDeleteId.value })
        );
        if (response.status === 200) {
            isLoading.value = false;
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

const openShareLink = () => {
    isShowShareLinkModal.value = true;
};

const closeShareLink = () => {
    isShowShareLinkModal.value = false;
};

const downloadVideo = async (videoRef) => {
    if (!videoRef) {
        alert("Chưa có video");
        return;
    }
    var url = route(("ai-video.downloadFile"), {url:videoRef, name:"video.mp4"})
    window.location.href = url
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
