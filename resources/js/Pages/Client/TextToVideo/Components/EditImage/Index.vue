<template>
    <DraggableResizeable class="min-w-[200px]" ref="spanRef" :resizable="resizable" :draggable="draggable" :style="style?.dragStyle" @onDragStop="onDragStop" @onResizeStop="onResizeStop">
        <div :style="{ animationDelay: `${style.delayTime / 1000}s` }" :class="` ${style.animation} group [&>img]:cursor-pointer relative before:bg-[rgba(0,0,0,0.5)] before:absolute before:inset-0 before:opacity-[0] hover:before:opacity-[1] before:duration-300 before:transition-all overflow-hidden w-full h-full`" v-if="imageLink" v-bind="$attrs" ref="target" @contextmenu.prevent="handleShowPopup" @click="showPopup = false">
            <template v-if="isShow">
                <slot></slot>
            </template>
            <div class="absolute inset-0 opacity-0 group-hover:opacity-[1] duration-300 delay-[150] transition-all" v-if="!preview">
                <div class="flex gap-[8px] items-center justify-center h-full">
                    <div class="w-[36px] h-[36px] p-[8px] rounded-[50%] border border-white text-white cursor-pointer hover:bg-white hover:text-black duration-300 transition-all" @click="handleShowEditPopup">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024">
                            <path fill="currentColor" d="m199.04 672.64 193.984 112 224-387.968-193.92-112-224 388.032zm-23.872 60.16 32.896 148.288 144.896-45.696zM455.04 229.248l193.92 112 56.704-98.112-193.984-112-56.64 98.112zM104.32 708.8l384-665.024 304.768 175.936L409.152 884.8h.064l-248.448 78.336zm384 254.272v-64h448v64h-448z"></path>
                        </svg>
                    </div>
                    <div class="w-[36px] h-[36px] p-[8px] rounded-[50%] border border-white text-white cursor-pointer hover:bg-white hover:text-black duration-300 transition-all" @click="imageLink = null">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024">
                            <path fill="currentColor" d="M764.288 214.592 512 466.88 259.712 214.592a31.936 31.936 0 0 0-45.12 45.12L466.752 512 214.528 764.224a31.936 31.936 0 1 0 45.12 45.184L512 557.184l252.288 252.288a31.936 31.936 0 0 0 45.12-45.12L557.12 512.064l252.288-252.352a31.936 31.936 0 1 0-45.12-45.184z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </DraggableResizeable>

    <div v-show="showEditPopup" class="bg-white w-[680px] rounded-xl shadow-2xl absolute top-[50%] left-[50%] -translate-x-1/2 -translate-y-1/2 z-[60]">
        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px">
                <li class="me-2" @click="activeTab = 'google_search_tab'">
                    <span class="cursor-pointer inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" :class="{ '!border-blue-600 text-blue-600': activeTab === 'google_search_tab' }">Tìm kiếm</span>
                </li>
                <li class="me-2" @click="activeTab = 'local_upload_tab'">
                    <span class="cursor-pointer inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" :class="{ '!border-blue-600 text-blue-600': activeTab === 'local_upload_tab' }">Tải từ máy tính</span>
                </li>
                <li class="me-2" @click="activeTab = 'ai-create'">
                    <span class="cursor-pointer inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" :class="{ '!border-blue-600 text-blue-600': activeTab === 'ai-create' }">Tạo hình ảnh bằng AI</span>
                </li>
                <li class="me-2" @click="activeTab = 'icon_search_tab'">
                    <span
                        class="cursor-pointer inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                        :class="{
                            '!border-blue-600 text-blue-600': activeTab === 'icon_search_tab',
                        }"
                        >Icons</span
                    >
                </li>
                <li class="me-2" @click="activeTab = 'style_tab'">
                    <span
                        class="cursor-pointer inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                        :class="{
                            '!border-blue-600 text-blue-600': activeTab === 'style_tab',
                        }"
                        >Style</span
                    >
                </li>
                <li class="ms-auto flex items-center w-[40px] justify-center">
                    <svg class="cursor-pointer" @click.prevent="handleClosePopup" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="18" height="18" viewBox="0 0 24 24">
                        <path d="M11 0.7H13V23.3H11z" transform="rotate(-45.001 12 12)"></path>
                        <path d="M0.7 11H23.3V13H0.7z" transform="rotate(-45.001 12 12)"></path>
                    </svg>
                </li>
            </ul>
        </div>

        <div class="h-[400px] w-[600px] p-[20px] overflow-y-auto">
            <div v-show="activeTab === 'google_search_tab'">
                <SearchImage v-model="imageLink" />
            </div>
            <div v-show="activeTab === 'local_upload_tab'">
                <UploadImage v-model="imageLink" />
            </div>
            <div v-show="activeTab === 'icon_search_tab'">
                <SearchIcon v-model="imageLink" />
            </div>
            <div v-show="activeTab === 'ai-create'">
                <CreateAIImage @image-generated="handleAIGenerate" :title="props.title" />
            </div>
            <div v-show="activeTab === 'style_tab'">
                <StyleImage v-model="style" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { onClickOutside } from "@vueuse/core";

