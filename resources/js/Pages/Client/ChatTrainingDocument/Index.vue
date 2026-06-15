<template>
    <Head title="Cài đặt Chat bot" />
    <Layout :breadcrumbs="breadcrumbsData">
        <Body :toneConfigs="toneConfigs" :documents="props.documents" :totalPage="props.totalPage" :currentPage="props.currentPage"
            :user="props.user" :fanpage_dify_apps="props.fanpage_dify_apps"/>
    </Layout>
</template>

<script setup>
import MainMenu from "@/Components/MainMenu.vue";
import { Head } from "@inertiajs/vue3";
import Layout from "@/Layouts/Client/Layout.vue";
import Body from "./Body.vue";
const breadcrumbsData = [{ text: "Cài đặt Chat bot", link: "chat-training-documents.setting-agent" }];
const props = defineProps({
    toneConfigs: Array,
    fanpage_dify_apps: Array,
    documents: Object,
    totalPage: Number,
    currentPage: Number,
    user: Object
});
</script>
