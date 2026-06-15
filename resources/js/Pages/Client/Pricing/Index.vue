<template>
    <Head title="Nâng cấp tài khoản - AI 3 GỐC - Cộng đồng AI tử tế" />
    <div class="flex flex-col lg:pt-[20px] lg:px-[40px] bg-white h-screen">
        <header class="flex items-center justify-between px-1 mt-[60px] sm:mt-0">
            <div class="flex items-center gap-1">
                <a href="/">
                    <img
                        src="/assets/img/orion/icon/header/home.svg"
                        alt="Trang chủ"
                        class="w-auto h-[19px] md:h-[24px] min-h-[19px] min-w-[18px]"
                    />
                </a>
                <a :href="route('home.index')" class="font-medium text-black text-[16px] md:text-[20px] line-clamp-1">
                        / Nâng cấp tài khoản
                </a>
            </div>
        </header>
        <div class="flex-1 flex items-center justify-center">
            <div v-for="(product, index) in filteredList" :key="index" class="flex flex-col items-center border-[4px] border=[#d4d4d4] rounded-[40px] lg:rounded-[60px] py-2 mx-auto w-[95%] md:w-[640px] gap-1 lg:gap-2 md:gap-2">
                <div class="flex items-center py-3 px-6 gap-2">
                    <img src="/assets/img/ai3goc/logo/circle.svg" alt="Robo Icon" class="w-auto h-[40px] md:h-[64px]" />
                    <div class="flex flex-col items-center justify-center w-full">
                        <span class="font-bold text-orion-primary text-[16px] md:text-[24px] uppercase">Nâng cấp tài khoản AI 3 GỐC</span>
                        <span class="text-[8px] md:text-[16px] text-center text-[#7C7C7C]">Tài khoản cá nhân của bạn sẽ được nâng cấp và bổ sung các tính năng giúp người dùng làm việc thuận tiện hơn trên AI 3 GỐC</span>
                    </div>
                </div>
                <img src="/assets/img/banner-pricing.png" alt="Robo Icon" class="w-full h-auto rounded-tl-[32px] rounded-tr-[32px] lg:rounded-tl-[80px] lg:rounded-tr-[80px]" />
                <div class="flex items-center justify-between w-full py-3 px-6">
                    <div class="relative flex items-center gap-2 justify-start">
                        <button @click="
                            checkoutAlepay(
                                product.id,
                                discountId,
                                'vi',
                                'QRCode'
                            )
                            "
                            class="hover:scale-[1.05] transition-all duration-300 rounded-full w-[148px] lg:w-[280px] py-1 md:py-2 px-2 md:px-4 bg-gradient-to-r from-[#207A91] to-[#149CBE] hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300  text-white font-semibold text-[16px] lg:text-[24px] md:text-2xl">
                            Chuyển khoản
                        </button>
                    </div>
                    <div class="relative flex items-center gap-2 justify-start">
                        <button @click="
                            checkoutAlepay(
                                product.id,
                                discountId,
                                'vi',
                                'VISA'
                            )
                            "
                            class="hover:scale-[1.05] transition-all duration-300 rounded-full w-[148px] lg:w-[280px] py-1 md:py-2 px-2 md:px-4 bg-gradient-to-r from-[#F7941D] via-[#FABD34] to-[#FBD240] hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-yellow-300 text-white font-semibold text-[16px] lg:text-[24px] md:text-2xl">
                            Visa
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="flex flex-col justify-evenly bg-no-repeat bg-center bg-cover overflow-hidden mx-auto w-[95%] md:w-[740px] md:h-[720px] 3xl:h-screen rounded-[25px] relative" style="background-image: url('/assets/img/orion/bg/price.png');">
            <div class=" absolute left-[5%] top-[5%]">
                <img src="/assets/img/orion/logo/slogan-white.png" alt="" class="w-[72px] lg:w-[123px]" />
            </div>
            <div class="flex flex-col md:flex-row items-center justify-center gap-4">
                <div v-for="(product, index) in filteredList" :key="index"
                    class="w-[320px] md:w-[520px] rounded-lg p-2 ">
                    <div class="w-full rounded-lg bg-center bg-cover p-4 flex items-center justify-center">
                        <div class="flex flex-col items-center justify-center rounded-b-lg pt-8 lg:pb-4 ">
                            <div
                                class="flex items-center justify-center w-[320px] bg-no-repeat bg-center bg-cover rounded-full" style="background-image: url('/assets/img/orion/deposit.svg');">
                                <span class="text-white font-bold text-[16px] lg:text-[32px] uppercase">
                                    Nạp tiền
                                </span>
                            </div>
                            <div class="mt-2 md:mt-4 z-10">
                                <div class="bg-center bg-contain bg-no-repeat w-[280px] lg:w-[440px] h-auto flex items-center justify-center py-2 px-6 md:py-4 md:px-12"
                                    style="background-image: url('/assets/img/orion/price-frame.svg');">
                                    <p class="font-semibold text-[20px] md:text-[42px] my-2 text-yellow-300">
                                        {{ formatNumber(product.price_actual) }} VNĐ
                                    </p>
                                </div>
                                <div class="flex justify-center">
                                    <img src="/assets/img/orion/price-arrow-down.svg" alt="" class="w-[16px] md:w-[62px]" />
                                </div>
                                <div class="bg-center bg-contain bg-no-repeat w-auto h-auto flex items-center justify-center py-2 px-6 md:py-4 md:px-12"
                                    style="background-image: url('/assets/img/orion/price-frame.svg');">
                                    <p class="font-semibold text-[20px] md:text-[42px] my-2 text-yellow-300">
                                        {{ product.credit }} ĐIỂM
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-col items-center w-full md:px-8 mt-6">
                                <div class="relative flex items-center gap-2 justify-start">
                                    <img src="/assets/img/orion/price-arrow-right.svg" alt="" class="absolute top-1/2 -translate-y-1/2 -left-8 lg:-left-14 w-[24px] md:w-[48px]" />
                                    <button @click="
                                        checkoutAlepay(
                                            product.id,
                                            discountId,
                                            'vi',
                                            'QRCode'
                                        )
                                        "
                                        class="hover:scale-[1.05] transition-all duration-300 rounded-full w-[148px] lg:w-[280px] py-1 md:py-2 px-2 md:px-4 my-1 bg-gradient-to-r from-[#39B54A] via-[#5EBC45] to-[#8DC63F] hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300  text-white font-bold text-[16px] lg:text-[32px] md:text-2xl">
                                        Chuyển khoản
                                    </button>
                                </div>
                                <div class="relative flex items-center gap-2 justify-start">
                                    <img src="/assets/img/orion/price-arrow-right.svg" alt="" class="absolute top-1/2 -translate-y-1/2 -left-8 lg:-left-14 w-[24px] md:w-[48px]" />
                                    <button @click="
                                        checkoutAlepay(
                                            product.id,
                                            discountId,
                                            'vi',
                                            'VISA'
                                        )
                                        "
                                        class="hover:scale-[1.05] transition-all duration-300 rounded-full w-[148px] lg:w-[280px] py-1 md:py-2 px-2 md:px-4  my-1 bg-gradient-to-r from-[#F7941D] via-[#FABD34] to-[#FBD240] hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-yellow-300 text-white font-bold text-[16px] lg:text-[32px] md:text-2xl">
                                        Visa
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
    <div v-if="stateLoading" class="fixed inset-0 flex items-center justify-center z-[104] bg-black bg-opacity-30">
        <div class="loader"></div>
    </div>
