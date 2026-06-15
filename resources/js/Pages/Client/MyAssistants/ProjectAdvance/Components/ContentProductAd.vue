<template>
    <div class="w-full">
        <div class="flex items-center gap-5 mb-8 md:mb-[42px]" v-if="typeContent === 'Bài viết Quảng cáo sản phẩm' && formData?.length > 0">
            <img src="/assets/img/robot-1.png" alt="" class="size-[68px] md:size-[62px] object-contain flex-shrink-0">
            <p class="text-xs md:text-base text-aiwow-primary">Bài viết quảng cáo sản phẩm là một bài giới thiệu Sản Phẩm một cách khéo léo và hấp dẫn.
                Bài viết sẽ sử dụng để đăng trên Fanpage của bạn hoặc đăng trên trang cá nhân của bạn. Sau khi tạo, bạn có thể đăng tải ngay lên Fanpage của bạn, hoặc đặt lịch đăng. </p>
        </div>
        <div class="flex items-center">
            <div class="hidden md:flex md:size-[62px]" v-if="typeContent === 'Bài viết Quảng cáo sản phẩm'"></div>
            <div class="flex flex-col flex-1">
                <div class="" v-for='(item, index) in formData' :key="index">
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
                        <div v-else-if="item.type === 'radio'" class="text-black flex flex-col md:gap-4 max-h-[524px] overflow-y-scroll border-[1px] border-[#D4D4D4] rounded-[15px] p-4 lg:px-[90px] lg:py-6">
                            <div
                                class="flex items-center gap-3 md:gap-5 cursor-pointer"
                                v-for="(option, i) in item.options"
                                :key="i"
                                @click="handleRadioSelect(item, option)"
                            >
                                <div
                                    :class="option.isActive ? 'border-aiwow-sec' : 'border-gray-400'"
                                    class="flex-shrink-0 size-5 md:size-6 rounded-full border-2 flex items-center justify-center transition-all"
                                >
                                    <div class="size-3 md:size-[15px] bg-aiwow-sec rounded-full" v-if="option.isActive"></div>
                                </div>

                                <div class="relative w-full" @click="handleRadioSelect(item, option)">
                                    <div
                                        :class="option.isActive ? 'text-aiwow-sec font-bold' : ''"
                                        class="text-xs md:text-sm font-semibold relative flex items-center justify-between rounded-2xl text-black px-4 py-2 flex-1 gap-1 h-[40px] transition-all"
                                    >
                                        <span class="select-none text-xs md:text-sm">{{ option.label }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-5 mt-7">
                    <button
                        :disabled="loading"
                        type="button" @click="sendDataInfo()" class="w-full max-w-[180px] px-4 py-3 bg-aiwow-primary text-white rounded-2xl font-bold">
                        <span v-if="!loading">
                            Xác nhận
                        </span>
                        <div v-else class="flex items-center justify-center gap-2">
                            <LoadingCircle size="!size-6" />
                            <span>{{ loadingPercent }} %</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import LoadingCircle from '@/Components/LoadingCircle.vue';

import { ref, watch, defineEmits } from 'vue';
const emit = defineEmits([]);
    const loadingPercent = ref(0)
    const loading = ref(false)
    const props = defineProps({
        open: Boolean,
        formData: Object,
        handleButtonClick: Function,
        typeContent:String,
        selectedContent:String,
    })
    const formData = ref(props.formData)
    const sendDataInfo = () => {
       loading.value = true
       emit('sendDataInfo', formData.value)
    }
    const updateData = (data) => {
        formData.value = data
    }

    const handleRadioSelect = (item, selectedOption) => {
        item.options.forEach((option) => {
            option.isActive = false;
        });

        selectedOption.isActive = true;

        item.value = selectedOption.value;
    };
    const updateIsLoading = () => {
        loading.value = false
    }
    setInterval(async () => {
        if(loading.value) {
            if(loadingPercent.value < 99) {
                loadingPercent.value =  loadingPercent.value + 1
            }
        } else if(loadingPercent.value < 99 && loadingPercent.value > 0) {
            for(var idx = 1; idx < 10; idx++) {
                if(loadingPercent.value < 99) {
                    loadingPercent.value =  loadingPercent.value + 1
                }
            }
            if(loadingPercent.value >= 99) {
                loadingPercent.value = 0
            }
        }
    }, 300)

    defineExpose({ updateData, updateIsLoading });
</script>

<style scoped></style>
