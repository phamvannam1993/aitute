<template>
    <img src="/assets/svgs/footer-head-robo.svg" alt="ai" class="absolute w-[32px] h-[32px] right-[10px] top-[10px]"
         @click.stop="handleOpenListContentAI" />
    <Modal :show="showListContentAI" :maxWidth=" modalStep === 1 ? 'xl' : '5xl'" custom-class="mx-[20px]" @close="showListContentAI = false">
        <div v-if="modalStep === 1" class="bg-white p-5 rounded-[10px] md:rounded-[25px] min-h-[400px] flex flex-col items-center justify-center gap-[20px]">
            <div class="w-full">
                <label class="text-black mr-auto">Nhập nội dung bạn muốn tạo</label>
                <textarea class="w-full h-[100px] text-black"
                          rows="30"
                          placeholder="Miêu tả con gấu"
                          v-model="userPrompt"
                ></textarea>
            </div>
            <button class="text-center right-0 -top-4 py-2 bg-blue-500 text-white font-semibold rounded-[10px] w-1/5"
                    @click="handleGetSuggest"
            >Tạo</button>

            <div class="w-full">
                <label class="text-black mr-auto mb-0">Nội dung gợi ý bởi AI</label>
                <textarea class="w-full h-[200px] text-black"
                          rows="30"
                          placeholder="Nội dung gợi ý"
                          v-model="suggestContent"
                ></textarea>
                <span v-if="suggestContentError" class="text-red-500 text-sm">{{ suggestContentError }}</span>
            </div>
            <button
                :disabled="!suggestContent"
                class="text-center right-0 -top-4 py-2 text-white font-semibold rounded-[10px] w-1/5"
                :class="{
                        'bg-blue-500': suggestContent,
                        'bg-gray-500': !suggestContent
                    }"
                @click="handleApplyContent"
            >Áp dụng</button>
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
import {usePage } from "@inertiajs/vue3";
import { computed, onMounted, ref } from "vue";
import axios from "axios";
import {toast} from "vue3-toastify";
import CreditBuyModal from '../../Components/ModalBuyCredit/BuyCredit.vue';
import PopupAff from '@/Components/PopupAff.vue';

const description = defineModel({ default: '' });
const isLoading = defineModel('isLoading', { default: false });
const showListContentAI = ref(false);
const modalStep = ref(1);

const { props: pageProps } = usePage();
const auth = computed(() => pageProps.value.auth);
const additionalCredit = ref(0);
const suggestContentError = ref("");
const showAffKeyPopup = ref(false);
const userPrompt = ref("");
const suggestContent = ref('')
const popupMessage = ref('Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!')
const acceptUpgrade = ref(true)

const handleOpenListContentAI = () => {
    showListContentAI.value = true;
}

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
        showListContentAI.value = false;
        return; // Dừng nếu không đủ credit
    }

    try {
        isLoading.value = true;
        const response = await axios.get(route('mc-virtual.suggest-content-imagevideo', { content: userPrompt.value }));
        suggestContent.value = response.data.data;
        if (suggestContent.value.length > 255) {
            suggestContentError.value = "Nội dung gợi ý vượt quá 255 ký tự. Vui lòng điều chỉnh prompt của bạn.";
            suggestContent.value = "";
        }
    } catch (e) {
        toast.error("Có lỗi xảy ra khi lấy nội dung gợi ý từ AI.");
    } finally {
        isLoading.value = false;
    }
}

const handleApplyContent = () => {
    if (suggestContent.value.length > 0 && suggestContent.value.length <= 255) {
        description.value = suggestContent.value;
    }
    else if (suggestContent.value.length > 255) {
        suggestContentError.value = "Nội dung gợi ý vượt quá 255 ký tự. Vui lòng điều chỉnh prompt của bạn.";
        suggestContent.value = "";
        return;
    }
    showListContentAI.value = false;
}
</script>
