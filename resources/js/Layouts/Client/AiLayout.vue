<script setup>
import { Head } from '@inertiajs/vue3';
import { defineProps, onMounted } from 'vue';
import { ref } from 'vue';
import LeftSideBar from './LeftSideBar.vue';
import AiHeader from '@/Components/AiHeader.vue'
import Footer from "../../Pages/Client/Home/Components/Footer.vue";

const props = defineProps({
  breadcrumbs: {
    type: Array,
  },
});

const routeName = route().current()
const activeClass = ref('bg-blue-700 v')
</script>

<template>
    <Head>
        <meta head-key="og-title"  property="og:title" content="AI 3 GỐC - Cộng đồng AI tử tế" />
        <meta head-key="og-image" property="og:image"
            content="https://firebasestorage.googleapis.com/v0/b/game-gotech.appspot.com/o/Logo%20Aiwow%201024x1024.png?alt=media&token=ba0a253b-42d4-491d-8f78-891b0edd3ce5" />
        <meta head-key="og-description" property="og:description" content="AI 3 GỐC - Cộng đồng AI tử tế" />
        <meta head-key="og-site_name" property="og:site_name" content="Orion AI" />
        <meta head-key="og-type" property="og:type" content="website" />
    </Head>
    <div class="bg-white">
        <div class="flex relative mb-10">
            <!-- Sidebar (Responsive) -->
            <LeftSideBar />
            <!-- Main Content (Responsive) -->
            <div class="w-11/12 lg:w-10/12 2xl:w-9/12 mx-auto">
                <ai-header :breadcrumbs="props.breadcrumbs" />
                <slot />
            </div>
        </div>
        <Footer class="mt-auto" />
    </div>
</template>
