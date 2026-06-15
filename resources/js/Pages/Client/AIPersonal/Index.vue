<template>
    <Head title="Tạo Video Cá Nhân - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData">
        <div class="w-full flex flex-col gap-11">
            <ImageToVideo :scrollToVideoToVideo="scrollToVideoToVideo" />
            <div ref="videoToVideoSection">
                <VideoToVideo />
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from "@/Layouts/Client/AiLayout.vue";
import ImageToVideo from "./ImageToVideo.vue";
import VideoToVideo from "./VideoToVideo.vue"
import { ref } from "vue";

const breadcrumbsData = [
    { text: "Video", link: "ai-video.ImgToVideoHistory" },
];

const videoToVideoSection = ref(null);

const scrollToVideoToVideo = () => {
    videoToVideoSection.value?.scrollIntoView({ behavior: "smooth" });
};
</script>

<style></style>
