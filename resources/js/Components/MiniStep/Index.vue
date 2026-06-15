<template>
    <label :for="forName" class="text-black font-semibold text-[14px] cursor-pointer">
        <div class="flex items-center gap-1">
            <div class="flex items-center rounded-full bg-ai3goc-primary w-fit"
            :class="{
                    'py-1.5 px-2': isFacebookStep, 
                    'py-1.5 px-3': !isFacebookStep
                }"
            >
                <!-- <img src="/assets/img/aiwow/icon/label-arrow.svg" alt="step" class="w-[16px] h-auto" /> -->
                <span class="text-white text-[12px] lg:text-[14px] leading-none font-bold">Bước {{ step }}</span>
            </div>
            <span 
                class="font-bold leading-none flex-1" 
                :class="{
                    'text-[12px] lg:text-[14px]': isFacebookStep, 
                    'text-[14px]': !isFacebookStep
                }">
                {{ stepName }}
            </span>
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
        isFacebookStep: {
            type: Boolean,
            default: false,
        }
    });
</script>