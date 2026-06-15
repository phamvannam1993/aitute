<template>
    <div v-if="showBuyCreditPopup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white py-6 px-4 md:p-8 shadow-lg w-[320px] md:w-[480px] xl:w-[580px] rounded-[40px]" >
            <div class="w-full flex items-center justify-center">
                <img src="/assets/img/orion/big-robot.png" alt="Thông báo" class="size-[160px] md:size-[200px]"/>
            </div>
            <div class="text-black text-center">
                <h2 class="text-[16px] md:text-[24px] font-bold text-center mt-1 md:mt-2">Xin chào {{pageProps.auth.user.name || ''}}</h2>
                <p class="text-[12px] md:text-[16px] font-medium mb-4">Tài khoản của bạn không đủ credit để sử dụng chức năng này.</p>
                <p class="text-[12px] md:text-[16px] font-medium mb-4">
                    <a :href="route('pricing.index')"  class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                        Bấm vào đây để nâng cấp tài khoản
                    </a>
                </p>
<!--                <p class="text-[12px] md:text-[16px] font-medium">-->
<!--                    <span class="font-bold">Credit hiện có:</span> {{ currentCredit }} <br>-->
<!--                    <span class="font-bold">Credit cần thêm:</span> {{ additionalCredit }}-->
<!--                </p>-->
            </div>
            <div class="flex items-center gap-4 md:gap-8 justify-center mt-6">
                <button @click="handleCancel" class="rounded-[16px] w-[200px] font-bold bg-white text-[#2C75E3] border-[2px] border-[#2C75E3] py-4 text-[14px] md:text-[16px] cursor-pointer">
                    Bỏ qua
                </button>
                <a @click="handleBuyCredit" class="text-center rounded-[16px] w-[200px] font-bold bg-[#2C75E3] text-white py-4 text-[14px] md:text-[16px] cursor-pointer">
                    Nâng cấp
                </a>
            </div>
        </div>
    </div>
</template>

<script setup>
    import {ref, defineEmits, computed} from 'vue';
    import {usePage} from "@inertiajs/vue3";

    const props = defineProps({
        showBuyCreditPopup: Boolean,
        currentCredit: Number,
        additionalCredit: Number,
    });
    const { props: pageProps } = usePage();
    const auth = computed(() => pageProps.value.auth);

    const emit = defineEmits(['buyCredit', 'cancel']);

    const handleBuyCredit = () => {
        emit('buyCredit');
    };

    const handleCancel = () => {
        emit('cancel');
    };
</script>

<style scoped>
    .popup {
        background-color: white;
        border: 1px solid black;
        padding: 10px;
        text-align: center;
    }
</style>
