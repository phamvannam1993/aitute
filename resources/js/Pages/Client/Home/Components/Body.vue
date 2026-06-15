<template>
  <div class="bg-cover bg-center">
    <div
      class="flex items-center bg-transparent md:bg-white rounded-[25px] mx-2 md:mx-0 mb-4 md:p-[8px] space-x-2 flex-wrap justify-center md:flex-nowrap">
      <div class="flex items-center justify-between md:justify-start w-full md:w-1/2 p-[4px] md:p-0 md:space-x-2 flex-row bg-white rounded-[10px] mb-4 md:mb-0">
        <a href="javascript:void(0)" @click="fetchOperationFocusedAI" :class="[
          'text-[10px] md:text-[14px] font-bold py-2 px-4 rounded-[10px] md:rounded-[15px]',
          activeTab === 'operation' ? 'bg-[#2C75E3] text-white' : 'text-black hover:bg-[#2C75E3] hover:text-white'
        ]">
          AI của bạn
        </a>
        <a href="javascript:void(0)" @click="fetchImageAI" :class="[
          'text-[10px] md:text-[14px] font-bold py-2 px-4 rounded-[10px] md:rounded-[15px]',
          activeTab === 'new' ? 'bg-[#2C75E3] text-white' : 'text-black hover:bg-[#2C75E3] hover:text-white'
        ]">
          AI mới
        </a>
        <a href="javascript:void(0)" @click="fetchOccupationFocusedAI" :class="[
          'text-[10px] md:text-[14px] font-bold py-2 px-4 rounded-[10px] md:rounded-[15px]',
          activeTab === 'occupation' ? 'bg-[#2C75E3] text-white' : 'text-black hover:bg-[#2C75E3] hover:text-white'
        ]">
          AI nghành nghề
        </a>
      </div>

      <div class="w-[280px] md:w-1/2 relative text-black">
        <input type="text" placeholder="Tìm kiếm AI..." v-model="searchQuery" @keyup.enter="searchAI"
          class="w-full text-[10px] md:text-[14px] px-4 py-0.5 md:py-2 border border-[#5FB2FF] rounded-[20px] md:rounded-[15px] focus:outline-none focus:ring-2 focus:ring-[#5FB2FF]" />
        <button class="absolute right-3 top-2.5 md:top-2 text-gray-500" @click="searchAI">
          <img src="/assets/svgs/search-icon.svg" alt="search icon" class="w-[10px] h-auto md:h-6 md:w-auto" />
        </button>
      </div>
    </div>

    <div class="flex flex-col xl:flex-row my-2 xl:space-x-6 text-black">
      <div class="px-[14px] md:px-0 xl:w-3/4 mx-auto">
        <div v-if="loadingSearch" class="flex items-center justify-center my-4 h-28">
          <svg  class="animate-spin h-5 w-5 mr-3 text-[#5FB2FF]" xmlns="http://www.w3.org/2000/svg"
            fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
          </svg>
        </div>
        <div v-if="!loadingSearch">
          <div v-if="type === 'image'" class="flex items-center mb-2 md:flex-none">
            <img src="/assets/svgs/ai-new-icon.svg" alt="AI Icon" class="h-[20px] w-[20px] md:w-auto md:h-8 mr-2" />
            <h2 v-if="activeTab == 'new'" class="text-[15px] md:text-[25px] font-semibold md:leading-[52px]">
              AI mới
            </h2>
            <h2 v-else-if="activeTab == 'operation'" class="text-[15px] md:text-[25px] font-semibold md:leading-[52px]">
              AI của bạn
            </h2>
            <h2 v-else class="text-[15px] md:text-[25px] font-semibold md:leading-[52px]">
              AI ngành nghề
            </h2>
          </div>

          <div v-else class="flex items-center mb-2 md:hidden">
            <img src="/assets/svgs/ai-new-icon.svg" alt="AI Icon" class="h-[20px] w-[20px]  md:w-auto md:h-8 mr-2" />
            <h2 v-if="activeTab == 'new'" class="text-[15px] md:text-[25px] font-semibold md:leading-[52px]">
              AI mới
            </h2>
            <h2 v-else-if="activeTab == 'operation'" class="text-[15px] md:text-[25px] font-semibold md:leading-[52px]">
              AI của bạn
            </h2>
            <h2 v-else class="text-[15px] md:text-[25px] font-semibold md:leading-[52px]">
              AI nghành nghề
            </h2>
          </div>
        </div>


        <div v-if="!loadingSearch && !displayedAssistants?.length" class="text-center text-gray-500">
          <p>Không tìm thấy AI</p>
        </div>

        <!-- AI Items -->
        <div v-if="type === 'image' && !loadingSearch" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-3 md:gap-4 px-[20px] md:px-0">
          <div v-for="(ai, index) in displayedAssistants" :key="index"
          class="ai-items w-full bg-white p-2 rounded-[10px] md:rounded-2xl relative" @click="navigateTo(ai.id, ai.type)">
            <div class="flex items-center justify-between space-x-2 md:space-x-1 md:mb-2">
              <div class="w-[60px] md:w-2/8 cursor-pointer"  @click="navigateTo(ai.id, ai.type)">
                <img :src="ai?.thumbnail_url || '/assets/svgs/sample-ai-icon-1.svg'" alt="AI Image"  class="size-[60px] md:size-[49px] rounded-[13px]" />
              </div>
              <div class="flex flex-1 flex-col space-y-[2px] cursor-pointer" @click="navigateTo(ai.id, ai.type)">
                <h3 class="text-[12px] font-bold line-clamp-1">{{ ai?.name || 'AI mới' }}</h3>
                <p class="text-gray-600 text-[12px] line-clamp-1">{{ ai?.description || "Đây là AI mới" }}</p>
                <div class="flex items-center space-x-1 md:space-x-2">
                  <div class="rounded-full p-1 md:p-2 bg-[#2C75E3]"></div>
                  <p class="text-[10px] md:text-[12px] line-clamp-1">{{ ai?.occupation?.name || 'Giáo Dục' }}</p>
                </div>
              </div>

              <div class="md:w-1/8 flex flex-col items-center justify-between space-y-5">
                <div class="flex flex-row justify-between md:flex-col md:space-y-2 w-full">
                  <button @click.stop="updateFavorites(ai)">
                    <img v-if="!ai?.is_favorited_by_user" src="/assets/svgs/heart-icon.svg" alt="Heart Icon" class="size-[12px] md:size-4" />
                    <img v-if="ai?.is_favorited_by_user" src="/assets/img/heart-active.png" alt="Heart Icon" class="size-[12px] md:size-4" />
                  </button>
                  <button>
                    <img src="/assets/svgs/zoom-icon.svg" alt="Zoom Icon" class="size-[12px] md:size-4" />
                  </button>
                  <button>
                    <img src="/assets/svgs/share-icon.svg" alt="Share Icon" class="size-[12px] md:size-4" />
                  </button>
                </div>
