<template>
    <Head title="Tạo video cá nhân hóa - AI 3 GỐC - Cộng đồng AI tử tế" />
        <Layout :breadcrumbs="breadcrumbsData" :credits="credits" isBig>
            <CreateForm :xFades="xFades" :positionCameras="positionCameras"/>
        </Layout>
</template>

<script setup>
import { router, usePage, Head } from "@inertiajs/vue3";
import { systemThemes as themes } from "./Components/CardStyleEditor/themes";
import { ref, onMounted, watch, onBeforeMount, watchEffect, computed, nextTick, onBeforeUnmount } from "vue";
import axios from "axios";
import { toast } from "vue3-toastify";
import "cropperjs/dist/cropper.css";
import Layout from "@/Layouts/Client/Layout.vue";
import { useTextToSpeech } from "@/helper/useTextToSpeech";
import CreateForm from "./CreateForm.vue";

const props = defineProps({
    imageUrl: String,
    xFades:String,
    positionCameras:String
});

onBeforeUnmount(() => {
    xFades.value = props.xFades;
    xFadeSeleted.value =  props.xFades ? props.xFades[0].key : "slideleft"
    positionCameras.value = props.positionCameras;
})
</script>