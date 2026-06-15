<template>
    <div class="group [&>img]:cursor-pointer relative before:bg-[rgba(0,0,0,0.5)] before:absolute before:inset-0 before:opacity-[0] hover:before:opacity-[1]
    before:duration-300 before:transition-all"
         v-if="imageLink"
         v-bind="$attrs" ref="target"
         @contextmenu.prevent="handleShowPopup"
         @click="showPopup = false"
    >
        <slot></slot>
        <!--        <div v-show="showPopup" ref="contextMenu" class="bg-white rounded shadow-2xl absolute">-->
        <!--            <ul class="text-[13px] cursor-pointer">-->
        <!--                <li class="px-3 py-1 hover:bg-[#e2e2e2] duration-150 whitespace-nowrap" @click="imageLink = null">Xoá ảnh</li>-->
        <!--                <li class="px-3 py-1 hover:bg-[#e2e2e2] duration-150 whitespace-nowrap" @click="handleShowEditPopup">Chỉnh sửa</li>-->
        <!--            </ul>-->
        <!--        </div>-->
        <div class="absolute inset-0 opacity-0 group-hover:opacity-[1] group-hover:z-[99] duration-300 delay-[150] transition-all">
            <div class="flex items-center justify-center h-full">
                <div class="w-[40px] p-[8px] rounded-[50%] border border-white text-white cursor-pointer hover:bg-white hover:text-black duration-300 transition-all" @click="handleShowEditPopup">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024">
                        <path fill="currentColor" d="m199.04 672.64 193.984 112 224-387.968-193.92-112-224 388.032zm-23.872 60.16 32.896 148.288 144.896-45.696zM455.04 229.248l193.92 112 56.704-98.112-193.984-112-56.64 98.112zM104.32 708.8l384-665.024 304.768 175.936L409.152 884.8h.064l-248.448 78.336zm384 254.272v-64h448v64h-448z"></path>
                    </svg>
                </div>
            </div>
        </div>

    </div>
    <div v-show="showEditPopup" class="bg-white rounded-xl shadow-2xl absolute top-[50%] left-[50%] -translate-x-1/2 -translate-y-1/2 z-20">
        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px">
                <li class="me-2" @click="activeTab = 'google_search_tab'">
                        <span class="cursor-pointer inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                              :class="{
                                '!border-blue-600 text-blue-600': activeTab === 'google_search_tab',
                              }"
                        >Tìm kiếm</span>
                </li>
                <li class="me-2" @click="activeTab = 'local_upload_tab'">
                        <span class="cursor-pointer inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                              :class="{
                                '!border-blue-600 text-blue-600': activeTab === 'local_upload_tab',
                              }"
                        >Tải từ máy tính</span>
                </li>
                <li class="me-2" @click="activeTab = 'icon_search_tab'">
                        <span class="cursor-pointer inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                              :class="{
                                '!border-blue-600 text-blue-600': activeTab === 'icon_search_tab',
                              }"
                        >Icons</span>
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
            <div v-show="activeTab === 'google_search_tab'">
                <SearchImage v-model="imageLink"/>
            </div>
            <div v-show="activeTab === 'local_upload_tab'">
                <UploadImage v-model="imageLink"/>
            </div>
            <div v-show="activeTab === 'icon_search_tab'">
                <SearchIcon v-model="imageLink"/>
            </div>
        </div>
    </div>
</template>

<script setup>
import {onClickOutside} from "@vueuse/core";

const props = defineProps({
    active: Boolean,
    isPresent: Boolean
})

import SearchImage from "@/Pages/Client/TextToVideo/Components/EditImage/SearchImage.vue";
import SearchIcon from "@/Pages/Client/TextToVideo/Components/EditImage/SearchIcon.vue";
import {ref} from "vue";
import UploadImage from "@/Pages/Client/TextToVideo/Components/EditImage/UploadImage.vue";

const activeTab = ref("icon_search_tab");
const imageLink = defineModel();
const showPopup = ref();
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
