<template>
    <div class="absolute h-[18px] rounded-full gap-1 left-[10px] bottom-[10px] cursor-pointer flex items-center"  @click.stop="handleOpenContentAI" title="Chatbot AI - Hỗ trợ tạo nội dung nhanh chóng!" >
        <img class="w-auto h-full object-top" src="/assets/img/ai3goc/logo/circle.svg" alt="Robot" />
        <span v-if="!hasContent" class="text-[14px] text-[#8A8A8A]">Mô tả nội dung bằng A.I</span>
    </div>
    <Modal :show="showContentAI" :maxWidth=" modalStep === 1 ? 'xl' : '5xl'" custom-class="mx-[20px]" @close="showContentAI = false">
        <div v-if="modalStep === 1" class="bg-white p-5 rounded-[10px] md:rounded-[25px] min-h-[400px] flex flex-col items-center justify-center gap-[20px]" @click.stop>
            <div class="w-full flex flex-col gap-1 items-center">
                <div class="w-full">
                    <div class="flex items-center gap-2 mb-2">
                        <img src="/assets/img/ai3goc/logo/circle.svg" class="size-[48px]" />
                        <h2 class="text-ai3goc-sec text-[24px] font-bold">Gợi ý nội dung</h2>
                    </div>
                    <label class="text-black font-semibold text-[14px] cursor-pointer">
                            <div class="flex items-center gap-1">
                                <img src="/assets/img/ai3goc/icon/follow_step.svg" alt="step" class="w-5 h-auto" />
                                <span class="">Nhập nội dung mô tả cho video bạn muốn tạo</span>
                            </div>
                    </label>
                    <textarea class="w-full h-[100px] text-black mt-2 rounded-[10px] border-[1px] border-ai3goc-sec"
                            rows="30"
                            :placeholder="`Nhập mô tả video deo bạn muốn tạo...`"
                            v-model="userPrompt"
                            @input="clearError"
                            @keydown="handleKeydown"
                    ></textarea>
                    <MicRecorder @update:text="handleSpeechToText" @clear:text="clearPrompt" />
                    <span v-if="promptError" class="text-red-500 text-sm">{{ promptError }}</span>
                </div>
                <button
                    :disabled="isLoading"
                    class="text-center right-0 -top-4 py-2 font-semibold rounded-[10px] w-[180px] flex items-center justify-center gap-4"
                    :class="{
                        'bg-ai3goc-sec text-white': !isLoading,
                        'bg-gray-300 text-gray-600 cursor-not-allowed': isLoading
                    }"
                    @click="handleGetSuggest"
                >
                    <span v-if="isLoading" class="w-5 h-5 border-2 border-t-2 border-white rounded-full animate-spin border-t-black"></span>
                    <span>{{ isLoading ? 'Đang tạo...' : 'Tạo' }}</span>
                </button>
            </div>

            <div class="w-full flex flex-col gap-1 items-center">
                <div class="w-full">
                    <label class="text-black font-semibold text-[14px] cursor-pointer">
                            <div class="flex items-center gap-1">
                                <img src="/assets/img/orion/icon/label-arrow.svg" alt="step" class="w-5 h-auto" />
                                <span class="">Nội dung gợi ý viết bới AI 3 GỐC</span>
                            </div>
                    </label>

                    <textarea class="w-full h-[188px] text-black mt-2 rounded-[10px] border-[1px] border-ai3goc-sec"
                            rows="30"
                            placeholder="Nội dung gợi ý..."
                            v-model="suggestContent"
                            @input="validateSuggestContent"
                    ></textarea>
                    <span v-if="suggestContentError" class="text-red-500 text-sm">{{ suggestContentError }}</span>
                </div>
                <button
                    :disabled="!suggestContent || (maxLength !== false && suggestContent.length > maxLength)"
                    class="text-center right-0 -top-4 py-2 text-white font-semibold rounded-[10px] w-[180px]"
                    :class="{
                        'bg-green-500': suggestContent && (maxLength === false || suggestContent.length <= maxLength),
                        'bg-gray-500': !suggestContent || (maxLength !== false && suggestContent.length > maxLength)
                    }"
                    @click="handleApplyContent">
                    Áp dụng
                </button>
            </div>
        </div>
    </Modal>
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="acceptUpgrade"/>
    <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup"
                    @buyCredit="handleBuyCredit"
                    @cancel="handleBuyCancel"
                    :currentCredit = "pageProps?.auth?.user?.credit || 0"
                    :additionalCredit = "additionalCredit"
    />
