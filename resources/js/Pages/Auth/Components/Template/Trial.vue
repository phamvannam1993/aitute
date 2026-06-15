<template>
    <div
        class="text-left bg-package rounded-3xl  px-5 py-2 sm:py-5 h-[97vh] wrapper-package w-[300px] sm:w-[380px] lg:w-[450px]">
        <div class="mb-2 sm:mb-7">
            <p
                class="package-name flex items-center justify-center uppercase text-center text-xs sm:text-sm lg:text-lg lx:text-xl">
                <span class="text-white font-bold mr-3 ">
                    {{ credit_package.name }}
                </span>
                <span class="text-[#fff901] font-bold">
                    {{ credit_package.price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") }} VNĐ
                </span>
            </p>
        </div>
        <div class="bg-white rounded-2xl pt-2 pb-4 px-7 mb-4 text-xs sm:text-sm lg:text-lg lx:text-xl">
            <p class="text-xl font-bold text-[#097bfe] mb-3">Quyền lợi</p>
            <div class="border-[#097bfe] border-2 rounded-2xl p-3 mb-3 text-xs">
                <p>Tặng <strong> {{ credit_package.credit.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") }} điểm
                    </strong> để sử dụng </p>
            </div>
            <div class="border-[#097bfe] border-2 rounded-2xl p-3 mb-3 text-xs ">
                <p>Thời hạn sử dụng: <strong> {{ credit_package.duration }} </strong> </p>
            </div>
            <div class="border-[#097bfe] border-2 rounded-2xl p-3 mb-3 text-xs">
                <div v-html="credit_package.title"> </div>
            </div>
            <div class="border-[#097bfe] border-2 rounded-2xl p-3 mb-3 text-xs">
                <div v-html="credit_package.content"> </div>
            </div>
        </div>

        <div class=" text-center w-full">
            <a target="_blank" :href="credit_package.link"
                class="bg-[#097bfe] px-6 py-2 text-white rounded-3xl text-sm ls:text-lg lx:text-xl">
                MUA NGAY
            </a>
        </div>
    </div>
</template>
<script setup>
import "./style.css";
const { credit_package } = defineProps({
    credit_package: {
        type: Object,
    },
});

</script>
