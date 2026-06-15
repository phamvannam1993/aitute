<template>

    <Head title="Lịch sử tạo video - AI 3 GỐC - Cộng đồng AI tử tế" />

    <Layout :breadcrumbs="breadcrumbsData">

        <div class="flex justify-between items-center my-5">
            <h2 class="text-[#092A99] font-bold text-2xl">Lịch sử tạo nhạc</h2>
            <a :href="route('text-to-song.index')"
                class="px-4 md:px-11 py-2 bg-[#2C75E3] text-white font-semibold rounded-[15px] flex justify-center items-center gap-1"><svg
                    width="14" height="14" viewBox="0 0 14 14" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M1.93691 5.73778H5.70135V1.63111C5.70135 1.25778 5.8258 0.946666 6.07469 0.697776C6.32358 0.448888 6.63469 0.324444 7.00802 0.324444C7.40209 0.324444 7.71321 0.448888 7.94135 0.697776C8.19024 0.946666 8.31469 1.25778 8.31469 1.63111V5.73778H12.048C12.4214 5.73778 12.7221 5.85185 12.9502 6.08C13.1991 6.30815 13.3236 6.60889 13.3236 6.98222C13.3236 7.35555 13.1991 7.6563 12.9502 7.88444C12.7221 8.11259 12.4214 8.22667 12.048 8.22667H8.31469V12.3333C8.31469 12.7067 8.19024 13.0178 7.94135 13.2667C7.71321 13.5156 7.40209 13.64 7.00802 13.64C6.63469 13.64 6.32358 13.5156 6.07469 13.2667C5.8258 13.0178 5.70135 12.7067 5.70135 12.3333V8.22667H1.93691C1.58432 8.22667 1.28358 8.11259 1.03469 7.88444C0.806539 7.6563 0.692465 7.35555 0.692465 6.98222C0.692465 6.60889 0.806539 6.30815 1.03469 6.08C1.28358 5.85185 1.58432 5.73778 1.93691 5.73778Z"
                        fill="white" />
                </svg>
                Tạo bài hát mới</a>
        </div>
        <div class="flex-col justify-between ">
            <div class="flex-col justify-between flex">
                <div
                    class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-4 w-full overflow-y-auto my-3 lg:my-0 ">
                    <div class="justify-items-center" v-for="(item, index) in histories" :key="index">
                        <div class="w-[200px] h-[200px] ">
                            <img :src="item.image_url" alt="" class="rounded-lg">
                        </div>
                        <div class="w-full relative  pb-2  rounded-xl">
                            <div class="rounded-xl text-black relative group">
                                <template v-if="item.prompt && item.prompt !== ''">
                                    <p
                                        class="truncate max-w-[350px] overflow-hidden whitespace-nowrap text-ellipsis ml-10">
                                        {{ item.prompt }}
                                    </p>
                                    <!-- Tooltip -->
                                    <span
                                        class="absolute left-0 bottom-full mb-1 hidden w-auto max-w-xs rounded-md bg-gray-800 px-2 py-1 text-white text-sm shadow-md group-hover:block group-hover:opacity-100 opacity-0 transition-opacity duration-200">
                                        {{ item.prompt }}
                                    </span>
                                </template>
                            </div>


                            <audio controls class="w-full rounded-md shadow-sm" :key="item.audioUrl">
                                <source :src="item.audioUrl" type="audio/mpeg">
                                Trình duyệt của bạn không hỗ trợ phần tử âm thanh.
                            </audio>
                            <div class="bg-gray-400 relative  rounded-md">
                                <button @click="item.showLyrics = !item.showLyrics"
                                    class="p-2">
                                    {{ item.showLyrics ? 'Ẩn Lyrics' : 'Lyrics' }}
                                </button>
                                <div class="absolute group-hover:flex w-fit right-0 bottom-1/2 translate-y-1/2 text-left items-center">
                                    <div class="flex justify-end w-full">
                                        <button @click="prepareDeleteFile(item.id)" class="p-2 rounded-full cursor-pointer hover:bg-white/30">
                                            <img class="w-5 h-5" src="/assets/svgs/trash-icon-white.svg"/>
                                        </button>
                                        <button @click="shareAIGeneratedMedia(item.id)" class="p-2 rounded-full cursor-pointer hover:bg-white/30">
                                            <img class="w-5 h-5" src="/assets/svgs/share-icon-white.svg"/>
                                        </button>
                                        <button @click="downloadMusic(item.audioUrl)" class="p-2 rounded-full cursor-pointer hover:bg-white/30">
                                            <img class="w-5 h-5" src="/assets/svgs/download-icon-white.svg" />
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Phần hiển thị lyrics -->
                            <div v-if="item.showLyrics && item.lyrics"
                                class="text-center text-black h-80 overflow-y-auto mt-4">
                                <h2 class="font-bold">Lyrics</h2>
                                <p v-html="formatLyrics(item.lyrics)"></p>
                            </div>
                            <!-- <div class="flex justify-end gap-2 mt-2 mr-2">
                                <button class="p-2 bg-black bg-opacity-50 rounded-full"
                                    @click.stop="createPostYoutube(item.id)">
                                    <img src="/assets/svgs/icon-youtube.svg" class="w-5 h-5" />
                                </button>
                                <button class="p-2 bg-black bg-opacity-50 rounded-full"
                                    @click.stop="createPost(item.s3_url)">
                                    <img src="/assets/svgs/edit-icon-white.svg" class="w-5 h-5" />
                                </button>
                                <button @click.stop="prepareDeleteFile(item.id)"
                                    class="p-2 bg-black bg-opacity-50 rounded-full">
                                    <img src="/assets/svgs/trash-icon-white.svg" class="w-5 h-5" />
                                </button>
                                <button class="p-2 bg-black bg-opacity-50 rounded-full"
                                    @click.stop="shareAIGeneratedMedia(item)">
                                    <img src="/assets/svgs/share-icon-white.svg" class="w-5 h-5" />
                                </button>
                                <button @click.stop="downloadVideo(item.s3_url)"
                                    class="p-2 bg-black bg-opacity-50 rounded-full">
                                    <img src="/assets/svgs/download-icon-white.svg" class="w-5 h-5" />
                                </button>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center my-4">
                <PaginationCustom :links="paginationLinks" @paginate="fetchPage" />
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
    <!-- Modal Xác Nhận Xóa -->
    <Popup
        v-if="showConfirmModal"
        title="Cảnh báo !"
        message="Bạn có chắc chắn muốn xóa bài hát này không?"
        cancelButtonText="Huỷ"
        acceptButtonText="Xoá"
        @cancel="cancelDelete"
        @accept="confirmDelete"
    />
    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink" />
    </Modal>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from 'axios';
