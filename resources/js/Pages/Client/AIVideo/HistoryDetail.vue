<template>

    <Head title="Lịch sử tạo video - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData">
        <div class="flex justify-between items-center my-5 ">
            <h1 class="text-black text-2xl font-bold my-3 lg:pl-[30px] h1-fz-18">
                {{ recordData.model === 'img-video' || recordData.model === 'merge-video-image' ? 'AITUTE Hình ảnh thành video' : 'AITUTE Phim'}}
            </h1>
            <a href="javascript:void(0)"
                class="bt-yellow px-4 md:px-11 py-2 bg-orion-sec text-white font-semibold rounded-[5px] flex justify-center items-center gap-1 mx-5"
                @click="videoHistory()">
                Lịch sử</a>
        </div>
        <div class="flex w-full flex-col lg:flex-row justify-around gap-10 lg:gap-0">
            <!-- Video Section -->
            <div class="relative w-full lg:w-[60%] overflow-hidden">
                <div class="aspect-video w-full">
                    <div class="w-full flex flex-row md:flex-col mt-2 md:w-full xl:w-full lg:pb-[4px] items-center">
                        <video :src="recordData.s3_url" controls preload="auto"
                            class="w-full h-full rounded-[20px] bg-black"
                            :class="recordData.ratio == '1080:1920' ? 'video-ratio' : ''"></video>
                    </div>
                    <div class="absolute top-4 right-4 cursor-pointer bg-black/50 p-2 rounded-full hover:bg-black/70 transition-all"
                        :class="{ 'opacity-50 cursor-not-allowed': isTranscribing }">
                        <img src="/assets/svgs/live-transcribe-svgrepo-com.svg" alt="Transcription" class="w-6 h-6"
                            :class="{ 'animate-spin': isTranscribing }" @click="toggleTranscription" />
                    </div>
                    <div class="w-full flex flex-row md:flex-col mt-2 md:w-full xl:w-full lg:pb-[4px] items-center">
                        <div class="flex lg:justify-start md:justify-end gap-4 lg:gap-5 items-center flex-wrap">
                            <a @click="$refs.fileInput.click()" :class="isShowRatio ? 'mt-80' : ''"
                                href="javascript:void(0)"
                                class="text-orion-sec flex justify-start items-center gap-2 hover:text-blue-600 transition-colors duration-300 mx-auto flex-col">
                                <img v-if="!isCallUploadAudio" src="/assets/img/image-to-image.png " alt="AI Video"
                                    class="min-w-[20px] w-[28px] md:w-[32px] xl:w-[28px] h-auto" />
                                <div v-if="isCallUploadAudio" class="loaderVideo loaderAudio"></div>
                                <span style="color:#fff"
                                    class="lg:text-sm lg:block font-medium bg-orion-primary hover:scale-105 px-2 lg:px-4 py-1.5 text-xs rounded-lg">Nhạc
                                    nền</span>
                                <input type="file" id="audio-file" @change="handleAudioFileChange" accept="audio/*"
                                    class="hidden" ref="fileInput" />
                            </a>

                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col lg:flex-row justify-between gap-4">
                <div v-if="isCropping"
                    class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
                    <div class="bg-white p-4 rounded-md w-full md:w-[80%] lg:w-[40%]">
                        <VueCropper ref="cropper" :src="imageUrl" :aspect-ratio="aspectRatio" :zoomable="true"
                            class="max-w-full max-h-[60vh] mx-auto overflow-hidden" />
                        <div class="flex justify-between mt-4">
                            <button @click="cancelCropping"
                                class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600">Hủy</button>
                            <button @click="cropImage"
                                class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Cắt
                                ảnh</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Right Section History List -->
            <div
                class="flex flex-col w-full lg:gap-1 max-h-[700px] h-[300px] md:h-[500px] lg:h-[350px] xl:h-[500px] lg:w-[25%] overflow-y-auto xl:items-end">
                <div class="flex items-center justify-center" v-for="(item, index) in histories" :key="index">
                    <div class="w-[90%] md:w-[60%] lg:w-full md:flex md:justify-center md:items-center aspect-video relative"
                        @click="
                            fetchData(
                                route('ai-video.historyDetail', {
                                    id: item.id,
                                })
                            )
                            ">
                        <!--  -->

                        <div class="flex justify-center items-center rounded-xl lazy-src overflow-hidden relative mb-4">
                            <div class="flex justify-center items-center" v-if="
                                item.thumbnail &&
                                item.thumbnail !== ''
                            ">
                                <div
                                    class="aspect-video w-full md:w-[75%] lg:w-[75%] h-[150px] sm:h-[180px] md:h-[240px] lg:h-[180px]">
                                    <!-- Giảm chiều cao cho box thumbnail -->
                                    <img :src="item.thumbnail"
                                        class="cursor-pointer image w-full h-full object-cover rounded-xl" />
                                </div>
                                <img src="/assets/img/icon_play.png"
                                    class="w-14 md:w-20 sm:w-16 lg:w-20 cursor-pointer absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-[65%] sm:-translate-y-[70%] lg:-translate-y-[50%] opacity-50"
                                    alt="like" />
                            </div>
                            <div v-else class="flex justify-center items-center">
                                <!-- Chiều cao cho box "Đang tạo video" -->
                                <div
                                    class="aspect-video w-full md:w-[75%] lg:w-[75%] h-[150px] sm:h-[180px] md:h-[240px] lg:h-[180px]">
                                    <!-- Giảm chiều cao cho box "Đang tạo video" -->
                                    <img src="/assets/img/no-image.png"
                                        class="cursor-pointer image w-full h-full object-cover rounded-xl" />
                                </div>
                                <img src="/assets/img/icon_play.png"
                                    class="w-14 md:w-20 sm:w-16 lg:w-20 cursor-pointer absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-[65%] sm:-translate-y-[70%] lg:-translate-y-[50%] opacity-50"
                                    alt="like" />
                            </div>
                        </div>

                        <!--  -->
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { ref, computed } from "vue";
import { Head } from "@inertiajs/vue3";
import Footer from "../Home/Components/Footer.vue";
import Credit from '@/Components/Credit.vue';
import MainMenu from "@/Components/MainMenu.vue";
import VueCropper from "vue-cropperjs";
import "cropperjs/dist/cropper.css";
import Layout from "@/Layouts/Client/AiLayout.vue";

