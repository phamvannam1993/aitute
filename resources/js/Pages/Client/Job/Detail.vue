<template>
    <Head title="Ngành nghề - AI 3 GỐC - Cộng đồng AI tử tế" />

    <div class="flex flex-col">
        <div class="mt-[48px] md:p-[34px] md:mt-2" >
            <div class="flex items-center justify-between p-[10px]">
                <div class="flex items-center space-x-2">
                    <a :href="route('home.index')">
                                <img src="/assets/img/admin/icon_left_home.png" alt="Trang chủ" class="w-auto h-[19px]"/>
                    </a>
                    <a :href="route('jobs.index')" class="font-medium text-[#2C75E3]">
                        / Ngành nghề
                    </a>
                    <span class="font-medium text-[#2C75E3] line-clamp-1 flex-1">/ {{ capitalize(occupation.name) }}</span>
                </div>
                <Credit />
            </div>
            <MainMenu/>
            <div class="flex flex-col xl:flex-row text-[#000] space-y-4 md:space-y-0 md:space-x-6 mt-4 md:mt-2">
                <div class="xl:w-4/5 flex flex-col space-y-4 p-[10px]">
                    <h1 class="text-[20px] md:text-[30px] font-bold text-[#092A99]">{{ occupation.name }}</h1>
                    <div class="flex flex-col space-y-4">
                        <div class="flex space-x-2 md:space-x-4 w-full">
                            <div class="w-2/3 md:w-3/5 h-[260px] md:h-[435px] ">
                                <img :src="occupation?.image_url || '/assets/svgs/sample-ai-icon-1.svg'" alt="Occupation Image" class="h-full w-full object-cover rounded-[15px] md:rounded-[20px]" />
                            </div>
                            <div class="w-1/3 md:w-2/5 h-[260px] md:h-[435px]  flex flex-col space-y-4">
                                <div class="flex flex-col h-full md:flex-row items-center md:h-2/5 space-y-4 md:space-y-0 md:space-x-4">
                                    <div class="w-full md:w-1/2 rounded-[15px] md:rounded-[20px] h-full bg-white shadow p-[6px] flex flex-col items-center space-y-2">
                                        <span class="bg-[#DBDBDB] font-bold text-[16px] rounded-[10px] md:rounded-[14px] w-full text-center py-1">
                                            Nghiệp vụ
                                        </span>
                                        <div class="relative flex items-center justify-center h-full">
                                            <span class="text-[40px] font-bold">
                                                {{occupation?.operations_count}}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="w-full md:w-1/2 rounded-[15px] md:rounded-[20px] h-full bg-white shadow p-[6px] flex flex-col items-center space-y-2">
                                        <span class="bg-[#DBDBDB] font-bold text-[16px] rounded-[10px] md:rounded-[14px] w-full text-center py-1">
                                            AI
                                        </span>
                                        <div class="relative flex items-center justify-center h-full">
                                            <span class="text-[40px] font-bold">
                                                {{occupation?.ais_count}}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="hidden md:block h-3/5 bg-white rounded-[20px] px-4 py-6">
                                    <p class="text-[16px] max-h-full overflow-y-auto">
                                        {{ occupation.description }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="block md:hidden h-[200px] bg-white rounded-[20px] px-4 py-6">
                            <p class="text-[12px] max-h-full overflow-y-auto">
                                {{ occupation.description }}
                            </p>
                        </div>
                    </div>
                    <div class="w-full" ref="parentDiv">
                        <h2 class="text-[20px] md:text-[25px] font-bold mb-2">
                            Ngành nghề liên quan
                        </h2>
                        <swiper
                            :modules="modules"
                            :slides-per-view="slidesPerView"
                            :space-between="20"
                            navigation
                            class=""
                            :style="{ maxWidth: swiperMaxWidth + 'px' }">
                            <swiper-slide
                                v-for="(occupation, index) in relatedOccupations"
                                :key="index"
                                class="py-2">
                                <div class="bg-white p-1 md:px-4 py-2 shadow-md flex flex-col md:flex-row md:items-center space-y-1 md:space-y-0 md:space-x-2 rounded-[10px] md:rounded-[25px] cursor-pointer" @click="goToDetail(occupation.id)">
                                    <img
                                        :src="occupation?.image_url || '/assets/svgs/sample-ai-icon-1.svg'"
                                        alt="Occupation Image"
                                        class="h-[90px] object-cover rounded-[15px] border-[1px] border-[#64C7FF]"/>
                                    <div class="flex flex-col items-start space-y-0.5 md:space-y-1">
                                        <h3 class="text-[8px] md:text-[18px] font-semibold line-clamp-1">
                                            {{ occupation.name }}
                                        </h3>
                                        <p class="text-[8px] md:text-[14px]">
                                            Nghiệp vụ:
                                            {{
                                                occupation?.operations_count || "0"
                                            }}
                                        </p>
                                        <p class="text-[8px] md:text-[14px]">
                                            AI:
                                            {{ occupation?.operations_ai || "0" }}
                                        </p>
                                    </div>
                                </div>
                            </swiper-slide>
                        </swiper>
                    </div>
    
                    <div>
                        <h2 class="text-[20px] md:text-[25px] font-bold mb-4">Nghiệp vụ</h2>
                        <div class="flex flex-col md:px-[40px]">
                            <div class="px-[40px] md:px-0 w-full">
                                <div class="w-full relative">
                                    <input
                                        type="text"
                                        placeholder="Tìm kiếm nghiệp vụ..."
                                        v-model="searchQuery"
                                        @keyup.enter="searchOperation"
                                        class="w-full text-[10px] md:text-[15px] px-4 py-1 md:py-2 border border-[#5FB2FF] rounded-full md:rounded-[15px] focus:outline-none focus:ring-2 focus:ring-[#5FB2FF]"
                                    />
                                    <button
                                        class="absolute right-3 top-2 text-gray-500"
                                    >
                                        <img
                                            src="/assets/svgs/search-icon.svg"
                                            alt="search icon"
                                            class="h-4 md:h-6"
                                        />
                                    </button>
                                </div>
                            </div>
                            <div class="md:px-[40px]">
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
                                    class="mt-4 bg-white p-2 rounded-[15px] shadow"
                                >
                                    <div
                                        v-if="
                                            !loadingSearch &&
                                            !displayedOperations.length
                                        "
                                        class="text-center text-gray-500"
                                    >
                                        <p>Không tìm thấy ngành nghề</p>
                                    </div>
                                    <div
                                        v-else
                                        class="max-h-[600px] overflow-y-auto overflow-x-auto max-w-[90vw] md:overflow-y-visible md:max-h-none"
                                    >
                                        <table
                                            class="w-full bg-white min-w-[760px]"
                                        >
                                            <thead>
                                                <tr
                                                    class="font-extrabold text-[13px] md:text-[15px]"
                                                >
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
                                                        AI phù hợp
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="(
                                                        operation, index
                                                    ) in displayedOperations"
                                                    :key="operation.id"
                                                    @click="
                                                        goToOperationDetail(
                                                            operation.id
                                                        )
                                                    "
                                                    :class="[
                                                        'hover:bg-gray-100 text-center text-[13px] md:text-[15px] font-semibold cursor-pointer',
                                                        index !==
                                                        displayedOperations.length -
                                                            1
                                                            ? 'border-b-2 border-gray-300'
                                                            : '',
                                                    ]"
                                                >
                                                    <td class="p-4">
                                                        {{ index + 1 }}
                                                    </td>
                                                    <td class="p-4 text-left">
                                                        {{ operation.name }}
                                                    </td>
                                                    <td class="p-4 underline">
                                                        {{
                                                            operation?.ais_count ||
                                                            "0"
                                                        }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <button
                                        v-if="showMoreButton"
                                        class="hidden md:block mt-4 p-2 w-full rounded-lg underline"
                                        @click="showMoreOperations"
                                    >
                                        Xem thêm
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="xl:w-1/5">
                    <Top10Ai :listAi="listAi"/>
                </div>
            </div>
        </div>
        <Footer class="mt-auto"/>
    </div>
</template>

<script setup>
import Layout from "@/Layouts/Client/ClientLayout.vue";
import { Head } from "@inertiajs/vue3";
import Top10Ai from "@/Components/Top10Ai.vue";
import Credit from '@/Components/Credit.vue';
import MainMenu from "@/Components/MainMenu.vue";
import Footer from "../Home/Components/Footer.vue";
import { ref, computed, watch, onBeforeUnmount, onMounted } from "vue";
import debounce from "lodash/debounce";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import "swiper/css/scrollbar";

import {
    Navigation,
    Pagination,
    Scrollbar,
    Autoplay,
    A11y,
} from "swiper/modules";

const modules = [Navigation, Pagination, Scrollbar, Autoplay, A11y];
defineOptions({ layout: Layout });

const props = defineProps({
    occupation: Object,
    operations: Array,
    relatedOccupations: Array,
    listAi: Array,
});

const occupationId = ref(props.occupation.id);
const displayedOperations = ref(props.operations);

const loadingMore = ref(false);
const searchQuery = ref("");
const offset = ref(12);
const isAllOperationsDisplayed = ref(props.operations.length < 12 || false);
const loadingSearch = ref(false);
let slidesPerView = ref(3);
let swiperMaxWidth = ref(380);
const parentDiv = ref(null);

const showMoreButton = computed(() => !isAllOperationsDisplayed.value);

const showMoreOperations = async () => {
    try {
        loadingMore.value = true;
        const response = await axios.post(route("jobs.load-more"), {
            occupation_id: occupationId.value,
            search: searchQuery.value,
            offset: offset.value,
        });
        const newOperations = response.data;

        displayedOperations.value = [
            ...displayedOperations.value,
            ...newOperations,
        ];

        offset.value += newOperations.length;
        isAllOperationsDisplayed.value = newOperations.length < 12;
    } catch (error) {
        console.error("Lỗi khi tải thêm dữ liệu:", error);
    } finally {
        loadingMore.value = false;
    }
};

const searchOperation = async () => {
    try {
        loadingSearch.value = true;
        const response = await axios.post(route("operation.search"), {
            search: searchQuery.value,
        });
        displayedOperations.value = response.data.data;
        offset.value = response.data.from;
        isAllOperationsDisplayed.value = displayedOperations.value.length < 12;
    } catch (error) {
        console.error("Lỗi khi tìm kiếm AI:", error);
    } finally {
        loadingSearch.value = false;
    }
};

const debouncedSearch = debounce(searchOperation, 500);

watch(searchQuery, () => {
    debouncedSearch();
});

const goToOperationDetail = (id) => window.location.href = route("operation.show", {id: id});

const updateSlidesPerView = () => {
    if (window.innerWidth <= 768) {
        slidesPerView.value = 3;
    } else if (window.innerWidth > 768 && window.innerWidth <= 1024) {
        slidesPerView.value = 2;
    } else {
        slidesPerView.value = 3;
    }
};

const updateSwiperMaxWidth = () => {
    if (parentDiv.value) {
        let parentWidth = parentDiv.value.clientWidth;
        swiperMaxWidth.value = parentWidth;
    }
};

const capitalize = (value) => {
    if (!value) return "";
    value = value.toString();
    return value.charAt(0).toUpperCase() + value.slice(1).toLowerCase();
};

const goToDetail = (id) => (window.location.href = route("jobs.show", { id: id }));

onMounted(() => {
    updateSwiperMaxWidth();
    updateSlidesPerView();
    window.addEventListener("resize", updateSwiperMaxWidth);
    window.addEventListener("resize", updateSlidesPerView);
});

onBeforeUnmount(() => {
    window.removeEventListener("resize", updateSlidesPerView);
    window.removeEventListener("resize", updateSwiperMaxWidth);
});
</script>

<style>
.progress {
    transition: stroke-dashoffset 0.35s;
    transform: rotate(-90deg);
    transform-origin: 50% 50%;
}

.swiper-button-next,
.swiper-button-prev {
    background-color: rgba(40, 126, 255, 0.3);
    color: white;
    height: 33px;
    width: 33px;
    border-radius: 1000px;
    margin-top: -10px;
}

.swiper-button-next::after,
.swiper-button-prev::after {
    font-size: 20px;
}

.swiper-button-next {
    right: 0px;
}

.swiper-button-prev {
    left: 0px;
}
</style>
