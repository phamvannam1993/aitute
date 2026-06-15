<template>
    <Head title="Lịch sử tạo video - AI 3 GỐC - Cộng đồng AI tử tế" />

    <div class="flex min-h-screen bg-[url('../../public/assets/img/aiwow/layout_background.png')] bg-cover bg-center">
        <div class="w-full p-5 lg:p-10">
            <div class="flex flex-col lg:flex-row items-start justify-between lg:items-end mb-5 space-y-4 lg:space-y-0 lg:space-x-4">
                <!-- Left Section (Breadcrumb and Search Bar) -->
                <div class="w-full">
                    <!-- Breadcrumb -->
                    <div class="flex items-center justify-between mt-6 lg:mt-2">
                        <div class="flex items-center space-x-2">
                            <a :href="route('home.index')">
                                <img src="/assets/img/admin/icon_left_home.png" alt="Trang chủ" class="w-auto h-[19px]" />
                            </a>
                            <span class="font-medium text-[#2C75E3]">/ Lịch sử tạo video từ hình ảnh</span>
                        </div>
                        <Credit />
                    </div>
                    <MainMenu />
                    <div class="flex justify-between items-center my-5">
                        <h2 class="text-[#092A99] font-bold text-2xl">Lịch sử tạo video từ hình ảnh</h2>
                        <a :href="route('ai-video.faceSwap')" class="px-4 md:px-11 py-2 bg-[#2C75E3] text-white font-semibold rounded-[15px] flex justify-center items-center gap-1"
                            ><svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1.93691 5.73778H5.70135V1.63111C5.70135 1.25778 5.8258 0.946666 6.07469 0.697776C6.32358 0.448888 6.63469 0.324444 7.00802 0.324444C7.40209 0.324444 7.71321 0.448888 7.94135 0.697776C8.19024 0.946666 8.31469 1.25778 8.31469 1.63111V5.73778H12.048C12.4214 5.73778 12.7221 5.85185 12.9502 6.08C13.1991 6.30815 13.3236 6.60889 13.3236 6.98222C13.3236 7.35555 13.1991 7.6563 12.9502 7.88444C12.7221 8.11259 12.4214 8.22667 12.048 8.22667H8.31469V12.3333C8.31469 12.7067 8.19024 13.0178 7.94135 13.2667C7.71321 13.5156 7.40209 13.64 7.00802 13.64C6.63469 13.64 6.32358 13.5156 6.07469 13.2667C5.8258 13.0178 5.70135 12.7067 5.70135 12.3333V8.22667H1.93691C1.58432 8.22667 1.28358 8.11259 1.03469 7.88444C0.806539 7.6563 0.692465 7.35555 0.692465 6.98222C0.692465 6.60889 0.806539 6.30815 1.03469 6.08C1.28358 5.85185 1.58432 5.73778 1.93691 5.73778Z" fill="white" />
                            </svg>
                            Tạo video đổi gương mặt mới</a
                        >
                    </div>
                    <div class="flex-col justify-between">
                        <div class="gap-0.5 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4">
                            <div class="mb-0.5 relative group" v-for="(item, index) in histories" :key="index">
                                <video :src="item.result" controls class="w-full h-auto rounded-md aspect-square bg-black" :poster="item.img" preload="none"></video>
                                <div class="text-white absolute bottom-2 px-2 hidden group-hover:flex w-full text-left items-center justify-end">
                                    <!-- <button class="p-2 rounded-full cursor-pointer hover:bg-white/30" @click.stop="createPostYoutube(item.id)">
                                        <img src="/assets/svgs/icon-youtube.svg" class="w-5 h-5" />
                                    </button> -->
                                    <button class="p-2 rounded-full cursor-pointer hover:bg-white/30" @click.stop="createPost(item.result)">
                                        <img src="/assets/svgs/edit-icon-white.svg" class="w-5 h-5" />
                                    </button>
                                    <button @click.stop="prepareDeleteFile(item.id)" class="p-2 rounded-full cursor-pointer hover:bg-white/30">
                                        <img src="/assets/svgs/trash-icon-white.svg" class="w-5 h-5" />
                                    </button>
                                    <button class="p-2 rounded-full cursor-pointer hover:bg-white/30" @click.stop="shareAIGeneratedMedia(item)">
                                        <img src="/assets/svgs/share-icon-white.svg" class="w-5 h-5" />
                                    </button>
                                    <button @click.stop="downloadVideo(item.result)" class="p-2 rounded-full cursor-pointer hover:bg-white/30">
                                        <img src="/assets/svgs/download-icon-white.svg" class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <Footer />
    <div v-if="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-xl font-bold text-[#092A99]">Xác nhận xóa video</h3>
            <p class="mt-4 text-gray-600">Bạn có chắc chắn muốn xóa video này không?</p>
            <div class="mt-6 flex justify-end space-x-4">
                <button @click="cancelDelete" class="px-4 py-2 bg-gray-300 text-black rounded">Hủy</button>
                <button @click="confirmDelete" class="px-4 py-2 bg-red-500 text-white rounded">Xóa</button>
            </div>
        </div>
    </div>

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
                <button @click="confirmPostVideo" class="px-4 py-2 bg-blue-500 text-white rounded">Xác nhận</button>
            </div>
        </div>
    </div>

    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink" />
    </Modal>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { Head } from "@inertiajs/vue3";
