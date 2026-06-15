<template>
    <div>
        <p v-if="imagesHistory.length < 1" class="text-black text-center">Hiện tại chưa có ảnh trong kho dữ liệu</p>
        <div v-else class="overflow-y-auto max-h-[60vh] lg:max-h-[70vh] grid grid-cols-3 lg:grid-cols-3 2xl:grid-cols-4 gap-2 lg:gap-5 w-full">
            <button @click="selectImage(collection)" v-for="collection in imagesHistory" :key="collection.id" class="relative w-full bg-[#D4D4D4] rounded-2xl h-[22vh]">
                <img v-lazy="collection.s3_url" alt="img" class="w-full object-contain rounded-2xl h-full" />
                <input type="radio" :value="collection" v-model="imageSelected" class="absolute top-2 checked:bg-primary-color lg:top-4 right-2 lg:right-4 cursor-pointer outline-none size-6" />
            </button>
        </div>
        <div v-if="!loadingMore && currentPage != lastPage && imagesHistory.length > 0" class="flex justify-center mt-4">
            <button @click="loadMoreHistories" class="bg-primary-color text-white px-4 py-2 rounded-full">Xem thêm</button>
        </div>
        <div v-if="loadingMore" class="flex justify-center mt-4">
            <span>Đang tải...</span>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch, watchEffect } from 'vue';
import axios from 'axios';
const props =  defineProps({
    imageTypeActive: Object,
});
const emit = defineEmits(['selectImage']);
const imagesHistory = ref([]);
const imageSelected = ref(null);
const currentPage = ref(1);
const loadingMore = ref(false);
const lastPage = ref();
const selectImage = (image) => {
    imageSelected.value = image;
    emit('selectImage', image);
};
const loadMoreHistories = async () => {
    if (currentPage.value < lastPage.value) {
        loadingMore.value = true;
        await getMyAIImageHistory(currentPage.value + 1);
        loadingMore.value = false;
    }
};
const getMyAIImageHistory = async (page) => {
    try {
        const response = await axios.get(route('ai-image.list-media-by-model', {
            model: props.imageTypeActive?.key,
            page: page,
        }));
        imagesHistory.value.push(...response.data.data.data);
        currentPage.value = response.data.data.current_page;
        lastPage.value = response.data.data.last_page;
    } catch (error) {
        console.error('Error fetching templates:', error);
    }
};
watch(() => props.imageTypeActive?.key, () => {
    imagesHistory.value = [];
    currentPage.value = 1;
    lastPage.value = null;
    getMyAIImageHistory(currentPage.value);
});
onMounted(() => {
    getMyAIImageHistory(currentPage.value);
});
</script>
