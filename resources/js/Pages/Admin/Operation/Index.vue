<template>
    <Master>
        <div class="lg:w-5/6 w-full bg-[url('../../public/assets/img/aiwow/layout_background.png')]  bg-cover bg-center min-h-screen p-5 lg:p-10">
            <div class="flex flex-col lg:flex-row  items-start justify-between lg:items-end  mb-5 space-y-4 lg:space-y-0 lg:space-x-4">
                <div class="w-full lg:w-3/4 flex flex-col space-y-2">

                    <div class="flex items-center space-x-2 text-blue-900">
                        <img src="/assets/img/admin/icon_left_home.png" alt="Trang chủ" class="w-auto h-[19px]">
                        <span class="font-medium text-[#2C75E3]">/ Nghiệp vụ</span>
                    </div>
                    <div class="relative w-full">
                        <input
                            v-model="searchQuery"
                            @input="debouncedSearch"
                            type="text"
                            placeholder="Tìm kiếm nghiệp vụ..." class="w-full p-2 pl-10 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-black">
                         <img src="/assets/img/admin/icon_seach.png"  class="w-auto h-[19px] absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-500">
                    </div>
                </div>

                <a :href="route('admin.operation.create')" class="bg-blue-600 text-white text-center px-4 py-2 rounded-lg shadow hover:bg-blue-700 focus:outline-none w-[270px] lg:w-[300px]">
                    + Tạo nghiệp vụ mới
                </a>
            </div>
            <div class="flex flex-col lg:flex-row  items-start justify-between lg:items-end mb-5 space-y-4 lg:space-y-0 lg:space-x-8">
                <div class="w-full lg:w-4/5 flex flex-col space-y-2">
                </div>

                <select
                    v-model="sortDirection"
                    @change="sort"
                    class="bg-white text-center text-black border border-gray-300 p-2 rounded-[22px] cursor-pointer shadow-sm focus:ring-blue-500 focus:border-blue-500 w-[300px] lg:w-[210px]">
                    <option selected value="asc">Sắp xếp theo A-Z</option>
                    <option value="desc">Sắp xếp theo Z-A</option>
                </select>

            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <template v-for="operation in operations.data" :key="operation.id" >
                    <div class="bg-white rounded-lg shadow-md p-4 flex space-x-4 items-start">
                        <img :src="operation.image_url ? operation.image_url : '/assets/img/admin/job_1.png'" alt="Icon" class="w-10 h-10 rounded-lg">

                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <h3 class="line-clamp-1 text-md font-semibold text-blue-900">{{ operation.name }}</h3>
                                <div class="flex items-center space-x-2">
                                    <button @click="editOperation(operation.id)" class="text-gray-400 hover:text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-6.804a2.5 2.5 0 113.536 3.536L6 21H2.5L2 17.5l12.732-12.732z" />
                                        </svg>
                                    </button>

                                    <button @click="openDeleteModal(operation.id)" class="text-gray-400 hover:text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-1 12a2 2 0 01-2 2H8a2 2 0 01-2-2L5 7m5 4v6m4-6v6M9 7h6m-7 0a2 2 0 012-2h4a2 2 0 012 2" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <p class="line-clamp-3 text-sm text-gray-500 mt-1 leading-tight h-12 overflow-hidden text-ellipsis">
                                {{ operation.description }}
                            </p>

                            <div class="flex items-center space-x-2 mt-3">
                                <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                                <span class="text-blue-500 text-sm font-medium">{{ operation.occupation_name }}</span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <div class="mt-5 text-center">
                <button
                    v-if="operations.next_page_url"
                    @click="loadMore"
                    class="bg-white text-[#3D3D3D] px-6 py-2 rounded-lg shadow hover:bg-blue-700 focus:outline-none">
                    Xem thêm
                </button>
            </div>
        </div>

        <!-- Edit Job Modal -->
        <edit-job-modal v-if="showModal"
                        :operation="selectedOperation"
                        :showModal="showModal"
                        @close="closeModal"
                        @updateData="handleUpdateData"
        >

        </edit-job-modal>

        <!-- Loading overlay -->
        <div v-if="isLoadingMore" class="loading-overlay">
            <div class="spinner"></div>
        </div>

        <div v-if="showDeleteModal" class="fixed inset-0 flex items-center justify-center bg-gray-500 bg-opacity-75 z-50">
            <div class="bg-white rounded-lg p-6 max-w-xl w-full">
                <h3 class="text-lg font-semibold text-red-600">Bạn có chắc chắn muốn xóa nghiệp vụ này không?</h3>
                <p class="mt-2 text-gray-700">
                    Nếu bạn xóa nghiệp vụ này, những AI sau đây cũng sẽ bị xóa theo:
                </p>
                <ul class="list-disc list-inside mt-2 max-h-40 overflow-y-auto bg-gray-200 p-3">
                    <li v-for="ai in selectedOperationDeleted" :key="ai.id" class="text-gray-700">
                        {{ ai.name }}
                    </li>
                </ul>
                <div class="mt-4 flex justify-end space-x-2">
                    <button @click="showDeleteModal = false" class="px-4 py-2 bg-gray-200 rounded-lg">Hủy</button>
                    <button @click="confirmDeleteOperation(idDeleted)" class="px-4 py-2 bg-red-600 text-white rounded-lg">Xóa</button>
                </div>
            </div>
        </div>

    </Master>
