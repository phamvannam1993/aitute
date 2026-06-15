<template>
    <div class="border-t border-gray-300 my-2 md:my-8"  v-if="props.formData?.length > 0"></div>
    <div class="flex items-center " v-if="props.formData?.length > 0">
        <!-- <div class="hidden md:flex md:size-[62px]"></div> -->
        <div class="flex flex-col flex-1" >
            <div class="flex items-center gap-3"  >
                <img src="/assets/img/ai3goc/logo/circle.svg" alt="step" class="w-[24px] h-auto" />
                <span class="text-[15px] lg:text-[18px] leading-none font-bold text-black">{{`Bổ sung thông tin cho ${selectedContent}`}}</span>
            </div>
            <div class="" v-for='(item, index) in props.formData' :key="index" >
                <div class="flex flex-col gap-2 mt-4 w-full">
                    <div v-if="item.type === 'text'" class="flex flex-col gap-1">
                        <label class="text-[14px] font-semibold text-black">{{index+1}}. {{ item.label }}</label>
                        <input v-model="item.value" class="w-full border border-gray-300 rounded-2xl p-3 text-[14px] text-black" />
                    </div>
                    <div v-else-if="item.type === 'textarea'" class="flex flex-col gap-1">
                        <label class="text-[14px] font-semibold text-black">{{index+1}}. {{ item.label }}</label>
                        <textarea v-model="item.value" class="w-full border border-gray-300 rounded-2xl p-3 text-[14px] text-black"></textarea>
                    </div>
                    <div v-else-if="item.type === 'select'" class="flex flex-col gap-1 md:flex-row items-center">
                        <label class="text-[14px] font-semibold text-black w-full">{{index+1}}. {{ item.label }}</label>
                        <select v-model="item.value" class="w-full max-w-96 border border-gray-300 rounded-2xl p-3 text-[14px] text-black">
                            <option v-for="(option, index) in item.options" :key="index" :value="option.value">{{ option.label }}</option>
                        </select>
                    </div>
                    <div v-else-if="item.type === 'radio'" class="text-black flex flex-col gap-4 max-h-[524px] overflow-y-scroll border-[1px] border-[#D4D4D4] rounded-[15px] p-4 lg:px-[90px] lg:py-6">
                        <div
                            class="flex items-center gap-5 cursor-pointer"
                            v-for="(option, i) in item.options"
                            :key="i"
                            @click="handleRadioSelect(item, option)"
                        >
                            <div
                                :class="option.isActive ? 'border-orion-sec' : 'border-gray-400'"
                                class="flex-shrink-0 size-6 rounded-full border-2 flex items-center justify-center transition-all"
                            >
                                <div class="size-[15px] bg-orion-sec rounded-full" v-if="option.isActive"></div>
                            </div>

                            <div class="relative w-full" @click="handleRadioSelect(item, option)">
                                <div
                                    :class="option.isActive ? 'text-orion-sec font-bold' : ''"
                                    class="relative flex items-center justify-between rounded-2xl text-black px-4 py-2 flex-1 gap-1 h-[40px] transition-all"
                                >
                                    <span class="select-none">{{ option.label }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-center gap-5 mt-7">
                <button
                    :disabled="loadingSubmit"
                    type="button" @click="props.handleButtonClick" class="flex justify-center items-center gap-1 w-full max-w-[180px] px-4 py-3 bg-ai3goc-bgclr text-white rounded-2xl font-bold">
                    <span v-if="!loadingSubmit">
                        Xác nhận
                    </span>
                    <LoadingCircle v-else size="!size-6" />
                    <div v-if="loadingSubmit" 
                        class="text-xs md:text-sm font-medium text-center p-0.5 leading-none rounded-full transition-all duration-300"
                        >{{ loadingPercent }} %</div>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
    import LoadingCircle from '@/Components/LoadingCircle.vue';
import { ref } from 'vue';
import { watch } from 'vue';

    const props = defineProps({
        open: Boolean,
        formData: Object,
        handleButtonClick: Function,
        loadingSubmit: Boolean,
        selectedContent:String,
    })

    const handleRadioSelect = (item, selectedOption) => {
        item.options.forEach((option) => {
            option.isActive = false;
        });

        selectedOption.isActive = true;

        item.value = selectedOption.value;
    };
    const loadingPercent = ref(0);

    const simulateLoading = () => {
    if (props.loadingSubmit && loadingPercent.value < 99) {
        const increment = Math.floor(Math.random() * 8) + 5; // Tăng ngẫu nhiên từ 5-12%
        loadingPercent.value = Math.min(loadingPercent.value + increment, 99);
        setTimeout(simulateLoading, 3000); // Cập nhật mỗi 5 giây
    }
    };
    watch(
        () => props.loadingSubmit,
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