<template>
    <div class="flex items-center px-2  sticky top-0 z-50 bg-white shadow h-16">
        <div class="w-1/7 flex items-center space-x-4 py-2 mr-20">
            <button class="p-2 rounded-lg hover:bg-gray-100">
                <img src="/assets/svgs/menu-button.svg" alt="menu" class="h-7" @click="toggleSidebar" />
            </button>

            <img src="/assets/svgs/logo.svg" alt="logo" class="h-10" />
        </div>
        <div class="flex w-6/7 items-center [#2C75E3] space-x-2 h-16">
            <img src="/assets/svgs/home-icon.svg" alt="logo" class="w-6 h-6" /> 
            <span class="font-bold text-[#2C75E3]">/</span>              
            <span class="font-bold text-[#2C75E3]">Trang chủ</span>
        </div>
    </div>
</template>

<script setup>
import { defineEmits } from 'vue';

const emit = defineEmits(['toggleSidebar']);

const toggleSidebar = () => {
  emit('toggleSidebar');
};
</script>