<template>
    <div class="group [&>video]:cursor-pointer relative before:bg-[rgba(0,0,0,0.5)] before:absolute before:inset-0 before:opacity-[0] hover:before:opacity-[1]
    before:duration-300 before:transition-all"
         v-if="videoLink"
         v-bind="$attrs" ref="target"
         @contextmenu.prevent="handleShowPopup"
         @click="showPopup = false"
    >
        <slot></slot>
        <div class="absolute inset-0 opacity-0 group-hover:opacity-[1] duration-300 delay-[150] transition-all">
            <div class="flex gap-[8px] items-center justify-center h-full">
                <div class="w-[36px] h-[36px] p-[8px] rounded-[50%] border border-white text-white cursor-pointer hover:bg-white hover:text-black duration-300 transition-all" @click="handleShowEditPopup">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M14,12A2,2 0 0,1 16,10A2,2 0 0,1 18,12A2,2 0 0,1 16,14A2,2 0 0,1 14,12M8,12A2,2 0 0,1 10,10A2,2 0 0,1 12,12A2,2 0 0,1 10,14A2,2 0 0,1 8,12M2,12A2,2 0 0,1 4,10A2,2 0 0,1 6,12A2,2 0 0,1 4,14A2,2 0 0,1 2,12M22,5V19A2,2 0 0,1 20,21H4A2,2 0 0,1 2,19V5A2,2 0 0,1 4,3H20A2,2 0 0,1 22,5M20,5H4V19H20V5Z" />
                    </svg>
                </div>
                <div class="w-[36px] h-[36px] p-[8px] rounded-[50%] border border-white text-white cursor-pointer hover:bg-white hover:text-black duration-300 transition-all" @click="videoLink = null">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024">
                        <path fill="currentColor" d="M764.288 214.592 512 466.88 259.712 214.592a31.936 31.936 0 0 0-45.12 45.12L466.752 512 214.528 764.224a31.936 31.936 0 1 0 45.12 45.184L512 557.184l252.288 252.288a31.936 31.936 0 0 0 45.12-45.12L557.12 512.064l252.288-252.352a31.936 31.936 0 1 0-45.12-45.184z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    <div v-show="showEditPopup" class="bg-white rounded-xl shadow-2xl absolute top-[50%] left-[50%] -translate-x-1/2 -translate-y-1/2 z-20">
        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px">
                <li class="me-2" @click="activeTab = 'search_tab'">
                    <span class="cursor-pointer inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                          :class="{ '!border-blue-600 text-blue-600': activeTab === 'search_tab' }">Tìm kiếm</span>
                </li>
                <li class="me-2" @click="activeTab = 'local_upload_tab'">
                    <span class="cursor-pointer inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                          :class="{ '!border-blue-600 text-blue-600': activeTab === 'local_upload_tab' }">Tải từ máy tính</span>
                </li>
                <li class="me-2" @click="activeTab = 'ai-create'">
                    <span class="cursor-pointer inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                          :class="{ '!border-blue-600 text-blue-600': activeTab === 'ai-create' }">Tạo video bằng AI</span>
                </li>
                <li class="ms-auto flex items-center w-[40px] justify-center">
                    <svg class="cursor-pointer" @click.prevent="handleClosePopup" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="18" height="18" viewBox="0 0 24 24">
                        <path d="M11 0.7H13V23.3H11z" transform="rotate(-45.001 12 12)"></path>
                        <path d="M0.7 11H23.3V13H0.7z" transform="rotate(-45.001 12 12)"></path>
                    </svg>
                </li>
            </ul>
        </div>

        <div class="h-[400px] w-[600px] p-[20px]">
            <div v-show="activeTab === 'search_tab'">
                <SearchVideo v-model="videoLink"/>
            </div>
            <div v-show="activeTab === 'local_upload_tab'">
                <UploadVideo v-model="videoLink"/>
            </div>
            <div v-show="activeTab === 'ai-create'">
                <CreateAIVideo @image-generated="handleAIGenerate"/>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onClickOutside } from "@vueuse/core";
import { ref } from "vue";
import SearchVideo from "@/Pages/Client/TextToVideo/Components/EditVideo/SearchVideo.vue";
import UploadVideo from "@/Pages/Client/TextToVideo/Components/EditVideo/UploadVideo.vue";
import CreateAIVideo from "./CreateAIVideo.vue";

const props = defineProps({
    active: Boolean,
    isPresent: Boolean
})

const activeTab = ref("search_tab");
const videoLink = defineModel();
const showPopup = ref(false);
const showEditPopup = ref(false);

const contextMenu = ref();
const handleShowPopup = (e) => {
    if (props.isPresent) {
        return;
    }

    contextMenu.value.style.top = `${e.offsetY}px`
    contextMenu.value.style.left = `${e.offsetX}px`
    if (!props.active) {
        return;
    }

    if (showPopup.value) {
        return;
    }

    showPopup.value = true
}

const handleShowEditPopup = () => {
    showEditPopup.value = true
    showPopup.value = false
}

const handleClosePopup = () => {
    setTimeout(() => {
        showEditPopup.value = false
    }, 0)
}

const target = ref(null);
onClickOutside(target, (event) => {
    showPopup.value = false
})
</script>