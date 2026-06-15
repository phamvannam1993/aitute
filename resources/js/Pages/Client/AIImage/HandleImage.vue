<template>
    <div class="w-full flex flex-col mb-6">
        <div class="flex flex-col justify-between">
            <Step :step="step" :stepName="stepName" />
            <!-- Overlay Buttons Container -->
            <div class="flex items-center gap-2 my-2 max-w-[340px] mx-auto w-full">
                <button type="button" @click="triggerFileInput"
                    class="flex-1 flex items-center w-fit border-[2px] border-ai3goc-primary justify-center gap-2 p-1 bg-white hover:bg-black/10 text-ai3goc-primary rounded-lg backdrop-blur-sm transition-colors">
                    <img src="/assets/img/ai3goc/icon/upload.svg" class="size-6 md:size-5 xl:size-6" />
                    <span class="text-[12px] xl:text-[16px]">Tải ảnh</span>
                </button>

                <button type="button" @click="initializeCamera"
                    class="flex-1 flex items-center w-fit border-[2px] border-ai3goc-primary justify-center gap-2 p-1 bg-white hover:bg-black/10 text-ai3goc-primary rounded-lg backdrop-blur-sm transition-colors">
                    <img src="/assets/img/ai3goc/icon/camera.svg" class="size-6 md:size-5 xl:size-6" />
                    <span class="text-[12px]  xl:text-[16px]">Camera</span>
                </button>

                <!-- <button type="button" @click="clearImage"
                    class="flex items-center w-1/3 justify-center gap-2 px-3 py-2 bg-black/50 hover:bg-black/70 text-white rounded-lg backdrop-blur-sm transition-colors">
                    <img src="/assets/img/trash.png" class="size-6 md:size-5 xl:size-6" />
                    <span class="text-[12px] md:hidden xl:block xl:text-[16px]">Xóa</span>
                </button> -->
            </div>
        </div>
        <h3 class="text-black font-bold text-[18px]">{{ title }}</h3>

        <div v-if="error" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start">
                <div class="ml-3">
                    <div class="text-sm text-red-700">
                        <p>{{ error }}</p>
                    </div>
                </div>
            </div>
        </div>

        <input type="file" ref="fileInput" @change="handleFileUpload" accept="image/*" class="hidden" />

        <!-- Camera Container -->
        <div v-if="showCamera" class="relative w-full h-[30rem] bg-black rounded-lg overflow-hidden">
            <video ref="videoRef" autoplay playsinline class="w-full h-full object-contain"
                style="transform: scaleX(-1);" />
            <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-4">
                <button @click="capturePhoto"
                    class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                    Chụp ảnh
                </button>
                <button @click="stopCamera"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                    Hủy
                </button>
            </div>
        </div>

        <!-- Image Preview Container with Overlay Buttons -->
        <div v-else class="w-full rounded-lg flex flex-col mt-2">
            <!-- Overlay Buttons Container -->
            <!-- <div class="flex gap-2 w-full items-center justify-between my-2">
                <button type="button" @click="triggerFileInput"
                    class="flex items-center w-1/3 justify-center gap-2 px-3 py-2 bg-black/50 hover:bg-black/70 text-white rounded-lg backdrop-blur-sm transition-colors">
                    <img src="/assets/img/aicon/icon/features/upload.png" class="size-6 md:size-5 xl:size-6" />
                    <span class="text-[12px] md:hidden xl:block xl:text-[16px]">Tải lên</span>
                </button>

                <button type="button" @click="initializeCamera"
                    class="flex items-center w-1/3 justify-center gap-2 px-3 py-2 bg-black/50 hover:bg-black/70 text-white rounded-lg backdrop-blur-sm transition-colors">
                    <img src="/assets/img/aicon/icon/features/camera.png" class="size-6 md:size-5 xl:size-6" />
                    <span class="text-[12px] md:hidden xl:block xl:text-[16px]">Camera</span>
                </button>
            </div> -->
            <img v-if="showImg" :src="showImg" alt="Preview" class="w-full max-h-[600px] object-contain rounded-xl" />
            <div v-else  class="flex items-center justify-center md:aspect-square md:h-[40vh] rounded-lg">
                <img :src="modelValue" alt="Preview" class="h-full w-auto" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import Step from '@/Components/Step/Index.vue';

