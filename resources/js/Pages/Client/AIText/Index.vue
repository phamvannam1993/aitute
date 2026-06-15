<template>

    <Head title="Bài viết - AI 3 GỐC - Cộng đồng AI tử tế" />
    <div
        class="relative w-full mx-auto bg-[url('../../public/assets/img/aiwow/layout_background.png')] bg-cover bg-center min-h-screen p-5 lg:p-10 text-xs lg:text-sm mb-20">

        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="flex gap-3 text-[#2C75E3] mt-14 ml-2 mb-6 text-sm">
                <a :href="route('home.index')">
                    <img src="/assets/img/admin/icon_left_home.png" alt="Trang chủ" class="w-auto h-[19px]" />
                </a>
                <span>/</span>
                <span>
                    <span>{{ job.name }}</span> / <span>{{ operation.name }}</span> / <span>{{ ai_assistant.name }}</span>
                </span>
            </div>
            <Credit :credits="credits" />
        </div>
        <MainMenu/>
        <div class="w-full overflow-y-auto bg-white text-black rounded-[25px] min-w-2/3 py-2 px-3">
            <AIText
            :ai_assistant = "props.ai_assistant"
            :job = "props.job"
            :operation = "props.operation"
            :fanpages = "props.fanpages"
            >
            </AIText>
        </div>

    </div>
    <Footer />



</template>

<script setup>
    import Layout from "@/Layouts/Client/ClientLayout.vue";
    import { Head, usePage } from "@inertiajs/vue3";
    import Footer from "../Home/Components/Footer.vue";
    import {reactive} from "@vue/reactivity";
    import AIText from '../AIText/AIText.vue';

    defineOptions({ layout: Layout });

    const props = defineProps({
        ai_assistant: Object,
        job: Object,
        operation: Object,
        fanpages: Array,
    });



</script>

<style>
</style>
