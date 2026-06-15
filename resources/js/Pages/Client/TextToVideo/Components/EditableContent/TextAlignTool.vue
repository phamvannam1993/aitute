<template>
    <div class="relative w-[36px] h-[36px] flex" ref="target">
        <button class="w-[36px] h-[36px] hover:bg-[#e2e2e2] rounded flex items-center justify-center"
                @click="showChildren = !showChildren">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" data-testid="textAlign-left"><path d="M12 12H0V14H12V12ZM12 4H0V6H12V4ZM0 10H18V8H0V10ZM0 18H18V16H0V18ZM0 0V2H18V0H0Z" fill="#808080"></path></svg>
        </button>

        <div v-show="showChildren"
             class="absolute flex gap-[4px] top-[100%] rounded shadow-xl border bg-white p-[12px] w-[160px]">
           <div class="w-[36px] h-[36px] hover:bg-[#e2e2e2] rounded flex items-center justify-center"
                :class="{
                    'bg-[#e2e2e2]': modelValue === 'left'
                }"
                @click="modelValue = 'left'">
               <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" data-testid="textAlign-left"><path d="M12 12H0V14H12V12ZM12 4H0V6H12V4ZM0 10H18V8H0V10ZM0 18H18V16H0V18ZM0 0V2H18V0H0Z" fill="#808080"></path></svg>
           </div>

            <div class="w-[36px] h-[36px] hover:bg-[#e2e2e2] rounded flex items-center justify-center"
                 :class="{
                    'bg-[#e2e2e2]': modelValue === 'center'
                }"
                 @click="modelValue = 'center'">
                <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg" data-testid="textAlign-center"><path d="M4 12.3488V14.3953H14V12.3488H4ZM0 18.4884H18V16.4419H0V18.4884ZM0 10.3023H18V8.25581H0V10.3023ZM4 4.16279V6.2093H14V4.16279H4ZM0 0.0697632V2.11627H18V0.0697632H0Z" fill="#808080"></path></svg>
            </div>

            <div class="w-[36px] h-[36px] hover:bg-[#e2e2e2] rounded flex items-center justify-center"
                 :class="{
                    'bg-[#e2e2e2]': modelValue === 'right'
                }"
                 @click="modelValue = 'right'">
                <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg" data-testid="textAlign-right"><path d="M0 18.4884H18V16.4419H0V18.4884ZM6 14.3953H18V12.3488H6V14.3953ZM0 10.3023H18V8.25581H0V10.3023ZM6 6.2093H18V4.16279H6V6.2093ZM0 0.0697632V2.11627H18V0.0697632H0Z" fill="#808080"></path></svg>
            </div>

            <div class="w-[36px] h-[36px] hover:bg-[#e2e2e2] rounded flex items-center justify-center"
                 :class="{
                    'bg-[#e2e2e2]': modelValue === 'justify'
                }"
                 @click="modelValue = 'justify'">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="black" xmlns="http://www.w3.org/2000/svg" data-testid="textAlign-justify"><path d="M0 0H18V1.5H0V0ZM0 4.125H18V5.625H0V4.125ZM0 8.25H18V9.75H0V8.25ZM0 12.375H18V13.875H0V12.375ZM0 16.5H12V18H0V16.5Z"></path></svg>
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref} from "vue";
import {onClickOutside} from "@vueuse/core";

const modelValue = defineModel();

const showChildren = ref(false)
const target = ref();
onClickOutside(target, (event) => {
    showChildren.value = false
})
</script>
