<template>
    <div :class="`absolute inline-block ${customClass}`" @mousedown="drag" :style="{ left: `${virtual.x}px`, top: `${virtual.y}px` }">
        <template v-if="isShow">
            <slot></slot>
        </template>
        <template v-if="!preview">
            <span @click="() => resize(1)" class="absolute top-4 right-4 cursor-pointer px-3 py-1 rounded-full text-red-500 border border-red-500">+</span>
            <span @click="() => resize(-1)" class="absolute top-4 right-20 cursor-pointer px-3 py-1 rounded-full text-red-500 border border-red-500">-</span>
        </template>
    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";

const isShow = ref(false);
const virtual = defineModel();
const props = defineProps({
    preview: {
        type: Boolean,
        default: false,
    },
    customClass: {
        type: String,
        default: "",
    },
});

let eleCX = 0;
let eleCY = 0;
let eleOX = virtual.value.x;
let eleOY = virtual.value.y;
let check = false;

onMounted(() => {
    document.addEventListener("mouseup", drop);
    document.addEventListener("mousemove", move);

    setTimeout(() => {
        isShow.value = true;
    }, Number(virtual.value.delayTime || 0) + 100);
});

const drag = ({ clientX, clientY }) => {
    eleOX = virtual.value.x;
    eleOY = virtual.value.y;
    eleCX = clientX;
    eleCY = clientY;
    check = true;
};
const drop = () => {
    if (check) {
        check = false;
    }
};

const move = ({ clientX, clientY }) => {
    if (check) {
        virtual.value.x = eleOX + clientX - eleCX;
        virtual.value.y = eleOY + clientY - eleCY;
    }
};

const resize = (type) => {
    virtual.value.width = Number(virtual.value.width) + type * 5;
};
</script>
<style scoped></style>
