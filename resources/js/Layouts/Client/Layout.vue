<script setup>
import { defineEmits, ref, onMounted, computed } from "vue";
import { Head } from "@inertiajs/vue3";
import { defineProps } from "vue";
import LeftSideBar from "./LeftSideBar.vue";
import Header from "@/Components/Header/Header.vue";
import Menu from "@/Components/Menu/Index.vue";
import Footer from "../../Pages/Client/Home/Components/Footer.vue";
import PopupAff from "@/Components/PopupAff.vue";
import CreditBuyModal from "@/Components/ModalBuyCredit/BuyCredit.vue";
import { eventBus } from "@/lib/eventBus.js";
import CreditPackageModal from "@/Pages/Auth/Components/CreditPackageModal.vue";
import { usePage } from "@inertiajs/vue3";
const page = usePage();
const user = page.props.auth.user;

const message = ref("");
const showAffKeyUpgradePopup = ref(false);
const creditPackageModalRef = ref(null);
eventBus.on("popup-aff", (state) => {
    message.value = state.message;
    showAffKeyUpgradePopup.value = true;
});

const openCreditPackageModal = () => {
    showAffKeyUpgradePopup.value = false;
    creditPackageModalRef.value.openModal();
};

const showBuyCreditPopup = ref(false);
const currentCredit = ref(0);
const additionalCredit = ref(0);
eventBus.on("popup-buy-credit", (state) => {
    showBuyCreditPopup.value = state.showBuyCreditPopup;
    currentCredit.value = state.currentCredit;
    additionalCredit.value = state.additionalCredit;
});

const handleBuyCredit = () => {
    window.location.href = "/pricing";
};

const handleBuyCancel = () => {
    showBuyCreditPopup.value = false;
};

const props = defineProps({
    credits: Number,
    breadcrumbs: {
        type: [String, Array],
        required: true,
    },
    isFullScreen: {
        type: Boolean,
        default: false,
    },
    isMobileFullScreen: {
        type: Boolean,
        default: false,
    },
    isHome: {
        type: Boolean,
        default: false,
    },
    isBig: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["show-marketing"]);

const isSidebarOpen = ref(false);

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const openChatBoxIframeRef = ref(null);
const chatBoxIframeRef = ref(null);
const isShowChatBox = ref(false);

const toggleChatBox = () => {
    isShowChatBox.value = !isShowChatBox.value;
};

const showMarketing = () => {
    if (props.isHome) {
        emit("show-marketing");
    } else {
        const homeUrl = "/?showProject=true";
        window.location.href = homeUrl;
    }
};

const isFullScreen = computed(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const media = urlParams.get("view") === "project";
    if(props.isHome || media) {
        return true;
    }
    return false;
});
console.log("🚀 ~ isFullScreen ~ isFullScreen:", isFullScreen.value)

onMounted(() => {
    if (user && user.is_vip_expired) {
        message.value = "Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!";
        showAffKeyUpgradePopup.value = true;
    }

    document.addEventListener("click", (event) => {
        if (openChatBoxIframeRef.value && !openChatBoxIframeRef.value.contains(event.target)
            && chatBoxIframeRef.value && !chatBoxIframeRef.value.contains(event.target)) {
                isShowChatBox.value = false;
        }
    });
});
</script>

<template>
    <Head>
        <meta head-key="og-title" property="og:title" content="Orion - Đơn giản thôi mà!" />
        <meta head-key="og-image" property="og:image" content="https://firebasestorage.googleapis.com/v0/b/game-gotech.appspot.com/o/Logo%20Aiwow%201024x1024.png?alt=media&token=ba0a253b-42d4-491d-8f78-891b0edd3ce5" />
        <meta head-key="og-description" property="og:description" content="Orion - Đơn giản thôi mà!" />
        <meta head-key="og-site_name" property="og:site_name" content="Orion" />
        <meta head-key="og-type" property="og:type" content="website" />
    </Head>
    <div class="flex flex-col">
        <div class="flex flex-col items-center w-full h-fit">
            <Header :breadcrumbs="breadcrumbs" :credits="credits" @toggle-sidebar="toggleSidebar" :isSidebarOpen="isSidebarOpen" />
            <div class="flex w-full h-fit">
                <LeftSideBar :activeMenu="isSidebarOpen" @toggle-menu="toggleSidebar" />
                <div class="flex-1 bg-[#F4F4F4] h-fit">
                    <div class="flex flex-col">
                        <Menu :isSidebarOpen="isSidebarOpen" :isHome="isHome"/>
                        <!-- <img :class="isHome ? 'block' : 'hidden'" src="/assets/img/ai3goc/banner/home-new.svg" alt="banner" class="w-full" /> -->
                    </div>
                    <div class="flex flex-col items-center w-full flex-1 relative h-fit">
                        <div :class="isSidebarOpen ? 'w-full my-5 px-2' : (isFullScreen ? 'w-full' : (props.isMobileFullScreen ? 'w-full md:w-3/4 mx-auto my-5' : 'w-11/12 md:w-3/4 mx-auto my-10'))">
                            <slot />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Footer class="mt-auto" />
    </div>
    <PopupAff v-if="showAffKeyUpgradePopup" :message="message" @close="showAffKeyUpgradePopup = false" :canClose="false" :acceptUpgrade="false" @openBuy="openCreditPackageModal()" />
    <CreditPackageModal ref="creditPackageModalRef" />
    <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleBuyCancel" :currentCredit="currentCredit" :additionalCredit="additionalCredit" />
    <div class="fixed cursor-pointer p-2 bottom-10 right-5 z-50 w-[66px] h-[66px] rounded-full shadow-lg bg-white"
    ref="openChatBoxIframeRef" v-show="false && !isShowChatBox" @click="toggleChatBox" >
        <img src="/assets/img/ai3goc/icon/chat-box.png" alt="">
    </div>
    <iframe class="fixed bottom-0 right-0 md:bottom-5 md:right-5 w-full md:w-[400px] h-[500px] border-none z-50" ref="chatBoxIframeRef" v-show="isShowChatBox"
        src="https://bds.aichot.vn/chat-box/iframe?token=eyJpdiI6IklRUXhiZlNIMldsaHpNajRjeEwvQnc9PSIsInZhbHVlIjoiREI3UGNEcWw3OTJaWUUrdFpickY2K1QvSjkxMnRRbVZ1L2tBWXhHdjJ4UkJtalBDWURHT2ZveDY3bFh4NUV6OCIsIm1hYyI6Ijk2NDIwNzQ4ZGRhM2ZiM2I3ZjVmNjA0MDNkMzc2YWI3M2RmYWY2NDBjMDVlN2U5YzZmNWY5NGU1ZGNkN2EzYTciLCJ0YWciOiIifQ%3D%3D" frameborder="0"></iframe>
</template>
