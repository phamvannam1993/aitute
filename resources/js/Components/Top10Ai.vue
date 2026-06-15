<template>
    <div class="mt-2 mb-8 md:mt-0 px-[14px] md:px-0">
              <div class="flex items-center md:leading-[52px] mb-2">
                <img src="/assets/svgs/start.svg" alt="AI Icon" class="w-[20px] h-[20px] md:w-[24px] md:h-auto mr-2" />
                <h2 class="text-[15px] md:text-[25px] font-semibold">Nổi bật</h2>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-1 gap-4 px-[20px] md:px-0">
            <div v-for="(ai, index) in displayedFeaturedAIItems" :key="index"
                      class="ai-items bg-white p-[10px] md:p-2 rounded-[10px] md:rounded-2xl relative cursor-pointer" @click="redirectBasedOnType(ai, ai.type)">
                      <div class="flex items-center justify-between space-x-2 md:mb-2">
                          <div class="w-[60px] h-[60px] md:w-2/8">
                              <img :src="ai?.thumbnail_url || '/assets/svgs/sample-ai-icon-1.svg'" alt="AI Image" class="size-[60px] md:size-[49px] rounded-[13px] cursor-pointer" @click="redirectBasedOnType(ai, ai.type)"/>
                          </div>
                          <div class="flex flex-1 flex-col space-y-[2px]">
                              <h3 class="text-[12px] font-bold line-clamp-1">{{ ai?.name || 'AI mới' }}</h3>
                              <p class="text-gray-600 text-[12px] line-clamp-1">{{ ai?.description || "Đây là AI được yêu thích" }}</p>
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
<!--                              <div class="md:hidden w-full bg-[#DBDBDB] text-[8px] md:text-xs font-bold px-3 md:px-4 py-1 rounded-full">Miễn phí</div>-->
                          </div>
                      </div>
<!--                      <div class="hidden md:block w-full bg-[#DBDBDB] text-[12px] font-bold px-4 py-1 rounded-full">Miễn phí</div>-->
                  </div>
          </div>
          <!-- <div v-if="showMoreFeaturedAIItemsButton" class="flex justify-center mt-4 w-full">
                  <button @click="showMoreFeaturedAIItems"
                      class="showMore px-12 py-2 bg-white rounded-full hover:opacity-80 flex items-center justify-center text-[12px]"
                      :disabled="loadingMoreFeaturedAIItems">
                      <svg v-if="loadingMoreFeaturedAIItems" class="animate-spin h-5 w-5 mr-3 text-gray-800"     xmlns="http://www.w3.org/2000/svg"
                      fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                      </svg>
                      {{ loadingMoreFeaturedAIItems ? 'Đang tải thêm...' : 'Xem thêm' }}
                  </button>
              </div> -->
          </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const loadingMoreFeaturedAIItems = ref(false);

const props = defineProps({
    listAi: Array
});

const displayedFeaturedAIItems = ref(props.listAi);

const showMoreFeaturedAIItemsButton = computed(() => {
  return displayedFeaturedAIItems?.value?.length >= 6;
});

const showMoreFeaturedAIItems = () => {
  loadingMoreFeaturedAIItems.value = true;
  const newItems = props.listAi.filter(ai => ai.featured).slice(displayedFeaturedAIItems.value.length, displayedFeaturedAIItems.value.length + 6);
  displayedFeaturedAIItems.value = [...displayedFeaturedAIItems.value, ...newItems];
  loadingMoreFeaturedAIItems.value = false;
}

const updateFavorites = async (ai) => {
  const response = await axios.post(route('favorites.update', { aiAssistantId: ai.id }));
  response.data.message === 'Added to favorites' ? ai.is_favorited_by_user = true : ai.is_favorited_by_user = false;
}

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
</script>

<style scoped>
.ai-items {
  box-shadow: 0px 3px 3px rgba(0, 0, 0, 0.15);
}

.showMore {
  box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
}
</style>
