<template>
    <Head title="Hình ảnh - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData" :credits="credits" :isBig="true">
        <TextToImageComponent/>
    </Layout>
</template>
<script setup>
import Layout from "@/Layouts/Client/Layout.vue";
import { Head } from "@inertiajs/vue3";
import TextToImageComponent from "./TextToImageComponent.vue";

const props = defineProps({
    ai_assistant: Object,
});

const breadcrumbsData = [
    { text: "Tạo sản phẩm MEDIA", link: "ai-image.history" },
    { text: "Tạo ảnh từ văn bản", link: "ai-image.index" },
];
</script>