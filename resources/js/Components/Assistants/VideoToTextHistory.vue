<template>
    <Head title="Video To Text History" />
    <div class="w-full h-full py-6">
        <div class="max-w-[1408px] mx-auto h-full">
            <div v-if="historyList.length" class="overflow-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                            <th class="md:py-3 md:px-6 text-left">URL Link Youtube</th>
                            <th class="md:py-3 md:px-6 md:text-left text-center">Tiêu đề Video</th>
                            <th class="md:py-3 md:px-6 text-left">Hoàn thành</th>
                            <th class="md:py-3 md:px-6 text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        <tr v-for="item in historyList" :key="item.id" class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="md:py-3 md:px-6"><a class="text-[#207A91] hover:text-blue-700 font-semibold" :href="item.youtube_url" target="_blank">Đi đến video</a></td>
                            <td class="md:py-3 md:px-6"><div class="text-gray-600 font-bold">{{ item.video_title }}</div></td>
                            <td class="md:py-3 md:px-6"><div class="text-gray-600 font-bold"> 100%</div></td>
                            <td class="md:py-3 md:px-6">
                                <a class="hover:text-blue-700 font-semibold text-red-500" href="javascript:void(0)" @click="showDelete(item.id)">Xóa</a>
                                <a @click="viewDetail(item)" class="text-[#207A91] hover:text-blue-700 font-semibold ml-2 " href="javascript:void(0)">Xem</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <Pagination :totalPage="totalPage" :initialPage="currentPage" @update-page="fetchData" />
            </div>
            <div v-if="totalPage && !historyList.length" class="flex items-center justify-center h-full">
                <div class="text-2xl font-bold text-center">Không có lịch sử nào</div>
            </div>
        </div>
    </div>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" v-if="showConfirm">
        <div class="bg-white py-6 px-4 md:p-8 shadow-lg w-[320px] md:w-[480px] xl:w-[480px] rounded-[40px]" >
            <div class="w-full flex items-center justify-center">
                <img src="/assets/images/warning.png" alt="Thông báo"/>
            </div>
            <div class="mt-[20px] text-black text-center text-[14px] md:text-[17px] font-medium">
                <p>Bạn có chắc chắn muốn xóa link</p><p>video này không?</p>
            </div>
            <div class="flex items-center gap-4 md:gap-8 justify-center mt-8">
                <button @click="closePopup" class="rounded-[16px] w-[200px] font-bold bg-[#ADADAD] text-white py-4 text-[16px] md:text-[19px]">
                   Hủy
                </button>
                <button @click="confirmDelete" class="rounded-[16px] w-[200px] font-bold  bg-ai3goc-bgclr text-white border-[2px] py-4 text-[16px] md:text-[19px]">
                    Xóa
                </button>

            </div>
        </div>
    </div>
</template>

<script setup>
import Pagination from "@/Components/Pagination.vue";

import { ref, onMounted, watch } from 'vue';
import { toast } from "vue3-toastify";
import axios from "axios";
const totalPage = ref(0);
const currentPage = ref(1);
const historyList = ref([]);
const historyListPagination = ref([]);
const showConfirm = ref(false)
const videoDeleteId = ref(0)
const emit = defineEmits(['viewDetail']);
const fetchData = async (page = 1) => {
    try {
        currentPage.value = page;
        const response = await axios.get(route("ai-video-to-text.history-list", {'page': page}));
        totalPage.value = response.data.last_page;
        currentPage.value = response.data.current_page;
        historyList.value = response.data.data;
        historyListPagination.value = response.data.links;
    } catch (error) {
        console.error('Error fetching user focused AI:', error);
    } finally {
    }
};
const viewDetail = (item) => {
    emit('viewDetail', item);
}
const closePopup = () => {
    showConfirm.value = false
}
const showDelete = (videoId) => {
    showConfirm.value = true
    videoDeleteId.value = videoId
}

const confirmDelete = async () => {
     try {
       const response = await axios.get(route("ai-video-to-text.delete", {'id': videoDeleteId.value}));
       if(response.data.success) {
            toast.success(response.data.message)
            fetchData()
       } else {
          toast.error(response.data.message)
       }
       showConfirm.value = false
    } catch (error) {

    } finally {
    }
}

onMounted(() => {
    fetchData(1);
})

defineExpose({
    fetchData
});
</script>

<style>
</style>
