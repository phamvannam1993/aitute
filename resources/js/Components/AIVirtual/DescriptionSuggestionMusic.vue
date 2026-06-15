<template>
    <div class="absolute w-[32px] h-[32px] overflow-hidden rounded-full border-[3px] border-[#d4d4d4] right-[10px] top-[10px] cursor-pointer"  @click.stop="handleOpenAudioContentAI">
        <img class="w-full h-auto object-top" src="/assets/img/orion/big-robot.png" alt="Robot" />
    </div>
    <Modal :show="showAudioContentAI" :maxWidth=" modalStep === 1 ? 'xl' : '5xl'" custom-class="mx-[20px]" @close="showAudioContentAI = false">
        <div v-if="modalStep === 1" class="bg-white p-5 rounded-[10px] md:rounded-[25px] min-h-[400px] flex flex-col items-center justify-center gap-[20px]">
            <div class="w-full flex flex-col gap-1 items-center">
                <div class="w-full">
                    <div class="flex items-center gap-2 mb-2">
                        <img src="/assets/img/orion/icon/label-circle.svg" class="size-[48px]" />
                        <h2 class="text-orion-primary text-[24px] font-bold">Gợi ý nội dung</h2>
                    </div>
                    <label class="text-black font-semibold text-[14px] cursor-pointer">
                            <div class="flex items-center gap-1">
                                <img src="/assets/img/orion/icon/label-arrow.svg" alt="step" class="w-5 h-auto" />
                                <span class="">Nhập nội dung mô tả cho âm nhạc bạn muốn tạo</span>
                            </div>
                    </label>
                    <textarea class="w-full h-[100px] text-black mt-2 rounded-[10px] border-[1px] border-orion-sec"
                            rows="30"
                            :placeholder="`Nhập mô tả âm nhạc bạn muốn tạo...`"
                            v-model="userAudioPrompt"
                            @input="clearError"
                            @keydown="handleKeydown"
                    ></textarea>
                    <MicRecorder @update:text="handleSpeechToText" @clear:text="clearPrompt" />
                    <span v-if="promptError" class="text-red-500 text-sm">{{ promptError }}</span>
                </div>
                <button
                    :disabled="isSuggestionLoading"
                    class="text-center right-0 -top-4 py-2 font-semibold rounded-[10px] w-[180px] flex items-center justify-center gap-4"
                    :class="{
                        'bg-green-500 text-white': !isSuggestionLoading,
                        'bg-gray-300 text-gray-600 cursor-not-allowed': isSuggestionLoading
                    }"
                    @click="handleGetAudioSuggest"
                >
                    <span v-if="isSuggestionLoading" class="w-5 h-5 border-2 border-t-2 border-white rounded-full animate-spin border-t-black"></span>
                    <span>{{ isSuggestionLoading ? 'Đang tạo...' : 'Tạo' }}</span>
                </button>
            </div>

            <div class="w-full flex flex-col gap-1 items-center">
                <div class="w-full">
                    <label class="text-black font-semibold text-[14px] cursor-pointer">
                            <div class="flex items-center gap-1">
                                <img src="/assets/img/orion/icon/label-arrow.svg" alt="step" class="w-5 h-auto" />
                                <span class="">Nội dung gợi ý viết bới Orion AI</span>
                            </div>
                    </label>
    
                    <textarea class="w-full h-[188px] text-black mt-2 rounded-[10px] border-[1px] border-orion-sec"
                            rows="30"
                            placeholder="Nội dung gợi ý..."
                            v-model="suggestAudioContent"
                            @input="validateSuggestContent"
                    ></textarea>
                    <span v-if="suggestContentError" class="text-red-500 text-sm">{{ suggestContentError }}</span>
                </div>
                <button
                    :disabled="!suggestAudioContent || suggestAudioContent.length > 255"
                    class="text-center right-0 -top-4 py-2 text-white font-semibold rounded-[10px] w-[180px]"
                    :class="{
                        'bg-green-500': suggestAudioContent && suggestAudioContent.length <= 255,
                        'bg-gray-500': !suggestAudioContent || suggestAudioContent.length > 255
                    }"
                    @click="handleApplyAudioContent">
                    Áp dụng
                </button>
            </div>
            <!-- <div class="w-full">
                <label class="text-black font-semibold">Nội dung gợi ý bởi AI</label>
                <textarea class="w-full h-[200px] text-black mt-2"
                          rows="30"
                          placeholder="Nội dung gợi ý cho âm nhạc"
                          v-model="suggestAudioContent"
                          @input="validateSuggestContent"

                ></textarea>
            </div>
            <button
                :disabled="!suggestAudioContent || suggestAudioContent.length > 255"
                class="text-center right-0 -top-4 py-2 text-white font-semibold rounded-[10px] w-[120px]"
                :class="{
                        'bg-blue-500': suggestAudioContent && suggestAudioContent.length <= 255,
                        'bg-gray-500': !suggestAudioContent || suggestAudioContent.length > 255
                    }"
                @click="handleApplyAudioContent"
            >Áp dụng</button> -->
        </div>
    </Modal>
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="acceptUpgrade"/>
    <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup"
                    @buyCredit="handleBuyCredit"
                    @cancel="handleBuyCancel"
                    :currentCredit = "pageProps.auth.user.credit || 0"
                    :additionalCredit = "additionalCredit"
    />