<!--                <div class="md:hidden w-full bg-[#DBDBDB] text-[8px] md:text-xs font-bold px-3 md:px-4 py-1 rounded-full">Miễn phí</div>-->
              </div>
            </div>
<!--          <div class="hidden md:block w-full bg-[#DBDBDB] text-[12px] font-bold px-4 py-1 rounded-full">Miễn phí</div>-->
          </div>
        </div>

        <div v-if="type === 'text' && !loadingSearch" class="space-y-4 md:px-[120px] xl:px-[152px] px-[20px]">
          <div v-for="(ai, index) in displayedAssistants" :key="index"
            class="bg-white rounded-[10px] md:rounded-[25px] px-[18px] py-[16px] ai-items w-full" @click="navigateTo(ai.id, ai.type)">
            <div class="flex items-start space-x-4 border-b-[2px] border-[#C5C5C5] pb-2">
              <div class="flex-shrink-0 my-auto cursor-pointer" >
                <img :src="ai.thumbnail_url || '/assets/svgs/sample-ai-icon-1.svg'" alt="Category Icon" class="size-[25px] md:size-[44px] rounded-full">
              </div>
              <div class="flex-grow">
                <div class="flex justify-between items-start">
                  <div class="cursor-pointer">
                    <h3 class="font-medium text-[10px] md:text-[16px] line-clamp-1 leading-[10px] md:leading-[16px]">{{ ai?.operation?.name }}</h3>
                    <p class="text-[8px] md:text-[14px] leading-[14px] md:leading-[20px] text-[#646464]">{{ formatDate(ai.created_at) }}</p>
                  </div>
                  <div class="flex space-x-3">
                    <button>
                      <img src="/assets/svgs/zoom-icon.svg" alt="Zoom" class="w-[14px] h-auto md:h-[16px] md:w-auto">
                    </button>
                    <button>
                      <img src="/assets/svgs/share-icon.svg" alt="Share" class="w-[14px] h-auto md:h-[16px] md:w-auto">
                    </button>
                    <button @click.stop="updateFavorites(ai)">
                      <img v-if="!ai?.is_favorited_by_user" src="/assets/svgs/heart-icon.svg" alt="Heart Icon"
                        class="w-[14px] h-auto md:h-[16px] md:w-auto" />
                      <img v-if="ai?.is_favorited_by_user" src="/assets/img/heart-active.png" alt="Heart Icon"
                        class="w-[14px] h-auto md:h-[16px] md:w-auto" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="mt-2 flex space-x-4 cursor-pointer items-center" @click="navigateTo(ai.id, ai.type)">
                <img :src="ai.thumbnail_url || '/assets/svgs/sample-ai-icon-1.svg'" alt="AI Thumbnail"
                  class="size-[60px] md:size-[87px] rounded-[16px]">
              <div class="flex flex-col">
                <h2 class="text-[20px] md:text-[30px] font-bold line-clamp-1">{{ ai.name }}</h2>
                <p class="text-[10px] md:text-[14px] line-clamp-3">{{ ai.description }}</p>
              </div>
            </div>
            <div class="mt-2 flex justify-end">
              <span class="bg-[#D9D9D9] text-[8px] md:text-[14px] px-8 py-1 rounded-[13px]">
                {{ ai?.price || 'Miễn phí' }}
              </span>
            </div>
          </div>
        </div>
        <div v-if="type === 'video' && !loadingSearch" class="space-y-2 grid grid-cols-1 md:gap-4 px-[20px] md:px-[100px] xl:px-[152px]">
          <div v-for="(occupation, index) in Object.values(displayedAssistantsByOccupation)" :key="index">
            <div>
              <h2 class="text-[20px] md:text-[28px] font-bold mb-2 line-clamp-1">{{ `${index + 1 + (currentPage - 1) * itemsPerPage}. ${occupation.name}` }}</h2>
            </div>
            <div class="flex flex-col space-y-2 h-full">
              <div v-for="(ai, index) in occupation.items" :key="index"
                class="bg-white rounded-[10px] md:rounded-[25px] px-[18px] py-[16px] ai-items w-full flex md:flex-col relative" @click="navigateTo(ai.id, ai.type)">
                <div class="flex items-center justify-between space-x-2 md:space-x-4 md:mb-2 w-full">
                  <div class="cursor-pointer"  @click="navigateTo(ai.id, ai.type)">
                <img :src="ai?.thumbnail_url || '/assets/svgs/sample-ai-icon-1.svg'" alt="AI Image"  class="size-[60px] md:size-[87px] rounded-[16px]" />
                  </div>
                  <div class="flex flex-1 flex-col space-y-[2px] cursor-pointer" @click="navigateTo(ai.id, ai.type)">
                <h3 class="text-[20px] md:text-[30px] font-bold line-clamp-1">{{ ai?.name || 'AI mới' }}</h3>
                <p class="text-[10px] md:text-[14px] line-clamp-2">{{ ai?.description || "Đây là AI mới" }}</p>
                <div class="flex items-center space-x-1 md:space-x-2">
                  <div class="rounded-full p-1 md:p-2 bg-[#2C75E3]"></div>
                  <p class="text-[10px] md:text-[14px] line-clamp-1">{{ ai?.occupation?.name || 'Giáo Dục' }}</p>
                </div>
                  </div>

                  <div class="md:w-1/8 flex flex-col items-center justify-between space-y-5 h-full">
                <div class="flex flex-row justify-between md:flex-col w-full pt-2 md:h-full">
                  <button @click.stop="updateFavorites(ai)">
                    <img v-if="!ai?.is_favorited_by_user" src="/assets/svgs/heart-icon.svg" alt="Heart Icon" class="w-[14px] h-auto md:h-[16px] md:w-auto" />
                    <img v-if="ai?.is_favorited_by_user" src="/assets/img/heart-active.png" alt="Heart Icon" class="w-[14px] h-auto md:h-[16px] md:w-auto" />
                  </button>
                  <button>
                    <img src="/assets/svgs/zoom-icon.svg" alt="Zoom Icon" class="w-[14px] h-auto md:h-[16px] md:w-auto" />
                  </button>
                  <button>
                    <img src="/assets/svgs/share-icon.svg" alt="Share Icon" class="w-[14px] h-auto md:h-[16px] md:w-auto" />
                  </button>
                </div>
