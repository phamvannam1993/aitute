<template>
    <div class="my-2 md:my-2"
         v-if="props.formData?.length > 0"
    ></div>
    <div class="w-full">
        <div class="flex items-center gap-5 mb-8 md:mb-[42px]" v-if="typeContent === 'Bài viết Quảng cáo sản phẩm' && props.formData?.length > 0">
            <img src="/assets/img/robot-1.png" alt="" class="size-[68px] md:size-[62px] object-contain flex-shrink-0">
            <p class="text-xs md:text-base text-ai3goc-bgclr">Bài viết quảng cáo sản phẩm là một bài giới thiệu Sản Phẩm một cách khéo léo và hấp dẫn.
                Bài viết sẽ sử dụng để đăng trên Fanpage của bạn hoặc đăng trên trang cá nhân của bạn. Sau khi tạo, bạn có thể đăng tải ngay lên Fanpage của bạn, hoặc đặt lịch đăng. </p>
        </div>
        <div class="flex items-center">
            <div class="hidden md:flex md:size-[62px]" v-if="typeContent === 'Bài viết Quảng cáo sản phẩm'"></div>
            <div class="flex flex-col flex-1"  v-if="props.formData?.length > 0">
                <div v-if="!showTitle" class="flex items-center gap-3">
                    <img src="/assets/img/ai3goc/logo/circle.svg" alt="step" class="w-[24px] h-auto" />
                    <span class="md:text-[20px] text-sm lg:text-[20px] leading-none font-bold text-black">Bổ sung thông tin</span>
                </div>
                <p class="font-bold text-sm md:text-[18px]" v-if="showTitle">Chọn tối đa 04 mục tiêu:</p>
                <div class="" v-for='(item, index) in props.formData' :key="index">
                    <div class="flex flex-col gap-2 mt-4 w-full">
                        <div v-if="item.type === 'text'" class="flex flex-col gap-1">
                            <label class="md:text-[19px] text-[14px] font-semibold text-black">{{index+1}}. {{ item.label }}</label>
                            <input v-model="item.value" class="w-full border border-gray-300 rounded-2xl p-3 text-[14px] text-black" />
                        </div>
                        <div v-else-if="item.type === 'textarea'" class="flex flex-col gap-1">
                            <label class="md:text-[19px] text-[14px] font-semibold text-black">{{index+1}}. {{ item.label }}</label>
                            <textarea v-model="item.value" class="w-full border border-gray-300 rounded-2xl p-3 text-[14px] text-black"></textarea>
                        </div>
                        <div v-else-if="item.type === 'select'" class="flex flex-col gap-2 mt-4 w-full">
                            <label class="md:text-[19px] text-[14px] font-semibold text-black w-full">{{index+1}}. {{ item.label }}</label>
                            <select v-model="item.value" class="w-full border border-gray-300 rounded-2xl p-3 text-[14px] text-black">
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
                                    :class="option.isActive ? 'border-ai3goc-primary' : 'border-gray-400'"
                                    class="flex-shrink-0 size-5 md:size-6 rounded-full border-2 flex items-center justify-center transition-all"
                                >
                                    <div class="size-3 md:size-[15px] bg-ai3goc-primary rounded-full" v-if="option.isActive"></div>
                                </div>

                                <div class="relative w-full" @click="handleRadioSelect(item, option)">
                                    <div
                                        :class="option.isActive ? 'text-ai3goc-primary font-bold' : ''"
                                        class="text-xs md:text-[19px] font-semibold relative flex items-center justify-between rounded-2xl text-black px-4 py-2 flex-1 gap-1 h-[40px] transition-all"
                                    >
                                        <span class="select-none text-xs md:text-[19px]">{{ option.label }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else-if="item.type === 'checkbox'" class="text-black flex flex-col md:gap-4 max-h-[524px] overflow-y-scroll border-[1px] border-[#D4D4D4] rounded-[15px] p-4 lg:px-[90px] lg:py-6">
                            <div
                                class="flex items-center gap-3 md:gap-5 cursor-pointer"
                                v-for="(option, i) in item.options"
                                :key="i"
                                @click="handleCheckBoxSelect(item, option)"
                            >
                                <div
                                    :class="option.isActive ? 'border-orion-orange' : 'border-gray-400'"
                                    class="flex-shrink-0 size-5 md:size-6 rounded-full border-2 flex items-center justify-center transition-all"
                                >
                                    <div class="size-3 md:size-[15px] bg-orion-orange rounded-full" v-if="option.isActive"></div>
                                </div>

                                <div class="relative w-full" @click.stop="handleCheckBoxSelect(item, option)">
                                    <div
                                        :class="option.isActive ? 'font-bold text-[#F55E00]' : 'text-black'"
                                        class="text-sm md:text-[15px] font-semibold relative flex items-center justify-between rounded-2xl  px-4 py-2 flex-1 gap-1 h-[40px] transition-all"
                                    >
                                        <span class="select-none text-sm md:text-[15px]">{{ option.label }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-5 mt-7">
                    <button
                        :disabled="loadingSubmit"
                        type="button" @click="props.handleButtonClick" class="text-sm md:text-[17px] relative gap-1 flex items-center justify-center h-[40px] md:h-[50px] place-items-center w-full max-w-[180px] px-4 bg-ai3goc-bgclr text-white rounded-2xl font-bold mg-b-20px">
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
    </div>
</template>

<script setup>
    import LoadingCircle from '@/Components/LoadingCircle.vue';
    import { ref, watch } from 'vue';
    import { toast } from "vue3-toastify";

    const props = defineProps({
        open: Boolean,
        formData: Object,
        handleButtonClick: Function,
        loadingSubmit: Boolean,
        typeContent:String,
        selectedContent:String,
        showTitle: {
            type: Boolean,
            default: true
        }
    })

    const handleRadioSelect = (item, selectedOption) => {
        item.options.forEach((option) => {
            option.isActive = false;
        });

        selectedOption.isActive = true;

        item.value = selectedOption.value;
    };

    const handleCheckBoxSelect = (item, selectedOption) => {
        const activeOptionsCount = item.options.filter(option => option.isActive).length
        if (activeOptionsCount > 3 && !selectedOption.isActive) {
            toast.error('Vui lòng lựa chọn tối đa 4 mục tiêu');
            return;
        }
        selectedOption.isActive = !selectedOption.isActive;
    };

    const loadingPercent = ref(0);

    // Hàm mô phỏng tiến độ loading
    const simulateLoading = () => {
    if (props.loadingSubmit && loadingPercent.value < 99) {
        const increment = Math.floor(Math.random() * 8) + 5; // Tăng ngẫu nhiên từ 5-12%
        loadingPercent.value = Math.min(loadingPercent.value + increment, 99);
        setTimeout(simulateLoading, 1000); // Cập nhật mỗi 5 giây
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
