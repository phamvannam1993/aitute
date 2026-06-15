<template>
    <Head title="Âm Nhạc - AI 3 GỐC - Cộng đồng AI tử tế" />
    <div class="relative w-full mx-auto bg-[url('../../public/assets/img/aiwow/layout_background.png')] bg-cover bg-center min-h-screen p-5 lg:p-10 text-xs lg:text-sm">
        <div class="flex items-center justify-between mt-8 px-[10px]">
            <div class="flex items-center space-x-2">
                <a :href="route('home.index')">
                    <img src="/assets/img/admin/icon_left_home.png" alt="Trang chủ" class="w-auto h-[19px]" />
                </a>
                <span class="font-medium text-[#2C75E3]"> / Danh sách template</span>
            </div>
            <Credit :credits="credits" />
        </div>
        <MainMenu />
        <div class="rounded-xl pb-4 flex flex-col justify-center items-center -mt-3 md:mt-12">
            <div
                class="flex w-full lg:w-11/12 bg-transparent md:bg-white rounded-[25px] md:mx-0 mb-4 md:p-[8px] flex-wrap gap-4">
                <div class="flex items-center  w-full p-[4px] md:p-0 md:space-x-2 flex-row bg-white rounded-[10px]">
                    <a href="javascript:void(0)" @click="changeActiveTab('new')" :class="[
                    'text-[10px] md:text-[14px] font-bold py-2 px-4 rounded-[10px] md:rounded-[15px] uppercase',
                    activeTab === 'new' ? 'bg-[#2C75E3] text-white' : 'text-black hover:bg-[#2C75E3] hover:text-white'
                    ]">
                    Mẫu mới
                    </a>
                    <a href="javascript:void(0)" @click="changeActiveTab('self')" :class="[
                    'text-[10px] md:text-[14px] font-bold py-2 px-4 rounded-[10px] md:rounded-[15px] uppercase',
                    activeTab === 'self' ? 'bg-[#2C75E3] text-white' : 'text-black hover:bg-[#2C75E3] hover:text-white'
                    ]">
                    Mẫu của bạn
                    </a>
                </div>

                <div class="flex flex-col lg:flex-row gap-1 md:gap-4 text-black w-full">
                    <!-- <button class="w-fit lg:font-bold text-xs lg:text-base">Kích thước video cần làm:</button>
                    <div class="flex items-center p-[4px] md:p-0 md:space-x-2 flex-row bg-white rounded-[10px] ">
                        <a href="javascript:void(0)" @click="changeSizeTab('')" :class="[
                        'text-[10px] md:text-[12px] font-bold py-1 px-2 rounded-[10px] md:rounded-[15px] uppercase',
                        activeSize === '' ? 'bg-[#2C75E3] text-white' : 'text-black hover:bg-[#2C75E3] hover:text-white'
                        ]">
                        Toàn bộ
                        </a>
                        <a href="javascript:void(0)" @click="changeSizeTab('1:1')" :class="[
                        'text-[10px] md:text-[12px] font-bold py-1 px-2 rounded-[10px] md:rounded-[15px] uppercase',
                        activeSize === '1:1' ? 'bg-[#2C75E3] text-white' : 'text-black hover:bg-[#2C75E3] hover:text-white'
                        ]">
                        1:1
                        </a>
                        <a href="javascript:void(0)" @click="changeSizeTab('16:9')" :class="[
                        'text-[10px] md:text-[12px] font-bold py-1 px-2 rounded-[10px] md:rounded-[15px] uppercase',
                        activeSize === '16:9' ? 'bg-[#2C75E3] text-white' : 'text-black hover:bg-[#2C75E3] hover:text-white'
                        ]">
                        16:9
                        </a>
                        <a href="javascript:void(0)" @click="changeSizeTab('9:16')" :class="[
                        'text-[10px] md:text-[12px] font-bold py-1 px-2 rounded-[10px] md:rounded-[15px] uppercase',
                        activeSize === '9:16' ? 'bg-[#2C75E3] text-white' : 'text-black hover:bg-[#2C75E3] hover:text-white'
                        ]">
                        9:16
                        </a>
                        <a href="javascript:void(0)" @click="changeSizeTab('4:5')" :class="[
                        'text-[10px] md:text-[12px] font-bold py-1 px-2 rounded-[10px] md:rounded-[15px] uppercase',
                        activeSize === '4:5' ? 'bg-[#2C75E3] text-white' : 'text-black hover:bg-[#2C75E3] hover:text-white'
                        ]">
                        4:5
                        </a>
                    </div> -->
                </div>

                <div class="w-full relative text-black">
                    <input type="text" placeholder="Tìm kiếm mẫu video" v-model="searchQuery" @keyup.enter="searchAI"
                    class="w-full text-[10px] md:text-xl font-bold px-4 py-0.5 md:py-2 border border-[#5FB2FF] rounded-[20px] md:rounded-[15px] focus:outline-none focus:ring-2 focus:ring-[#5FB2FF]" />
                    <button class="absolute right-3 top-2.5 md:top-2 text-gray-500" @click="searchAI">
                    <img src="/assets/svgs/search-icon.svg" alt="search icon" class="w-[10px] h-auto md:h-6 md:w-auto" />
                    </button>
                </div>
                </div>

            <div v-if="!isLoading" class="w-full lg:w-11/12">
                <!-- Template Grid -->
                <div v-if="templates.length" class="rounded-xl py-4 w-full">
                    <div class="flex-row md:flex-col md:justify-between justify-center flex flex-wrap w-full">
                        <div class="grid lg:grid-cols-4 md:grid-cols-2 grid-cols-1 gap-4 ">
                            <div class="mb-0.5 relative group rounded-xl cursor-pointer" v-for="(item, index) in templates" :key="index" 
                                @click="navigateToTemplate(item.id)"
                                @mouseenter="playVideo(item.id)"
                                @mouseleave="pauseVideo(item.id)"
                                >
                                <div v-if="item.template_thumbnail && item.template_thumbnail !== ''">
                                    <img 
                                    :src="item.template_thumbnail"
                                    alt="img"
                                    class="w-full rounded-xl h-[240px] object-cover"
                                    />
                                </div>
                                <div v-else class="h-[167px] flex items-center justify-center bg-gray-200">
                                    <p class="text-gray-600">Film đang được tạo</p>
                                </div>
                                <h2 class="absolute uppercase z-10 bottom-0 px-4 py-2 bg-white w-full text-[16px] md:text-[18px] xl:text-[20px] line-clamp-2 font-semibold text-[#0747C8] text-center shadow-lg leading-7 rounded-b-xl">{{ item.title }}</h2>
                                <video 
                                    v-if="item.demo_url && item.demo_url !== ''" 
                                    :id="'video-' + item.id" 
                                    class="absolute top-0 left-0 w-full h-[240px] object-cover rounded-xl" 
                                    :src="item.demo_url" 
                                    muted 
                                    loop
                                    style="display: none;">
                                </video>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center text-gray-500 py-10">
                    <p>Không có dữ liệu phù hợp!</p>
                </div>
            </div>
            <div v-else class="flex flex-col items-center">
                <svg aria-hidden="true"
                    class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                    viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                        fill="currentColor" />
                    <path
                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                        fill="currentFill" />
                </svg>
                <span class=" text-black">Đang tải...</span>
            </div>
            <div class="flex justify-end w-full lg:w-11/12">
                <a :href="route('creatomate.history')" class="text-xl font-bold text-white bg-blue-500 px-8 py-4 rounded-lg w-fit">Lịch sử</a>
            </div>
            <!-- Pagination -->
            <div class="flex justify-center my-4 text-black">
                <Pagination :totalPage="totalPage" :initialPage="currentPage" @updatePage="fetchData" />
            </div>
        </div>
    </div>
    <Footer />
