<template>
    <Head title="Trang chủ - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="'Trang Chủ'" :credits="credits" :isHome="isHome">
        <div v-if="isHome" class="pb-4 sm:pb-10">
            <section class="flex justify-between items-center bg-white">
                <div class="w-11/12 xl:w-3/4 m-auto pb-8 sm:pb-10 md:pb-14">
                    <h1 class="pt-0 sm:pt-6 md:pt-[51px] text-[#1B405A] text-xl md:text-4xl font-bold">XÂY DỰNG THƯƠNG HIỆU CÁ NHÂN</h1>
                    <div class="sm:grid flex justify-between sm:gap-4 sm:grid-cols-2 md:gap-3.5 items-center sm:justify-center mt-2 sm:mt-6">
                        <div class="grid col-span-3 sm:col-span-1 sm:flex">
                            <div class="flex flex-col gap-y-1 sm:gap-y-2.5 col-span-4">
                                <div class="flex gap-3.5 items-center">
                                    <img src="/assets/img/ai3goc/icon/checked.svg" alt="logo" class="size-4 md:size-6" />
                                    <h2 class="text-sm md:text-2xl md:leading-10">Tạo uy tín và sự tin tưởng</h2>
                                </div>
                                <div class="flex gap-3.5 items-center">
                                    <img src="/assets/img/ai3goc/icon/checked.svg" alt="logo" class="size-4 md:size-6" />
                                    <h2 class="text-sm md:text-2xl md:leading-10">Thu hút nhiều cơ hội kinh doanh</h2>
                                </div>
                                <div class="flex gap-3.5 items-center">
                                    <img src="/assets/img/ai3goc/icon/checked.svg" alt="logo" class="size-4 md:size-6" />
                                    <h2 class="text-sm md:text-2xl md:leading-10">Gia tăng thu nhập dễ dàng</h2>
                                </div>
                                <div class="flex gap-2 md:gap-8 items-center mt-1 sm:mt-2 md:mt-8">
                                    <img src="/assets/img/ai3goc/icon/arrow.svg" alt="logo" class="w-5 md:w-10" />
                                    <button @click="showConfirmFineTune" class="text-sm md:text-2xl uppercase font-bold bg-[#FFA328] py-2 px-4 md:px-11 rounded-[30px] border-4 md:border-[8px] border-[#FFFFFF] text-white [box-shadow:0px_4px_0px_0px_rgba(0,0,0,0.15)] active:translate-y-1 transition-transform">BẮT ĐẦU NGAY</button>
                                </div>
                            </div>
                        </div>
                        <img src="/assets/img/ai3goc/banner/brand.png" alt="logo" class="h-20 sm:h-36 md:h-72 sm:m-auto" />
                    </div>
                </div>
            </section>
            <section class="w-full items-center bg-[#F4F4F4]">
                <div class="w-11/12 xl:w-3/4 m-auto pb-6 sm:pb-10 md:pb-14">
                    <h1 class="pt-6 md:pt-[51px] text-[#1B405A] text-xl md:text-4xl font-bold">PHỄU DATA KHÁCH HÀNG CHỦ ĐỘNG</h1>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5 items-center mt-2 sm:mt-6">
                        <div class="text-center hidden sm:block">
                            <!-- <img src="/assets/img/ai3goc/banner/brand.png" alt="logo" class="h-52 sm:h-36 md:h-72 m-auto" /> -->
                            <img src="/assets/img/ai3goc/banner/banner.png" alt="logo" class="h-52 sm:h-36 md:h-72 m-auto" />
                        </div>
                        <div class="flex flex-col gap-y-1 sm:gap-y-2.5 col-span-2">
                            <div class="flex gap-3.5 items-start">
                                <img src="/assets/img/ai3goc/icon/checked.svg" alt="logo" class="size-4 md:size-6 mt-2" />
                                <h2 class="text-sm md:text-2xl md:leading-10">Thu hút data khách hàng tiềm năng chỉ với vài nghìn đồng</h2>
                            </div>
                            <div class="flex gap-3.5 items-start">
                                <img src="/assets/img/ai3goc/icon/checked.svg" alt="logo" class="size-4 md:size-6 mt-2" />
                                <h2 class="text-sm md:text-2xl md:leading-10">Tổng hợp và phân loại khách hàng (lạnh, ấm, nóng)</h2>
                            </div>
                            <div class="flex gap-3.5 items-center">
                                <img src="/assets/img/ai3goc/icon/checked.svg" alt="logo" class="size-4 md:size-6" />
                                <h2 class="text-sm md:text-2xl md:leading-10">Phục vụ nhanh chóng 24/7</h2>
                            </div>
                            <div class="flex sm:block  sm:gap-2 md:gap-8 items-center w-full justify-between">
                                <div class="sm:flex sm:gap-4 lg:gap-16">
                                    <div class="flex gap-1 sm:gap-2 xl:gap-8 items-center sm:mt-2">
                                        <img src="/assets/img/ai3goc/icon/arrow.svg" alt="logo" class="w-5 md:w-10 sm:hidden" />
                                        <a :href="route('ai-chat.index')" class="text-sm md:text-2xl flex justify-center uppercase font-bold bg-[#FFA328] py-2 px-3 md:px-0 w-40 md:w-56 lg:w-72 rounded-[30px] border-4 md:border-[8px] border-[#FFFFFF] text-white [box-shadow:0px_4px_0px_0px_rgba(0,0,0,0.15)] active:translate-y-1 transition-transform">TRẢI NGHIỆM</a>
                                    </div>
                                    <div class="flex gap-1 sm:gap-2 xl:gap-8 items-center mt-2">
                                        <img src="/assets/img/ai3goc/icon/arrow.svg" alt="logo" class="w-5 md:w-10 sm:hidden" />
                                        <a :href="route('customer-care.index')" class="text-sm md:text-2xl flex justify-center uppercase font-bold bg-[#FFA328] py-2 px-3 md:px-0 w-40 md:w-56 lg:w-72 rounded-[30px] border-4 md:border-[8px] border-[#FFFFFF] text-white [box-shadow:0px_4px_0px_0px_rgba(0,0,0,0.15)] active:translate-y-1 transition-transform">LIÊN KẾT SOCIAL</a>
                                    </div>
                                </div>
                                <img src="/assets/img/ai3goc/banner/banner.png" alt="logo" class="h-36 sm:h-36 md:h-72 sm:m-auto block sm:hidden" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div :class="bgClass">
            <div class="w-11/12 md:w-3/4 m-auto pb-10 md:pb-14">
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
const bgClass = ref('');

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
const setBgClassValue = () => {
    bgClass.value = (location.pathname === '/' && location.search === '') ? 'bg-white' : 'bg-[#F4F4F4]';
}
const updateIsHome = (value) => {
    isHome.value = value;
    setBgClassValue();
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
    setBgClassValue();
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

const showConfirmFineTune = () => {
    // Bỏ popup đào tạo ở trang chủ: vào thẳng trang Thương hiệu cá nhân
    window.location.href = route("ai-image.my-ai-image");
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