const props = defineProps({
    active: Boolean,
    title: String,
    isPresent: Boolean,
    width: {
        type: [Number, String],
        default: "auto",
    },
    height: {
        type: [Number, String],
        default: "auto",
    },
    resizable: {
        type: Boolean,
        default: false,
    },
    draggable: {
        type: Boolean,
        default: false,
    },
    preview: {
        type: Boolean,
        default: false,
    },
});

import SearchImage from "@/Pages/Client/TextToVideo/Components/EditImage/SearchImage.vue";
import SearchIcon from "@/Pages/Client/TextToVideo/Components/EditImage/SearchIcon.vue";
import { ref } from "vue";
import UploadImage from "@/Pages/Client/TextToVideo/Components/EditImage/UploadImage.vue";
import CreateAIImage from "@/Pages/Client/TextToVideo/Components/EditImage/CreateAIImage.vue";
import StyleImage from "@/Pages/Client/TextToVideo/Components/EditImage/StyleImage.vue";
import DraggableResizeable from "@/Pages/Client/TextToVideo/Components/DraggableResizeable.vue";
import { onMounted } from "@vue/runtime-core";

const activeTab = ref("google_search_tab");
const imageLink = defineModel();
const style = defineModel("customStyle");
const showPopup = ref();
const showEditPopup = ref(false);

const isShow = ref(false);

const contextMenu = ref();
const handleShowPopup = (e) => {
    if (props.isPresent) {
        return;
    }

    contextMenu.value.style.top = `${e.offsetY}px`;
    contextMenu.value.style.left = `${e.offsetX}px`;
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

const handleAIGenerate = (generatedImageUrl) => {
    imageLink.value = generatedImageUrl;
    handleClosePopup();
};

const target = ref(null);
onClickOutside(target, (event) => {
    showPopup.value = false;
});

const onDragStop = ({ left, top }) => {
    if (style.value) {
        style.value.dragStyle.left = left;
        style.value.dragStyle.top = top;
    }
};

const onResizeStop = ({ left, top, width, height }) => {
    if (style.value) {
        style.value.dragStyle.left = left;
        style.value.dragStyle.top = top;
        style.value.dragStyle.width = width;
        style.value.dragStyle.height = height;
    }
};

onMounted(() => {
    if (style.value && !style.value.dragStyle) {
        style.value.dragStyle = {
            top: 0,
            left: 0,
            width: 250,
            height: 250,
        };
    }

    setTimeout(() => {
        isShow.value = true;
    }, Number(style.value.delayTime) + 100);
});
</script>
<style scoped>
.ani-left-to-right {
    animation: ani-left-to-right 1s ease-in-out;
}

.ani-right-to-left {
    animation: ani-right-to-left 1s ease-in-out;
}

.ani-top-to-bottom {
    animation: ani-top-to-bottom 1s ease-in-out;
}

.ani-bottom-to-top {
    animation: ani-bottom-to-top 1s ease-in-out;
}

@keyframes ani-left-to-right {
    0% {
        transform: translateX(-300px);
    }
    100% {
        transform: none;
    }
}

@keyframes ani-right-to-left {
    0% {
        transform: translateX(300px);
    }
    100% {
        transform: none;
    }
}

@keyframes ani-top-to-bottom {
    0% {
        transform: translateY(-300px);
    }
    100% {
        transform: none;
    }
}

@keyframes ani-bottom-to-top {
    0% {
        transform: translateY(300px);
    }
    100% {
        transform: none;
    }
}
</style>