</template>
<script setup>
import { ref, computed } from "vue";
import Layout from "@/Layouts/Client/ClientLayout.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({
    list: Array
});
defineOptions({ layout: Layout });

const activeMonth = ref(1);
const discountId = ref("");
const stateLoading = ref(false);
let checkoutCalled = ref(false);
const urlParams = new URLSearchParams(window.location.search);

const checkoutAlepay = async (productId, discountId, lang, type) => {
    stateLoading.value = true;
    try {
        if (checkoutCalled.value) {
            console.log("Thử lại sau", checkoutCalled.value);
            stateLoading.value = false;
            return;
        }

        checkoutCalled.value = true;

        const data = {
            products: productId,
            couponId: discountId,
            language: lang,
            request_path: urlParams.get("request_path") || "",
            type: type,
        };
        const res = await axios.post("pricing/alepay", data);
        window.location.href = res?.data?.url;
        setTimeout(() => {
            stateLoading.value = false;
            checkoutCalled.value = false;
        }, 1000);
    } catch (error) {
        if (error?.response?.data?.message === "Unauthorized") {
            isModalOpen.value = true;
            stateLoading.value = false;
            alias_product_id = productId;
            alias_discount_id = discountId;
            alias_lang = lang;
            alias_type = type;
        }
        console.error(error?.response?.data?.message);
    }
};

const setActiveMonth = (month) => {
    activeMonth.value = month;
};

const formatNumber = (number) => {
    let numStr = Math.floor(number).toString();
    return numStr.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
};

const filteredList = computed(() => {
    return props.list.filter((product) => product.month === activeMonth.value);
});
</script>
<style scoped>

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
