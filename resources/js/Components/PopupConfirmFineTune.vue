<template>
    <div v-if="isShowConfirm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[100]">
        <div class="bg-white py-6 px-4 md:p-8 shadow-lg w-[320px] md:w-[480px] xl:w-[580px] rounded-[40px]">
            <div class="w-full flex flex-col items-center justify-center mb-4">
                <img src="/assets/img/ai3goc/logo/logo-text.svg" alt="Thông báo" class="" />
            </div>
            <div class="text-black text-center">
                <p class="text-[12px] md:text-[16px] font-medium">{{ message }}</p>
            </div>
            <div class="flex items-center gap-4 md:gap-8 justify-center mt-6">
                <button @click="closePopup" class="rounded-[16px] w-[200px] font-bold bg-[#B9B9B9] text-white py-4 text-[14px] md:text-[16px]">Bỏ qua</button>
                <button @click="redirectToMyAIIMage" class="rounded-[16px] w-[200px] font-bold bg-[#1E405A] text-white py-4 text-[14px] md:text-[16px]">Chấp nhận</button>
            </div>
        </div>
    </div>

    <div v-if="trainingImagePopup" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="trainingImagePopup = false">
        <!-- Modal -->
        <div class="relative z-50 bg-white rounded-[25px] shadow-lg h-fit overflow-auto w-[90%] md:w-3/4">
            <!-- Nút đóng -->
            <div class="absolute top-2 right-2 z-10 flex justify-end p-2">
                <button @click="trainingImagePopup = false" class="text-gray-500 hover:text-gray-800 text-2xl">
                     <img src="/assets/img/orion/icon/close_yellow.svg" alt="Close" class="w-6 h-auto" />
                </button>
            </div>

            <!-- Nội dung -->
            <TraningImageComponent />
        </div>
    </div>
</template>

<script setup>
import { ref, defineEmits } from "vue";
import TraningImageComponent from "../Pages/Client/AIImage/FineTune/TraningImageComponent.vue";

const props = defineProps({
    message: String,
    handleConfirm: Function,
});
const isShowConfirm = ref(true);
const trainingImagePopup = ref(false);

const emit = defineEmits();

const closePopup = () => {
    emit("close");
};

const redirectToMyAIIMage = () => {
    isShowConfirm.value = false;
    trainingImagePopup.value = true;
};
</script>
