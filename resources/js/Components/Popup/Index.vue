<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[999]" @click.self="cancel">
        <div class="bg-white py-6 px-4 md:p-8 shadow-lg w-[320px] md:w-[480px] xl:w-[580px] rounded-[40px] flex flex-col gap-8" >
            <div class="w-full flex items-center justify-center">
                <img src="/assets/img/orion/logo/slogan.svg" alt="Thông báo" class="h-auto w-[160px] md:w-[200px]"/>
            </div>
            <div class="text-black text-center flex flex-col gap-4">
                <h2 class="text-[16px] md:text-[24px] font-bold text-center mt-1 md:mt-2 text-orion-primary">{{ title }} </h2>
                <p class="text-[12px] md:text-[16px] font-medium" v-html="message"></p>
            </div>
            <div class="flex items-center gap-4 md:gap-8 justify-center mt-6">
                <button @click="cancel" class="rounded-[16px] w-[200px] font-bold  bg-[#D4D4D4] text-primary-button border-[2px] border-[#D4D4D4] py-4 text-[14px] md:text-[16px] text-[#787878] hover:bg-[#C0C0C0] hover:text-[#585858]">
                    {{ cancelButtonText }}
                </button>
                <button v-if="acceptButtonText" @click="accept" class="rounded-[16px] w-[200px] font-bold  bg-primary-button text-white bg-orion-primary border-[2px] border-orion-primary py-4 text-[14px] md:text-[16px] hover:bg-orion-sec hover:border-orion-sec">
                    {{ acceptButtonText }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineEmits } from 'vue';

const props = defineProps({
    title: String,
    message: String,
    cancelButtonText: String,
    acceptButtonText: String
});

const emit  = defineEmits();

const cancel = () => {
    emit('cancel');
};

const accept = () => {
    emit('accept');
};
</script>