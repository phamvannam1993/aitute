<template>
  <div class="bg-cover bg-center lg:w-[95%] w-full">
    <div
      class="flex items-center flex-col lg:flex-row bg-transparent lg:bg-[#00000005] rounded-[25px] md:mx-0 mb-4 md:p-[8px] md:gap-4 shadow-[inset_0px_2px_5px_2px_#00000015]">
      <div
        class="flex items-center w-full justify-evenly lg:justify-start lg:w-9/12 p-[4px] md:p-0 md:space-x-2 flex-row rounded-[10px] ">
        <a href="javascript:void(0)" @click="fetchOperationFocusedAI" :class="[
          'text-[10px] md:text-[14px] font-bold py-3 px-2 md:px-6 gap-1 rounded-[10px] md:rounded-[15px] uppercase',
          activeTab === 'operation' ? 'bg-[#2C75E3] text-white' : 'text-black hover:bg-[#2C75E3] hover:text-white'
        ]">
          AI của bạn
        </a>
        <a href="javascript:void(0)" @click="fetchImageAI" :class="[
          'text-[10px] md:text-[14px] font-bold py-3 px-2 md:px-6 rounded-[10px] md:rounded-[15px] uppercase',
          activeTab === 'new' ? 'bg-[#2C75E3] text-white' : 'text-black hover:bg-[#2C75E3] hover:text-white'
        ]">
          AI mới
        </a>
        <a href="javascript:void(0)" @click="fetchMarketingAI" :class="[
          'text-[10px] md:text-[14px] font-bold py-3 px-2 md:px-6 rounded-[10px] md:rounded-[15px] uppercase',
          activeTab === 'marketing' ? 'bg-[#2C75E3] text-white' : 'text-black hover:bg-[#2C75E3] hover:text-white'
        ]">
          AI truyền thông
        </a>
      </div>

      <div class="lg:w-2/5 w-full relative text-black">
        <input type="text" placeholder="Tìm kiếm AI..." v-model="searchQuery" @keyup.enter="searchAI"
          class="w-full text-[10px] md:text-[14px] px-4 py-0.5 md:py-2 border border-[#5FB2FF] rounded-[20px] md:rounded-[15px] focus:outline-none focus:ring-2 focus:ring-[#5FB2FF]" />
        <button class="absolute right-3 top-2.5 md:top-2 text-gray-500" @click="searchAI">
          <img src="/assets/svgs/search-icon.svg" alt="search icon" class="w-[10px] h-auto md:h-6 md:w-auto" />
        </button>
      </div>
    </div>

    <div v-if="activeTab !== 'marketing'">
      <div class="flex flex-col xl:flex-row my-2 xl:space-x-6 text-black w-full">
        <div class="md:px-0 w-full">
          <div v-if="loadingSearch" class="flex items-center justify-center my-4 h-28">
            <svg class="animate-spin h-5 w-5 mr-3 text-[#5FB2FF]" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
          </div>

          <!-- AI Items -->
          <div v-if="!loadingSearch"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-3 md:gap-4 md:px-0 w-full">
            <div v-for="(ai, index) in displayedAssistants" :key="index"
              class="ai-items w-full bg-white p-2 rounded-[10px] md:rounded-3xl relative border-2 border-[#D4D4D4]"
              @click="selectAssistant(ai)">
              <div class="flex items-start justify-between space-x-2 md:space-x-1">
                <div class="w-[60px] md:w-2/8 cursor-pointer">
                  <img :src="ai?.thumbnail_url || '/assets/img/aiwow/default.png'" alt="AI Image"
                    class="size-[60px] md:size-[49px] rounded-[13px]" />
                </div>
                <div class="flex flex-1 flex-col space-y-[2px] cursor-pointer">
                  <h3 class="text-[12px] font-bold line-clamp-1">{{ ai?.name }} </h3>
                  <p class="text-gray-600 text-[12px] line-clamp-1">{{ ai?.description || "Đây là AI mới" }}</p>
                  <div class="flex items-center space-x-1 md:space-x-2">
                    <div class="rounded-full p-1 md:p-2 bg-[#2C75E3]"></div>
                    <p class="text-[10px] md:text-[12px] line-clamp-1">{{ ai?.occupation?.name || 'Giáo Dục' }}</p>
                  </div>
                </div>

                <div class="md:w-1/8 items-center justify-between space-y-5">
                  <div class="flex flex-col gap-2 justify-between md:flex-col md:space-y-2 w-full">
                    <button @click.stop="updateFavorites(ai)">
                      <img v-if="!ai?.is_favorited_by_user" src="/assets/svgs/heart-icon.svg" alt="Heart Icon"
                        class="size-[16px] md:size-4" />
                      <img v-if="ai?.is_favorited_by_user" src="/assets/img/heart-active.png" alt="Heart Icon"
                        class="size-[16px] md:size-4" />
                    </button>
                    <button>
                      <img src="/assets/svgs/zoom-icon.svg" alt="Zoom Icon" class="size-[16px] md:size-4" />
                    </button>
                    <button>
                      <img src="/assets/svgs/share-icon.svg" alt="Share Icon" class="size-[16px] md:size-4" />
                    </button>
                  </div>
                  <!--                <div class="md:hidden w-full bg-[#DBDBDB] text-[8px] md:text-xs font-bold px-3 md:px-4 py-1 rounded-full">Miễn phí</div>-->
                </div>
              </div>
              <!--          <div class="hidden md:block w-full bg-[#DBDBDB] text-[12px] font-bold px-4 py-1 rounded-full">Miễn phí</div>-->
            </div>

          </div>

          <div v-if="!loadingSearch && displayedAssistants.length > 0" class="flex justify-center mt-4 w-full">
            <div v-if="activeTab == 'occupation'">
              <Pagination :totalPage="totalPage" :initialPage="currentPage" @updatePage="fetchOccupationFocusedAI" />
            </div>
            <div v-if="activeTab == 'new'">
              <Pagination :totalPage="totalPage" :initialPage="currentPage" @updatePage="fetchImageAI" />
            </div>
            <div v-if="activeTab == 'operation'">
              <Pagination :totalPage="totalPage" :initialPage="currentPage" @updatePage="fetchOperationFocusedAI" />
            </div>
          </div>
        </div>
      </div>
      <div v-if="displayedAssistants.length === 0 && !loadingSearch"
        class="flex flex-col justify-center items-center text-center ">
        <p class="text-gray-500 text-lg font-semibold">Không tìm thấy AI nào phù hợp.</p>
      </div>
    </div>
    <div v-else>
      <Project/>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, defineEmits, onMounted } from 'vue';

