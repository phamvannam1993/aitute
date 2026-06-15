<template>
    <Head title="Nghiệp vụ - AI 3 GỐC - Cộng đồng AI tử tế" />

    <div class="flex flex-col min-h-screen">
        <div class="mt-[48px] md:p-[34px] md:mt-2" >
            <div class="flex items-center justify-between p-[10px]">
               <div class="flex items-center space-x-2">
                    <a :href="route('home.index')">
                        <img src="/assets/img/admin/icon_left_home.png" alt="Trang chủ" class="w-auto h-[19px]"/>
                    </a>
                    <a :href="route('operation.index')" class="font-medium text-[#2C75E3]">
                    / Nghiệp vụ
                    </a>
                    <span class="font-medium text-[#2C75E3] line-clamp-1 flex-1">
                        / {{ capitalize(operation.name) }}
                    </span>
               </div>
                <Credit :credits="credits" />
            </div>
            <MainMenu/>
            <div class="flex flex-col xl:flex-row text-[#000] space-y-4 md:space-y-0 md:space-x-6 mt-4 md:mt-2">
                <div class="xl:w-4/5 flex flex-col space-y-4 p-[10px]">
                    <div class="flex flex-col space-y-2">
                        <h1 class="text-[30px] font-bold text-[#092A99]">{{ operation.name }}</h1>
                        <div v-if="!displayedAssistants.length" class="text-center text-gray-500 bg-white rounded-md">
                            <p>Không tìm thấy AI</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="(ai, index) in displayedAssistants" :key="index" class="ai-items bg-white p-4 rounded-2xl relative" >
                                <div class="flex items-center justify-between space-x-4 mb-2 cursor-pointer" @click="redirectBasedOnType(ai, ai.type)">
                                    <div class="w-2/8">
                                        <img :src="ai?.thumbnail_url || '/assets/svgs/sample-ai-icon-1.svg'" alt="AI Image" class="rounded-[13px] w-20 h-20" />
                                    </div>
                                    <div class="flex flex-1 flex-col space-y-1">
                                        <h3 class="font-medium line-clamp-2 text-sm">{{ ai?.name || 'AI mới'}}</h3>
                                        <div class="flex items-center space-x-2">
                                            <div class="rounded-full p-3 bg-[#2C75E3]"></div>
                                            <p class="text-[14px] line-clamp-1">{{ ai?.operation?.name || 'Giáo Dục'}}</p>
                                        </div>

                                    </div>

                                    <div class="w-1/8 flex flex-col items-center justify-between space-y-3">
                                        <button>
                                            <img src="/assets/svgs/heart-icon.svg" alt="Heart Icon" class="h-4" />
                                        </button>
                                        <button>
                                            <img src="/assets/svgs/zoom-icon.svg" alt="Zoom Icon" class="h-4" />
                                        </button>
                                        <button>
                                            <img src="/assets/svgs/share-icon.svg" alt="Share Icon" class="h-4" />
                                        </button>
                                    </div>
                                </div>
<!--                                <div class="w-full bg-[#DBDBDB] text-xs font-bold px-4 py-2 rounded-full">Miễn phí</div>-->
                            </div>

                        </div>

                        <div v-if="showMoreButton" class="flex justify-center mt-4">
                            <button
                                @click="showMoreItems"
                                class="showMore px-24 py-2 bg-white rounded-full hover:opacity-80 flex items-center justify-center"
                                :disabled="loadingMore">
                                <svg
                                    v-if="loadingMore"
                                    class="animate-spin h-5 w-5 mr-3 text-gray-800"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                {{ loadingMore ? 'Đang tải thêm...' : 'Xem thêm' }}
                            </button>
                        </div>
                    </div>
                    <div class="flex flex-col space-y-2">
                        <h2 class="text-[30px] font-bold text-[#092A99]">Nghiệp vụ liên quan</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="(ai, index) in relatedOperations" :key="index" class="ai-items bg-white p-4 rounded-2xl relative">
                                <div  @click="goToOperation(ai.id)" class="flex items-center justify-between space-x-4 mb-2 cursor-pointer">
                                    <div class="w-2/8">
                                        <img :src="ai?.image_url || '/assets/svgs/sample-ai-icon-1.svg'" alt="AI Image" class="rounded-[13px] w-20 h-20" />
                                    </div>
                                    <div class="flex flex-1 flex-col space-y-1">
                                        <h3 class="text-sm font-medium line-clamp-2">{{ ai?.name || 'AI mới'}}</h3>
                                        <div class="flex items-center space-x-2">
                                            <div class="rounded-full p-3 bg-[#2C75E3]"></div>
                                            <p class="text-[14px] line-clamp-1">{{ ai?.occupation?.name || 'Giáo Dục'}}</p>
                                        </div>

                                    </div>

                                    <div class="w-1/8 flex flex-col items-center justify-between space-y-3">
                                        <button>
                                            <img src="/assets/svgs/heart-icon.svg" alt="Heart Icon" class="h-4" />
                                        </button>
                                        <button>
                                            <img src="/assets/svgs/zoom-icon.svg" alt="Zoom Icon" class="h-4" />
                                        </button>
                                        <button>
                                            <img src="/assets/svgs/share-icon.svg" alt="Share Icon" class="h-4" />
                                        </button>
                                    </div>
                                </div>
