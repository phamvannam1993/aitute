<template>
    <Head title="Lịch sử tạo video - AITUTE - Ở đâu có Internet - Ở đó có AITUTE" />

    <Layout :breadcrumbs="breadcrumbsData">
        <section class="flex flex-col gap-4">
            <div class="flex flex-row gap-4 justify-between text-black">
                <div class="flex gap-1 items-center font-bold">
                    <img src="/assets/img/orion/icon/label-arrow.svg" alt="Trang chủ" class="w-7" />
                    <h1>Video đã tạo</h1>
                </div>
                <div class="flex gap-4">
                    <a :href="route('text-to-video.index')"
                        class="flex justify-center gap-1 lg:gap-3 rounded-[10px] bg-orion-sec text-white py-2 px-3 lg:px-7 items-center hover:scale-105 transform transition-transform">
                        <img class="size-6 lg:size-7" src="/assets/img/aiwow/create_video.svg" alt="merge video" />
                        <p class="font-bold text-sm lg:text-base text-center">Tạo video mới</p>
                    </a>
                </div>
            </div>

            <div v-if="histories.length > 0" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <a :href="route('video.historyDetail', { id: item.id })" v-for="(item, index) in histories" :key="index"
                    class="w-full h-[30vh] rounded-2xl relative bg-[#00000033]">
                    <img :src="item.thumbnail" alt="video thumbnail" class="w-full h-full object-contain rounded-2xl" />
                    <img src="/assets/img/aiwow/play_button.png" alt="" class="w-20 h-20 absolute inset-0 m-auto" />
                    <div
                        class="text-white absolute z-10 bottom-0 px-2 right-0 bg-gradient-to-t from-gray-800 to-transparent rounded-b-2xl w-full justify-end flex py-2">
                        <button @click.prevent="editVideo(item.id)"
                            class="p-2 rounded-full cursor-pointer hover:bg-white/30">
                            <img src="/assets/svgs/aiwow/edit_video.svg" class="w-6" />
                        </button>
                        <button class="p-2 rounded-full cursor-pointer hover:bg-white/30"
                            @click.prevent="createPostYoutube(item.id)">
                            <img src="/assets/svgs/aiwow/icon_youtube.svg" class="w-6" />
                        </button>
                        <button class="p-2 rounded-full cursor-pointer hover:bg-white/30"
                            @click.prevent="createPost(item)">
                            <img src="/assets/img/aiwow/create_post.svg" class="w-6" />
                        </button>
                        <button @click.prevent="prepareDeleteFile(item.id)"
                            class="p-2 rounded-full cursor-pointer hover:bg-white/30">
                            <img src="/assets/svgs/aiwow/icon_delete.svg" class="w-6" />
                        </button>
                        <button class="p-2 rounded-full cursor-pointer hover:bg-white/30"
                            @click.prevent="shareVideo(item)">
                            <img src="/assets/svgs/aiwow/icon_share.svg" class="w-6" />
                        </button>
                        <button @click.prevent="downloadVideo(item.s3_url)"
                            class="p-2 rounded-full cursor-pointer hover:bg-white/30">
                            <img src="/assets/svgs/aiwow/icon_download.svg" class="w-6" />
                        </button>
                    </div>
                </a>
            </div>
            <p class="text-black text-center" v-else>Không có video nào</p>
            <Pagination v-if="histories.length > 0" :totalPage="totalPage" :initialPage="currentPage"
                @updatePage="fetchPage" />
        </section>
    </Layout>
    <!-- Details Modal & Other Parts Same as Before -->
    <!-- Modal Xác Nhận Xóa -->
    <Popup v-if="showConfirmModal" title="Cảnh báo !" message="Bạn có chắc chắn muốn xóa video này không?"
        cancelButtonText="Huỷ" acceptButtonText="Xoá" @cancel="cancelDelete" @accept="confirmDelete" />

    <div v-if="showConfirmModalVideo"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white py-8 px-10 rounded-[40px] shadow-lg w-[654px] w-m-360">
            <div class="flex gap-2 items-center">
                <img src="/assets/img/orion/icon/label-circle.svg" class="size-[48px]" />
                <h3 class="text-xl font-bold text-orion-primary">Đăng bài Youtube</h3>
            </div>
            <p class="mt-4 text-gray-600">Vui lòng nhập thông tin đăng bài</p>
            <div class="mb-4 px-2 w-full">
                <label class="block mb-1 text-sm" for="input1">Tiêu đề:</label>
                <input id="input1" style="color: #6b7280" v-on:keyup="handleInputTitle($event.target.value)"
                    class="w-full border px-4 py-2 rounded focus:border-purple-500 focus:shadow-outline outline-none"
                    type="text" autofocus placeholder="Tiêu đề..." />
            </div>
            <div class="mb-4 px-2 w-full">
                <input style="color: #6b7280" id="input1" v-on:keyup="handleInputDes($event.target.value)"
                    class="w-full border px-4 py-2 rounded focus:border-purple-500 focus:shadow-outline outline-none"
                    type="text" autofocus placeholder="Nhập nội dung mô tả..." />
            </div>
            <div class="mt-6 flex justify-center space-x-4">
                <button @click="cancelPostVideo"
                    class="w-[200px] px-4 py-2 bg-white text-gray-500 font-bold border-[3px] border-gray-300 rounded">Hủy</button>
                <button v-if="!isDisableButton" @click="confirmPostVideo"
                    class="w-[200px] px-4 py-2 bg-orion-primary text-white rounded font-bold">Xác nhận</button>
                <button v-else disabled class="w-[200px] px-4 py-2 bg-orion-primary text-white rounded font-bold">Xác
                    nhận</button>
            </div>
        </div>
    </div>

    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink" />
    </Modal>

    <PostForm ref="postFormRef" @onSuccess="onSuccessPostForm" />
