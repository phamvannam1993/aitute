<template>
    <Master>
        <div
            class="bg-[url('../../public/assets/img/admin/operation-create.png')] bg-cover bg-center relative py-7 px-6 min-h-screen w-full">
            <div
                class="flex flex-col lg:flex-row items-start justify-between lg:items-end mb-5 space-y-4 lg:space-y-0 lg:space-x-4">
                <div class="w-full lg:w-3/4 flex flex-col space-y-2">
                    <div class="flex items-center space-x-2 text-white">
                        <img src="/assets/img/admin/icon/home-white.svg" alt="Trang chủ" class="w-auto h-[19px]" />
                        <span class="font-medium text-white">/ Tạo Key</span>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-[30px] md:w-2/3 md:min-h-4/5 mx-auto p-4 md:px-24 md:pt-10 md:pb-8">
                <h1 class="text-[#092A99] font-semibold text-[25px]">
                    Tạo key
                </h1>
                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <!-- Chọn gói ConfigAffs -->
                    <div class="text-black">
                        <label for="configAff" class="block font-medium text-gray-700">Chọn gói</label>
                        <select
                            id="configAff"
                            v-model="form.configAff"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-black"
                        >
                            <option v-for="option in configAffs" :key="option.id" :value="option.id" class="text-black">
                                {{ option.name }}
                            </option>
                        </select>
                    </div>
<!--                    <div class="text-black">-->
<!--                        <label for="agent" class="block font-medium text-gray-700">Chọn Đại Lý</label>-->
<!--                        <select-->
<!--                            id="agent"-->
<!--                            v-model="form.agent"-->
<!--                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-black"-->
<!--                        >-->
<!--                            <option value="" class="text-black">-->
<!--                                Không chọn đại lý-->
<!--                            </option>-->
<!--                            <option v-for="option in agents" :key="option.id" :value="option.id" class="text-black">-->

<!--                                {{ `${option.name} - ${option.email}` }}-->
<!--                            </option>-->
<!--                        </select>-->
<!--                    </div>-->

                    <div class="text-black">
                        <label class="block font-medium text-gray-700">Chọn Đại Lý</label>
                        <SearchableSelect
                            v-model="selectedId"
                            :options="agents"
                            placeholder="Chọn đại lý"
                            searchPlaceholder="Tìm kiếm đại lý..."
                            @change="handleAgentChange"
                        />
                    </div>

                    <!-- Số lượng keys cần tạo -->
                    <div>
                        <label for="keyQuantity" class="block font-medium text-gray-700">Số lượng keys cần tạo</label>
                        <input
                            type="number"
                            id="keyQuantity"
                            v-model="form.keyQuantity"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-black"
                            placeholder="Nhập số lượng keys"
                            min="1"
                        />
                    </div>

                    <!-- Nút tạo -->
                    <div>
                        <button
                            type="submit"
                            class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        >
                            Tạo
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </Master>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import Master from "../../../Layouts/Admin/Master.vue";
import { ref } from "vue";
import {toast} from "vue3-toastify";
import SearchableSelect from '../../../Components/SearchableSelect.vue';

const { user } = defineProps({ user: Object, configAffs: Array, agents: Array});

const form = ref({
    configAff: "1",
    keyQuantity: "1",
    agent: ""
});

const selectedId = ref('')
const handleAgentChange = (selectedAgent) => {
    form.value.agent = selectedAgent.id;
}

const handleSubmit = () => {
    router.post(route("admin.keys.store"), form.value, {
        onSuccess: () => {
            toast.success("Người dùng đã được tạo thành công!");
        },
        onError: (errors) => {
            console.error(errors);
        },
    });
};
</script>
