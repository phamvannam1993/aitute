<template>
    <Head title="Lịch sử tạo hình ảnh - AI 3 GỐC - Cộng đồng AI tử tế" />

        <Layout :breadcrumbs="breadcrumbsData">
            <div class="flex flex-col lg:flex-row  md:justify-end md:items-end lg:items-center mt-4 flex-wrap lg:flex-nowrap lg:justify-between">
                            <h2 class="text-black font-bold text-2xl mb-4">
                                Lịch sử tạo hình ảnh
                            </h2>
                            <a
                                :href="route('ai-image.index')"
                                class="px-4 md:px-11 py-2 bg-[#1E405A] text-white font-semibold rounded-[15px] flex justify-center items-center gap-1"
                                ><svg class="mr-2" width="25" height="26" viewBox="0 0 25 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.7917 3.625H5.20833C4.05774 3.625 3.125 4.55774 3.125 5.70833V20.2917C3.125 21.4423 4.05774 22.375 5.20833 22.375H19.7917C20.9423 22.375 21.875 21.4423 21.875 20.2917V5.70833C21.875 4.55774 20.9423 3.625 19.7917 3.625Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 11C10.1046 11 11 10.1046 11 9C11 7.89543 10.1046 7 9 7C7.89543 7 7 7.89543 7 9C7 10.1046 7.89543 11 9 11Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M21.8737 16.1248L16.6654 10.9165L5.20703 22.3748" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>

                                Tạo ảnh mới</a
                            >
            </div>
            <div class="flex flex-col justify-between gap-0 lg:gap-5 mt-6">
                            <div class="flex flex-col justify-between">
                            <div class="columns-1 md:columns-2 lg:columns-4 gap-0.5">
                            <div
                                class="mb-0.5 relative group"
                                v-for="(item, index) in histories"
                                :key="index"
                            >
                                <img :src="item.s3_url" alt="img" class="rounded-2xl w-full h-auto object-contain">
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
                        <div class="flex justify-center my-4 text-black">
                            <Pagination
                                :totalPage="totalPage"
                                :initialPage="currentPage"
                                @updatePage="fetchData"
                                />
                        </div>
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
import Layout from "@/Layouts/Client/Layout.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref, onBeforeMount, onBeforeUnmount  } from "vue";
import { toast } from "vue3-toastify";
import Pagination from '@/Components/Pagination.vue';
import ShowDetailImage from "@/Components/ShowDetailImage.vue"
import FormShareLink from '@/Components/FormShareLink.vue';
import Modal from '@/Components/Modal.vue';
import Popup from '@/Components/Popup/Index.vue'

const props = defineProps({
    list: Array,
});

const breadcrumbsData = [
    { text: "Tạo sản phẩm MEDIA" },
    { text: "Lịch sử" },
];

const histories = ref(props.list?.data || []);
const paginationLinks = ref(props.list?.links || []);
const selectedImage = ref(null);
const showConfirmModal = ref(false);

const totalPage = ref(props.list?.last_page || 1);
const currentPage = ref(props.list?.current_page || 1);
const getRandomHeight = () => {
    const heights = ['h-64', 'h-80', 'h-96', 'h-48'];
    return heights[Math.floor(Math.random() * heights.length)];
}

const isLoading = ref(false);
const imageDeleteId = ref(null);
const isShowShareLinkModal = ref(false);
const shareLink = ref(null);

const fetchData = (page) => {
  const url = `/ai-image/history?page=${page}`;
  window.location.href = url;
  currentPage.value = props.list?.current_page;
};

const lazyLoad = (element) => {
    const url = element.getAttribute('lazy-src');
    element.style.backgroundImage = `url(${url})`
}

onBeforeMount(() => {
    histories.value = props.list.data.map((item) => ({...item, randomHeight: getRandomHeight()}))
    console.log("🚀 ~ onBeforeMount ~ histories.value:", histories.value)
});

// Handle Lazyload By Intersection Observer
onMounted(() => {
    if('IntersectionObserver' in window) {
        var lazyImgs = document.querySelectorAll('.lazy-src')
        const observer = new IntersectionObserver((entries) => {
            entries.forEach( entry => {
                if (entry.isIntersecting) {
                    lazyLoad(entry.target)
                    observer.unobserve(entry.target)
                }
            })
        })
        lazyImgs.forEach( img => {
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
</style>
