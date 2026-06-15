<template>
    <div>
        <button @click="generateImage" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2" :disabled="isLoading">
            {{ isLoading ? 'Đang tạo video...' : 'Tạo video bằng AI' }}
        </button>
        <div v-if="isLoading" class="mt-4 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
            <p class="mt-2">Đang tạo ảnh, vui lòng đợi...</p>
        </div>
        <div v-if="imageUrl" class="mt-4 w-4/5">
            <img :src="imageUrl" alt="Hình ảnh sẽ hiển thị ở đây khi tạo ảnh bằng AI" class="mx-auto max-w-full" />
            <button @click="useGeneratedImage" class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Sử dụng ảnh này
            </button>
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

const generateImage = async () => {
    isLoading.value = true;
    errorMessage.value = ''; // Clear previous error message
    try {
        const response = await axios.get(route("slide-ai.generateVideo"));
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