const props = defineProps({
    record: Object,
    list: Array,
});

const recordData = ref({ ...props.record });

const breadcrumbsData = [
    { text: "Lịch sử tạo video từ hình ảnh", link: "ai-video.ImgToVideoHistory" },
];

const listHistory = ref([]);
const showConfirmModal = ref(false);
const isLoading = ref(false);
const selectedVideo = ref(null);
const showTranscription = ref(false);
const isTranscribing = ref(false);
const audioFile = ref(null);

const goBack = () => {
    window.history.back();
};

// Pagination fetch without full page reload
const fetchData = (url) => {
    window.location.href = url;
};

// Thêm thuộc tính `isPlaying` vào từng video để quản lý việc phát video
const histories = ref(
    props.list?.map((item) => ({
        ...item,
        isPlaying: false,
    })) || []
);

// Lazy load for videos
const openDetail = (item) => {
    selectedVideo.value = item;
};

const videoHistory = () => {
    if (props.record.model === 'img-video' || props.record.model === 'merge-video-image') {
        window.location.href = "/ai-video/ImgToVideoHistory"
    } else {
        window.location.href = "/ai-video/history"
    }

}

const isShowRatio = ref(false)
const isCallConvertRatio = ref(false)
const ratio = ref("")
const showRatio = () => {
    if (isShowRatio.value) {
        isShowRatio.value = false;
    } else {
        isShowRatio.value = true
    }
}
const selectRatio = async (value) => {
    ratio.value = value
    isShowRatio.value = false;
    const formData = new FormData();
    formData.append('ai_media_id', recordData.value.id);
    formData.append('ratio', value);
    try {
        const response = await axios.post(
            route("ai-video.convertRatioQueue"),
            formData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            }
        );
        if (response.data.success) {
            isCallConvertRatio.value = true
        }
    } catch (error) {
        isCallConvertRatio.value = false
    } finally {
    }
}

