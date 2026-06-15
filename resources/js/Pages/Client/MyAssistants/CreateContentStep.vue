<template>
    <div ref="provideInformation" :class="open ? 'flex-col lg:rounded-[35px] items-start' : 'flex-row lg:rounded-full items-center'"
        class="bg-white shadow-custom-shadow md:p-5 p-3 flex justify-between mt-4">
        <div class="flex items-center gap-2 mb-2 text-xs lg:text-sm mt-2 text-black">
            <Step :step="2" stepName="Tạo nội dung" />
        </div>
        <div v-if="open" class="w-full lg:w-4/5 mt-5 text-black text-xs lg:text-[15px] self-center">
            <div class="flex flex-col gap-4" v-if="listCreateContent">
                <div class="flex items-center gap-5" v-for="(item, index) in listCreateContent" :key="index">
                    <div @click="toggleItem(index)" :class="item.isActive ? 'border-orion-sec' : 'border-gray-400'"
                        class="flex-shrink-0 size-6 rounded-full border-2 flex items-center justify-center cursor-pointer transition-all">
                        <div class="size-[15px] bg-orion-sec rounded-full" v-if="item.isActive">
                        </div>
                    </div>
                </div>
                <div class="flex justify-center gap-4 mt-4 mb-4">
                    <button type="button" class="w-full max-w-[180px] px-4 py-3 border border-gray-300 text-gray-500 rounded-xl text-sm lg:text-base lg:rounded-2xl font-bold">Hủy</button>
                    <button type="button" @click="handleButtonClick('finalTarget', $event, refScroll, 4, null)"
                        class="w-full max-w-[180px] px-4 py-3 bg-orion-primary text-white rounded-xl text-sm lg:text-base lg:rounded-2xl font-bold">
                        <LoadingCircle v-if="loadingSubmit" />
                        <span v-else>Xác nhận</span>
                    </button>
                </div>
            </div>
            <LoadingCircle v-if="loadingSubmit" loading-title="Hệ thống đang xử lý. Xin vui lòng đợi." />
         </div>


        <div @click="toggleSection(3)" :class="open ? 'self-end' : ''" class="text-orion-sec font-medium flex items-center cursor-pointer">
            <span>{{ open ? "Thu gọn" : "Hiển thị" }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>
</template>

<script>
const props = defineProps({
    open: Boolean,
    listCreateContent: Object,
    handleSubmit: Function,
    loadingSubmit: Boolean,
    toggleItem: Function,
    toggleSection: Function,
    refScroll
})
</script>

<style lang="scss" scoped></style>
