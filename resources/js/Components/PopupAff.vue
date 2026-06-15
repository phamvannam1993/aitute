<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="relative bg-white py-6 px-4 md:p-8 shadow-lg w-[320px] md:w-[480px] xl:w-[580px] rounded-[40px]" >
            <button class="absolute top-4 right-4 text-black font-bold text-xl" @click="closePopup">x</button>
            <div class="w-full flex items-center justify-center">
                <img src="/assets/img/orion/big-robot.png" alt="Thông báo" class="w-[160px] md:w-[200px]"/>
            </div>
            <div class="text-black text-center">
                <p class="text-[12px] md:text-[16px] font-medium mt-2">
                    {{ props.message }}
                </p>
                <a
                    :href="props.acceptUpgrade ? route('pricing.index') : '#'"
                    @click="!props.acceptUpgrade ? openPopupBuyCredit() : null"
                     class="text-[#FF7A00] underline text-[12px] md:text-[14px] font-medium mt-2 inline-block">
                    Bấm vào đây để nâng cấp tài khoản
                </a>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineEmits } from 'vue';

// Props
const props = defineProps({
    message: String,
    canClose: Boolean,
    acceptUpgrade: Boolean
});

// Biến môi trường
const urlUpgradeAff = import.meta.env.VITE_UPGRADE_AFF_URL;
const appName = import.meta.env.VITE_APP_NAME;

// Emit sự kiện
const emit = defineEmits();

const closePopup = () => {
    emit('close');
};

const openPopupBuyCredit = () => {
    emit('openBuy');
};

</script>
