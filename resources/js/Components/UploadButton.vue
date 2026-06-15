<template>
    <button type="button" @click="props.handleClick" class="w-full h-full rounded-xl bg-[#D4D4D4]" :class="className">
        <div class="flex flex-col inset-0 m-auto items-center">
            <img :src="imageButtonSrc" class="size-6 md:size-8 xl:size-[50px]" />
            <span class="text-[12px] lg:text-[16px] font-semibold">{{props.text}}</span>
        </div>
    </button>
</template>

<script setup>
const props = defineProps({
    text: String,
    handleClick: Function,
    imageButtonSrc: {
        type: String,
        default: '/assets/img/ai3goc/icon/upload.svg',
    },
    className: {
        type: String,
        default: 'text-orion-primary border-2 border-ai3goc-primary'
    }
});
</script>
