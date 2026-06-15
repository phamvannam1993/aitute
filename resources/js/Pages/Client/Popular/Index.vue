<template>
  <Head title="Nhiều người yêu thích - AI 3 GỐC - Cộng đồng AI tử tế" />
    <div class="flex flex-col min-h-screen text-black">
      <div class="mt-[48px] md:p-[34px] md:mt-2" >
        <header class="flex items-center px-2 justify-between">
          <div class="flex items-center">
            <a :href="route('home.index')">
              <img src="/assets/img/admin/icon_left_home.png" alt="Trang chủ" class="w-auto h-[19px]"/>
            </a>
            <span class="text-[14px] text-[#2C75E3] font-medium ml-2"> / Nhiều người yêu thích</span>
          </div>
          <Credit :credits="credits" />
        </header>
        <MainMenu/>
         <div class="flex flex-1 flex-col mt-[12px] md:mt-[8px]">
           <div class="flex flex-col xl:flex-row my-2 xl:space-x-6 text-black">
               <div class="px-[14px] md:px-0 xl:w-3/4 mb-4">
                    <h1 class="font-bold text-[25px] text-black leading-[52px]">Nhiều người yêu thích</h1>
                    <div class="flex items-center space-x-2 md:space-x-4 px-[20px] md:px-0 mb-4">
                        <div class="w-full relative text-black">
                            <input type="text" placeholder="Tìm kiếm trợ lí..." v-model="searchQuery" @keyup.enter="searchAI"
                            class="w-full text-[10px] md:text-[14px] px-3 md:px-4 py-[2px] md:py-2 border border-[#5FB2FF] rounded-[20px] md:rounded-[15px] focus:outline-none focus:ring-2 focus:ring-[#5FB2FF]" />
                            <button class="absolute right-3 top-2 text-gray-500" @click="searchAI">
                                <img src="/assets/svgs/search-icon.svg" alt="search icon" class="w-[10px] h-[15px] md:size-6" />
                            </button>
                        </div>
                        <div>
                            <button class="bg-white rounded-[22px] text-black text-[10px] md:text-[14px] py-2 px-4 flex items-center justify-between md:w-[132px] w-[88px]">
                                <span>Mới nhất</span>
                                <img src="/assets/svgs/arrow-down-icon.svg" alt="Arrow Down Icon" class="size-[6px]" />
                            </button>
                        </div>
                    </div>

                    <div v-if="loadingSearch" class="flex items-center justify-center h-32 mt-8">
                      <svg class="animate-spin h-5 w-5 mr-3 text-[#5FB2FF]" xmlns="http://www.w3.org/2000/svg"
                      fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                      </svg>
                    </div>

                    <div v-if="!loadingSearch && !displayedAssistants?.length" class="text-center text-gray-500 my-4">
                        <p>Không có kết quả nào phù hợp</p>
                    </div>

                    <div v-if="!loadingSearch && displayedAssistants?.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-3 md:gap-4 px-[20px] md:px-0">
                        <div v-for="(ai, index) in displayedAssistants" :key="index"
                            class="ai-items bg-white p-2 rounded-2xl relative cursor-pointer" @click="navigateTo(ai.id, ai.type)">
                            <div class="flex items-center justify-between space-x-1 md:mb-2">
                                <div class="w-[60px] md:w-2/8">
                                    <img :src="ai?.thumbnail_url || '/assets/svgs/sample-ai-icon-1.svg'" alt="AI Image" class="size-[60px] md:size-[49px] rounded-[13px]" />
                                </div>
                                <div class="flex flex-1 flex-col space-y-[2px]">
                                    <h3 class="text-[12px] font-bold line-clamp-1">{{ ai?.name || 'AI mới' }}</h3>
                                    <p class="text-gray-600 text-[12px] line-clamp-1">{{ ai?.description || "Đây là AI được Nhiều người yêu thích" }}</p>
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
<!--                                    <div class="md:hidden w-full bg-[#DBDBDB] text-[8px] md:text-xs font-bold px-3 md:px-4 py-1 rounded-full">Miễn phí</div>-->
                                </div>
                            </div>
<!--                            <div class="hidden md:block w-full bg-[#DBDBDB] text-[12px] font-bold px-4 py-1 rounded-full">Miễn phí</div>-->
                        </div>
                    </div>
                    <div v-if="showMoreButton" class="flex justify-center mt-4 w-full">
                        <button @click="showMoreItems"
                            class="showMore px-24 py-2 bg-white rounded-full hover:opacity-80 flex items-center justify-center text-[12px]"
                            :disabled="loadingMore">
                            <svg v-if="loadingMore" class="animate-spin h-5 w-5 mr-3 text-gray-800"     xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            {{ loadingMore ? 'Đang tải thêm...' : 'Xem thêm' }}
                        </button>
                    </div>
                </div>
              <div class="xl:w-1/4">
                  <Top10Ai :listAi="listAi"/>
              </div>
           </div>
         </div>
      </div>
      <Footer class="mt-auto"/>
    </div>
</template>

  <script setup>
  import Layout from "@/Layouts/Client/ClientLayout.vue";
  import Footer from "../Home/Components/Footer.vue";
  import debounce from 'lodash/debounce';
  import { ref, computed, watch } from 'vue';
  import { Head } from '@inertiajs/vue3'
import Credit from '@/Components/Credit.vue';
import MainMenu from "@/Components/MainMenu.vue";
import Top10Ai from "./../../../Components/Top10Ai.vue";

  defineOptions({ layout: Layout });

  const props = defineProps({
    popularAssistants: Array,
    listAi: Array
  });

  const displayedAssistants = ref(props.popularAssistants);

  const searchQuery = ref('');
  const offset = ref(18);
  const loadingMore = ref(false);
  const loadingSearch = ref(false);



  const showMoreButton = computed(() => {
  return displayedAssistants?.value?.length >= 18 && !loadingSearch.value;
  });

  const searchAI = async () => {
  try {
    loadingSearch.value = true;
    const response = await axios.post(route('popular.search'), { search: searchQuery.value });
    displayedAssistants.value = response.data;
    offset.value = response.data.from;

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
    const response = await axios.post(route('popular.load-more'), { search: searchQuery.value, offset: offset.value });

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
  }

  const navigateTo = async (id, type) => {
  console.log(type)
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

  </script>

  <style scoped>
  .ai-items {
  box-shadow: 0px 3px 3px rgba(0, 0, 0, 0.15);
  }

  .showMore {
  box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
  }
  </style>