<!--                                <div class="w-full bg-[#DBDBDB] text-xs font-bold px-4 py-2 rounded-full">Miễn phí</div>-->
                            </div>
                        </div>

                        <div v-if="showMoreButton" class="flex justify-center mt-8">
                            <button
                                @click="showMoreItems"
                                class="showMore px-24 py-2 bg-white rounded-full hover:opacity-80 flex items-center justify-center"
                                :disabled="loadingMore">
                                <svg
                                    v-if="loadingMore"
                                    class="animate-spin h-5 w-5 mr-3 text-gray-800"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                {{ loadingMore ? 'Đang tải thêm...' : 'Xem thêm' }}
                            </button>
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
import Footer from "../Home/Components/Footer.vue";
import Top10Ai from "@/Components/Top10Ai.vue";
import { Head } from "@inertiajs/vue3";
import MainMenu from "@/Components/MainMenu.vue";
import Credit from '@/Components/Credit.vue';
import { ref, computed } from 'vue';
defineOptions({ layout: Layout });

const props = defineProps({
    operation: Object,
    aiAssistants: Array,
    relatedOperations: Object,
    listAi: Array
});

const offset = ref(9);
const loadingMore = ref(false);

const aiItems = [
    {
      name: 'Trợ lí số 7',
      description: 'Tạo 10 ý tưởng trong 1 giây',
      job: 'Giáo dục và Đào tạo',
      thumbnail: '/assets/svgs/sample-ai-icon-1.svg',
      featured: true
    },
    {
      name: 'Trợ lí số 8',
      description: 'Tạo 10 ý tưởng trong 1 giây',
      job: 'Giáo dục và Đào tạo',
      thumbnail: '/assets/svgs/sample-ai-icon-1.svg',
      featured: true
    },
    {
      name: 'Trợ lí số 1',
      description: 'Tạo 10 ý tưởng trong 1 giây',
      job: 'Giáo dục và Đào tạo',
      thumbnail: '/assets/svgs/sample-ai-icon-1.svg',
      featured: true
    },
    {
      name: 'Trợ lí số 2',
      description: 'Tạo 10 ý tưởng trong 1 giây',
      job: 'Giáo dục và Đào tạo',
      thumbnail: '/assets/svgs/sample-ai-icon-1.svg',
      featured: true
    },
    {
      name: 'Trợ lí số 3',
      description: 'Tạo 10 ý tưởng trong 1 giây',
      job: 'Giáo dục và Đào tạo',
      thumbnail: '/assets/svgs/sample-ai-icon-1.svg',
      featured: true
    },
    {
      name: 'Trợ lí số 4',
      description: 'Tạo 10 ý tưởng trong 1 giây',
      job: 'Giáo dục và Đào tạo',
      thumbnail: '/assets/svgs/sample-ai-icon-1.svg',
      featured: true
    },
]

const displayedFeaturedAIItems = ref(aiItems.filter(ai => ai.featured).slice(0, 6));

const displayedAssistants = ref(props.aiAssistants);

const showMoreButton = computed(() => {
    return displayedAssistants.value.length >= 9;
});

const showMoreItems = async () => {
  try {
        loadingMore.value = true;
        const response = await axios.post('/load-more', { offset: offset.value });
        const newAssistants = response.data;

        displayedAssistants.value = [...displayedAssistants.value, ...newAssistants];
        offset.value += newAssistants.length;
      } catch (error) {
        console.error('Lỗi khi tải thêm dữ liệu:', error);
      } finally {
        loadingMore.value = false;
      }
};

const capitalize = (value) => {
    if (!value) return '';
    value = value.toString();
    return value.charAt(0).toUpperCase() + value.slice(1).toLowerCase();
};

const redirectBasedOnType = async (id, type) => {
    try {
        if(type === 'text') {
            window.location.href = route('ai-text.index', {id: id});
        } else if(type === 'image') {
            window.location.href = route('ai-image.index');
        } else {
            // chuyển hướng đến màn ai video
        }
    } catch (error) {
        console.error('Error during navigation:', error);
    }
};

const goToOperation = (id) => window.location.href = route("operation.show", {id: id});
</script>

<style scoped>
.ai-items {
  box-shadow: 0px 3px 3px rgba(0, 0, 0, 0.15);
}
.showMore {
  box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
}
</style>