</template>

<script setup>
import Modal from "@/Components/Modal.vue";
import { computed, onMounted, ref } from "vue";
import {usePage } from "@inertiajs/vue3";
import axios from "axios";
import { toast } from "vue3-toastify";
import CreditBuyModal from '@/Components/ModalBuyCredit/BuyCredit.vue';
import PopupAff from '@/Components/PopupAff.vue';
import MicRecorder from "@/Components/MicRecorder/Index.vue";

const props = defineProps({
    type: {
        type: String,
        default: 'content',
    },
    video_des: {
        type: String,
        default: ''
    },
    duraion: {
        type: String,
        default: ''
    },
    isLimited: {
        type: Boolean,
        default: true,
    }
});
const { props: pageProps } = usePage();
const auth = computed(() => pageProps.value.auth);
const additionalCredit = ref(0);
const showAffKeyPopup = ref(false);
const emit = defineEmits(['update:modelValue', 'update:isLoading']);
const showContentAI = ref(false);
const modalStep = ref(1);
const userPrompt = ref("");
const suggestContent = ref("");
const suggestContentError = ref("");
const isLoading = ref(false);
const promptError = ref("");

const popupMessage = ref('Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!')
const acceptUpgrade = ref(true)
const hasContent = computed(() => props.modelValue && props.modelValue.trim().length > 0);

const maxLength = computed(() => {
    switch (props.type) {
        case 'audio': return 10000;
        case 'music': return 200;
        case 'song': return 100;
        case 'film': return 255;
        case 'film-narration': return 255;
        case 'image': return 10000;
        case 'image-to-image': return 10000;
        case 'image-to-video': return 10000;
        case 'mc-virtual': return 8000;
        default: return false;
    }
});

const handleSpeechToText = (text) => {
    userPrompt.value = text;
};

const clearPrompt = () => {
    userPrompt.value = "";
};

const handleOpenContentAI = () => {
    showContentAI.value = true;
};

let showBuyCreditPopup = ref(false);
const handleBuyCancel = () => {
    showBuyCreditPopup.value = false;
};
const showBuyCreditModal = () => {
    showBuyCreditPopup.value = true;
};

const handleBuyCredit = () => {
    window.location.href = '/pricing'
}

const handleGetSuggest = async () => {
    if (!userPrompt.value.trim()) {
        promptError.value = "Vui lòng nhập nội dung để tạo gợi ý.";
        return;
    }
    if (isLoading.value) return;
    try {
        isLoading.value = true;
        const response = await axios.post(route("short-video.getAudioDes"), {
            video_des: userPrompt.value,
            duration:props.duraion
        });
        suggestContent.value = response.data.audio_des;
        validateSuggestContent();
    } catch (e) {
        toast.error("Có lỗi xảy ra khi lấy nội dung gợi ý từ AI.");
    } finally {
        isLoading.value = false;
    }
};
const clearError = () => {
    suggestContentError.value = "";
    promptError.value = "";
};

const validateSuggestContent = () => {
    if (props.isLimited && maxLength.value && suggestContent.value.length > maxLength.value) {
        suggestContentError.value = `Nội dung gợi ý vượt quá ${maxLength.value} ký tự. Vui lòng điều chỉnh nội dung.`;
    } else {
        suggestContentError.value = "";
    }
};

const handleKeydown = (event) => {
    if (event.key === "Enter" && !event.shiftKey) {
        event.preventDefault();
        handleGetSuggest();
    }
};
const handleApplyContent = () => {
    if (suggestContent.value.length > 0 && (maxLength.value === false || suggestContent.value.length <= maxLength.value)) {
        emit('update:modelValue', suggestContent.value);
    }
    showContentAI.value = false;
};
</script>

<style scoped>
.loader {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #24AA69;
    border-radius: 50%;
    width: 60px;
    height: 60px;
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
