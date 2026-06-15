<template>
    <div>
        <button @click="generateImage" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2"
            :disabled="isLoading">
            {{ isLoading ? 'Đang tạo ảnh...' : 'Tạo hình ảnh bằng AI' }}
        </button>
        <button @click="removeBackground"
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mr-2">
            Xóa nền
        </button>
        <button v-if="imageUrl" @click="useGeneratedImage"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Sử dụng ảnh này
        </button>
        <div v-if="isLoading" class="mt-4 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
            <p class="mt-2">Đang tạo ảnh, vui lòng đợi...</p>
        </div>
        <div v-if="imageUrl" class="mt-4 ">
            <img :src="imageUrl" alt="Hình ảnh sẽ hiển thị ở đây khi tạo ảnh bằng AI" class="object-fit " />
        </div>
        <div v-if="errorMessage" class="mt-4 text-red-500">
            {{ errorMessage }}
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const emit = defineEmits(['image-generated']);

const imageUrl = ref('');
const isLoading = ref(false);
const errorMessage = ref('');

const props = defineProps({
    title: String,
});

const generateImage = async () => {
    isLoading.value = true;
    errorMessage.value = ''; // Clear any previous error message
    try {
        const response = await axios.post(route("text-to-video.generateImage"), {
            description: props.title,
            model: 'Flux Schnell',
            width: '1024',
            height: '1024',
            aspect_ratio: '16:9'
        });
        imageUrl.value = response.data.url;
        isLoading.value = false;
    } catch (error) {
        handleErrorResponse(error);
        isLoading.value = false;
    }
};

const removeBackground = async () => {
    isLoading.value = true;
    errorMessage.value = ''; // Clear any previous error message
    if (!imageUrl.value) {
        console.error('No image to remove background from');
        errorMessage.value = "Không có ảnh để xoá nền."
        isLoading.value = false;
        return;
    }
    try {
        const response = await axios.post(route("text-to-video.removeBackground"), {
            imageUrl: imageUrl.value
        });

        imageUrl.value = response.data;
        isLoading.value = false;
    } catch (error) {
        handleErrorResponse(error);
        isLoading.value = false;
    }
};

const handleErrorResponse = (error) => {
    if (error.response && error.response.status === 403) {
        errorMessage.value = 'Bạn đã hết credit hãy nâng cấp tài khoản trả phí để dùng chức năng này.';
    } else {
        errorMessage.value = 'Đã xảy ra lỗi khi thực hiện yêu cầu của bạn.';
    }
};

const useGeneratedImage = () => {
    emit('image-generated', imageUrl.value);
};
</script>

<style scoped>
.max-w-full {
    max-width: 100%;
}
</style>
