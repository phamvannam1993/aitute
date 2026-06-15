<template>
    <div
        class="group [&>video]:cursor-pointer relative before:bg-[rgba(0,0,0,0.5)] before:absolute before:inset-0 before:opacity-[0] hover:before:opacity-[1]
        before:duration-300 before:transition-all"
        v-if="videoUrl"
        v-bind="$attrs"
        ref="target"
        @contextmenu.prevent="handleShowPopup"
        @click="showPopup = false"
    >
        <slot></slot>
        <div class="absolute inset-0 opacity-0 group-hover:opacity-[1] duration-300 delay-[150] transition-all">
            <div class="flex gap-[8px] items-center justify-center h-full">
                <div
                    class="w-[36px] h-[36px] p-[8px] rounded-[50%] border border-white text-white cursor-pointer hover:bg-white hover:text-black duration-300 transition-all"
                    @click="handleShowEditPopup"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024">
                        <path
                            fill="currentColor"
                            d="m199.04 672.64 193.984 112 224-387.968-193.92-112-224 388.032zm-23.872 60.16 32.896 148.288 144.896-45.696zM455.04 229.248l193.92 112 56.704-98.112-193.984-112-56.64 98.112zM104.32 708.8l384-665.024 304.768 175.936L409.152 884.8h.064l-248.448 78.336zm384 254.272v-64h448v64h-448z"
                        ></path>
                    </svg>
                </div>
                <div
                    class="w-[36px] h-[36px] p-[8px] rounded-[50%] border border-white text-white cursor-pointer hover:bg-white hover:text-black duration-300 transition-all"
                    @click="resetToDefault"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024">
                        <path
                            fill="currentColor"
                            d="M764.288 214.592 512 466.88 259.712 214.592a31.936 31.936 0 0 0-45.12 45.12L466.752 512 214.528 764.224a31.936 31.936 0 1 0 45.12 45.184L512 557.184l252.288 252.288a31.936 31.936 0 0 0 45.12-45.12L557.12 512.064l252.288-252.352a31.936 31.936 0 1 0-45.12-45.184z"
                        ></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div
        v-show="showEditPopup"
        class="bg-white rounded-xl shadow-2xl absolute top-[50%] left-[50%] md:left-[18rem] -translate-x-1/2 -translate-y-1/2 z-20"
    >
        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-pc">
                <li class="me-2" @click="activeTab = 'local_upload_tab'">
                    <span
                        class="cursor-pointer inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                        :class="{ '!border-blue-600 text-blue-600': activeTab === 'local_upload_tab' }"
                    >
                        Tải video từ máy tính
                    </span>
                </li>
                <li class="ms-auto flex items-center w-[40px] justify-center">
                    <svg
                        class="cursor-pointer"
                        @click.prevent="handleClosePopup"
                        xmlns="http://www.w3.org/2000/svg"
                        x="0px"
                        y="0px"
                        width="18"
                        height="18"
                        viewBox="0 0 24 24"
                    >
                        <path d="M11 0.7H13V23.3H11z" transform="rotate(-45.001 12 12)"></path>
                        <path d="M0.7 11H23.3V13H0.7z" transform="rotate(-45.001 12 12)"></path>
                    </svg>
                </li>
            </ul>
        </div>

        <div class="h-[400px] w-[350px] sm:w-[500px] md:w-[600px] p-[20px] overflow-y-auto">
            <div v-show="activeTab === 'local_upload_tab'">
                <UploadVideo v-model="videoUrl" @videoUploaded="handleVideoUploaded"  @videoUrl="handleVideoUrl" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import UploadVideo from "@/Pages/Client/AIVideo/EditVideo/UploadVideo.vue";
import { onClickOutside } from "@vueuse/core";

const props = defineProps({
    modelValue: {
        type: String,
        required: false,
        default: "", // default empty string if not provided
    },
});

const emit = defineEmits(['update:modelValue', 'urlSubmitted']);



const showPopup = ref(false);
const showEditPopup = ref(false);
const activeTab = ref("local_upload_tab");

const defaultVideoUrl = 'https://segmind-sd-models.s3.amazonaws.com/display_images/videoFaceSwap/faceswap-input.mp4';
const videoUrl = ref('');

// Initialize `videoUrl` based on `modelValue` or `defaultVideoUrl`
onMounted(() => {
    videoUrl.value = props.modelValue || defaultVideoUrl;
});


const handleVideoUrl = (url) => {
    videoUrl.value = url;
    console.log("URL của video đã được truyền từ component con:", videoUrl.value);
    emit('urlSubmitted', videoUrl.value); 
};

// Watch for changes in `modelValue` and update `videoUrl`
watch(
    () => props.modelValue,
    (newValue) => {
        videoUrl.value = newValue || defaultVideoUrl;
    }
);

const handleShowPopup = (e) => {
    if (props.isPresent) {
        return;
    }

    if (!props.active) {
        return;
    }

    if (showPopup.value) {
        return;
    }

    showPopup.value = true;
};

const handleShowEditPopup = () => {
    showEditPopup.value = true;
    showPopup.value = false;
};

const handleClosePopup = () => {
    setTimeout(() => {
        showEditPopup.value = false;
    }, 0);
};

const resetToDefault = () => {
    videoUrl.value = defaultVideoUrl;
    emit('urlSubmitted', videoUrl.value);
};

const target = ref(null);
onClickOutside(target, (event) => {
    showPopup.value = false;
});

watch(videoUrl, (newUrl) => {
    emit('update:modelValue', newUrl);
});

</script>
