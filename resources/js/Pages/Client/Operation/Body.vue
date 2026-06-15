<template>
    <div class="flex flex-col xl:flex-row text-[#000] mt-4">
        <div class="xl:w-4/5 flex flex-col pt-2 px-[10px]">
            <div class="flex items-center bg-white rounded-[10px] md:rounded-[25px] p-[8px] space-x-1 md:space-x-8 flex-wrap md:flex-nowrap">
                <div class="flex items-center justify-between w-1/2 flex-row space-x-1 md:space-x-4">
                        <a href="javascript:void(0)"
                            @click="searchJob()"
                            :class="['text-[10px] md:text-[14px] font-semibold p-2 lg:py-3 lg:px-4 rounded-[15px]', activeTab === 'operation' ? 'bg-[#2C75E3] text-white' : 'text-black hover:bg-[#2C75E3] hover:text-white',]">
                            Dành cho bạn
                        </a>
                        <a
                            href="javascript:void(0)"
                            @click="TabJobPB()"
                            :class="[
                                'text-[10px] md:text-[14px] font-semibold p-2 lg:py-3 lg:px-4 rounded-[15px]',
                                activeTab === 'new'
                                    ? 'bg-[#2C75E3] text-white'
                                    : 'text-black hover:bg-[#2C75E3] hover:text-white',
                            ]"
                        >
                            Phổ biến
                        </a>
                        <a
                            href="javascript:void(0)"
                            @click="TabUserQT()"
                            :class="[
                                'text-[10px] md:text-[14px] font-semibold p-2 lg:py-3 lg:px-4 rounded-[15px]',
                                activeTab === 'occupation'
                                    ? 'bg-[#2C75E3] text-white'
                                    : 'text-black hover:bg-[#2C75E3] hover:text-white',
                            ]"
                        >
                            Đang quan tâm
                        </a>
                </div>

                <div class="flex-1 relative text-black">
                    <input
                        type="text"
                        placeholder="Tìm kiếm Nghiệp vụ..."
                        v-model="searchQuery"
                        @input="runSearchFunction"
                        class="w-full text-[10px] md:text-[14px] py-1 px-4 md:py-2 border border-[#5FB2FF] rounded-full md:rounded-[15px] focus:outline-none focus:ring-2 focus:ring-[#5FB2FF]"
                    />
                    <button
                        class="absolute hidden md:block right-3 top-2 text-gray-500"
                        @click="searchAI"
                    >
                        <img
                            src="/assets/svgs/search-icon.svg"
                            alt="search icon"
                            class="h-6"
                        />
                    </button>
                </div>
            </div>

            <div
                    v-if="loadingSearch"
                    class="flex items-center justify-center my-4"
                >
                    <svg
                        class="animate-spin h-5 w-5 mr-3 text-[#5FB2FF]"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                        ></path>
                    </svg>
            </div>
            <!-- Create job table here -->
            <div
                v-if="!loadingSearch && !operationDisplayed.length"
                class="text-center text-gray-500"
            >
                <p>Không tìm thấy nghiệp vụ</p>
            </div>
            <div v-if="!loadingSearch && operationDisplayed.length" class="mt-4 bg-white p-2 rounded-[15px] shadow">
                    <div
                        
                        class="max-h-[600px] overflow-y-auto overflow-x-auto max-w-[90vw] md:overflow-y-visible md:max-h-none"
                    >
                        <table
                            class="w-full bg-white min-w-[300px] md:min-w-[560px] lg:min-w-[760px]"
                        >
                            <thead>
                                <tr class="font-extrabold text-[13px] md:text-[15px]">
                                    <th
                                        class="border-b-2 border-gray-300 px-4 py-2 text-center"
                                    >
                                        #
                                    </th>
                                    <th
                                        class="border-b-2 border-gray-300 px-4 py-2 text-left"
                                    >
                                        Nghiệp vụ
                                    </th>
                                    <th
                                        class="border-b-2 border-gray-300 px-4 py-2 text-center"
                                    >
                                        Số người quan tâm
                                    </th>
                                    <th
                                        class="border-b-2 border-gray-300 px-4 py-2 text-center"
                                    >
                                        Quan tâm
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(
                                        operation, index
                                    ) in operationDisplayed"
                                    :key="operation.id"
                                    :class="[
                                        'hover:bg-gray-100 text-center text-[13px] md:text-[15px] font-semibold cursor-pointer',
                                        index !== operationDisplayed.length - 1
                                            ? 'border-b-2 border-gray-300'
                                            : '',
                                    ]"
                                >
                                    <td
                                        class="p-4"
                                        @click="goToDetail(operation.id)"
                                    >
                                        {{ index + 1 }}
                                    </td>
                                    <td
                                        class="p-4 text-left"
                                        @click="goToDetail(operation.id)"
                                    >
                                        {{ operation.name }}
                                    </td>

                                    <td
                                        class="p-4"
                                        @click="goToDetail(operation.id)"
                                    >
                                        {{ operation?.num_of_interests }}
                                    </td>
                                    <td class="p-4">
                                        <label
                                            class="relative inline-flex items-center cursor-pointer"
                                        >
                                            <input
                                                type="checkbox"
                                                v-model="
                                                    operation.is_interested
                                                "
                                                @change="
                                                    toggleInterest(operation)
                                                "
                                                class="sr-only peer"
                                                :true-value="1"
                                                :false-value="0"
                                            />
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"
                                            ></div>
                                        </label>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button
                        v-if="showMoreButton"
                        class="hidden md:block mt-4 p-2 w-full rounded-lg underline"
                        @click="loadMoreJobs"
                    >
                        Xem thêm
                    </button>
            </div>
        </div>
        <div class="xl:w-1/5 mt-4 md:p-[10px] md:mt-0 xl:ml-6">
              <Top10Ai :listAi="listAi"/>
        </div>
        <!-- Loading overlay -->
        <!-- <div v-if="loadingSearch" class="loading-overlay">
            <div class="spinner"></div>
        </div> -->
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import debounce from "lodash/debounce";
import axios from "axios";
import Top10Ai from "./../../../Components/Top10Ai.vue";
const props = defineProps({
    operations: Object,
    listAi: Array
});

