<template>
    <div :class="props.open ? 'flex-col lg:rounded-[35px] items-start' : 'flex-row lg:rounded-full items-center'" class="bg-white shadow-custom-shadow md:p-5 p-3 md:px-[80px] md:py-[20px] flex justify-between mt-4">
        <div class="flex items-center gap-2 mb-2 text-xs lg:text-sm mt-2 text-black lg:w-4/5">
            <Step class="flex-shrink-0" :step="getStepByMode()" step-name="Kết quả phân tích" />
        </div>
        <div v-if="props.open" class="w-full lg:w-4/5 mt-5 text-black text-xs lg:text-[15px] self-center">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col  gap-5 p-[12px] bg-[#F6F6F6] rounded-[35px]" v-for="(item, index) in props.list" :key="index">
                    <div @click="toggleItem(index,`buoc3_${index+1}`)" class="cursor-pointer flex items-center gap-1 text-ai3goc-primary">
                        <img src="/assets/img/ai3goc/logo/circle.svg" alt="step" class="w-[16px] h-auto" />
                        <span class="text-[12px] lg:text-[14px] leading-none font-bold">{{index + 1}}. {{ item.name }}</span>
                    </div>
                    <LoadingCircle loading-title="Hệ thống đang xử lý. Xin vui lòng đợi." v-if="item.isLoading" />
                    <div v-else class="border border-gray-300 bg-white p-4 rounded-2xl max-h-[500px] overflow-y-auto container-content" v-if="item.isActive">
                        <div v-html="marked(item.content)"></div>
                    </div>
                </div>
            </div>
            <LoadingCircle v-if="loadingSubmit" loading-title="Hệ thống đang xử lý. Xin vui lòng đợi." />
        </div>


        <div @click="toggleSection()" :class="props.open ? 'self-end' : ''" class="text-orion-sec mt-2 font-medium flex items-center cursor-pointer text-xs md:text-sm">
            <span>{{ props.open ? "Thu gọn" : "Hiển thị" }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>
</template>

<script setup>
    import LoadingCircle from "@/Components/LoadingCircle.vue";
    import Step from "@/Components/Step.vue";
    import { marked } from 'marked';

    const props = defineProps({
        open: Boolean,
        list: Object,
        handleSubmit: Function,
        loadingSubmit: Boolean,
        toggleItem: Function,
        toggleSection: Function,
        refScroll: Object,
        analysisName: String,
        mode: String,
    })

    console.log(props.open);
    const getStepByMode = () => {
        return props.mode === 'basic' ? 3 : props.mode === 'expert' ? 6 : 4;
    }


</script>

<style >
    .container-content strong{
        margin-top: 8px;
        display: block;
    }
    .container-content hr{
        margin: 8px 0;
    }

    .container-content p,
    .container-content h1,
    .container-content h2,
    .container-content h3,
    .container-content h4,
    .container-content h5,
    .container-content h6,
    .container-content ul,
    .container-content ol,
    .container-content li,
    .container-content blockquote,
    .container-content pre,
    .container-content code,
    .container-content table,
    .container-content tr,
    .container-content th,
    .container-content td {
        font-size: 15px;
    }

    .container-content p,h3,ul {
        margin-bottom: 10px;
        line-height: 1.6;
    }

    .container-content strong {
        display: inline;
    }

</style>
