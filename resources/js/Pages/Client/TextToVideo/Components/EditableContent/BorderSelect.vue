<template>
    <div class="relative w-[36px] h-[36px] flex" ref="target">
        <button class="w-[36px] h-[36px] hover:bg-[#e2e2e2] rounded flex items-center justify-center" @click="showColorSelect = !showColorSelect">
            <svg fill="none" width="24" height="24" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
                <path d="M880 112H144c-17.7 0-32 14.3-32 32v736c0 17.7 14.3 32 32 32h736c17.7 0 32-14.3 32-32V144c0-17.7-14.3-32-32-32zm-40 728H184V184h656v656z" :fill="modelValue ? modelValue : '#808080'" />
            </svg>
        </button>

        <div v-show="showColorSelect" class="absolute grid grid-cols-4 gap-[4px] top-[100%] rounded shadow-xl border bg-white p-[12px] w-[140px]">
            <div
                v-for="color in colors"
                class="w-[24px] h-[24px] rounded-[50%] border cursor-pointer"
                @click="modelValue = color"
                :style="{
                    backgroundColor: color,
                }"
            ></div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { onClickOutside } from "@vueuse/core";

const modelValue = defineModel();

const colors = ["rgb(255, 255, 255)", "rgb(242, 242, 242)", "rgb(178, 178, 178)", "rgb(77, 77, 77)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(73, 178, 27)", "rgb(0, 0, 255)", "rgb(153, 0, 222)", "rgb(0, 175, 178)", "rgb(71, 194, 197)", "rgb(156, 223, 224)", "rgb(255, 193, 7)"];

const showColorSelect = ref(false);

const target = ref();
onClickOutside(target, (event) => {
    showColorSelect.value = false;
});
</script>