const props = defineProps({
    title: {
        type: String,
    },
    modelValue: {
        type: String,
        required: true
    },
    defaultValue: {
        type: String,
        required: true
    },
    step: {
        type: Number,
        required: true
    },
    stepName: {
        type: String,
        required: true
    }
});

const emit = defineEmits(['update:modelValue', 'fileChange']);

const error = ref('');
const showCamera = ref(false);
const fileInput = ref(null);
const videoRef = ref(null);
const showImg = ref(null);
let mediaStream = null;

const triggerFileInput = () => {
    fileInput.value?.click();
};
let allowedExtension = ['image/jpeg', 'image/jpg', 'image/png'];
const handleFileUpload = (event) => {
    let type =  event.target.files[0].type;
    if(allowedExtension.indexOf(type) < 0){
        alert('Vui lòng chọn lại ảnh có định dạng .png, .jpg, .jpeg')
        return false;
    }
    const file = event.target.files[0];
    if (file) {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                showImg.value = e.target.result;
                // emit('update:modelValue', e.target.result);
                emit('fileChange', file);
                error.value = '';
            };
            reader.readAsDataURL(file);
        } else {
            error.value = 'Vui lòng chọn tệp hình ảnh hợp lệ';
        }
    }
};

const initializeCamera = async () => {
    try {
        showCamera.value = true;
        await nextTick();
        await startCamera();
    } catch (err) {
        console.error('Failed to initialize camera:', err);
        error.value = 'Không thể khởi tạo camera';
        showCamera.value = false;
    }
};

const startCamera = async () => {
    try {
        if (!navigator.mediaDevices?.getUserMedia) {
            throw new Error('Browser không hỗ trợ truy cập camera');
        }

        mediaStream = await navigator.mediaDevices.getUserMedia({
            video: {
                width: { ideal: 1280 },
                height: { ideal: 720 },
                facingMode: 'user'
            },
            audio: false
        });

        if (!videoRef.value) {
            throw new Error('Video element không tồn tại');
        }

        videoRef.value.srcObject = mediaStream;
        await videoRef.value.play();
        error.value = '';
    } catch (err) {
        console.error('Camera error:', err);
        error.value = `Không thể truy cập camera: ${err.message}`;
        await stopCamera();
        throw err;
    }
};

const stopCamera = async () => {
    if (mediaStream) {
        mediaStream.getTracks().forEach(track => track.stop());
        mediaStream = null;
    }

    if (videoRef.value) {
        videoRef.value.srcObject = null;
    }

    showCamera.value = false;
    error.value = '';
};

const capturePhoto = () => {
    if (!videoRef.value) return;

    const canvas = document.createElement('canvas');
    const video = videoRef.value;
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    const ctx = canvas.getContext('2d');
    // Flip the image horizontally to match the preview
    ctx.scale(-1, 1);
    ctx.translate(-canvas.width, 0);
    ctx.drawImage(video, 0, 0);

    const imageUrl = canvas.toDataURL('image/jpeg', 0.9);
    emit('update:modelValue', imageUrl);
    showImg.value = imageUrl
    // Convert base64 to File object
    fetch(imageUrl)
        .then(res => res.blob())
        .then(blob => {
            const file = new File([blob], "camera-capture.jpg", { type: "image/jpeg" });
            emit('fileChange', file);
        });

    stopCamera();
};

const clearImage = () => {
    showImg.value = null;
    // emit('update:modelValue', '/assets/img/talk.png');
    emit('fileChange', null);
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

onMounted(() => {
    window.addEventListener('beforeunload', stopCamera);
});

onUnmounted(() => {
    stopCamera();
    window.removeEventListener('beforeunload', stopCamera);
});
</script>