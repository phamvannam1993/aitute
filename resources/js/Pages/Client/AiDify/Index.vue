<template>
    <Head title="Tạo Video Cá Nhân - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData">
        <div class="w-full flex flex-col gap-11">
            <DynamicChat />
        </div>
    </Layout>
</template>

<script setup>
import Layout from "@/Layouts/Client/AiLayout.vue";
import { ref } from "vue";
import DynamicChat from "../../../Components/DynamicChat.vue";
const breadcrumbsData = [
    { text: "Video", link: "ai-video.ImgToVideoHistory" },
];


</script>

<style></style>
