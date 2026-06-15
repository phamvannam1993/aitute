<template>
    <form class="max-w-lg mx-auto mb-[20px]" @submit="handleSubmit">
        <div class="flex">
            <button @click="showDropdown = !showDropdown" id="dropdown-button" data-dropdown-toggle="dropdown"
                    class="relative flex-shrink-0 z-10 inline-flex items-center py-1.5 px-3 text-sm font-medium text-center border border-gray-300 rounded-s-lg focus:outline-none"
                    type="button">
                {{ durationName }}
                <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                </svg>
                <div v-show="showDropdown" id="dropdown" class="z-10 absolute top-[120%] left-0 divide-y divide-gray-500 rounded-lg shadow w-44 bg-white">
                    <ul class="py-1 text-sm" aria-labelledby="dropdown-button">
                        <li v-for="duration in durationOptions">
                            <button type="button" class="inline-flex w-full px-4 py-2 text-black hover:bg-gray-50" @click="handleChooseDuration(duration)">{{ duration.name }}</button>
                        </li>
                    </ul>
                </div>
            </button>
            <div class="relative w-full">
                <input v-model="keyword" type="search" id="search-dropdown" class="block p-1.5 pr-3.5 w-full z-20 text-sm rounded-e-lg border border-gray-300 border-s-0 focus:outline-none" placeholder="Nhập từ khoá..." required/>
            </div>
        </div>
    </form>
    <div class="max-h-[314px] overflow-auto p-[12px]">
        <div class="grid grid-cols-3 gap-4 overflow-y-scroll">
            <div class="rounded shadow cursor-pointer" v-for="(video, k) in searchVideos" :key="k" @click="() => videoLink = video.link">
                <video :src="video.link" class="w-full rounded-md" :alt="video.title" controls></video>
                <p class="text-sm mt-2">{{ video.title }}</p>
                <p class="text-xs text-gray-500">{{ formatDuration(video.duration) }}</p>
            </div>
        </div>

        <div class="text-center mt-[24px]">
            <button v-if="hasMore" type="button" class="py-2.5 px-5 me-2 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:outline-none focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 inline-flex items-center" @click="handleNextPage">
                <svg v-show="isLoading" aria-hidden="true" role="status" class="inline w-4 h-4 me-3 text-gray-200 animate-spin dark:text-gray-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="#1C64F2"/>
                </svg>
                {{ isLoading ? "Loading..." : "Xem thêm" }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";

const videoLink = defineModel()

const durationOptions = [
    {
        name: "Tất cả",
        value: "any",
    },
    {
        name: "Ngắn (<4 phút)",
        value: "short",
    },
    {
        name: "Trung bình (4-20 phút)",
        value: "medium",
    },
    {
        name: "Dài (>20 phút)",
        value: "long",
    },
];
const durationType = ref("any");
const durationName = ref("Tất cả");
const handleChooseDuration = async (duration) => {
    durationType.value = duration.value;
    durationName.value = duration.name;
    await searchFirstPage();
};
const showDropdown = ref(false);
const keyword = ref("");
const searchVideos = ref([]);
const isLoading = ref(false);
const currentPage = ref(1);
const hasMore = ref(false);
const callSearchVideo = async () => {
    try {
        if (!keyword.value) {
            return;
        }

        isLoading.value = true;
        const response = await axios.get(route("video.search"), {
            params: {
                keyword: keyword.value,
                duration_type: durationType.value,
                page: currentPage.value,
            },
        });
        if (currentPage.value === 1) {
            searchVideos.value = response.data.items;
        } else {
            searchVideos.value = [...searchVideos.value, ...response.data.items];
        }
        hasMore.value = response.data.queries.nextPage?.length > 0;
    } catch (e) {
        // console.log(e);
    } finally {
        isLoading.value = false;
    }
};

const handleNextPage = async () => {
    currentPage.value++;
    await callSearchVideo();
};

const searchFirstPage = async () => {
    currentPage.value = 1;
    await callSearchVideo();
};

const handleSubmit = async (e) => {
    e.preventDefault();
    await searchFirstPage();
};

const formatDuration = (seconds) => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
};

</script>