</template>

<script setup>
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
import Layout from "@/Layouts/Client/Layout.vue";
import FormShareLink from "@/Components/FormShareLink.vue";
import Modal from "@/Components/Modal.vue";
import { toast } from "vue3-toastify";
import Pagination from "@/Components/Pagination.vue";
import Popup from '@/Components/Popup/Index.vue'
import PostForm from '@/Components/ShareAiText/PostForm.vue';

const props = defineProps({
    list: Array,
    typeQuery: String,
});

const breadcrumbsData = [
    { text: "Tạo sản phẩm MEDIA" },
    { text: "Lịch sử" },
];

// Thêm thuộc tính `isPlaying` vào từng video để quản lý việc phát video
const histories = ref([]);

const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
const showConfirmModal = ref(false);
const showConfirmModalVideo = ref(false);
const isDisableButton = ref(false);
const isLoading = ref(false);
const videoDeleteId = ref(null);
const videoPostId = ref(null);
const title = ref(null);
const description = ref(null);
const postFormRef = ref(null);

// Handle video deletion without page reload
const prepareDeleteFile = (id) => {
    videoDeleteId.value = id;
    showConfirmModal.value = true;
};

const createPostYoutube = (id) => {
    videoPostId.value = id;
    showConfirmModalVideo.value = true;
};

const editVideo = (id) => {
    window.location.href = route('video.edit', { id: id })
}

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
    if (isDisableButton.value) {
        return;
    }
    var url = "/youtube/upload?title=" + title.value + "&description=" + description.value + "&id=" + videoPostId.value;
    try {
        isDisableButton.value = true;
        const response = await fetch(url);
        isDisableButton.value = false;
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
        isDisableButton.value = false;
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
        const response = await axios.post(route("video.delete", { id: videoDeleteId.value }));
        if (response.status === 200) {
            window.location.reload();
            showConfirmModal.value = false;
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi khi xóa:", error.message);
    } finally {
        isLoading.value = false;
        videoDeleteId.value = null; // Reset ID video sau khi xóa
    }
};