<!--                <div class="md:hidden w-full bg-[#DBDBDB] text-[8px] md:text-[14px] font-bold px-3 md:px-4 py-1 rounded-full">Miễn phí</div>-->
                  </div>
                </div>
<!--                <div class="hidden md:block w-full bg-[#DBDBDB] text-[8px] md:text-[14px] font-bold px-4 py-1 rounded-full">Miễn phí</div>-->
              </div>
            </div>
          </div>
        </div>

        <div v-if="type === 'text' && !loadingSearch" class="flex justify-center mt-4 w-full">
          <Pagination
            :links="displayedAssistantsPagination"
            @paginate="fetchOperationFocusedAI" />
        </div>
        <div v-if="type === 'video' && !loadingSearch" class="flex justify-center mt-4 w-full">
          <Pagination
            :links="displayedAssistantsPagination"
            @paginate="fetchOccupationFocusedAI" />
        </div>
        <div v-if="type === 'image' && !loadingSearch" class="flex justify-center mt-4 w-full">
          <Pagination
            :links="displayedAssistantsImagePagination"
            @paginate="fetchImageAI"
            />
        </div>

        <!-- <div v-if="showMoreButton && (type !== 'text' && type !== 'video') && !loadingSearch" class="flex justify-center mt-4 w-full">
          <button @click="showMoreItems"
            class="showMore px-12 md:px-24 py-2 bg-white rounded-[17px] md:rounded-full hover:opacity-80 flex items-center justify-center text-[12px]"
            :disabled="loadingMore">
            <svg v-if="loadingMore" class="animate-spin h-5 w-5 mr-3 text-gray-800" xmlns="http://www.w3.org/2000/svg"
            fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            {{ loadingMore ? 'Đang tải thêm...' : 'Xem thêm' }}
          </button>
        </div> -->
      </div>

      <div class="xl:w-1/4">
          <Top10Ai :listAi="listAi"/>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, defineEmits } from 'vue';
