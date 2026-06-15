<template>
    <div class="relative w-[36px] h-[36px] flex" ref="target">
        <button class="w-[36px] h-[36px] hover:bg-[#e2e2e2] rounded flex items-center justify-center"
                @click="showChidlren = !showChidlren">
            <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg" data-testid="font-lineHeight-svg"><path d="M10 1H20V3H10V1ZM5 4V8H3V4H0L4 0L8 4H5ZM5 14H8L4 18L0 14H3V10H5V14ZM10 15H20V17H10V15ZM8 8H20V10H8V8Z" fill="#808080"></path></svg>
        </button>

        <ul v-show="showChidlren"
             class="absolute gap-[4px] top-[100%] rounded shadow-xl border bg-white py-[8px] w-[60px]">
            <li v-for="option in options" @click="modelValue = option"
            class="hover:bg-[#c8e7eb] text-center text-[14px] py-[4px] cursor-pointer"
            :class="{
                'bg-[#c8e7eb]': modelValue === option
            }">
                {{ option }}
            </li>


            <!--            <label class="w-[24px] h-[24px] rounded-[50%] border cursor-pointer relative">-->
            <!--                <input type="color" class="absolute top-0 -z-1">-->
            <!--            </label>-->
        </ul>
    </div>
</template>

<script setup>
import {ref} from "vue";
import {onClickOutside} from "@vueuse/core";

const modelValue = defineModel();

const showChidlren = ref(false)

const options = [
    '1.0',
    '1.15',
    '1.2',
    '1.3',
    '1.45',
    '1.6',
    '1.8',
    '2.0',
    '2.5',
    '3.0',
]

const target = ref();
onClickOutside(target, (event) => {
    showChidlren.value = false
})
</script>
