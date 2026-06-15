<template>

    <Head title="Lịch sử - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData">
        <div
                            class="flex flex-col lg:flex-row md:justify-end md:items-end lg:items-center mt-4 flex-wrap lg:flex-nowrap lg:justify-between">
                            <h2 class="text-orion-primary font-bold text-2xl mb-4">
                                Lịch sử tạo hình ảnh
                            </h2>
                            <a :href="route('ai-image.faceSwap')"
                                class="px-4 md:px-11 py-2 bg-[#149CBE] text-white font-semibold rounded-[15px] flex justify-center items-center gap-1"><svg
                                    width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M1.93691 5.73778H5.70135V1.63111C5.70135 1.25778 5.8258 0.946666 6.07469 0.697776C6.32358 0.448888 6.63469 0.324444 7.00802 0.324444C7.40209 0.324444 7.71321 0.448888 7.94135 0.697776C8.19024 0.946666 8.31469 1.25778 8.31469 1.63111V5.73778H12.048C12.4214 5.73778 12.7221 5.85185 12.9502 6.08C13.1991 6.30815 13.3236 6.60889 13.3236 6.98222C13.3236 7.35555 13.1991 7.6563 12.9502 7.88444C12.7221 8.11259 12.4214 8.22667 12.048 8.22667H8.31469V12.3333C8.31469 12.7067 8.19024 13.0178 7.94135 13.2667C7.71321 13.5156 7.40209 13.64 7.00802 13.64C6.63469 13.64 6.32358 13.5156 6.07469 13.2667C5.8258 13.0178 5.70135 12.7067 5.70135 12.3333V8.22667H1.93691C1.58432 8.22667 1.28358 8.11259 1.03469 7.88444C0.806539 7.6563 0.692465 7.35555 0.692465 6.98222C0.692465 6.60889 0.806539 6.30815 1.03469 6.08C1.28358 5.85185 1.58432 5.73778 1.93691 5.73778Z"
                                        fill="white" />
                                </svg>
                                Tạo ảnh mới</a>
        </div>
        <div class="flex flex-col justify-between gap-0 lg:gap-5 mt-6">
                        <div class="flex flex-col justify-between">
                            <div class="columns-4 gap-0.5">
                                <div
                                    class="mb-0.5 relative group"
                                    v-for="(item, index) in histories"
                                    :key="index"
                                >
                                    <img :src="item.s3_url" alt="img" class="w-full h-auto object-contain">
                                    <div @click="openDetail(item)" class="absolute cursor-pointer inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-0"></div>
                                    <div class="text-white absolute bottom-2 px-2 hidden group-hover:flex w-full text-left items-center">
                                        <div class="flex justify-end w-full">
                                            <button class="p-2 rounded-full cursor-pointer hover:bg-white/30"
                                                @click="createPost(item.s3_url)">
                                                <img src="/assets/img/orion/icon/history/post.svg" class="h-[24px] w-auto" />
                                            </button>
                                            <button @click="prepareDeleteFile(item.id)" class="p-2 rounded-full cursor-pointer hover:bg-white/30">
                                                <img src="/assets/img/orion/icon/history/delete.svg" class="h-[24px] w-auto" />
                                            </button>
                                            <button @click="shareAIGeneratedMedia(item)" class="p-2 rounded-full cursor-pointer hover:bg-white/30">
                                                <img src="/assets/img/orion/icon/history/share.svg" class="h-[24px] w-auto" />
                                            </button>
                                            <button @click="downloadImage(item.s3_url)" class="p-2 rounded-full cursor-pointer hover:bg-white/30">
                                                <img src="/assets/img/orion/icon/history/download.svg" class="h-[24px] w-auto" />
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
                                    @updatePage="fetchData"
                                    />
                            <!-- <PaginationCustom :links="paginationLinks" @paginate="fetchData" /> -->
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
    <ShowDetailImage :selectedImage="selectedImage" :closeDetail="closeDetail" />
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
import FormShareLink from '@/Components/FormShareLink.vue';
import Modal from '@/Components/Modal.vue';
import Layout from "@/Layouts/Client/Layout.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import { toast } from "vue3-toastify";
import Pagination from '@/Components/Pagination.vue';
import ShowDetailImage from "@/Components/ShowDetailImage.vue"
import Popup from '@/Components/Popup/Index.vue'

const props = defineProps({
    list: Array,
});

const breadcrumbsData = [
    { text: "Tạo sản phẩm MEDIA" },
    { text: "Lịch sử" },
];

const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
const showConfirmModal = ref(false);
const histories = ref(props.list?.data || []);
const paginationLinks = ref(props.list?.links || []);
const isLoading = ref(false);
const selectedImage = ref(null);
const imageDeleteId = ref(null);

const totalPage = ref(props.list?.last_page || 1);
const currentPage = ref(props.list?.current_page || 1);

const fetchData = (page) => {
  const url = `/ai-image/swap-face-history?page=${page}`;
  window.location.href = url;
  currentPage.value = props.list?.current_page;
};

const lazyLoad = (element) => {
    const url = element.getAttribute('lazy-src');
    element.style.backgroundImage = `url(${url})`
}

// Handle Lazyload By Intersection Observer
onMounted(() => {
    if ('IntersectionObserver' in window) {
        var lazyImgs = document.querySelectorAll('.lazy-src')
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    lazyLoad(entry.target)
                    observer.unobserve(entry.target)
                }
            })
        })
        lazyImgs.forEach(img => {
            observer.observe(img)
        })
    }
})


const openDetail = (item) => {
    selectedImage.value = item;
};

const closeDetail = () => {
    selectedImage.value = null;
};

const prepareDeleteFile = (id) => {
    imageDeleteId.value = id;
    showConfirmModal.value = true;
};

const confirmDelete = async () => {
    isLoading.value = true;

    try {
        const response = await axios.post(
            route("ai-image.delete", { id: imageDeleteId.value })
        );
        if (response.status === 200) window.location.reload();
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
        localStorage.setItem("aiImage", JSON.stringify(image));
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
            share_linkable_type: 'AIGeneratedMedia',
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
