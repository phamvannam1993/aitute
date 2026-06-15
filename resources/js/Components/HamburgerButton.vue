<template>
    <button
        class="inline-block md:hidden cursor-pointer space-y-1 w-8 h-12"
        @click="showNavBarLinks()"
        :class="{ close: showNavLinks }"
    >
        <div
            class="h-1 w-full bg-neutral-700 transition-all ease-linear duration-150 bar1"
        ></div>
        <div
            class="h-1 w-full bg-neutral-700 transition-all ease-linear duration-150 bar2"
        ></div>
        <div
            class="h-1 w-full bg-neutral-700 transition-all ease-linear duration-150 bar3"
        ></div>
    </button>
</template>

<script setup>
import { ref } from "vue";
const emit = defineEmits(["click"]);
const showNavLinks = ref(false);
function showNavBarLinks() {
    showNavLinks.value = !showNavLinks.value;
    emit("click");
}
</script>

<style scoped>
/* Rotate first bar */
.close .bar1 {
    transform: translate(0, 100%) rotate(-45deg);
}

/* Fade out the second bar */
.close .bar2 {
    display: none;
}

/* close last bar */
.close .bar3 {
    transform: translate(0, -100%) rotate(45deg);
}
</style>
