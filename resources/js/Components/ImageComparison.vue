<template>
    <div class="comparison-wrapper">
        <img-comparison-slider class="slider-wrapper">
            <img slot="first" :src="props.beforeImage" class="comparison-image" :alt="props.beforeAlt || 'Before image'" />
            <img slot="second" :src="props.afterImage" class="comparison-image" :alt="props.afterAlt || 'After image'" />
            <div slot="handle" class="custom-handle cursor-ew-resize" xmlns="http://www.w3.org/2000/svg" width="100" viewBox="-8 -3 16 6">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </div>
        </img-comparison-slider>
    </div>
</template>

<script setup>
import { ImgComparisonSlider } from '@img-comparison-slider/vue';

const props = defineProps({
    beforeImage: {
        type: String,
        required: true
    },
    afterImage: {
        type: String,
        required: true
    },
    beforeAlt: {
        type: String,
        default: ''
    },
    afterAlt: {
        type: String,
        default: ''
    }
});
</script>

<style scoped>
.comparison-wrapper {
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
    position: relative;
}

.slider-wrapper {
    --divider-width: 3px;
    --divider-color: white;
    --default-handle-color: white;
    width: 100%;
    height: 100%;
    position: relative;
    border-radius: 10px;
    overflow: hidden;
}

.custom-handle {
    display: grid;
    grid-auto-flow: column;
    place-content: center;
    flex-shrink: 0;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    border-style: solid;
    border-width: 2px;
    pointer-events: auto;
    backdrop-filter: blur(7px);
    background-color: rgba(0, 0, 0, 0.125);
    box-shadow: rgba(0, 0, 0, 0.35) 0px 0px 4px;
}
.comparison-image{
    width: 100%;
    height: auto;
    display: block;
    border-radius: 10px;
    object-fit: cover;
    object-position: center;
    pointer-events: none;
}
</style>
