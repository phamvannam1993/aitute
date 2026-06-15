<template>
    <div class="flex flex-col xl:flex-row text-[#000]">
        <div class="xl:w-3/4 py-4 px-[10px] flex flex-col">
            <div class="px-[40px] md:px-0">
                <div class="w-full relative">
                    <input
                            type="text"
                            v-model="searchQuery"
                            @keyup.enter="searchJob"
                            placeholder="Tìm kiếm ngành nghề..."
                            class="w-full text-[10px] md:text-[15px] px-4 py-1 md:py-2 border border-[#5FB2FF]  rounded-full md:rounded-[15px] focus:outline-none focus:ring-2 focus:ring-[#5FB2FF]"
                        />
                    <button class="absolute right-3 top-2 text-gray-500">
                            <img
                                src="/assets/svgs/search-icon.svg"
                                alt="search icon"
                                class="h-4 md:h-6"
                            />
                    </button>
                </div>
            </div>

            <div v-if="loadingSearch" class="flex items-center justify-center my-4">
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
            <div v-if="!loadingSearch && !occupationsDisplayed.length" class="text-center text-gray-500 mt-4">
                <p>Không tìm thấy ngành nghề</p>
            </div>
            <div v-if="!loadingSearch && occupationsDisplayed.length > 0" class="mt-4 bg-white p-2 rounded-[15px] shadow">
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
                                            Ngành nghề
                                        </th>
                                        <th
                                            class="border-b-2 border-gray-300 px-4 py-2 text-center"
                                        >
                                            Nghiệp Vụ
                                        </th>
                                        <th
                                            class="border-b-2 border-gray-300 px-4 py-2 text-center"
                                        >
                                            AI
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(
                                            occupation, index
                                        ) in occupationsDisplayed"
                                        :key="occupation.id"
                                        @click="goToDetail(occupation.id)"
                                        :class="[
                                            'hover:bg-gray-100 text-center text-[13px] md:text-[15px] font-semibold cursor-pointer',
                                            index !==
                                            occupationsDisplayed.length - 1
                                                ? 'border-b-2 border-gray-300'
                                                : '',
                                        ]"
                                    >
                                        <td class="p-4">{{ index + 1 }}</td>
                                        <td class="p-4 text-left">
                                            {{ occupation.name }}
                                        </td>

                                        <td class="p-4 underline">
                                            {{ occupation?.operations_count }}
                                        </td>
                                        <td class="p-4 underline">
                                            {{ occupation?.ais_count }}
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
        <div class="xl:w-1/4 md:px-[10px]">
            <Top10Ai :listAi="listAi"/>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import debounce from "lodash/debounce";
import Top10Ai from "./../../../Components/Top10Ai.vue";

const props = defineProps({
    occupations: Object,
    listAi: Array,
});

const searchQuery = ref("");
const loadingSearch = ref(false);
const isMobile = ref(window.innerWidth < 768);

const occupationsDisplayed = ref(props.occupations);
const isAllOperationsDisplayed = ref(props.occupations.length < 12 || false);
const offset = ref(12);
const showMoreButton = computed(() => !isAllOperationsDisplayed.value);

const loadMoreJobs = async () => {
    try {
        loadingSearch.value = true;
        const response = await axios.post(route("jobs.load-all-more"), {
            search: searchQuery.value,
            offset: offset.value,
        });
        const newOperations = response.data;
        occupationsDisplayed.value = [
            ...occupationsDisplayed.value,
            ...newOperations,
        ];
        offset.value += newOperations.length;
        isAllOperationsDisplayed.value = newOperations.length < 12;
    } catch (error) {
        console.error("Lỗi khi tải thêm dữ liệu:", error);
    } finally {
        loadingSearch.value = false;
    }
};

const searchJob = async () => {
    try {
        loadingSearch.value = true;
        const response = await axios.post(route("jobs.search"), {
            search: searchQuery.value.trim(),
        });
        occupationsDisplayed.value = response.data.data;
        offset.value = response.data.from;
        isAllOperationsDisplayed.value = occupationsDisplayed.value.length < 12;
    } catch (error) {
        console.error("Lỗi khi tìm kiếm:", error);
    } finally {
        loadingSearch.value = false;
    }
};

const debouncedSearch = debounce(searchJob, 500);

watch(searchQuery, () => {
    debouncedSearch();
});

const goToDetail = (id) =>
    (window.location.href = route("jobs.show", { id: id }));
window.addEventListener("resize", () => {
    isMobile.value = window.innerWidth < 768;
});
</script>

<style scoped>
.custom-shadow {
    box-shadow: 0px 3px 3px rgba(0, 0, 0, 0.15);
}
</style>