const activeTab = ref("operation"); // Add this line to track the active tab
const isToggled = ref("true");
const searchQuery = ref("");
const loadingSearch = ref(false);
const isMobile = ref(window.innerWidth < 768);

const operationDisplayed = ref(props.operations);
const isAllOperationsDisplayed = ref(props.operations.length < 12 || false);
const offset = ref(12);
const showMoreButton = computed(() => !isAllOperationsDisplayed.value);
const type = ref(1);
const loadMoreJobs = async () => {
    try {
        loadingSearch.value = true;
        const response = await axios.post(route("operation.load-more"), {
            search: searchQuery.value,
            offset: offset.value,
            type: type.value,
        });
        const newOperations = response.data;
        operationDisplayed.value = [
            ...operationDisplayed.value,
            ...newOperations,
        ];
        offset.value += newOperations.length;
        isAllOperationsDisplayed.value = newOperations.length < 12;
    } catch (error) {
        console.error("Lỗi khi tải thêm dữ liệu:", error);
    } finally {
        setTimeout(() => {
            loadingSearch.value = false;
        }, 500);
    }
};

const searchJob = async () => {
    try {
        type.value = 1;
        loadingSearch.value = true;
        const response = await axios.post(route("operation.search"), {
            search: searchQuery.value.trim(),
            type: type.value,
        });
        operationDisplayed.value = response.data.data;
        offset.value = 12;
        console.log("offset", offset);
        isAllOperationsDisplayed.value = operationDisplayed.value.length < 12;
        activeTab.value = "operation";
    } catch (error) {
        console.error("Lỗi khi tìm kiếm:", error);
    } finally {
        setTimeout(() => {
            loadingSearch.value = false;
        }, 500);
    }
};

const TabUserQT = async () => {
    try {
        loadingSearch.value = true;
        type.value = 3;
        const response = await axios.post(route("operation.search"), {
            search: searchQuery.value.trim(),
            type: type.value,
        });
        operationDisplayed.value = response.data.data;
        offset.value = 12;
        console.log("offset", offset);
        isAllOperationsDisplayed.value = operationDisplayed.value.length < 12;
        activeTab.value = "occupation";
    } catch (error) {
        console.error("Lỗi khi tìm kiếm:", error);
    } finally {
        setTimeout(() => {
            loadingSearch.value = false;
        }, 500);
    }
};

const TabJobPB = async () => {
    try {
        loadingSearch.value = true;
        type.value = 2;
        const response = await axios.post(route("operation.search_pb"), {
            search: searchQuery.value.trim(),
            type: type.value,
        });
        operationDisplayed.value = response.data.data;
        offset.value = 12;
        console.log("offset", offset);
        isAllOperationsDisplayed.value = operationDisplayed.value.length < 12;
        activeTab.value = "new";
    } catch (error) {
        console.error("Lỗi khi tìm kiếm:", error);
    } finally {
        setTimeout(() => {
            loadingSearch.value = false;
        }, 500);
    }
};

const toggleInterest = async (operation) => {
    try {
        loadingSearch.value = true;
        await axios.post(
            route("operation.interest", {
                operation_id: operation.id,
            }),
            {
                is_interested: operation.is_interested,
            }
        );
        runSearchFunction();
    } catch (e) {
        console.error("Lỗi khi tải thm dữ liệu:", e);
    }
    setTimeout(() => {
        loadingSearch.value = false;
    }, 500);
};

const runSearchFunction = () => {
    if (type.value === 1) {
        const debouncedSearch = debounce(searchJob, 500);
        debouncedSearch();
    }
    if (type.value === 2) {
        const debouncedSearch = debounce(TabJobPB, 500);
        debouncedSearch();
    }
    if (type.value === 3) {
        const debouncedSearch = debounce(TabUserQT, 500);
        debouncedSearch();
    }
};

// const goToDetail = (id) => window.location.href = route("jobs.show", {id: id});
window.addEventListener("resize", () => {
    isMobile.value = window.innerWidth < 768;
});
const goToDetail = (id) =>
    (window.location.href = route("operation.show", { id: id }));
</script>

<style>
.custom-shadow {
    box-shadow: 0px 3px 3px rgba(0, 0, 0, 0.15);
}

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