import debounce from 'lodash/debounce';
import dayjs from 'dayjs';
import 'dayjs/locale/vi'; // Import locale tiếng Việt
import axios from 'axios';
import Top10Ai from "./../../../../Components/Top10Ai.vue";
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
  aiAssistants: Array,
  listAi: Array,
  aiAssistantLinks: Array
});

const listAi = ref(props.listAi);
const displayedAssistants = ref(props.aiAssistants);

const displayedAssistantsByOccupation = ref([]);
const displayedAssistantsPagination = ref([])
const displayedAssistantsImagePagination = ref(props.aiAssistantLinks)
const searchQuery = ref('');
const offset = ref(15);
const loadingMore = ref(false);
const loadingSearch = ref(false);
const activeTab = ref('new'); // Add this line to track the active tab
const emit = defineEmits(['updateListAi']);

const itemsPerPage = ref(3);
const currentPage = ref(1);

const type = ref('image')

const showMoreButton = computed(() => {
  return displayedAssistants?.value?.length >= 18;
});

const searchAI = async (changeType) => {
  try {
    if (changeType)
      type.value = 'image'
    loadingSearch.value = true;
    const response = await axios.post('/search', { search: searchQuery.value, type: type.value });
    displayedAssistants.value = response.data;
    offset.value = response.data.from;
    if (changeType)
      activeTab.value = 'new'; // Add this line
  } catch (error) {
    console.error('Lỗi khi tìm kiếm AI:', error);
  } finally {
    loadingSearch.value = false;
  }
};

