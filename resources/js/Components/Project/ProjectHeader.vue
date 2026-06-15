<template>
    <div class="flex items-center justify-end m-6">
        <div class="relative w-full" ref="menuContainer">
            <div class="flex items-center justify-center md:justify-end w-full md:mb-5">
                <!-- <span class="text-sm font-bold text-[#092A99] primary-color md:hidden">Các bước tạo AI bán hàng</span> -->
                <ProjectList ref="projectListComponent" @select-project="emit('selectProject', $event)" @new-project="emit('newProject')" @show-history="emit('showHistory')" :mode="mode" />
            </div>
<!--            <div class="bg-white shadow-xl lg:rounded-[35px] -mt-[30px] md:mt-auto md:px-[32px] md:py-[20px] md:p-3 px-2 py-3 mb-5">-->
<!--                <div class="w-full">-->
<!--                    <div class="flex flex-col md:flex-row justify-start items-start md:items-center gap-3">-->
<!--                        <div class="flex items-center gap-2">-->
<!--                            <div class="w-[52px] overflow-hidden rounded-full border-[1px] border-[#d4d4d4]">-->
<!--                                <img class="w-full h-auto object-top" src="/assets/img/ai3goc/logo/circle.svg" alt="Robot" />-->
<!--                            </div>-->
<!--    -->
<!--                            <h2 class="text-black font-bold text-[14px] lg:text-[18px] leading-none w-[150px]">Chọn chế độ</h2>-->
<!--                        </div>-->
<!--                        <div class="flex items-center gap-2 w-full">-->
<!--                            <button class="w-1/2 md:w-1/3 px-2 md:px-4 py-2 rounded-2xl border-2 border-gray-300 text-black font-semibold text-sm md:text-base transition-all" :class="mode === 'basic' ? 'bg-orion-orange text-white border-orion-orange' : 'bg-white'" @click="toggleMode('basic')">Tiêu chuẩn</button>-->
<!--                            <button class="w-1/2 md:w-1/3 px-2 md:px-4 py-2 rounded-2xl border-2 border-gray-300 text-black font-semibold text-sm md:text-base transition-all" :class="mode === 'advanced' ? 'bg-orion-orange text-white border-orion-orange' : 'bg-white'" @click="toggleMode('advanced')">Nâng cao</button>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
        </div>
    </div>
</template>

<script setup>
import ProjectList from "./ProjectList.vue";
import { nextTick, ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import { eventBus } from "@/lib/eventBus.js";
const page = usePage();
const user = page.props.auth.user;

const mode = defineModel("mode");
let modeTmp = "";
const toggleMode = (selectedMode) => {
    let message = "";
    const modes = { basic: 1, advanced: 2, expert: 3 };
    const packages = { aff_basic: 1, aff_advanced: 2, aff_expert: 3 };

    if (user.is_vip_expired) {
        message = "Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!";
        if (user.package_code == "aff_trial") {
        }
    } else if (modes[selectedMode] > packages[user.package_code]) {
        message = "Rất tiếc, bạn không có quyền truy cập tính năng này. Vui lòng nâng cấp tài khoản để sử dụng đầy đủ các tính năng.";
    }

    if (message !== "") {
        eventBus.emit("popup-aff", { message });
        return true;
    }

    if (mode.value !== selectedMode) {
        mode.value = selectedMode;
        modeTmp = mode.value;
    }

    nextTick(() => {
        if (!mode.value) {
            mode.value = modeTmp;
        }

        const url = new URL(window.location.href);
        if (mode.value) {
            url.searchParams.set("mode", mode.value);
        } else {
            url.searchParams.delete("mode");
        }

        window.location.href = url.toString();
    });
};

const emit = defineEmits(["selectProject", "newProject", "showHistory"]);
</script>