import { Head } from "@inertiajs/vue3";
import FormShareLink from "@/Components/FormShareLink.vue";
import Footer from "../Home/Components/Footer.vue";
import Modal from "@/Components/Modal.vue";
import Credit from "@/Components/Credit.vue";
import { toast } from "vue3-toastify";
import PaginationCustom from "@/Components/PaginationCustom.vue";
import MainMenu from "@/Components/MainMenu.vue";
import Layout from "@/Layouts/Client/Layout.vue";
import Popup from '@/Components/Popup/Index.vue'

const props = defineProps({
    list: Array,
});

const breadcrumbsData = [
    { text: "Lịch sử tạo bài hát", link: "text-to-song.history" },
];
const goBack = () => {
    window.history.back(); // Điều hướng về trang trước đó
};
const histories = ref([]); // Khởi tạo mảng rỗng để chứa dữ liệu

// Hàm để lấy dữ liệu từ API

// Hàm để lấy dữ liệu từ API
const fetchMusic = async () => {
    try {
        const response = await axios.get(route('text-to-song.getAllMusic'));

        // Kiểm tra và log response để debug
        console.log('Response:', response);

        // Kiểm tra xem data có tồn tại và là array không
        if (response.data && Array.isArray(response.data.data)) {
            histories.value = response.data.data.map(item => ({
                id: item.id,
                prompt: item.prompt, // Sửa propmt thành prompt
                audioUrl: item.result_audio,
                lyrics: item.lyrics,
                image_url: item.image_url,
                showLyrics: false
            }));
        } else {
            // Nếu response.data không phải array, gán trực tiếp nếu nó là object
            histories.value = response.data.data || [];
        }
    } catch (error) {
        console.error("Error fetching music history:", error);
        toast.error("Không thể tải lịch sử tạo nhạc");
    }
};

