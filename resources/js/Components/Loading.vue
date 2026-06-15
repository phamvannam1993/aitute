<template>
    <div :class="position" class=" inset-0 flex items-center justify-center z-[999] bg-black bg-opacity-30">
        <div class="flex flex-col items-center justify-center">
            <div class="loader"></div>
            <div v-if="time" class="text-white">{{ pageData.progress }}%</div>
        </div>
    </div>
</template>
<script setup>
import { reactive } from 'vue';

const { time } = defineProps({
    position: {
        type: String,
        default: 'fixed',
    },
    time: {
        type: Number,
        default: 0,
    }
});

const pageData = reactive({
    progress: 0,
});

function startProgress(durationInSeconds, callback) {
    let progress = 0;
    let maxProgress = 98;
    console.log('durationInSeconds', durationInSeconds);
    let fastPhase = Math.floor((2 / 3) * durationInSeconds * 1000);
    let slowPhase = durationInSeconds * 1000 - fastPhase; 

    let fastTarget = Math.floor(Math.random() * (85 - 75 + 1)) + 75;
    let slowTarget = maxProgress - fastTarget;

    let fastIntervalTime = fastPhase / fastTarget;
    let slowIntervalTime = slowPhase / slowTarget; 

    let phase = "fast";

    let interval = setInterval(() => {
        if (phase === "fast") {
            progress += 1;
            callback(progress);

            if (progress >= fastTarget) {
                phase = "slow";
                clearInterval(interval);
                interval = setInterval(() => {
                    progress += 1;
                    callback(progress);

                    if (progress >= maxProgress) {
                        clearInterval(interval);
                    }
                }, slowIntervalTime);
            }
        }
    }, fastIntervalTime);
}

startProgress(time, (progress) => {
    pageData.progress = progress;
});


</script>
<style>
.loader {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #24AA69;
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}
</style>