const debouncedSearch = debounce(searchAI, 500);

watch(searchQuery, () => {
  debouncedSearch();
});

const showMoreItems = async () => {
  try {
    loadingMore.value = true;
    const response = await axios.post('/load-more', { search: searchQuery.value, offset: offset.value, type: type.value });
    const newAssistants = response.data;

    displayedAssistants.value = [...displayedAssistants.value, ...newAssistants];

    offset.value += newAssistants.length;
  } catch (error) {
    console.error('Lỗi khi tải thêm dữ liệu:', error);
  } finally {
    loadingMore.value = false;
  }
};

const updateFavorites = async (ai) => {
    const response = await axios.post(route('favorites.update', { aiAssistantId: ai.id }));
    response.data.message === 'Added to favorites' ? ai.is_favorited_by_user = true : ai.is_favorited_by_user = false;

    const index = props.listAi.findIndex(item => item.id === ai.id);
    if (index !== -1) {
        props.listAi[index].is_favorited_by_user = ai.is_favorited_by_user;
    }

    emit('updateListAi', listAi.value);
}

const fetchOperationFocusedAI = async (page = 1) => {
  try {
    type.value = 'text'
    loadingSearch.value = true;
    const response = await axios.post('/search', { search: searchQuery.value, type: type.value, page  });
    displayedAssistants.value = response.data.data;
    displayedAssistantsPagination.value = response.data.links
    activeTab.value = 'operation';
  } catch (error) {
    console.error('Error fetching user focused AI:', error);
  } finally {
    loadingSearch.value = false;
  }
};

const fetchOccupationFocusedAI = async (page = 1) => {
  try {
    type.value = 'video'
    loadingSearch.value = true;
    const response = await axios.post('/search', { search: searchQuery.value, type: type.value, page });
    // group by occupation
    const groupedByOccupation = response.data.data.reduce((acc, item) => {
      const occupationId = item.occupation.id;
      if (!acc[occupationId]) {
        acc[occupationId] = {
          id: occupationId,
          name: item.occupation.name,
          image_url: item.occupation.image_url,
          items: []
        };
      }
      acc[occupationId].items.push(item);
      return acc;
    }, {});

    displayedAssistantsByOccupation.value = Object.values(groupedByOccupation);
    displayedAssistants.value = response.data.data;
    currentPage.value = response.data.current_page;
    itemsPerPage.value = response.data.per_page;
    displayedAssistantsPagination.value = response.data.links;
    activeTab.value = 'occupation';
  } catch (error) {
    console.error('Error fetching user focused AI:', error);
  } finally {
    loadingSearch.value = false;
  }
};

const fetchImageAI = async (page = 1) => {
  try {
    type.value = 'image'
    loadingSearch.value = true;
    const response = await axios.post('/search', { search: searchQuery.value, type: type.value, page });
    displayedAssistants.value = response.data.data;
    displayedAssistantsImagePagination.value = response.data.links
    offset.value = response.data.from;
    activeTab.value = 'new';
  } catch (error) {
    console.error('Error fetching user focused AI:', error);
  } finally {
    loadingSearch.value = false;
  }
};

const formatDate = (date) => {
  dayjs.locale('vi');
  return dayjs(date).format('D [tháng] M [năm] YYYY');
}

const navigateTo = async (id, type) => {
  console.log(type)
  try {
    if(type === 'text') {
      window.location.href = route('ai-text.index', {id: id});
    } else if(type === 'image') {
      window.location.href = route('ai-image.index', {id: id});
    } else {
      // chuyển hướng đến màn ai video
    }

  } catch (error) {
    console.error('Error during navigation:', error);
  }
};
</script>

<style scoped>
.ai-items w-full {
  box-shadow: 0px 3px 3px rgba(0, 0, 0, 0.15);
}

.showMore {
  box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
}
</style>
