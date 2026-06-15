<template>
    <div v-if="showCreditPopup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white py-6 px-4 md:p-8 shadow-lg w-[320px] md:w-[480px] xl:w-[580px] rounded-[40px]" >
            <div class="w-full flex items-center justify-center">
                <img src="/assets/img/orion/big-robot.png" alt="Thông báo" class="size-[160px] md:size-[200px]"/>
            </div>
            <div class="text-black text-center">
                <h2 class="text-[16px] md:text-[24px] font-bold text-center mt-1 md:mt-2">Xin chào</h2>
                <p class="text-[12px] md:text-[16px] font-medium">Tài khoản của bạn chưa được nâng cấp để sử dụng chức năng này. Để được sử dụng mọi tính năng, vui lòng nâng cấp gói tháng.</p>
            </div>
            <div class="flex items-center gap-4 md:gap-8 justify-center mt-6">
                <button @click="handleCancel" class="rounded-[16px] w-[200px] font-bold  bg-white text-[#2C75E3] border-[2px] border-[#2C75E3] py-4 text-[14px] md:text-[16px] cursor-pointer">
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
    import { ref, defineEmits } from 'vue';

    const props = defineProps({
        showCreditPopup: Boolean,
    })

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
