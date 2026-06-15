<template>
    <div class="w-full flex flex-col gap-2">
        <div class="w-full flex gap-2 items-center">
            <img src="/assets/img/orion/icon/label-arrow.svg" alt="Logo" class="w-[24px] h-auto" />
            <p class="text-black font-bold text-xs lg:text-base">TÍNH NĂNG NỔI BẬT</p>
        </div>
        <div class="flex flex-col gap-3 lg:gap-6 items-center lg:px-[32px] mb-10">
            <div
                v-for="(feature, index) in outstandingFeatures"
                :key="index"
                class="relative flex items-center w-full rounded-[16px] lg:rounded-[30px] border-[4px] border-white overflow-hidden shadow-[1px_0px_8px_2px_#24AA69]"
                >
                    <img :src="feature.background" alt="Background" class="w-full aspect-auto" />
                    <div class="absolute right-[5%] top-[5%] lg:top-[10%] flex items-center gap-2">
                        <component
                            :is="getTagProps(feature).tag"
                            v-bind="getTagProps(feature).props"
                            class="min-w-[152px] flex items-center justify-center gap-2 rounded-full border-[3px] border-white px-2 lg:px-6 py-1 lg:py-2 bg-gradient-to-b from-[#FFA411] to-[#F26100] hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-orange-300 shadow-orange-500/50"
                            >
                            <img :src="feature.icon" alt="Logo" class="h-[16px] lg:h-[28px] w-auto" />
                            <span class="text-[12px] lg:text-[16px] uppercase font-bold text-white">{{ feature.name }}</span>
                        </component>
                    </div>
            </div>          
        </div>
    </div>

    <PopupConfirmFineTune v-if="isShowConfirmModal" :message="'Để tạo phiên bản hình ảnh A.I của bạn thì bạn phải đào tạo ảnh mẫu. Chi phí đào tạo ảnh mẫu cho 1 lần với 1 mẫu là 250.000 VNĐ. Bạn có muốn tiếp tục không?'" @close="isShowConfirmModal = false" />
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="acceptUpgrade"/>
    <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup"
                @buyCredit="handleBuyCredit"
                @cancel="handleBuyCancel"
                :currentCredit = "pageProps?.auth?.user?.credit || 0"
                :additionalCredit = "additionalCredit"
    />
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';

import PopupConfirmFineTune from '@/Components/PopupConfirmFineTune.vue';
import {Link, usePage } from "@inertiajs/vue3";
import {toast} from "vue3-toastify";
import CreditBuyModal from '@/Components/ModalBuyCredit/BuyCredit.vue';
import PopupAff from '@/Components/PopupAff.vue';

const { props: pageProps } = usePage();
const isShowConfirmModal = ref(false)

const showConfirmFineTune = async () => {
    try {
        const response = await axios.get(route('ai-image.has-my-ai-image'));
        const hasImage = response.data.exists;

        if (hasImage) {
            window.location.href = route('ai-image.my-ai-image')
        } else {
            const hasEnoughCredit = await checkCredit();
            if (!hasEnoughCredit) {
                return;
            }
            isShowConfirmModal.value = !isShowConfirmModal.value;
        }
    } catch (error) {
        console.error("Lỗi khi gọi API:", error);
    }
};

const outstandingFeatures = [
    {
        name: "Tạo ảnh ngay",
        background: "/assets/img/orion/bg/outstanding-features/selfie.png",
        icon: "/assets/img/aiwow/icon/outstanding-features/selfie.svg",
        onClick: showConfirmFineTune,
    },
    {
        name: "Tạo video ngay",
        background: "/assets/img/orion/bg/outstanding-features/personalization-video.png",
        icon: "/assets/img/aiwow/icon/outstanding-features/personalization-video.svg",
        route: route('text-to-video.index'),
    },
];

const getTagProps = (feature) => {
  if (feature.route) {
    return {
      tag: "a",
      props: { href: feature.route },
    };
  } else {
    return {
      tag: "button",
      props: { type: "button", onClick: feature.onClick },
    };
  }
};

const showAffKeyPopup = ref(false);
const showBuyCreditPopup = ref(false)
const additionalCredit = ref(0);
const showBuyCreditModal = () => {
    showBuyCreditPopup.value = true;
};
const handleBuyCancel = () => {
    showBuyCreditPopup.value = false;
};
const handleBuyCredit = () => {
    window.location.href = '/pricing'
}
const popupMessage = ref('Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!')
const acceptUpgrade = ref(true)

const checkCredit = async () => {
    try {
        const formData = new FormData();
        formData.append("model", 'my_ai_image');
        formData.append("type", "my_ai_image");
        const response = await axios.post(route("credit.check_credit"), formData);
        if (response.data.success) {
            return true;
        } else if (response.data.is_vip_expired) {
            showAffKeyPopup.value = true;
            if (response.data.affCode == "aff_trial") {
                popupMessage.value = " Gói của bạn đã hết hạn. Xin vui lòng gia hạn tài khoản để tiếp tục sử dụng tính năng này.";
                acceptUpgrade.value = false;
            }
            return false;
        } else {
            additionalCredit.value = response?.data?.data?.total_price - pageProps.auth.user.credit;
            showBuyCreditModal();
            return false;
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi khi kiểm tra credit:", error);
        toast.error("Không thể kiểm tra credit. Vui lòng thử lại.");
        if (error.response && error.response.status === 422) {
            const errors = error.response.data.errors;
            Object.values(errors).forEach((errorMessages) => {
                if (Array.isArray(errorMessages)) {
                    errorMessages.forEach((error) => {
                        toast.error(error);
                    });
                } else {
                    toast.error(errorMessages);
                }
            });
        } else {
            if (error.response) {
                toast.error(error.response.data.message || "Đã xảy ra lỗi.");
            } else if (error.request) {
                toast.error("Không thể kết nối đến server.");
            } else {
                toast.error("Lỗi: " + error.message);
            }
        }
        return false;
    }
};
</script>