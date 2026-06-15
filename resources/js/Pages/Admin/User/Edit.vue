<template>
    <Master>
        <div
            class="bg-[url('../../public/assets/img/admin/operation-create.png')] bg-cover bg-center relative py-7 px-6 min-h-screen w-full">
            <div
                class="flex flex-col lg:flex-row items-start justify-between lg:items-end mb-5 space-y-4 lg:space-y-0 lg:space-x-4">
                <div class="w-full lg:w-3/4 flex flex-col space-y-2">
                    <div class="flex items-center space-x-2 text-white">
                        <img src="/assets/img/admin/icon/home-white.svg" alt="Trang chủ" class="w-auto h-[19px]" />
                        <span class="font-medium text-white">/ User</span>
                        <span class="font-medium text-white">/ Chỉnh sửa người dùng</span>
                    </div>
                </div>
            </div>

            <div class="bg-white mb-10 rounded-[30px] md:w-2/3 md:min-h-4/5 mx-auto p-4 md:px-12 md:pt-10 md:pb-8">
                <h1 class="text-[#092A99] font-semibold text-[25px]">
                    Chỉnh sửa người dùng
                </h1>
                <div class="">
                    <Form :submit="submit" :user="user" :configAffs="configAffs" />
                </div>
            </div>

            <div class="bg-white rounded-[30px] md:w-2/3 md:min-h-4/5 mx-auto p-4 md:px-12 md:pt-10 md:pb-8">
                <h1 class="text-[#092A99] font-semibold text-[25px]">
                    Thêm credit
                </h1>
                <div class="">
                    <FormAddCredit :user="user" />
                </div>
            </div>
        </div>
    </Master>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import Master from "../../../Layouts/Admin/Master.vue";
import Form from "./Components/Form.vue";
import FormAddCredit from "./Components/FormAddCredit.vue";

const { user } = defineProps({ user: Object, configAffs: Array });

let submit = (form) => {
    router.put(route("admin.users.update", { user: user.id }), form);
};
</script>
