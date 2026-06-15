<template>
    <div class="text-left bg-package rounded-3xl px-5 py-4 sm:py-5 max-h-[80vh] wrapper-package w-[340px] sm:w-[380px] lg:w-[450px]">
        <div class="package-name w-full mb-4 flex items-center justify-center uppercase text-[#FEF601] font-bold text-xl">
            {{ credit_package.name }}
        </div>
        <div class="flex items-center justify-between text-center mb-2 gap-2">
            <p v-for="duration in availableDurations" :key="duration" @click="selectedDuration = duration" :class="selectedDuration === duration ? 'bg-[#2DA1B9] text-white' : 'bg-white text-black'" class="flex-1 px-2 py-1.5 rounded-full cursor-pointer uppercase text-xs sm:text-sm lg:text-lg lx:text-xl font-bold flex items-center justify-center">{{ duration }} Tháng</p>
        </div>

        <div class="bg-white overflow-auto h-[250px] sm:h-[400px] rounded-2xl pt-2 pb-4 px-4 md:px-7 mb-4">
            <p class="text-[#EB8100] mb-3 text-lg font-bold">Giá bán: {{ displayPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") }} VNĐ</p>
            <p class="text-base font-bold text-[#0E7A94]">Quyền lợi</p>
            <p>
                Tặng <strong>{{ displayCredit.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") }} điểm</strong> để sử dụng
            </p>
            <p>
                Thời hạn sử dụng: <strong>{{ selectedDuration }} Tháng</strong>
            </p>

            <div class="relative">
                <div class="mb-3 text-xs">
                    <div class="mb-2" v-html="credit_package.title"></div>
                    <div v-html="credit_package.content" class="package-content relative z-10"></div>
                </div>
                <div class="package-content-img"></div>
            </div>

            <div v-if="availableDurations.length > 1 || credit_package.name.trim().toLowerCase() === 'vip'" class="text-center text-xs">
                <span class="text-[#0E7A94] font-bold">Lưu ý:</span>
                <span>Khi sử dụng hết điểm, nạp tiền theo nhu cầu sử dụng với chi phí: 499.000 VNĐ = 499.000 điểm</span>
            </div>

            <!-- <div v-else class="text-center text-xs"><span class="text-[#0E7A94] font-bold">Lưu ý:</span> <span>Gói trải nghiệm được sử dụng các tính năng trong vòng 15 ngày với 300.000 điểm. Khi sử dụng hết điểm hoặc hết thời hạn sử dụng cần nâng cấp lên gói cao hơn.</span></div> -->
        </div>

        <div class="text-center w-full">
            <a target="_blank" :href="credit_package.link" class="bg-[#0E7A94] bg-gradient-to-r from-[#f8941a] to-[#fad03e] px-6 py-2 text-white rounded-3xl text-sm ls:text-lg lx:text-xl"> MUA NGAY </a>
        </div>
    </div>
</template>
<script setup>
import { ref } from "vue";
import "./style.css";
import { computed } from "vue";
const { credit_package } = defineProps({
    credit_package: {
        type: Object,
    },
});
const availableDurations = Object.keys(credit_package.durations);

// Chọn mặc định là cái đầu tiên
const selectedDuration = ref(availableDurations[0]);

const displayPrice = computed(() => {
    return credit_package.durations?.[selectedDuration.value]?.price ?? 0;
});

const displayCredit = computed(() => {
    return credit_package.durations?.[selectedDuration.value]?.credit ?? 0;
});
</script>
