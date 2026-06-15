<template>
    <Master>

        <div class="custom-background lg:w-5/6 w-full p-5 lg:p-10 relative overflow-hidden min-h-screen">
            <!-- Home Icon in Top Left Corner -->
            <div class="absolute top-5 left-5 z-20">
                <a href="#" class="text-white text-3xl hover:text-yellow-400">
                    <img src="/assets/img/admin/icon_home.png" alt="Home">
                </a>
            </div>

            <!-- Stats Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 my-10 z-10">
                <div class="custom-info_1 rounded-[25px] p-8 text-center">
                    <h2 class="flex justify-center items-center text-[20px] lg:text-[25px] font-bold mb-2 bg-[#02F0FF80] rounded-[20px] px-6 py-2 mx-auto">
                        Ngành nghề
                    </h2>
                    <p class="text-4xl lg:text-[90px] lg:leading-[1em] font-bold">{{countOccupation || 0}}</p>
                </div>
                <div class="custom-info_1 p-8 rounded-lg text-center">
                    <h2 class="flex justify-center items-center text-[20px] lg:text-[25px] font-bold mb-2 bg-[#02F0FF80] rounded-[20px] px-6 py-2 mx-auto">
                        Nghiệp vụ
                    </h2>
                    <p class="text-4xl lg:text-[90px] lg:leading-[1em] font-bold">{{countOperation || 0}}</p>
                </div>
                <div class="custom-info_1 p-8 rounded-lg text-center">
                    <h2 class="flex justify-center items-center text-[20px] lg:text-[25px] font-bold mb-2 bg-[#02F0FF80] rounded-[20px] px-6 py-2 mx-auto">
                        AI
                    </h2>
                    <p class="text-4xl lg:text-[90px] lg:leading-[1em] font-bold">{{countAi || 0}}</p>
                </div>
            </div>
        </div>

    </Master>
</template>

<script setup>
    import {defineProps, ref} from "vue";
import { Head } from "@inertiajs/vue3";
import Master from "../../Layouts/Admin/Master.vue";
    const props = defineProps({
        countOccupation: Number,
        countOperation: Number,
        countAi: Number,
    });
</script>
<style scoped>
    .multicolor-text {
        background: linear-gradient(to left, violet, indigo, blue, green, orange, red);
        -webkit-background-clip: text;
        color: transparent;
    }

    .custom-background {
        background-image: url('../../../../public/assets/img/admin/bg_dashboard_default.png'); /* Update with your actual path */
        background-size: cover;
        background-position: center top;
    }

    .custom-info_1 {
        border: 2px solid #5CE2FFB2;
        background: #0085FF75;
        box-shadow: 0 0 15px rgba(0, 209, 255, 0.8); /* Shadow with glow effect */
    }
</style>