// Gọi API khi component được mount
onMounted(() => {
    fetchMusic();
});

const formatLyrics = (lyrics) => {
    return lyrics.replace(/\n/g, '<br>');
}
const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
const showConfirmModal = ref(false);
const showConfirmModalVideo = ref(false);
const paginationLinks = ref(props.list?.links || []);
const isLoading = ref(false);
const selectedVideo = ref(null);
const audioDeleteId = ref(null);
const videoPostId = ref(null);
const title = ref(null);
const description = ref(null);

// Lazy load for videos
const openDetail = (item) => {
    selectedVideo.value = item;
};

const closeDetail = () => {
    selectedVideo.value = null;
};

// Handle video deletion without page reload
const prepareDeleteFile = (id) => {
    audioDeleteId.value = id;
    showConfirmModal.value = true;
};

const createPostYoutube = (id) => {
    videoPostId.value = id;
    showConfirmModalVideo.value = true;
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
    var url = "/youtube/upload?title=" + title.value + "&description=" + description.value + "&id=" + videoPostId.value;
    try {
        const response = await fetch(url);
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
        console.error(error.message);
    }
};

const cancelDelete = () => {
    showConfirmModal.value = false; // Đóng modal xác nhận xóa
    audioDeleteId.value = null; // Xóa ID video cần xóa
};

const confirmDelete = async () => {
    isLoading.value = true;

    try {
        const response = await axios.post(route("text-to-music.delete", { id: audioDeleteId.value }));
        if (response.status === 200) {
            // Remove the deleted video from the list instead of reloading the page
            histories.value = histories.value.filter((item) => item.id !== audioDeleteId.value);
            showConfirmModal.value = false;
            toast.success('Xóa thành công.')
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi khi xóa:", error.message);
    } finally {
        isLoading.value = false;
        audioDeleteId.value = null; // Reset ID video sau khi xóa
    }
};

// Pagination fetch without full page reload
const fetchPage = (page) => {
    const url = `/ai-video/history?page=${page}`;
    window.location.href = url;
};

const fetchData = (url) => {
    window.location.href = url;
};

const downloadMusic = async (audio) => {
    if (audio) {
        try {
            // Dow file từ S3
            const response = await fetch(audio);
            if (!response.ok) throw new Error('Không thể tải file từ S3.');

            // Blob
            const blob = await response.blob();
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.href = url;
            link.download = 'audio-file.mp3';
            link.click();

            // Hủy object URL sau khi tải xuống
            URL.revokeObjectURL(url);
        } catch (error) {
            console.error('Lỗi tải xuống:', error);
            toast.warning('Không thể tải tệp âm thanh xuống!');
        }
    } else {
        toast.warning('Không có tệp âm thanh để tải xuống!');
    }
};

const createPost = (imageUrl) => {
    if (imageUrl) {
        let image = {
            s3_url: imageUrl,
            type: "image",
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

const shareAIGeneratedMedia = async (id) => {
    try {
        const response = await axios.post(route("share-link.store"), {
            share_linkable_id: id,
            share_linkable_type: "Music",
        });

        shareLink.value = route("share-link.show", { token: response.data.data.link_token });
        openShareLink();
    } catch (error) {
        toast.error("Chia sẻ ảnh thất bại");
    }
};
</script>

<style scoped>
/* CSS for loader and other styles */
</style>
