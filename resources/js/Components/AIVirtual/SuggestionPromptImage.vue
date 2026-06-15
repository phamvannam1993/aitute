<template>
    <img src="/assets/svgs/footer-head-robo.svg" alt="ai" class="absolute w-[32px] h-[32px] right-[10px] top-[10px]"
         @click.stop="handleOpenContentAI" />
    <Modal :show="showContentAI" :maxWidth=" modalStep === 1 ? 'xl' : '5xl'" custom-class="mx-[20px]" @close="showContentAI = false">
        <div v-if="modalStep === 1" class="bg-white p-5 rounded-[10px] md:rounded-[25px] min-h-[400px] flex flex-col items-center justify-center gap-[20px]">
            <div class="w-full">
                <label class="text-black font-semibold">Nhập nội dung bạn muốn tạo cho {{ typeLabel }}</label>
                <textarea class="w-full h-[100px] text-black mt-2"
                        rows="30"
                        :placeholder="`Miêu tả ${typeLabel.toLowerCase()}`"
                        v-model="userPrompt"
                        @input="clearError"
                        @keydown="handleKeydown"
                ></textarea>
                <div class="object-mic relative ml-auto">
                    <div
                        v-if="isRecording"
                        class="outline-mic right-3 bottom-3 flex items-center justify-center">
                    </div>
                    <div
                        v-if="isRecording"
                        class="outline-mic right-3 bottom-3 flex items-center justify-center"
                        id="delayed">
                    </div>
                    <div
                        v-if="isRecording"
                        class="button-mic right-3 bottom-3 flex items-center justify-center">
                    </div>
                    <button
                        class="button-mic icon-mic absolute right-3 bottom-3 flex items-center justify-center"
                        @click="startRecording"
                        type="button">
                        <svg class="h-6 w-6" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000">
                            <g>
                                <path d="M500,683.8c84.6,0,153.1-68.6,153.1-153.1V163.1C653.1,78.6,584.6,10,500,10c-84.6,0-153.1,68.6-153.1,153.1v367.5C346.9,615.2,415.4,683.8,500,683.8z M714.4,438.8v91.9C714.4,649,618.4,745,500,745c-118.4,0-214.4-96-214.4-214.4v-91.9h-61.3v91.9c0,141.9,107.2,258.7,245,273.9v124.2H346.9V990h306.3v-61.3H530.6V804.5c137.8-15.2,245-132.1,245-273.9v-91.9H714.4z"/>
                            </g>
                        </svg>
                    </button>
                </div>
                <span v-if="promptError" class="text-red-500 text-sm">{{ promptError }}</span>
            </div>
            <button
                :disabled="isLoading"
                class="text-center right-0 -top-4 py-2 font-semibold rounded-[10px] w-[180px] flex items-center justify-center gap-4"
                :class="{
                    'bg-blue-500 text-white': !isLoading,
                    'bg-gray-300 text-gray-600 cursor-not-allowed': isLoading
                }"
                @click="handleGetSuggest"
            >
                <span v-if="isLoading" class="w-5 h-5 border-2 border-t-2 border-white rounded-full animate-spin border-t-black"></span>
                <span>{{ isLoading ? 'Đang tạo...' : 'Tạo' }}</span>
            </button>

            <div class="w-full">
                <label class="text-black font-semibold">Nội dung gợi ý bởi AI</label>
                <textarea class="w-full h-[200px] text-black mt-2"
                        rows="30"
                        placeholder="Nội dung gợi ý"
                        v-model="suggestContent"
                        @input="validateSuggestContent"
                ></textarea>
                <span v-if="suggestContentError" class="text-red-500 text-sm">{{ suggestContentError }}</span>
            </div>
            <button
                :disabled="!suggestContent || (maxLength !== false && suggestContent.length > maxLength)"
                class="text-center right-0 -top-4 py-2 text-white font-semibold rounded-[10px] w-[180px]"
                :class="{
                    'bg-blue-500': suggestContent && (maxLength === false || suggestContent.length <= maxLength),
                    'bg-gray-500': !suggestContent || (maxLength !== false && suggestContent.length > maxLength)
                }"
                @click="handleApplyContent">
                Áp dụng
            </button>
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
import { computed, onMounted, ref } from "vue";
import {usePage } from "@inertiajs/vue3";
import axios from "axios";
import { toast } from "vue3-toastify";
import CreditBuyModal from '../../Components/ModalBuyCredit/BuyCredit.vue';
import PopupAff from '@/Components/PopupAff.vue';

