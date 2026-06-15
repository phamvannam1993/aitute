<template>
    <div class="flex flex-col flex-1">
        <div class="w-full flex flex-col" v-if="!props.data?.overview?.isLoading">
            <!-- <div class="border-t border-gray-300 my-2 md:my-8"></div> -->
            <div class="" v-for='(item, index) in props.data?.overview?.formData' :key="index">
                <div class="flex flex-col gap-2 mt-4 w-full">
                    <div v-if="item.type === 'text'" class="flex flex-col gap-1">
                        <label class="text-sm md:text-[19px] font-semibold text-black">{{ index + 1 }}. {{ item.label }}</label>
                        <input v-model="item.value" class="w-full border border-gray-300 rounded-2xl p-3 text-[14px] text-black" />
                    </div>
                    <div v-else-if="item.type === 'textarea'" class="flex flex-col gap-1">
                        <label class="text-sm md:text-[19px] font-semibold text-black">{{ index + 1 }}. {{ item.label }}</label>
                        <textarea v-model="item.value" class="w-full border border-gray-300 rounded-2xl p-3 text-[14px] text-black"></textarea>
                    </div>
                    <div v-else-if="item.type === 'select'" class="flex flex-col gap-1 md:flex-row items-center">
                        <label class="text-sm md:text-[16px] font-semibold text-black w-full">{{ index + 1 }}. {{ item.label }}</label>
                        <select v-model="item.value" class="w-full max-w-96 border border-gray-300 rounded-2xl p-3 text-[14px] md:text-[14px] text-black">
                            <option v-for="(option, index) in item.options" :key="index" :value="option.value">{{ option.label }}</option>
                        </select>
                    </div>
                </div>

            </div>
            <div class="flex items-center justify-center gap-5 mt-7">
                <button :disabled="props.data.overview?.isLoading" type="button" @click="props.handleButtonClick('overview')"
                    class="md:text-[19px] text-sm flex items-center justify-center gap-1 w-full max-w-[180px] px-4 py-3 bg-ai3goc-bgclr text-white rounded-2xl font-bold">
                    <span v-if="!props.data?.big_ideas?.isLoading">
                        Tạo ý tưởng
                    </span>
                    <LoadingCircle v-else size="!size-6" />
                    <div v-if="props.data?.big_ideas?.isLoading"
                        class="text-xs md:text-sm font-medium text-center p-0.5 leading-none rounded-full transition-all duration-300"
                        >{{ loadingPercent }} %</div>
                </button>
            </div>
        </div>
        <div class="w-full  flex flex-col" v-if="!props.data?.big_ideas?.isLoading && props.data?.big_ideas?.formData">
            <div class="border-t border-gray-300 my-2 md:my-8"></div>
            <div class="flex items-center gap-3 mt-4">
                <img src="/assets/img/ai3goc/logo/circle.svg" alt="step" class="w-[24px] h-auto" />
                <span class="text-sm md:text-[19px] lg:text-[19px] leading-none font-bold">Lựa chọn Big Idea</span>
            </div>
            <div class="flex flex-col gap-8 border border-gray-300 rounded-2xl p-4 md:p-8 mt-6">
                <div class="flex items-start gap-6" v-for='(item, index) in props.data?.big_ideas?.formData' :key="index">
                    <div @click="toggleBigIdeaMusic(index)" class="flex gap-2 items-center flex-shrink-0">
                        <div :class="item.isActive ? 'border-orion-orange' : 'border-gray-400'"
                            class="size-6 rounded-full border-2 flex items-center justify-center cursor-pointer transition-all">
                            <div class="size-[15px] bg-orion-orange rounded-full" v-if="item.isActive">
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col flex-1 gap-3">
                        <p @click="toggleBigIdeaMusic(index)" class="cursor-pointer font-bold md:text-[15px] leading-none text-sm text-black" :class="{ 'text-orion-orange': item.isActive }">
                            Big idea {{ index + 1}}:"{{ item.y_tuong }}"
                        </p>
                        <p class="text-sm md:text-[14px] font-normal" @click="toggleBigIdeaMusic(index)">
                            {{ item.content }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-center gap-5 my-5 ">
                <button
                    @click="props.handleButtonClick('lyric')"
                    :disabled="props.data?.lyric?.isLoading"
                    type="button" class="md:text-[19px] text-sm flex items-center justify-center gap-1 w-full max-w-[180px] px-4 py-3 bg-ai3goc-bgclr text-white rounded-xl md:rounded-2xl font-bold">
                    <LoadingCircle v-if="props.data?.lyric?.isLoading" size="!size-6" />
                    <span v-else>Xác nhận</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import LoadingCircle from '@/Components/LoadingCircle.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    open: Boolean,
    data: Object,
    handleButtonClick: Function,
    toggleBigIdeaMusic: Function,
    loading: Boolean,
})

    const loadingPercent = ref(0);

    const simulateLoading = () => {
    // Nếu có bất kỳ trạng thái loading nào đang hoạt động và loadingPercent chưa đạt 99%
    if ((props.loading || props.data?.overview?.isLoading || props.data?.big_ideas?.isLoading) && loadingPercent.value < 99) {
        const increment = Math.floor(Math.random() * 8) + 5; // Tăng ngẫu nhiên từ 5-12%
        loadingPercent.value = Math.min(loadingPercent.value + increment, 99);
        setTimeout(simulateLoading, 2000); // Cập nhật mỗi 2 giây
    }
};
    watch(
        () => props.loading,
        (newVal) => {
            if (newVal) {
            loadingPercent.value = 0;
            simulateLoading();
            } else {
            loadingPercent.value = 0;
            }
        }
    );
    watch(
        () => props.data?.big_ideas?.isLoading,
        (newVal) => {
            if (newVal) {
            loadingPercent.value = 0;
            simulateLoading();
            } else {
            loadingPercent.value = 0;
            }
        }
    );
    watch(
        () => props.data?.overview?.isLoading,
        (newVal) => {
            if (newVal) {
                loadingPercent.value = 0;
                simulateLoading();
            } else {
                loadingPercent.value = 0;
            }
        }
    );
</script>

<style scoped></style>
