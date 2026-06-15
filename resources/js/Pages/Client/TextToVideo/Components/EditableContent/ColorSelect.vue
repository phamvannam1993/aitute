<template>
    <div class="relative w-[36px] h-[36px] flex" ref="target">
        <button class="w-[36px] h-[36px] hover:bg-[#e2e2e2] rounded flex items-center justify-center"
                @click="showColorSelect = !showColorSelect">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" data-testid="font-color-svg">
                <path d="M2 18.1064H22V22.0213H2V18.1064ZM5.49 15.1702H7.91L9.18 11.6664H14.83L16.09 15.1702H18.51L13.25 1.46808H10.75L5.49 15.1702ZM9.91 9.67957L11.94 4.01276H12.06L14.09 9.67957H9.91Z" :fill="modelValue ?? '#808080'"></path>
            </svg>
        </button>

        <div v-show="showColorSelect"
             class="absolute grid
             grid-cols-4 gap-[4px] top-[100%] rounded shadow-xl border bg-white p-[12px] w-[140px]">
            <div v-for="color in colors" class="w-[24px] h-[24px] rounded-[50%] border cursor-pointer"
                 @click="modelValue = color"
                 :style="{
                backgroundColor: color
            }"></div>

<!--            <label class="w-[24px] h-[24px] rounded-[50%] border cursor-pointer relative">-->
<!--                <input type="color" class="absolute top-0 -z-1">-->
<!--            </label>-->
        </div>
    </div>
</template>

<script setup>
import {ref} from "vue";
import {onClickOutside} from "@vueuse/core";

const modelValue = defineModel();

const colors = [
    'rgb(255, 255, 255)',
    'rgb(242, 242, 242)',
    'rgb(178, 178, 178)',
    'rgb(77, 77, 77)',
    'rgb(255, 0, 0)',
    'rgb(255, 153, 0)',
    'rgb(73, 178, 27)',
    'rgb(0, 0, 255)',
    'rgb(153, 0, 222)',
    'rgb(0, 175, 178)',
    'rgb(71, 194, 197)',
    'rgb(156, 223, 224)',
    'rgb(255, 193, 7)',
];

const showColorSelect = ref(false)

const target = ref();
onClickOutside(target, (event) => {
    showColorSelect.value = false
})
</script>
