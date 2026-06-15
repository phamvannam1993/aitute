<template>

    <Head title="Hình ảnh - AI 3 GỐC - Cộng đồng AI tử tế" />
  <FacebookForm :facebook="facebook" />

</template>
<script setup>
import Credit from '@/Components/Credit.vue';
import MainMenu from "@/Components/MainMenu.vue";
import Layout from "@/Layouts/Client/Layout.vue";
import { Head } from "@inertiajs/vue3";
import Footer from "../Home/Components/Footer.vue";
import FacebookForm from "./components/FacebookForm.vue";

defineOptions({ layout: Layout });
defineProps({ facebook: Object });

</script>