import debounce from 'lodash/debounce';
import dayjs from 'dayjs';
import 'dayjs/locale/vi';
import axios from 'axios';
import Pagination from '@/Components/Pagination.vue';
import Project from '../../MyAssistants/Project.vue';

const props = defineProps({
  aiAssistants: Object,
  listAi: Array,
  aiAssistantLinks: Object,
  activeTab: String,
  pageWhenSelected: Number
});

const totalPage = ref(0);

const aiAssistants = ref([...props.aiAssistants.data]);
const aiAssistantLinks = ref(props.aiAssistants.links);
const listAi = ref([...props.listAi]);
const displayedAssistants = ref(props.aiAssistants.data);
const displayedAssistantsByOccupation = ref([]);
const displayedAssistantsPagination = ref([]);
const displayedAssistantsImagePagination = ref(props.aiAssistantLinks);
const searchQuery = ref('');
const offset = ref(15);
const loadingSearch = ref(false);
const pageWhenSelected = ref(props.pageWhenSelected) || '';

const emit = defineEmits(['updateListAi', 'selectAssistant']);

const itemsPerPage = ref(4);
const currentPage = ref(1);

const type = ref('video');

const activeTab = ref('marketing');

const searchAI = async (changeType) => {
  try {
    loadingSearch.value = true;
    const response = await axios.post('/search', {
      search: searchQuery.value,
      activeTab: activeTab.value
    });
    displayedAssistants.value = response.data.data;
    offset.value = response.data.from;
    totalPage.value = response.data.last_page;
    currentPage.value = response.data.current_page;
    if (changeType)
      activeTab.value = 'occupation';
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

const updateFavorites = async (ai) => {
  const response = await axios.post(route('favorites.update', { aiAssistantId: ai.id }));
  const isFavorited = response.data.message === 'Added to favorites';

  // Cập nhật trạng thái yêu thích
  const index = displayedAssistants.value.findIndex(item => item.id === ai.id);
  if (index !== -1) {
    if (!isFavorited) {
      // Nếu không còn yêu thích, xóa khỏi danh sách
      displayedAssistants.value.splice(index, 1);
    } else {
      // Nếu yêu thích, cập nhật trạng thái
      displayedAssistants.value[index].is_favorited_by_user = isFavorited;
    }
  }

  // Cập nhật trong props.listAi nếu cần
  const listIndex = props.listAi.findIndex(item => item.id === ai.id);
  if (listIndex !== -1) {
    props.listAi[listIndex].is_favorited_by_user = isFavorited;
  }

  emit('updateListAi', listAi.value); // Thông báo cập nhật danh sách cha
};

const selectAssistant = (ai) => {
  emit('selectAssistant', ai);
};

const fetchOperationFocusedAI = async (page = 1) => {
  try {
    type.value = 'text';
    loadingSearch.value = true;
    currentPage.value = page;
    const response = await axios.post('/search', {
      search: searchQuery.value,
      type: type.value, page,
      activeTab: 'operation',
      limitPage: pageWhenSelected.value
    });
    totalPage.value = response.data.last_page;
    currentPage.value = response.data.current_page;
    displayedAssistants.value = response.data.data;
    displayedAssistantsImagePagination.value = response.data.links;
    activeTab.value = 'operation';
  } catch (error) {
    console.error('Error fetching user focused AI:', error);
  } finally {
    loadingSearch.value = false;
  }
};

const fetchOccupationFocusedAI = async (page = 1) => {
  try {
    type.value = 'video';
    loadingSearch.value = true;
    currentPage.value = page;
    const response = await axios.post('/search', {
      search: searchQuery.value,
      type: type.value, page,
      activeTab: 'occupation',
      limitPage: pageWhenSelected.value
    });

    totalPage.value = response.data.last_page;

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
    displayedAssistantsImagePagination.value = response.data.links;
    activeTab.value = 'occupation';
  } catch (error) {
    console.error('Error fetching user focused AI:', error);
  } finally {
    loadingSearch.value = false;
  }
};

const fetchImageAI = async (page = 1) => {
  try {
    type.value = 'image';
    loadingSearch.value = true;
    currentPage.value = page;
    const response = await axios.post('/search', {
      search: searchQuery.value,
      type: type.value,
      page,
      activeTab: 'new',
      limitPage: pageWhenSelected.value
    });
    displayedAssistants.value = response.data.data;
    displayedAssistantsImagePagination.value = response.data.links
    totalPage.value = response.data.last_page;
    currentPage.value = response.data.current_page;
    offset.value = response.data.from;
    activeTab.value = 'new';
  } catch (error) {
    console.error('Error fetching user focused AI:', error);
  } finally {
    loadingSearch.value = false;
  }
};

const fetchMarketingAI = () => {
  activeTab.value = 'marketing';
}

const formatDate = (date) => {
  dayjs.locale('vi');
  return dayjs(date).format('D [tháng] M [năm] YYYY');
};
const handlePagination = (page) => {
  switch (activeTab.value) {
    case 'operation':
      fetchOperationFocusedAI(page);
      break;
    case 'occupation':
      fetchOccupationFocusedAI(page);
      break;
    default:
      fetchImageAI(page);
      break;
  }
};

onMounted(() => {
  // fetchImageAI();
  const getActiveTabFromURL = () => {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('activeTab') || 'marketing';
  };

  activeTab.value = getActiveTabFromURL();
});

</script>

<style>
.ai-items w-full {
  box-shadow: 0px 3px 3px rgba(0, 0, 0, 0.15);
}

.showMore {
  box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
}

.pagination {
  display: flex;
  justify-content: center;
  list-style-type: none;
}

.pagination li {
  width: 40px;
  height: 40px;
  margin: 0 5px;
  cursor: pointer;
  background-color: white;
  border: solid 1px #DEDEDE;
  display: flex;
  justify-content: center;
  align-items: center;
}

.pagination li.active {
    font-weight: bold;
    color: white;
    background-color: #FFA411;
}
</style>
