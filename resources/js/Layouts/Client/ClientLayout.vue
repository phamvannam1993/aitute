<script setup>
import { Head } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import LeftSideBar from "./LeftSideBar.vue";
import ChatBox from "@/Components/ChatBox.vue";
const routeName = route().current();
const activeClass = ref("bg-blue-700 v");
import PopupAff from "@/Components/PopupAff.vue";
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

onMounted(() => {
    if (user && user.is_vip_expired) {
        message.value = "Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!";
        showAffKeyUpgradePopup.value = true;
    }
});
</script>

<template>
    <ChatBox />
    <Head>
        <meta head-key="og-title" property="og:title" content="AI 3 GỐC - Cộng đồng AI tử tế" />
        <meta head-key="og-image" property="og:image" content="https://firebasestorage.googleapis.com/v0/b/game-gotech.appspot.com/o/Logo%20Aiwow%201024x1024.png?alt=media&token=ba0a253b-42d4-491d-8f78-891b0edd3ce5" />
        <meta head-key="og-description" property="og:description" content="AI 3 GỐC - Cộng đồng AI tử tế" />
        <meta head-key="og-site_name" property="og:site_name" content="Orion AI" />
        <meta head-key="og-type" property="og:type" content="website" />
    </Head>
    <div class="bg-white">
        <div class="flex relative">
            <!-- Sidebar (Responsive) -->
            <LeftSideBar />

            <!-- Main Content (Responsive) -->
            <div class="h-screen overflow-y-auto w-full">
                <slot />
            </div>
        </div>
    </div>
    <PopupAff v-if="showAffKeyUpgradePopup" :message="message" @close="showAffKeyUpgradePopup = false" :canClose="false" :acceptUpgrade="false" @openBuy="openCreditPackageModal()" />
    <CreditPackageModal ref="creditPackageModalRef" />
</template>