const props = defineProps({
    type: {
        type: String,
        default: 'content',
    },
    screenId: {
        type: Number,
        default: 0,
    },
    modelValue: {
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

const maxLength = computed(() => {
    switch (props.type) {
        case 'image': return 8000;
        default: return false;
    }
});

const typeLabel = computed(() => {
    switch (props.type) {
        case 'audio': return 'âm thanh';
        case 'music': return 'âm nhạc';
        case 'film': return 'phim';
        case 'image': return 'hình ảnh';
        case 'film-narration': return 'thuyết minh phim';
        case 'video': return 'video';
        case 'mc-virtual': return 'MC ảo';
        default: return 'nội dung';
    }
});
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
const checkCredit = async () => {
    try {
        const formData = new FormData();
        formData.append('model', "suggestContentImagevideo");
        formData.append('type', 'suggestContentImagevideo');
        formData.append('content', userPrompt.value);
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

const handleGetSuggest = async () => {
    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        showContentAI.value = false;
        return; // Dừng nếu không đủ credit
    }

    if (!userPrompt.value.trim()) {
        promptError.value = "Vui lòng nhập nội dung để tạo gợi ý.";
        return;
    }
    var screen_id = props.screenId ? props.screenId : 0
    if (isLoading.value) return;
    try {
        isLoading.value = true;
        const response = await axios.get(route('ai-image.suggest-content', { content: userPrompt.value, screen_id:screen_id }));
        suggestContent.value = response.data.data;
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

const isRecording = ref(false);
const audioBlob = ref(null);
const audioUrl = ref(null);
let mediaRecorder = null;
let audioChunks = [];
let device = ref(null);
const startRecording = async () => {
    if (!isRecording.value) {
        try {
            isRecording.value = true;
            device = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(device);
            mediaRecorder.addEventListener("dataavailable", (event) => {
                audioChunks.value.push(event.data);
            });
            mediaRecorder.addEventListener("stop", async () => {
                audioBlob.value = new Blob(audioChunks.value, { type: "audio/mp3" });
                audioUrl.value = URL.createObjectURL(audioBlob.value);
                const formData = new FormData();
                const file = new File([audioBlob.value], "audio.mp3", { type: "audio/mp3" });
                formData.append("audio", file);
                try {
                    const response = await axios.post('/speech-to-text', formData, {
                        headers: { "Content-Type": "multipart/form-data" },
                    });
                    userPrompt.value = response?.data?.text || 'Vui lòng thử lại';
                } catch (error) {
                    console.error("Error in Speech-to-Text:", error);
                } finally {
                }
                isRecording.value = false;
            });
            mediaRecorder.onstart = () => {
                audioChunks.value = [];
            };
            mediaRecorder.start();
        } catch (error) {
            console.error("Lỗi khi bắt đầu ghi âm:", error);
            isRecording.value = false;
        }
    } else {
        isRecording.value = false;
        mediaRecorder.stop();
        device.getTracks().forEach((track) => track.stop());
    }
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
.object-mic {
    display: flex;
    flex: 0 1 100%;
    justify-content: center;
    align-items: center;
    align-content: stretch;
}
.outline-mic {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: 8px solid #b5a4a4;
    animation: pulseMic 3s;
    animation-timing-function: ease-out;
    animation-iteration-count: infinite;
    position: absolute;
}
.button-mic {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    box-shadow: 0px 0px 80px #0084f9;
    position: absolute;
}
@keyframes pulseMic {
    0% {
        transform: scale(0.5);
        opacity: 1;
    }
    50% {
        transform: scale(1.5);
        opacity: 0.4;
    }
    100% {
        transform: scale(2);
        opacity: 0;
    }
}
</style>
