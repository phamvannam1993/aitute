<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="closePopup">
        <div class="bg-white py-6 px-4 md:p-8 shadow-lg w-[320px] md:w-[480px] xl:w-[580px] rounded-[40px]" >
            <div class="w-full flex items-center justify-center">
                <img src="/assets/img/ai3goc/logo/circle.svg" alt="Thông báo" class="size-[160px] md:size-[200px]"/>
            </div>
            <div class="text-black text-center">
                <p class="text-[12px] md:text-[16px] font-medium">SẮP RA MẮT.</p>
            </div>
            <div class="flex items-center gap-4 md:gap-8 justify-center mt-6">
                <button @click="closePopup" class="rounded-[16px] w-[200px] font-bold bg-white text-ai3goc-primary border-[2px] border-ai3goc-primary py-4 text-[14px] md:text-[16px]">
                    Đóng
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, defineEmits } from 'vue';

const props = defineProps({
    message: {
        type: String,
        default: ''
    },
});

const emit  = defineEmits();

const closePopup = () => {
    emit('closeNew');
};
</script>
