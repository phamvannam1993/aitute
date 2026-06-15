<template>
    <Master>
        <div class="lg:w-5/6 w-full bg-[url('../../public/assets/img/aiwow/layout_background.png')] bg-cover bg-center min-h-screen p-5 lg:p-10">
            <div class="flex flex-col lg:flex-row  items-start justify-between lg:items-end  mb-5 space-y-4 lg:space-y-0 lg:space-x-4">
                <div class="w-full lg:w-3/4 flex flex-col space-y-2">

                    <div class="flex items-center space-x-2 text-blue-900">
                        <img src="/assets/img/admin/icon_left_home.png" alt="Trang chủ" class="w-auto h-[19px]">
                        <span class="font-medium text-[#2C75E3]">/ Run AI</span>
                    </div>
                </div>
            </div>
            <div class="w-full overflow-y-auto bg-white text-black rounded-[25px] min-w-2/3 py-2 px-3">
                <AIText
                    :ai_assistant = "props.ai_assistant"
                    :job = "props.job"
                    :operation = "props.operation"
                >
                </AIText>
            </div>
        </div>

    </Master>
</template>

<script setup>
    import { ref } from "vue";
    import {Head, router, usePage} from "@inertiajs/vue3";
    import Master from "../../../Layouts/Admin/Master.vue";
    import AIText from './AIText.vue';

    const props = defineProps({
        ai_assistant: Object,
        job: Object,
        operation: Object,
    });



</script>

<style>
</style>