</template>

<script setup>
import { router, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import Master from "../../../Layouts/Admin/Master.vue";
import EditJobModal from "../../../Pages/Admin/Operation/EditJobModal.vue";

import { debounce } from "lodash";
import { toast } from "vue3-toastify";

const props = defineProps({
    operations: Object
});

// Lấy dữ liệu từ props của Inertia
let { filters } = usePage().props;
// Biến trạng thái hiển thị modal
const showModal = ref(false);
const selectedOperation = ref(null);
// Biến lưu trữ giá trị tìm kiếm và sắp xếp
const searchQuery = ref(filters.search || '');
const sortDirection = ref('asc');
const sortColumn = 'name';
const showDeleteModal = ref(false);
const selectedOperationDeleted = ref(null);
const idDeleted = ref(null);
// Hàm tìm kiếm
const search = () => {
    router.get(route('admin.operation.index'), {
        search: searchQuery.value,
        sort: sortColumn,
        direction: sortDirection.value
    }, {
        preserveState: true,
        replace: true,
        onSuccess: (page) => {
            // Khi có kết quả mới từ tìm kiếm, thay thế danh sách occupations hiện tại
            // allOccupations.value = [...page.props.occupations.data];
            // console.log('Occupations sau khi tìm kiếm:', allOccupations.value);
        }
    });
};

const handleUpdateData = () => {
    search();
};

// Hàm sắp xếp
const sort = () => {
    router.get(route('admin.operation.index'), {
        search: searchQuery.value,
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
    if (props.operations.next_page_url) {
        isLoadingMore.value = true; // Hiển thị trạng thái loading
        try {
            await router.get(props.operations.next_page_url, {}, {
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
const editOperation = async (id) => {
    try {
        isLoadingMore.value = true; // Hiển thị trạng thái loading
        const response = await axios.get(`/admin/operation/${id}/edit`,{}, {
                preserveState: true,
                replace: true,
                onSuccess: (page) => {}
        });
        setTimeout(() => {
            isLoadingMore.value = false;
        }, 500);
        selectedOperation.value = response.data.operation;
        showModal.value = true; // Mở modal
    } catch (error) {
        isLoadingMore.value = false; // Hiển thị trạng thái loading
        console.error('Error loading occupation:', error);
    }
};

const openDeleteModal  = async (id) => {
    try {
        isLoadingMore.value = true;
        idDeleted.value = id;
        const response = await axios.get(`/admin/operation/${id}/listAis`,{}, {
            preserveState: true,
            replace: true,
            onSuccess: (page) => {}
        });
        setTimeout(() => {
            isLoadingMore.value = false;
        }, 500);
        selectedOperationDeleted.value = response.data.listAis;
    } catch (error) {
        isLoadingMore.value = false;
        console.error('Error loading operation:', error);
    }

    showDeleteModal.value = true;
};
const confirmDeleteOperation = async (id) => {
    try {
        isLoadingMore.value = true;
        try {
            const response = await axios.delete(`/admin/operation/${id}`);
            search();
            console.log('Deleted successfully', response.data);
            toast.success("Xóa Nghiệp vụ Thành công !");
        } catch (error) {
            toast.success("Error deleting assistant: !");
            console.error('Error deleting assistant:', error);
        }
        isLoadingMore.value = false;
    } catch (error) {
        isLoadingMore.value = false;
        console.error('Error deleting assistant:', error);
    } finally {
        showDeleteModal.value = false;
    }
};


const closeModal = () => {
    showModal.value = false;
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