</template>

<script setup>
import Layout from "@/Layouts/Client/ClientLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import axios from "axios";
import "suneditor/dist/css/suneditor.min.css";
import MainMenu from "@/Components/MainMenu.vue";
import { computed, onMounted, ref, watch } from "vue";
import Credit from "@/Components/Credit.vue";
import Footer from "../Home/Components/Footer.vue";
import Pagination from "@/Components/Pagination.vue";

defineOptions({ layout: Layout });

const props = defineProps({
    credits: Number,
    list: Object,
});
console.log(props.list)
// Dữ liệu từ server
const credits = ref(props.credits);
const totalPage = ref(props.list?.last_page || 1);
const currentPage = ref(props.list?.current_page || 1);
const templates = ref(props.list.data || []);
const activeTab = ref('new');
const activeSize = ref('');

// Lọc dữ liệu
const searchQuery = ref("");
const selectedResolution = ref("");

const isLoading = ref(false);
let timeoutId = null;
const uniqueResolutions = ref(['1:1', '16:9', '9:16', '4:5']);

const changeActiveTab = (type) => {
    activeTab.value = type;
    fetchData(1);
}
const changeSizeTab = (type) => {
    activeSize.value = type;
    selectedResolution.value = type;
    fetchData(1);
}

// Điều hướng đến trang đích
const navigateToTemplate = (id) => {
  const baseURL = '/creatomate/create';
  const query = `?id=${id}`;
  window.location.href = baseURL + query;
};

// Hàm phát video khi hover
const playVideo = (id) => {
  const video = document.getElementById('video-' + id)
  if (video) {
    video.style.display = 'block'
    video.play()
  }
}

// Hàm dừng video khi không hover
const pauseVideo = (id) => {
  const video = document.getElementById('video-' + id)
  if (video) {
    video.style.display = 'none'
    video.pause()
  }
}
// Gọi API để lấy dữ liệu
const fetchData = async (page = 1) => {
    try {
        isLoading.value = true;
        const response = await axios.get(route('creatomate.getTemplates'), {
            params: {
                page,
                search: searchQuery.value,
                resolution: selectedResolution.value,
            },
        });
        if (response) {
            console.log(response.data)
            templates.value = response.data.data;
            totalPage.value = response.data.last_page;
            currentPage.value = response.data.current_page;
        }
    } catch (error) {
        console.error("Lỗi khi tải dữ liệu:", error);
    } finally {
        isLoading.value = false;
    }
};

const debounce = (func, delay) => {
  return (...args) => {
    if (timeoutId) clearTimeout(timeoutId);
    timeoutId = setTimeout(() => func(...args), delay);
  };
};

const debouncedFetch = debounce((query) => fetchData(query), 1000);

watch(selectedResolution, () => {
    fetchData(1);
});
watch(searchQuery, (newQuery) => {
  debouncedFetch(newQuery);
});
</script>

<style scoped></style>
