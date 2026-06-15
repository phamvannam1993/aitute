
<template>
    <h1 class="text-3xl font-bold mb-5 text-center text-gray-800">Quản lý fanpages</h1>
    <Head title="Cấu hình Facebook Fanpage" />
    <Layout :breadcrumbs="breadcrumbsData" :isMobileFullScreen=true>
        <div class="bg-gray-50 p-3 md:p-5 md:rounded-3xl shadow-lg">
            <div class="flex items-center border-b py-3">
                <h1 class="font-bold md:text-[20px]">1. Quản lý Fanpage</h1>
                <button type="button" @click="loginWithFacebook"
                    class="px-5 md:px-10 flex items-center text-white font-bold px-1 py-2.5 justify-center gap-2 bg-ai3goc-sec rounded-xl backdrop-blur-sm transition-colors hover:scale-105 border-2 ml-auto">
                    <img src="/assets/img/orion/icon/link.svg" alt="">
                    <span class="text-[12px] md:text-[14px] text-nowrap">Liên kết Fanpage</span>
                </button>
            </div>
            <div class="flex flex-wrap gap-4 mt-5 border-b pb-5">
                <div v-for="(item, index) in listObjects" class="relative flex flex-col bg-gray-200 pt-4 pb-2 px-5 rounded-2xl w-[110px]">
                    <img class="rounded-full h-auto w-[65px] mx-auto" :src="item.page_picture && item.page_picture != '' ? item.page_picture : '/assets/img/default-image.webp'" :alt="item.page_name">
                    <span class="text-center leading-[1] mt-1">{{ item.page_name }}</span>
                    <img @click="deleteFacebookFanpage(item.id)" class="absolute cursor-pointer top-1.5 right-1.5 w-[22px] h-[22px]" src="/assets/img/icon/delete.png" alt="">
                </div>
            </div>
            <div class="text-gray-500 mt-2">
                Tổng số pages: {{ listObjects.length }}
            </div>
        </div>
        <ChatSetting :toneConfigs="toneConfigs" :documents="props.documents" :totalPage="props.totalPage" :currentPage="props.currentPage"
            :user="props.user" :fanpage_dify_apps="props.fanpage_dify_apps"/>
    </Layout>
    <div v-if="isLoading" class="fixed inset-0 flex items-center justify-center z-[104] bg-black bg-opacity-30">
        <div class="loader"></div>
    </div>
    <Popup
        v-if="showDelete"
        title="Cảnh báo !"
        message="Bạn có chắc chắn muốn xóa liên kết với Fanpage này không?"
        cancelButtonText="Huỷ"
        acceptButtonText="Đồng ý"
        @cancel="closePopup"
        @accept="submitDeleteFacebookFanpage"
    />
</template>

<script setup>
    import { ref } from 'vue';
    import { Head } from '@inertiajs/vue3';
    import { toast } from "vue3-toastify";
    import Layout from "@/Layouts/Client/Layout.vue";
    import ChatSetting from "./ChatSetting.vue";
    import Popup from '@/Components/Popup/Index.vue'

    const breadcrumbsData = [{ text: "Cấu hình Facebook Fanpage", link: "embed-config.facebook.index" }];
    const props = defineProps({
        facebookConfigs: {
            type: Boolean,
            required: true
        },
        fanpage_dify_apps: {
            type: Array,
            default: () => []
        },
        fanpagesProp: {
            type: Array,
            default: () => []
        },
        toneConfigs: Array,
        documents: Object,
        totalPage: Number,
        currentPage: Number,
        user: Object
    });
    const isLoading = ref(false);
    const listObjects = ref(props.facebookConfigs?.data || []);
    const perPage = ref(props.facebookConfigs?.per_page || 10);
    const totalPage = ref(props.facebookConfigs?.last_page || 1);
    const currentPage = ref(props.facebookConfigs?.current_page || 1);
    const totalRecords = ref(props.facebookConfigs?.total || 0);

    const fetchData = async (page=1) => {
        currentPage.value = page;
        isLoading.value = true;
        try {
            const response = await axios.get(route('embed-config.fetch-data', {page: currentPage.value}));
            listObjects.value = response.data.facebookConfigs.data;
            totalPage.value = response.data.facebookConfigs.last_page;
            currentPage.value = response.data.facebookConfigs.current_page;
            totalRecords.value = response.data.facebookConfigs.total;
        } catch (error) {
            toast.error('Lấy dữ liệu thất bại!');
        } finally {
            isLoading.value = false;
        }
    }

    const showDelete = ref(false)
    const fanpageRemoveId = ref(0)
    const deleteFacebookFanpage = (id) => {
        showDelete.value = true
        fanpageRemoveId.value = id
    }
    const closePopup = () => {
        showDelete.value = false
    }

    const submitDeleteFacebookFanpage = async () => {
        isLoading.value = true;
        showDelete.value = false;
        try {
            const response = await axios.post(route('embed-config.facebook.delete', { id: fanpageRemoveId.value }));
            toast.success(response.data.message || 'Xóa thành công!');
            fetchData();
        } catch (error) {
            toast.error(error.response.data.message || 'Xóa thất bại!');
        } finally {
            isLoading.value = false;
        }
    }

    const loginWithFacebook = () => {
        window.location.href = route('facebook.auth.redirect');
    };
</script>