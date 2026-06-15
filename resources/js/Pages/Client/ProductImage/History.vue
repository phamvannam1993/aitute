<template>

    <Head title="Lịch sử tạo ảnh tự sướng - AI 3 GỐC - Cộng đồng AI tử tế" />
        <Layout :breadcrumbs="breadcrumbsData">
            <div
                class="flex flex-col lg:flex-row md:justify-end md:items-end lg:items-center mt-4 flex-wrap lg:flex-nowrap lg:justify-between">
                <h2 class="text-black font-bold text-[25px] mb-4">
                    Quản lý kho ảnh sản phẩm
                </h2>
                <label for="upload-image"
                    @click="triggerUpload"
                    class="h-[40px] cursor-pointer  flex flex-row text-sm lg:text-base px-4 bg-[#1E405A] text-white rounded-lg lg:rounded-2xl font-bold line-h-40"
                    >
                    <img class="w-5" src="/assets/svgs/upload_white.svg" alt="">
                    <input id="upload-image" type="file" class="hidden" @change="uploadImage" />
                    <span class="ml-2 text-[18px]">Thêm ảnh sản phẩm</span>
                    </label
                >
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
  const url = `/ai-image/history?page=${page}`;
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
            route("product-image-delete", { id: imageDeleteId.value })
        );
        if(response.data.success) {
            var images = [];
            for(var i = 0; i < histories.value.length; i++) {
                if(histories.value[i].id != imageDeleteId.value) {
                    images.push(histories.value[i])
                }
            }
            histories.value = images
            toast.success('Xóa ảnh sản phẩm thành công')
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi khi xóa:", error.message);
    } finally {
        isLoading.value = false;
    }
    showConfirmModal.value = false;
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
            share_linkable_type: 'ProductImage',
        });

        shareLink.value = route("share-link.show", { 'token': response.data.data.link_token })
        openShareLink();

    } catch (error) {
        toast.error("Chia sẻ tin thất bại");
    }
};

const uploadImage = async (e) => {
    const file = e.target.files[0];
    if (!file) return;
    // Validate file type
    const validImageTypes = ["image/jpeg", "image/png", "image/jpg", "image/gif"];
    if (!validImageTypes.includes(file.type)) {
        toast.error("File phải là hình ảnh có định dạng: jpeg, png, jpg, gif");
        e.target.value = "";
        return;
    }
    // Validate file size (max 2MB)
    const maxSize = 10 * 1024 * 1024; // 2MB in bytes
    if (file.size > maxSize) {
        toast.error("Kích thước hình ảnh tối đa là 2MB");
        e.target.value = "";
        return;
    }
    const data = await getS3URL(file);
    var images = [];
    if(data) {
        images.push(data)
        for(var i = 0; i < histories.value.length; i++) {
            images.push(histories.value[i])
        }
        histories.value = images
    } else {
        toast.error('Có lỗi xảy ra trong quá trình thực hiện')
    }
};
const getS3URL = async (file) => {
    const formData = new FormData();
    formData.append("image_file", file);
    var url = route("ai-business.product-image-upload")
    try {
        const response = await axios.post(url, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
        if (response.data.success) {
            return response.data.data
        } else {
            return ""
        }
    } catch (err) {
        return ""
    }
}

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
.line-h-40 {
    line-height:40px
}
</style>
