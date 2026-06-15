<template>
    <Master>
        <div class="lg:w-5/6 w-full bg-[url('../../public/assets/img/aiwow/layout_background.png')] bg-cover bg-center min-h-screen p-5 lg:p-10">
            <div class="flex flex-col lg:flex-row  items-start justify-between lg:items-end  mb-5 space-y-4 lg:space-y-0 lg:space-x-4">
                <div class="w-full lg:w-3/4 flex flex-col space-y-2">

                    <div class="flex items-center space-x-2 text-blue-900">
                        <img src="/assets/img/admin/icon_left_home.png" alt="Trang chủ" class="w-auto h-[19px]">
                        <span class="font-medium text-[#2C75E3]">/ AI</span>
                    </div>
                    <div class="relative w-full">
                        <input
                            v-model="searchQuery"
                            @input="debouncedSearch"
                            type="text" placeholder="Tìm kiếm AI..." class="text-black w-full p-2 pl-10 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                         <img src="/assets/img/admin/icon_seach.png"  class="w-auto h-[19px] absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-500">
                    </div>
                </div>

                <a :href="route('admin.ai-assistants.create')" class="bg-blue-600 text-white text-center px-4 py-2 rounded-lg shadow hover:bg-blue-700 focus:outline-none w-[270px] lg:w-[300px]">
                    + Tạo AI mới
                </a>
            </div>
            <div class="flex flex-col lg:flex-row  items-start justify-between lg:items-end mb-5 space-y-4 lg:space-y-0 lg:space-x-8">
                <div class="w-3/4">
                    <MultiSelect :options="occupations"  @update:selectedOptions="handleSearchByOccupation"/>
                </div>
                <div class="w-48 mt-4">
                    <select
                        @change="sort"
                        v-model="publicFilter"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 text-black">
                        <option value="">Trạng thái public</option>
                        <option value="true">Đã public</option>
                        <option value="false">Chưa public</option>
                    </select>
                </div>
                <select
                    v-model="sortDirection"
                    @change="sort"
                    class="bg-white text-center text-black border border-gray-300 p-2 rounded-[22px] cursor-pointer shadow-sm focus:ring-blue-500 focus:border-blue-500 w-[300px] lg:w-[210px]">
                    <option selected value="asc">Sắp xếp theo A-Z</option>
                    <option value="desc">Sắp xếp theo Z-A</option>
                </select>

            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                <template v-for="assistant in ai_assistants.data" :key="assistant.id" >

                    <div class="bg-white rounded-lg shadow-md p-4 flex flex-col h-full">
                        <div class="flex items-start gap-4">
                            <img
                                :src="assistant?.thumbnail_url ? assistant?.thumbnail_url : '/assets/img/admin/ai.png'"
                                alt="Avatar" class="w-12 h-12 rounded-lg">
                            <div class="flex-1">
                                <h3 class="text-md font-semibold text-black line-clamp-2">{{assistant.name}}</h3>
                                <p class="line-clamp-1 text-sm text-gray-500 flex items-center">
                                    <span class="bg-blue-500 w-2 h-2 rounded-full inline-block mr-2"></span>
                                    {{assistant.operation_name}}
                                </p>
                                <div class="mt-auto">

                            <span :class="[
                                'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium',
                                assistant.is_public
                                    ? 'bg-green-100 text-green-700 border border-green-400'
                                    : 'bg-yellow-100 text-yellow-700 border border-yellow-400'
                            ]">
                                <span :class="[
                                    'w-2 h-2 rounded-full mr-1.5',
                                    assistant.is_public ? 'bg-green-500' : 'bg-yellow-500'
                                ]"></span>
                                {{ assistant.is_public ? 'Đã public' : 'Chưa public' }}
                            </span>
                                </div>
                            </div>
                            <div class="ml-auto">
                                <button @click="edit(assistant.id)" class="text-gray-400 hover:text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-6.804a2.5 2.5 0 113.536 3.536L6 21H2.5L2 17.5l12.732-12.732z" />
                                    </svg>
                                </button>
                            </div>

                            <div class="ml-auto">
                                <button @click="deleteAssistant(assistant.id)" class="text-gray-400 hover:text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-1 12a2 2 0 01-2 2H8a2 2 0 01-2-2L5 7m5 4v6m4-6v6M9 7h6m-7 0a2 2 0 012-2h4a2 2 0 012 2" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="mt-auto">
                            <div class="mt-4 flex items-center justify-between bg-gray-100 rounded-lg py-2 px-4 shadow-sm">
                                <!-- Chạy thử AI -->
                                <button @click="runAssistant(assistant.id)"
                                        class="bg-blue-500 text-white px-3 py-1 rounded-lg shadow hover:bg-blue-600 text-sm">
                                    Chạy thử AI
                                </button>

                                <!-- Miễn phí -->