import Layout from "@/Layouts/Client/ClientLayout.vue";
import FormShareLink from "@/Components/FormShareLink.vue";
import Footer from "../../Home/Components/Footer.vue";
import Modal from "@/Components/Modal.vue";
import Credit from "@/Components/Credit.vue";
import { toast } from "vue3-toastify";
import PaginationCustom from "@/Components/PaginationCustom.vue";
import MainMenu from "@/Components/MainMenu.vue";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    list: Array,
});

console.log("list : ", props.list);

defineOptions({ layout: Layout });

const goBack = () => {
    window.history.back(); // Điều hướng về trang trước đó
};

// Thêm thuộc tính `isPlaying` vào từng video để quản lý việc phát video
const histories = ref(
    props.list?.map((item) => ({
        ...item,
        isPlaying: false, // Ban đầu, tất cả video đều không được phát
    })) || []
);

const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
const showConfirmModal = ref(false);
const showConfirmModalVideo = ref(false);
const paginationLinks = ref(props.list?.links || []); // Pagination links
const isLoading = ref(false);
const selectedVideo = ref(null);
const videoDeleteId = ref(null);
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
    videoDeleteId.value = id;
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
    videoDeleteId.value = null; // Xóa ID video cần xóa
};

const confirmDelete = async () => {
    isLoading.value = true;

    try {
        const response = await axios.post(route("ai-video.delete-faceswap", { id: videoDeleteId.value }));
        if (response.status === 200) {
            // Remove the deleted video from the list instead of reloading the page
            histories.value = histories.value.filter((item) => item.id !== videoDeleteId.value);
            showConfirmModal.value = false;
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi khi xóa:", error.message);
    } finally {
        isLoading.value = false;
        videoDeleteId.value = null; // Reset ID video sau khi xóa
    }
};

const downloadVideo = (videoUrl) => {
    fetch(videoUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error("Không thể tải video - kiểm tra URL hoặc quyền truy cập.");
            }
            return response.blob();
        })
        .then(blob => {
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            link.href = url;
            link.download = "video.mp4";
            link.style.display = "none";
            document.body.appendChild(link);
            link.click();
            link.remove();
            URL.revokeObjectURL(url);
        })
        .catch(error => {
            console.error("Download failed:", error);
            alert("Có lỗi xảy ra khi tải video. Vui lòng kiểm tra URL hoặc quyền truy cập.");
        });
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

const shareAIGeneratedMedia = async (item) => {
    try {
        const response = await axios.post(route("share-link.store"), {
            share_linkable_id: item.id,
            share_linkable_type: "SwapFace",
        });

        shareLink.value = route("share-link.show", { token: response.data.data.link_token });
        openShareLink();
    } catch (error) {
        toast.error("Chia sẻ tin thất bại");
    }
};
</script>

<style scoped>
/* CSS for loader and other styles */
</style>
