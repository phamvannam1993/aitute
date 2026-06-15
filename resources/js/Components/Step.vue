<template>
    <label :for="forName" class="text-black font-semibold text-[14px] cursor-pointer my-auto">
        <div class="flex items-center gap-1">
            <div class="flex items-center justify-center gap-1 py-1 md:py-1.5 px-2 md:px-5 rounded-full bg-ai3goc-primary min-w-[60px]">
                <!-- <img src="/assets/img/aiwow/right_arrow.png" alt="step" class="w-5 h-auto" /> -->
                <!-- <img src="/assets/img/orion/icon/label-arrow.svg" alt="step" class="w-[16px] h-auto" /> -->
                <span :class="className" class="text-white text-sm font-bold">Bước {{ step }}</span>
            </div>
            <div>
                <div class="flex">
                    <span :class="className" class="font-bold text-sm">{{ stepName }} </span>
                    <span v-if="required" class="font-bold text-red-500">*</span>
                </div>
                <span v-if="subInfo" class="italic text-sm font-normal text-ai3goc-primary">{{ subInfo }}</span>
            </div>
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
    subInfo: {
        type: String,
    },
    required: {
        type: Boolean,
        default: false,
    },
    className: {
        type: String,
        default: 'md:text-base',
    }
});


</script>