<!--                                <span class="text-gray-700 font-semibold bg-green-100 text-green-700 px-3 py-1 rounded-full">-->
<!--                                    Miễn phí-->
<!--                                 </span>-->
                            </div>
                        </div>

                    </div>
                </template>
            </div>

            <div class="mt-5 text-center">
                <button
                    v-if="ai_assistants.next_page_url"
                    @click="loadMore"
                    class="bg-white text-black px-6 py-2 rounded-lg shadow hover:bg-blue-700 focus:outline-none ">
                    Xem thêm
                </button>
            </div>
        </div>

        <!-- Edit Job Modal -->
        <edit-assistant-modal v-if="showModal"
                        :assistant="selectedAssistant"
                        :showModal="showModal"
                        @close="closeModal"
                        @updateData="handleUpdateData"
        >

        </edit-assistant-modal>
        <!-- Loading overlay -->
        <div v-if="isLoadingMore" class="loading-overlay">
            <div class="spinner"></div>
        </div>
    </Master>
</template>

<script setup>
import { ref } from "vue";
import {Head, router, usePage} from "@inertiajs/vue3";
import Master from "../../../Layouts/Admin/Master.vue";
import ItemAIAssistantCard from "../../../Components/ItemAIAssistantCard.vue";
import EditAssistantModal from "../../../Pages/Admin/AIAssistant/EditAssistantModal.vue";
import MultiSelect from '../../../Components/MultiSelect.vue';
import {toast} from "vue3-toastify";

import {debounce} from "lodash";

const props = defineProps({
    ai_assistants: Object,
    occupations: Object,
});

// Lấy dữ liệu từ props của Inertia
let { filters } = usePage().props;
// Biến trạng thái hiển thị modal
const showModal = ref(false);
const selectedAssistant= ref(null);
// Biến lưu trữ giá trị tìm kiếm và sắp xếp
const searchQuery = ref(filters.search || '');
const sortDirection = ref('asc');
const sortColumn = 'name';
const selectedOccupations = ref([]);
const publicFilter = ref('');

// Hàm tìm kiếm
const search = () => {
    router.get(route('admin.ai-assistants.index'), {
        occupation_ids: selectedOccupations.value,
        search: searchQuery.value,
        public: publicFilter.value,
        sort: sortColumn,
        direction: sortDirection.value
    }, {
        preserveState: true,
        replace: true,
        onSuccess: (page) => {
        }
    });
};

const handleSearchByOccupation = (occupations) => {
    selectedOccupations.value = occupations;
    // Thực hiện gọi API tìm kiếm theo ngành nghề
    router.get(route('admin.ai-assistants.index'), {
        occupation_ids: selectedOccupations.value,
        search: searchQuery.value,
        public: publicFilter.value,
        sort: sortColumn,
        direction: sortDirection.value
    }, {
        preserveState: true,
        replace: true
    });
};

const handleUpdateData = () => {
    search();
};

// Hàm sắp xếp
const sort = () => {
    router.get(route('admin.ai-assistants.index'), {
        occupation_ids: selectedOccupations.value,
        search: searchQuery.value,
        public: publicFilter.value,
        sort: sortColumn,
        direction: sortDirection.value
    }, {
        preserveState: true,
        replace: true,
        onSuccess: (page) => {
        }
    });
};

const isLoadingMore = ref(false);
// Hàm phân trang - "Xem thêm"
const loadMore = async () => {
    if (props.ai_assistants.next_page_url) {
        isLoadingMore.value = true; // Hiển thị trạng thái loading
        try {
            await router.get(props.ai_assistants.next_page_url, {}, {
                preserveState: true,
                replace: true,
                onSuccess: (page) => {
                    setTimeout(() => {
                        isLoadingMore.value = false;
                    }, 500)

                }
            });
        } catch (error) {
            isLoadingMore.value = false;
            console.error('Error loading more operations:', error);
        } finally {

        }
    }
};

// Hàm để chỉnh sửa ngành nghề
const edit = async (id) => {
    try {
        isLoadingMore.value = true; // Hiển thị trạng thái loading
        const response = await axios.get(`/admin/ai-assistants/${id}/edit`,{}, {
            preserveState: true,
            replace: true,
            onSuccess: (page) => {}
        });
        setTimeout(() => {
            isLoadingMore.value = false;
        }, 500);
        selectedAssistant.value = response.data.assistant;
        showModal.value = true;
    } catch (error) {
        isLoadingMore.value = false;
        console.error('Error loading occupation:', error);
    }
};
const deleteAssistant = async (id) => {
    try {
        isLoadingMore.value = true; // Hiển thị trạng thái loading
        try {
            const confirmDelete = confirm('Xác nhận xóa AI này?');
            if (confirmDelete) {
                const response = await axios.delete(`/admin/ai_assistants/${id}`);
                search();
                console.log('Deleted successfully', response.data);
                toast.success("Xóa Ai Thành công !");
            }
        } catch (error) {
            toast.success("Error deleting assistant: !");
            console.error('Error deleting assistant:', error);
        }
        isLoadingMore.value = false;
    } catch (error) {
        isLoadingMore.value = false;
        console.error('Error deleting assistant:', error);
    }
};

const closeModal = () => {
    showModal.value = false;
};
const runAssistant = (id) => {
    window.location.href = `/admin/ai-assistants/${id}/run`;
};

// Debounced function (trì hoãn 300ms)
const debouncedSearch = debounce(search, 300);

</script>
<style>
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .spinner {
        border: 6px solid rgba(255, 255, 255, 0.3);
        border-left-color: #ffffff;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        100% {
            transform: rotate(360deg);
        }
    }
</style>
