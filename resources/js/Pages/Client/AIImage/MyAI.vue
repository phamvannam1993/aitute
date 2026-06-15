<template>
    <Head title="Tạo ảnh tự sướng - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData" :credits="credits" :isBig="true">
        <MyAIComponent :ai_assistant="props.ai_assistant" :my_ai_image_models="my_ai_image_models"/>
    </Layout>
</template>
<script setup>
import Layout from "@/Layouts/Client/Layout.vue";
import { Head } from "@inertiajs/vue3";
import MyAIComponent from "./MyAIComponent.vue";


const props = defineProps({
    ai_assistant: Object,
    my_ai_image_models: Array,
});

const breadcrumbsData = [
    { text: "Tạo sản phẩm MEDIA" },
    { text: "Tạo ảnh tự sướng", link: "ai-image.history-my-ai-image" },
];
</script>