const isCallUploadAudio = ref(false)
const audioName = ref('');
const handleAudioFileChange = async (event) => {
    var type = event.target.files[0].type;
    const allowedAudioTypes = ["audio/mpeg", "audio/wav"];
    if (!allowedAudioTypes.includes(type)) {
        alert("Xin vui lòng tải lên các tệp âm thanh có định dạng .mp3 hoặc .wav.");
        audioInput.value.value = "";
        return;
    }
    audioFile.value = event.target.files[0];
    audioName.value = audioFile.value ? audioFile.value.name : 'Chưa có tệp nào được chọn';
    isCallUploadAudio.value = true
    const formData = new FormData();
    formData.append('ai_media_id', recordData.value.id);
    if (audioFile.value) {
        formData.append('audio_file', audioFile.value);
    }
    try {
        const response = await axios.post(
            route("ai-video.mergeAudioVideoQueue"),
            formData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            }
        );
        if (response.data.success) {
            isCallUploadAudio.value = true
        }
    } catch (error) {
        isLoadingVideo.value = false
    } finally {
    }
};

const isCallUploadImage = ref(false)
const callApiQueue = async () => {
    const formData = new FormData();
    formData.append('ai_media_id', recordData.value.id);
    formData.append('image_file', imageInput.value);
    try {
        const response = await axios.post(
            route("ai-video.mergeImageVideoQueue"),
            formData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            }
        );
        if (response.data.success) {
            isCallUploadImage.value = true
        }
    } catch (error) {
        isLoadingVideo.value = false
    } finally {
    }
}

const croppedImageUrl = ref(null);
const cropper = ref(null);
const isCropping = ref(false);
const imageInput = ref(null)
const imageName = ref('');
const imageGenerate = ref(null);
const imageUrl = ref(null)
const isTransparentImage = ref(null);
const handleImageFileChange = (event) => {
    var type = event.target.files[0].type;
    const alloweImageTypes = ["image/jpeg", "image/png", "image/jpg"];
    if (!alloweImageTypes.includes(type)) {
        alert("Xin vui lòng tải lên các ảnh có định dạng .png, .jpeg, .jpg");
        imageInput.value.value = "";
        return;
    }
    if (type) {
        isCropping.value = true;
        imageGenerate.value = null;
        imageInput.value = event.target.files[0];
        imageUrl.value = URL.createObjectURL(event.target.files[0]);
        checkImageTransparency(imageUrl.value, function (isTransparent) {
            isTransparentImage.value = isTransparent;
        });
    }
    imageInput.value = event.target.files[0];
    imageName.value = imageInput.value ? imageInput.value.name : 'Chưa có tệp nào được chọn';
};

function checkImageTransparency(imageUrl, callback) {
    const img = new Image();
    img.src = imageUrl;

    img.onload = function () {
        const canvas = document.createElement("canvas");
        const ctx = canvas.getContext("2d");

        canvas.width = img.width;
        canvas.height = img.height;
        ctx.drawImage(img, 0, 0);

        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const data = imageData.data;

        let hasTransparentPixel = false;
        for (let i = 0; i < data.length; i += 4) {
            if (data[i + 3] < 255) {
                // Kiểm tra alpha channel (pixel trong suốt)
                hasTransparentPixel = true;
                break;
            }
        }

        callback(hasTransparentPixel);
    };

    img.onerror = function () {
        callback(false);
    };
}

const aspectRatio = computed(() => {
    const resolution = "1:1";
    const [width, height] = resolution.split(":");
    return parseFloat(width) / parseFloat(height);
});

const cancelCropping = (key) => {
    if (imageUrl.value) {
        const img = new Image();
        img.src = imageUrl.value;
        img.onload = function () {
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");
            const targetSize = 1080;
            const aspectRatio = img.width / img.height;
            if (aspectRatio > 1) {
                canvas.width = targetSize;
                canvas.height = targetSize / aspectRatio;
            } else {
                canvas.height = targetSize;
                canvas.width = targetSize * aspectRatio;
            }
            ctx.drawImage(img, 0, 0, img.width, img.height, 0, 0, canvas.width, canvas.height);
            const resizedImageUrl = canvas.toDataURL();
            imageUrl.value = resizedImageUrl;
            const file = dataURLtoFile(resizedImageUrl, "resized-image.png");
            imageInput.value = file;
            isCropping.value = false;
            callApiQueue()
        };
    }
};

