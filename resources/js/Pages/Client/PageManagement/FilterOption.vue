<template>
    <div class="flex flex-col">
        <div class="flex items-center justify-between hover:bg-[#EFEFEF] rounded-[5px] px-4 py-2 mt-1 group" @click="toggleDropdown"
             :class="{'bg-[#EFEFEF]': isOpen}">
            <span class="text-[12px]">{{ title }}</span>
            <svg class="-mr-1 ml-1 h-3 w-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                 :class="{'opacity-100': isOpen}"
                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#545252">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <div v-if="isOpen" class="flex flex-col mt-1 bg-[#EFEFEF] p-4 space-y-2">
            <div v-if="optionItems && optionItems.length > 0">
                <label v-for="(item, index) in optionItems" :key="index" class="flex items-center text-[12px] py-1 cursor-pointer">
                    <input :type="inputType" :name="item.name" :value="item.name" class="mr-2">
                    {{ item.name }}
                </label>
            </div>

            <div v-else class="flex flex-col items-center">
                    <div class="">
                        <svg width="64" height="41" viewBox="0 0 64 41" xmlns="http://www.w3.org/2000/svg"><g transform="translate(0 1)" fill="none" fill-rule="evenodd"><ellipse fill="#f5f5f5" cx="32" cy="33" rx="32" ry="7"></ellipse><g fill-rule="nonzero" stroke="#d9d9d9"><path d="M55 12.76L44.854 1.258C44.367.474 43.656 0 42.907 0H21.093c-.749 0-1.46.474-1.947 1.257L9 12.761V22h46v-9.24z"></path><path d="M41.613 15.931c0-1.605.994-2.93 2.227-2.931H55v18.137C55 33.26 53.68 35 52.05 35h-40.1C10.32 35 9 33.259 9 31.137V13h11.16c1.233 0 2.227 1.323 2.227 2.928v.022c0 1.605 1.005 2.901 2.237 2.901h14.752c1.232 0 2.237-1.308 2.237-2.913v-.007z" fill="#fafafa"></path></g></g></svg>
                    </div>
                    <span class="text-[#999] text-[12px] mt-2">
                        Không có {{ title.toLowerCase() }} 
                    </span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const isOpen = ref(false);

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

const props = defineProps({
    title: String,
    inputType: String,
    optionItems: Array,
});

</script>