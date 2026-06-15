<template>
    <div class="w-[160px] md:w-[230px] flex items-center text-black gap-2 flex-shrink-0 mr-6">
        <div class="flex-shrink-0 flex items-center justify-center w-fit text-[10px] md:text-sm font-bold py-1 px-2 rounded-2xl text-white bg-primary-color">
            <img class="h-2 w-4" src="/assets/img/aiwow/right_arrow.png" />
            <p>{{ stepLabel }}</p>
        </div>
        <span class="font-bold text-[10px] md:text-sm">{{ stepName }}</span>
    </div>
</template>

<script setup>
const props = defineProps({
    stepLabel: String,
    stepName: String
});
</script>
