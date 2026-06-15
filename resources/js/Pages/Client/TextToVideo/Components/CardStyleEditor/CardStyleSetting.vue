<template>
    <div class="bg-white p-[20px] rounded-xl shadow w-[350px]">
        <div class="flex items-center gap-[12px]">
            <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="fill-drip"
                 class="w-[16px] h-[16px]" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                <path fill="currentColor" d="M39 7C48.4-2.3 63.6-2.3 73 7l89.4 89.4 58.7-58.7c28.1-28.1 73.7-28.1 101.8 0L474.3 189.1c28.1 28.1 28.1 73.7 0 101.8L283.9 481.4c-37.5 37.5-98.3 37.5-135.8 0L30.6 363.9c-37.5-37.5-37.5-98.3 0-135.8l97.8-97.8L39 41C29.7 31.6 29.7 16.4 39 7zM231 233l-68.7-68.7L64.6 262.1c-7.3 7.3-11.8 16.4-13.4 25.9H409.4l31-31c9.4-9.4 9.4-24.6 0-33.9L289 71.6c-9.4-9.4-24.6-9.4-33.9 0l-58.7 58.7L265 199c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0zM512 512c-35.3 0-64-28.7-64-64c0-25.2 32.6-79.6 51.2-108.7c6-9.4 19.5-9.4 25.5 0C543.4 368.4 576 422.8 576 448c0 35.3-28.7 64-64 64z"></path>
            </svg>
            <p class="whitespace-nowrap">Màu sắc của thẻ</p>
            <div class="ml-auto relative">
                <div class="cursor-pointer flex items-center gap-[4px] px-[8px] py-[4px]  border rounded min-w-[165px]"
                     @click="isShowMenu = !isShowMenu">
                    <div class="w-[16px] h-[16px] rounded-[4px] border" :style="{
                        backgroundColor: slideAI.currentSlide?.props?.card_style?.backgroundColor ?? ''
                    }"></div>
                    <p>{{ slideAI.currentSlide?.props?.card_style?.theme ? slideAI.currentSlide?.props?.card_style?.theme : 'Mặc định' }}</p>
                    <svg viewBox="0 0 24 24" focusable="false" class="pl-[6px] w-[20px] h-[20px] ml-auto">
                        <path fill="currentColor" d="M16.59 8.59L12 13.17 7.41 8.59 6 10l6 6 6-6z"></path>
                    </svg>
                </div>

                <div v-if="isShowMenu" class="bg-white shadow rounded-xl absolute left-0 top-[40px] w-[200px] overflow-hidden">
                    <div class="bg-white px-[12px] py-[8px]">
                        <div>
                            <p class="text-[14px]">Màu trong hệ thống</p>
                            <div class="flex gap-[4px] flex-wrap">
                                <div class="cursor-pointer w-[26px] h-[26px] rounded-[4px] border flex items-center justify-center"
                                     v-for="(theme, index) in systemCardThemes"
                                     :key="index"
                                     :style="{
                                        backgroundColor: theme.slideColor,
                                        color: theme.headingColor
                                     }"
                                     @click="handleChangeTheme(theme)">
                                    <svg v-if="theme.name === slideAI.currentSlide.props?.card_style?.theme" viewBox="0 0 14 14" focusable="false" class="h-[12px] w-[12px]"><g fill="currentColor"><polygon points="5.5 11.9993304 14 3.49933039 12.5 2 5.5 8.99933039 1.5 4.9968652 0 6.49933039"></polygon></g></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-[#f7f3f2] px-[12px] py-[6px] text-[#8f8b8b] flex items-center gap-[10px] justify-center group cursor-pointer"
                         @click="handleResetTheme"
                    >
                        <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="arrow-rotate-left" class="w-[16px] h-[16px]" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path fill="currentColor" d="M40 224c-13.3 0-24-10.7-24-24V56c0-13.3 10.7-24 24-24s24 10.7 24 24v80.1l20-23.5C125 63.4 186.9 32 256 32c123.7 0 224 100.3 224 224s-100.3 224-224 224c-50.4 0-97-16.7-134.4-44.8c-10.6-8-12.7-23-4.8-33.6s23-12.7 33.6-4.8C179.8 418.9 216.3 432 256 432c97.2 0 176-78.8 176-176s-78.8-176-176-176c-54.3 0-102.9 24.6-135.2 63.4l-.1 .2 0 0L93.1 176H184c13.3 0 24 10.7 24 24s-10.7 24-24 24H40z"></path>
                        </svg>
                        <span class="whitespace-nowrap group-hover:underline transition-all duration-150">Khôi phục mặc định</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>

import {useSlideAIStore} from "@/Pages/Client/TextToVideo/Store/state.js";
import {ref} from "vue";
import {systemCardThemes} from "./themes.js";

const props = defineProps({
    theme: String
});

const theme = props.theme;
const slideAI = useSlideAIStore();

const handleChangeTheme = (theme) => {
    if (!slideAI.currentSlide.props.card_style) {
        slideAI.currentSlide.props.card_style = {}
    }
    slideAI.currentSlide.props.card_style.backgroundColor = theme.slideColor;
    slideAI.currentSlide.props.card_style.theme = theme.name;
    slideAI.currentSlide.props.title_style.color = theme.headingColor;
    slideAI.currentSlide.props.description_style.color = theme.textColor;
    slideAI.currentSlide.props.subtitle_style.color = theme.headingColor;
    if (!slideAI.currentSlide.props.background_style) {
        slideAI.currentSlide.props.background_style = {}
    }
    // slideAI.currentSlide.props.background_style.backgroundColor = theme.backgroundColor;
}

const handleResetTheme = () => {
    if (!slideAI.currentSlide.props.card_style) {
        slideAI.currentSlide.props.card_style = {}
    }
    slideAI.currentSlide.props.card_style.backgroundColor = theme.slideColor;
    slideAI.currentSlide.props.card_style.theme = '';
    slideAI.currentSlide.props.title_style.color = theme.headingColor;
    slideAI.currentSlide.props.description_style.color = theme.textColor;
    slideAI.currentSlide.props.subtitle_style.color = theme.headingColor;
    if (!slideAI.currentSlide.props.background_style) {
        slideAI.currentSlide.props.background_style = {}
    }
    slideAI.currentSlide.props.background_style.backgroundColor = theme.backgroundColor;
}

const isShowMenu = ref(false);
</script>

<style>
</style>