// Pagination fetch without full page reload
const totalPage = ref(props.list?.last_page || 1);
const currentPage = ref(props.list?.current_page || 1);

const fetchPage = async (page) => {
    const url = route("video.ApiGetHistory", { page: page });
    const response = await axios.get(url);
    currentPage.value = response.data.list?.current_page;
    totalPage.value = response.data.list?.last_page;
    histories.value = response.data.list?.data ? response.data.list?.data : [];
};

fetchPage(1);

const downloadVideo = (video) => {
    var url = route("ai-video.downloadFile", { url: video, name: "video.mp4" });
    window.location.href = url;
};

const createPost = (item) => {
    let postForm = {
        files: [{
            s3_url: item.s3_url,
            type: 'video'
        }],
    };

    postFormRef.value.openPostDetail(postForm);

};

const openShareLink = () => {
    isShowShareLinkModal.value = true;
};

const closeShareLink = () => {
    isShowShareLinkModal.value = false;
};

const shareVideo = async (item) => {
    try {
        const response = await axios.post(route("share-link.store"), {
            share_linkable_id: item.id,
            share_linkable_type: "Video",
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
.bt-yellow {
    background: #ff7b00;
}

.all-video {
    max-height: 720px;
    overflow: auto;
}

.icon-tick {
    top: 10px;
    right: -94%;
}

/* HTML: <div class="loader"></div> */
.loader {
    width: 50px;
    aspect-ratio: 1;
    display: grid;
    border-radius: 50%;
    background: linear-gradient(0deg, rgb(0 71 171/50%) 30%, #0000 0 70%, rgb(0 71 171/100%) 0) 50%/8% 100%, linear-gradient(90deg, rgb(0 71 171/25%) 30%, #0000 0 70%, rgb(0 71 171/75%) 0) 50%/100% 8%;
    background-repeat: no-repeat;
    animation: l23 1s infinite steps(12);
}

.loader::before,
.loader::after {
    content: "";
    grid-area: 1/1;
    border-radius: 50%;
    background: inherit;
    opacity: 0.915;
    transform: rotate(30deg);
}

.loader::after {
    opacity: 0.83;
    transform: rotate(60deg);
}

@keyframes l23 {
    100% {
        transform: rotate(1turn);
    }
}

.show-load {
    width: 400px;
    text-align: center;
    height: 120px;
    background: rgb(255, 255, 255);
}

.loader {
    position: relative;
    margin-left: 190px;
    margin-top: 10px;
}

.show-load p {
    color: #000;
    margin-top: 20px;
}

@keyframes l27 {
    100% {
        transform: rotate(1turn);
    }
}

.box-video {
    padding: 20px;
    border: 2px solid #2d75e3;
}

.text-des {
    font-weight: bold;
    font-size: 20px;
    margin-top: 10px;
}

.box-left {
    float: left;
}

.box-right {
    float: right;
}

.h-240 {
    height: 240px;
}

.icon-success {
    margin-left: 45%;
    margin-bottom: 10px;
}

@media only screen and (max-width: 600px) {
    .flex-col-mobile {
        flex-direction: column;
    }

    .text-des[data-v-623ab925] {
        font-weight: bold;
        font-size: 15px;
        margin-top: 10px;
    }

    .pd-10 {
        padding: 10px;
    }

    .icon-tick {
        right: -90%;
    }

    .justify-center-mobile {
        justify-content: center;
    }

    .text-des {
        font-size: 15px;
    }
    .w-m-360 {
        width:360px !important;
    }
}

.text-name-audio {
    max-width: 170px;
    overflow: hidden;
    color: #000;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.no-data {
    color: #000;
    font-size: 15px;
    font-weight: bold;
}

::v-deep(.pagination .page-item:first-child) {
    border-radius: 100%;
    margin: 0 20px;
    background: #ffffff4d;
}

::v-deep(.pagination .page-item:last-child) {
    border-radius: 100%;
    margin: 0 20px;
    background: #ffffff4d;
}
</style>