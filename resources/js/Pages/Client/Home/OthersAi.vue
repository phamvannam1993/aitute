<template>
    <Head title="Trang chủ - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="'Trang Chủ'" :credits="credits">
        <div class="">
            <div class="full m-auto">
                <Body :canShowProject="props.canShowProject" :my_ai_assistant="my_ai_assistant" :my_ai_image_models="my_ai_image_models" :message_alert_pictory="message_alert_pictory" :aff_key_missing="aff_key_missing" :aiAssistants="props.aiAssistants" :listAi="props.listAi" :aiAssistantLinks="aiAssistantLinks" @updateListAi="updateListAi" @update-credits="credits = $event" @update-isHome="updateIsHome" />
            </div>
        </div>
    </Layout>
    <Popup v-if="showPopup" :message="message_alert" @close="showPopup = false" />
    <PopupAff v-if="showAffKeyUpgradePopup" message="Bạn đang sử dụng tài khoản thử nghiệm vui lòng nâng cấp lên tài khoản chính thức!" @close="showAffKeyUpgradePopup = false" :canClose="true" :acceptUpgrade="false" @openBuy="openCreditPackageModal()" />
    <CreditPackageModal ref="creditPackageModalRef" />
    <PopupConfirmFineTune v-if="isShowConfirmModal" :message="'Để tạo phiên bản hình ảnh A.I của bạn thì bạn phải đào tạo ảnh mẫu. Chi phí đào tạo ảnh mẫu cho 1 lần với 1 mẫu là 250.000 VNĐ. Bạn có muốn tiếp tục không?'" @close="isShowConfirmModal = false" />
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="acceptUpgrade"/>

</template>

<script setup>
import PopupAff from "@/Components/PopupAff.vue";
import Popup from "@/Components/Popup.vue";
import Layout from "@/Layouts/Client/Layout.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import Body from "./Body.vue";
import CreditPackageModal from "../../Auth/Components/CreditPackageModal.vue";
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import PopupConfirmFineTune from '@/Components/PopupConfirmFineTune.vue';

const props = defineProps({
    aiAssistants: Object,
    isFirstLogin: Number,
    fields: Array,
    listAi: Array,
    message_alert: String,
    aff_key_missing: Boolean,
    affCode: String,
    message_alert_pictory: String,
    my_ai_assistant: Object,
    my_ai_image_models: Array,
    canShowProject: Boolean,
});

const isLoggedIn = ref(false);

const credits = ref(0);

const aiAssistantLinks = ref(props.aiAssistants.links);

const listAi = ref([...props.listAi]);

const isShowEnterpriseField = ref(false);

const showPopup = ref(false);
const page = usePage();
const user = computed(() => page.props.auth.user);
const packageName = ref(user.value?.package_name);
const showAffKeyUpgradePopup = ref(false);
const creditPackageModalRef = ref(null);
const isHome = ref(true);

const showAffKeyPopup = ref(false);
const isShowConfirmModal = ref(false)
const showBuyCreditPopup = ref(false)
const popupMessage = ref('Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!')
const acceptUpgrade = ref(true)
const additionalCredit = ref(0);

const updateIsHome = (value) => {
    isHome.value = value;
};
console.log(isHome.value);
onMounted(() => {
    isShowEnterpriseField.value = props.isFirstLogin === 1;
    if (props.message_alert) {
        showPopup.value = true;
    }

    if (props.affCode == "aff_trial") {
        showAffKeyUpgradePopup.value = true;
    }
});

const updateListAi = (newListAi) => {
    listAi.value = [...newListAi];
    console.log(listAi.value);
};

const checkAuthStatus = async () => {
    try {
        const response = await axios.get("/auth-check");
        isLoggedIn.value = response.data.authenticated;
    } catch (error) {
        console.error("Failed to check authentication status:", error);
    }
};

const openCreditPackageModal = () => {
    showAffKeyUpgradePopup.value = false;
    creditPackageModalRef.value.openModal();
};

onMounted(() => {
    checkAuthStatus();
});

const showConfirmFineTune = async () => {
    try {
        const response = await axios.get(route("ai-image.has-my-ai-image"));
        const hasImage = response.data.exists;

        if (hasImage) {
            window.location.href = route("ai-image.my-ai-image");
        } else {
            const hasEnoughCredit = await checkCredit();
            console.log(hasEnoughCredit);
            if (!hasEnoughCredit) {
                return;
            }
            isShowConfirmModal.value = !isShowConfirmModal.value;
        }
    } catch (error) {
        console.error("Lỗi khi gọi API:", error);
    }
};

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
            // Không đủ credit, hiển thị modal yêu cầu nạp thêm
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

const showBuyCreditModal = () => {
    showBuyCreditPopup.value = true;
};
const handleBuyCancel = () => {
    showBuyCreditPopup.value = false;
};

const handleBuyCredit = () => {
    window.location.href = '/pricing'
}
</script>
