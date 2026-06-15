<template>
    <Head title="Lịch sử TÔI LÀ AI - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData">
        <div class="flex flex-col lg:flex-row gap-2 justify-between items-start lg:items-center my-5 w-full">
            <h2 class="text-orion-primary font-bold text-2xl">Lịch sử TÔI LÀ AI</h2>
            <a :href="route('lipsync.lipsync')"
                class="px-4 md:px-11 py-2 bg-[#149CBE] text-white font-semibold rounded-[15px] flex justify-center items-center gap-1 w-full lg:w-[320px]">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1.93691 5.73778H5.70135V1.63111C5.70135 1.25778 5.8258 0.946666 6.07469 0.697776C6.32358 0.448888 6.63469 0.324444 7.00802 0.324444C7.40209 0.324444 7.71321 0.448888 7.94135 0.697776C8.19024 0.946666 8.31469 1.25778 8.31469 1.63111V5.73778H12.048C12.4214 5.73778 12.7221 5.85185 12.9502 6.08C13.1991 6.30815 13.3236 6.60889 13.3236 6.98222C13.3236 7.35555 13.1991 7.6563 12.9502 7.88444C12.7221 8.11259 12.4214 8.22667 12.048 8.22667H8.31469V12.3333C8.31469 12.7067 8.19024 13.0178 7.94135 13.2667C7.71321 13.5156 7.40209 13.64 7.00802 13.64C6.63469 13.64 6.32358 13.5156 6.07469 13.2667C5.8258 13.0178 5.70135 12.7067 5.70135 12.3333V8.22667H1.93691C1.58432 8.22667 1.28358 8.11259 1.03469 7.88444C0.806539 7.6563 0.692465 7.35555 0.692465 6.98222C0.692465 6.60889 0.806539 6.30815 1.03469 6.08C1.28358 5.85185 1.58432 5.73778 1.93691 5.73778Z" fill="white"/>
                </svg>
                Tạo video TÔI LÀ AI mới
            </a>
        </div>

        <!-- Grid hiển thị video -->
        <div class="flex-col justify-between">
            <div class="flex-col justify-between flex">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-4 w-full overflow-y-auto my-3 lg:my-0">
                    <div v-for="(item, index) in histories" :key="index" class="w-full relative pb-2 rounded-xl">
                        <div class="rounded-xl text-black relative group">
                            <!-- Original Video -->
                            <p class="font-medium mb-2">Video gốc:</p>
                            <video :src="item.video" controls class="w-full rounded-md mb-4"></video>

                            <!-- Audio gốc -->
                            <p class="font-medium mb-2">Audio gốc:</p>
                            <audio controls class="w-full rounded-md mb-4">
                                <source :src="item.audio" type="audio/mpeg">
                                Trình duyệt của bạn không hỗ trợ phần tử âm thanh.
                            </audio>

                            <!-- Kết quả Lip Sync -->
                            <p class="font-medium mb-2">Kết quả nhép lời:</p>
                            <video :src="item.result" controls class="w-full rounded-md"></video>

                            <!-- Action buttons -->
                            <div class="flex justify-end gap-2 mt-2">
                                <button class="p-2 bg-black bg-opacity-50 rounded-full"
                                        @click.stop="shareAIGeneratedMedia(item)">
                                    <img src="/assets/svgs/share-icon-white.svg" class="w-5 h-5" />
                                </button>
                                <button @click.stop="downloadVideo(item.result)"
                                        class="p-2 bg-black bg-opacity-50 rounded-full">
                                    <img src="/assets/svgs/download-icon-white.svg" class="w-5 h-5" />
                                </button>
                                <button @click.stop="prepareDeleteFile(item.id)"
                                        class="p-2 bg-black bg-opacity-50 rounded-full">
                                    <img src="/assets/svgs/trash-icon-white.svg" class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center my-4">
                <Pagination
                    :totalPage="totalPage"
                    :initialPage="currentPage"
                    @updatePage="fetchPage"
                    />
            </div>
        </div>
    </Layout>

    <!-- Modal xác nhận xóa -->
    <Popup
        v-if="showConfirmModal"
        title="Cảnh báo !"
        message="Bạn có chắc chắn muốn xóa video này không?"
        cancelButtonText="Huỷ"
        acceptButtonText="Xoá"
        @cancel="cancelDelete"
        @accept="confirmDelete"
    />

    <!-- Modal chia sẻ -->
     <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink"/>
    </Modal>
</template>

<script setup>
import { ref } from "vue";
import axios from 'axios';
import { Head } from "@inertiajs/vue3";
import Layout from '@/Layouts/Client/Layout.vue';
import FormShareLink from "@/Components/FormShareLink.vue";
import Modal from '@/Components/Modal.vue';
import Footer from "../Home/Components/Footer.vue";
import Credit from "@/Components/Credit.vue";
import { toast } from "vue3-toastify";
import MainMenu from "@/Components/MainMenu.vue";
import Pagination from '@/Components/Pagination.vue';
import Popup from '@/Components/Popup/Index.vue'

const props = defineProps({
    list: Array,
});

const breadcrumbsData = [
    { text: "Tôi là A.I", link: "lipsync.lipsync" },
    { text: "Lịch sử" },
];

const histories = ref(props.list.data);
const showConfirmModal = ref(false);
const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
const videoDeleteId = ref(null);
const isLoading = ref(false);
const totalPage = ref(props.list?.last_page || 1);
const currentPage = ref(props.list?.current_page || 1);
// Delete functions
const prepareDeleteFile = (id) => {
    videoDeleteId.value = id;
    showConfirmModal.value = true;
};

const cancelDelete = () => {
    showConfirmModal.value = false;
    videoDeleteId.value = null;
};

const confirmDelete = async () => {

    isLoading.value = true;
    try {
        const response = await axios.post(route("lipsync.delete", { id: videoDeleteId.value }));
        if (response.status === 200) {
            histories.value = histories.value.filter((item) => item.id !== videoDeleteId.value);
            showConfirmModal.value = false;
            toast.success("Xóa thành công");
        }

    } catch (error) {
        console.error("Lỗi khi xóa:", error.message);
        toast.error("Xóa thất bại");
    } finally {
        isLoading.value = false;
        videoDeleteId.value = null;
    }
};

// Download function
const downloadVideo = (videoUrl) => {
    if (!videoUrl) {
        alert("Chưa có video");
        return;
    }
    const url = route(("ai-video.downloadFile"), {url:videoUrl, name:"video.mp4"})
    window.location.href = url
};

// Share functions
const shareAIGeneratedMedia = async (item) => {
    try {
        const response = await axios.post(route("share-link.store"), {
            share_linkable_id: item.id,
            share_linkable_type: "Lipsync",
        });

        shareLink.value = route("share-link.show", { token: response.data.data.link_token });
        openShareLink();
    } catch (error) {
        toast.error("Chia sẻ thất bại");
    }
};

const openShareLink = () => {
    isShowShareLinkModal.value = true;
};

const closeShareLink = () => {
    isShowShareLinkModal.value = false;
};

// Pagination
const fetchPage = (page) => {
    const url = `/lipsync/history?page=${page}`;
    window.location.href = url;
};

</script>

<style scoped>
video, audio {
    max-width: 100%;
    background: #f1f1f1;
}

.grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
}
</style>
