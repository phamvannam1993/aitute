<template>
    <label :for="forName" class="text-black font-semibold text-[10px] lg:text-[14px] cursor-pointer">
        <div class="flex items-center gap-1">
            <div class=" text-center px-2 rounded-full bg-ai3goc-primary min-w-16 md:min-w-[90px] h-[22px] md:h-[26px]">
                <span class="text-white text-sm lg:text-base leading-[22px] md:leading-[26px]">Bước {{ step }}</span>
            </div>
            <span class="text-sm lg:text-base leading-none">{{ stepName }}</span>
        </div>
    </label>
</template>

<script setup>

defineProps({
    step: {
        type: Number,
    },
    stepName: {
        type: String,
    },
    forName: {
        type: String,
    },
});
</script>