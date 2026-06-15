<template>
    <div class="bg-white rounded-2xl absolute w-full h-full inset-0">
        <img src="/assets/img/close.png" @click="closeMediaLibrary" alt="closebtn"
            class="absolute -top-10 -right-10 z-20 cursor-pointer" />
        <div class="w-full h-full p-6">
            <h2 class="text-black font-semibold">
                Danh sách file đã tạo từ AI của bạn:
            </h2>
            <div class="flex mb-4">
                <div class="cursor-pointer px-4 py-2 border-b-2" :class="{
                    'border-[#0000B4] text-[#0000B4]': isImageTab,
                    'text-gray-500': !isImageTab,
                }" @click="toggleTab('image')">
                    Ảnh
                </div>
                <div class="cursor-pointer px-4 py-2 border-b-2" :class="{
                    'border-[#0000B4] text-[#0000B4]': !isImageTab,
                    'text-gray-500': isImageTab,
                }" @click="toggleTab('video')">
                    Video
                </div>
            </div>
            <div v-if="isImageTab"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 max-h-[80%] py-4 overflow-y-scroll gap-2">
                <div v-for="(item, index) in mediaLibrary.image.data" :key="index"
                    class="relative bg-gray-500 bg-opacity-30">
                    <img :src="item.s3_url" :alt="item.id" class="object-fill" />
                    <input type="checkbox" @change="selectMedia(item)" :checked="isChecked(item)"
                        class="absolute top-0 right-0 w-8 h-8" />
                </div>
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 max-h-[80%] py-4 overflow-y-scroll gap-2">
                <div v-for="(item, index) in mediaLibrary.video.data" :key="index"
                    class="relative bg-gray-500 bg-opacity-30">
                    <video :src="item.s3_url" :alt="item.id" class="object-fill" controls
                        @click="selectMedia(item)"></video>
                    <img src="/assets/svgs/check-icon.svg" alt="icon" v-if="isChecked(item)"
                        class="w-8 h-8 absolute top-1 right-1" />
                </div>
            </div>
            <div v-if="isLoading" class="py-2">
                Loading...
            </div>
            <div v-if="mediaLibrary['image'].nextPage && isImageTab" @click="loadMediaLibrary('image')"
                class="text-blue-600 text-center cursor-pointer">
                Hiển thị thêm
            </div>
            <div v-if="mediaLibrary['video'].nextPage && !isImageTab" @click="loadMediaLibrary('video')"
                class="text-blue-600 text-center cursor-pointer">
                Hiển thị thêm
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
const { mediaLibrary, filesSelected } = defineProps(
    {
        mediaLibrary: Object,
        published: Boolean,
        filesSelected: Array
    }
);

const emit = defineEmits();
const closeMediaLibrary = () => {
    emit('closeMediaLibrary');
};

const isImageTab = ref(true);
const isLoading = ref(false);

const isChecked = (item) => {
    let selected = filesSelected.filter((file, index) => item.id === file.id);
    return selected.length;
}

const selectMedia = (item) => {
    item.isCheck = !isChecked(item)
    emit('selectMedia', item);
};

onMounted(() => {
    if (!mediaLibrary['image'].data.length)
        loadMediaLibrary('image')
})

const toggleTab = (type) => {
    if (type === "image") {
        isImageTab.value = true;
    } else {
        isImageTab.value = false;
    }

    if (!mediaLibrary[type].data.length)
        loadMediaLibrary(type)
};

const loadMediaLibrary = async (type) => {
    try {
        isLoading.value = true;
        const response = await axios.get(
            route("ai-image.list-media", {
                page: mediaLibrary[type].nextPage,
                type: type,
            })
        );

        mediaLibrary[type].currentPage = response?.data?.data?.current_page

        if (mediaLibrary[type].currentPage < response?.data?.data?.last_page) {
            mediaLibrary[type].nextPage = mediaLibrary[type].currentPage + 1;
        } else {
            mediaLibrary[type].nextPage = null
        }

        let list = response.data.data.data || [];
        list = list.map((item) => ({
            ...item,
            isCheck: false,
        }));
        if (list.length > 0) {
            mediaLibrary[type].data = [...mediaLibrary[type].data, ...list];
        }
    } catch (error) {
        console.error("Error generating AI content:", error);
    } finally {
        isLoading.value = false;
    }
};
</script>