const cropImage = () => {
    if (cropper.value) {
        const croppedCanvas = cropper.value.getCroppedCanvas();
        let width = croppedCanvas.width;
        let height = croppedCanvas.height;
        if (width > 2048 || height > 2048) {
            const resizedCanvas = document.createElement("canvas");
            const ctx = resizedCanvas.getContext("2d");

            const maxDimension = 1080;
            if (width > height) {
                resizedCanvas.width = maxDimension;
                resizedCanvas.height = (height * maxDimension) / width;
            } else {
                resizedCanvas.height = maxDimension;
                resizedCanvas.width = (width * maxDimension) / height;
            }

            ctx.drawImage(croppedCanvas, 0, 0, width, height, 0, 0, resizedCanvas.width, resizedCanvas.height);

            croppedCanvas.width = resizedCanvas.width;
            croppedCanvas.height = resizedCanvas.height;
            croppedCanvas.getContext("2d").drawImage(resizedCanvas, 0, 0);

            width = croppedCanvas.width;
            height = croppedCanvas.height;
        }
        const croppedImage = croppedCanvas.toDataURL();
        imageUrl.value = croppedImage;
        const file = dataURLtoFile(croppedImage, "cropped-image.png");
        console.log("Image Dimensions:", width, "x", height);
        imageInput.value = file;
        isCropping.value = false;
        callApiQueue()
    }
};

const dataURLtoFile = (dataUrl, fileName) => {
    const [header, base64Data] = dataUrl.split(";base64,");
    const mime = header.split(":")[1];
    const binary = atob(base64Data);
    const array = new Uint8Array(binary.length);

    for (let i = 0; i < binary.length; i++) {
        array[i] = binary.charCodeAt(i);
    }

    return new File([array], fileName, { type: mime });
};


var intervalid1 = setInterval(async () => {
    if (isCallUploadAudio.value || isCallUploadImage.value || isCallConvertRatio.value) {
        var url = route("ai-video.detail", { id: recordData.value.id })
        try {
            const response = await axios.get(url)
            if ((response.data.is_upload_audio && isCallUploadAudio.value) || (response.data.is_upload_image && isCallUploadImage.value) || (isCallConvertRatio.value && response.data.is_convert_ratio)) {
                recordData.value.s3_url = response.data.s3_url;
                recordData.value.ratio = response.data.data.ratio;
                isCallUploadAudio.value = false
                isCallUploadImage.value = false
                isCallConvertRatio.value = false
            }
        } catch (error) {
            isCallUploadAudio.value = false
            isCallUploadImage.value = false
            isCallConvertRatio.value = false
        }
    }
}, 10000);


const closeDetail = () => {
    selectedVideo.value = null;
};

const toggleTranscription = async () => {
    if (isTranscribing.value) return;

    if (recordData.value.transcription_url) {
        showTranscription.value = !showTranscription.value;
        return;
    }

    try {
        isTranscribing.value = true;
        const response = await axios.post(route('ai-video.create-video-with-transcription'), {
            ai_media_id: recordData.value.id,
        });
        const transcription = response?.data?.video;
        recordData.value.transcription_url = transcription;
        showTranscription.value = true;
    } catch (error) {
        console.error('Error creating transcription:', error);
        toast.error('Có lỗi xảy ra khi tạo video. Vui lòng thử lại.');
    } finally {
        isTranscribing.value = false;
    }
};
</script>

<style scoped>
.loaderVideo {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #24AA69;
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

@media (max-width: 600px) {
    .h1-fz-18 {
        font-size: 18px;
    }
}

.loaderAudio {
    width: 30px !important;
    height: 30px !important;
    margin-left: 0px !important;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #24AA69;
}

.icon-svg {
    float: right;
    margin-left: 10px;
    margin-top: 4px;
}

.show-item {
    width: 144px;
    position: relative;
    margin-top: -8px;
    padding-right: 20px;
    border: 1px solid #0f68ef;
}

.show-item li {
    padding-left: 20px;
    padding-right: 20px;
    border-bottom: 1px solid #0f68ef;
    cursor: pointer;
}

.text-ratio {
    width: 145px;
    cursor: pointer;
    margin-top: 10px;
}

.mt-80 {
    margin-top: -80px;
}

.video-ratio {
    height: auto;
    max-width: 500px;
}
</style>