</template>

<script setup>
import Modal from "@/Components/Modal.vue";
import axios from "axios";
import { toast } from "vue3-toastify";
import { computed, onMounted, ref } from "vue";
import {usePage } from "@inertiajs/vue3";
import CreditBuyModal from '../../Components/ModalBuyCredit/BuyCredit.vue';
import PopupAff from '@/Components/PopupAff.vue';
import MicRecorder from "@/Components/MicRecorder/Index.vue";

const props = defineProps({
    screenId: {
        type: Number,
        default: 0,
    },
});

const { props: pageProps } = usePage();
const auth = computed(() => pageProps.value.auth);
const additionalCredit = ref(0);
const showAffKeyPopup = ref(false);

const audioDescription = defineModel({ default: '' });
const isSuggestionLoading = ref(false)
const showAudioContentAI = ref(false);
const modalStep = ref(1);

const userAudioPrompt = ref("");
const suggestAudioContent = ref("");

const handleOpenAudioContentAI = () => {
    showAudioContentAI.value = true;
}
const handleSpeechToText = (text) => {
    userAudioPrompt.value = text;
};

const clearPrompt = () => {
    userAudioPrompt.value = ""; 
};

const suggestContentError = ref("");
const promptError = ref("");

const clearError = () => {
    suggestContentError.value = "";
    promptError.value = "";
};


const popupMessage = ref('Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!')
const acceptUpgrade = ref(true)

let showBuyCreditPopup = ref(false);
const handleBuyCancel = () => {
    showBuyCreditPopup.value = false;
};
const showBuyCreditModal = () => {
    showBuyCreditPopup.value = true;
};
const checkCredit = async () => {
    try {
        const formData = new FormData();
        formData.append('model', "suggestContentImagevideo");
        formData.append('type', 'suggestContentImagevideo');
        // Gọi API để kiểm tra credit của user
        const response = await axios.post(route("credit.check_credit"), formData);
        if (response.data.success) {
            // Đủ credit để tiếp tục
            return true;
        } else if (response.data.is_vip_expired){
            showAffKeyPopup.value = true
            if(response.data.affCode == 'aff_trial') {
                popupMessage.value = " Gói của bạn đã hết hạn. Xin vui lòng gia hạn tài khoản để tiếp tục sử dụng tính năng này."
                acceptUpgrade.value = false
            }
            return false;
        } else {
            additionalCredit.value = response?.data?.data?.total_price - pageProps.auth.user.credit;
            // Không đủ credit, hiển thị modal yêu cầu nạp thêm
            showBuyCreditModal();
            return false;
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi khi kiểm tra credit:", error);
        toast.error("Không thể kiểm tra credit. Vui lòng thử lại.");
        if (error.response && error.response.status === 422) {
            const errors = error.response.data.errors;
            Object.values(errors).forEach(errorMessages => {
                if (Array.isArray(errorMessages)) {
                    errorMessages.forEach(error => {
                        toast.error(error);
                    });
                } else {
                    toast.error(errorMessages);
                }
            });
        } else {
            if (error.response) {
                toast.error((error.response.data.message || 'Đã xảy ra lỗi.'));
            } else if (error.request) {
                toast.error('Không thể kết nối đến server.');
            } else {
                toast.error('Lỗi: ' + error.message);
            }
        }
        return false;
    }
};

const validateSuggestContent = () => {
    if (props.isLimited && maxLength.value && suggestAudioContent.value.length > maxLength.value) {
        suggestContentError.value = `Nội dung gợi ý vượt quá ${maxLength.value} ký tự. Vui lòng điều chỉnh nội dung.`;
    } else {
        suggestContentError.value = "";
    }
};

const handleGetAudioSuggest = async () => {
    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        showAudioContentAI.value = false;
        return; // Dừng nếu không đủ credit
    }
    try {
        var screen_id = props.screenId ? props.screenId : 0
        isSuggestionLoading.value = true;
        const response = await axios.get(route('text-to-song.suggest-content-music', { content: userAudioPrompt.value, screen_id: screen_id}));
        suggestAudioContent.value = response.data.data;
    } catch (e) {
        toast.error("Có lỗi xảy ra khi lấy nội dung gợi ý từ AI.");
    } finally {
        isSuggestionLoading.value = false;
    }
}

const handleApplyAudioContent = () => {
    if (suggestAudioContent.value.length > 0) {
        audioDescription.value = suggestAudioContent.value;
    }
    showAudioContentAI.value = false;
}

const handleKeydown = (event) => {
    if (event.key === "Enter" && !event.shiftKey) {
        event.preventDefault();
        handleGetAudioSuggest();
    }
};
</script>
