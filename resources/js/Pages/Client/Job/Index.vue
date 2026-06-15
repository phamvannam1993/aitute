<template>
    <Head title="Ngành nghề - AI 3 GỐC - Cộng đồng AI tử tế" />
    <div class="flex flex-col">
        <div class="w-full bg-cover bg-center min-h-screen lg:p-10 pt-[50px] lg:pt[25px]" >
            <div class="flex items-center justify-between px-[10px]">
                <div class="flex items-center space-x-2">
                    <a :href="route('home.index')">
                        <img src="/assets/img/admin/icon_left_home.png" alt="Trang chủ" class="w-auto h-[19px]"/>
                    </a>
                    <span class="font-medium text-[#2C75E3]">/ Ngành nghề</span>
                </div>
                <Credit />
            </div>
            <MainMenu/>
            <Body :occupations="occupations" :listAi="listAi"/>
        </div>
        <Footer class="mt-auto"/>
    </div>
</template>

<script setup>
import Footer from "../Home/Components/Footer.vue";
import Layout from "@/Layouts/Client/ClientLayout.vue";
import Credit from '@/Components/Credit.vue';
import Body from "./Body.vue";
import MainMenu from "@/Components/MainMenu.vue";
import { Head } from "@inertiajs/vue3";

defineOptions({ layout: Layout });

const props = defineProps({
    occupations: Array,
    listAi: Array,
});
</script>