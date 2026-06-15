<template>
    <form @submit.prevent="submit" enctype="multipart/form-data">
        <div class="grid grid-cols-1 gap-6 mt-4 text-black">
            <div v-if="$page.props.errors.message" class="mt-5 text-red-500">
                {{ $page.props.errors.message }}
            </div>

            <div class="flex-col md:flex-row flex items-left gap-5">
                <label class="block text-gray-700 text-sm font-bold w-4/12" for="id">
                    Credit cộng
                </label>
                <div class="w-full">

                    <input v-model="form.credit_add"
                        class="w-full p-2 border border-gray-300 rounded-md focus:border-indigo-600 focus:ring focus:ring-opacity-40 focus:ring-indigo-500"
                        type="text" />
                    <div v-if="$page.props.errors.credit_add" v-text="$page.props.errors.credit_add"
                        class="text-red-500 mt-1"></div>
                </div>
            </div>


            <div class="flex-col md:flex-row flex items-left gap-5">
                <label class="block text-gray-700 text-sm font-bold w-4/12" for="id">
                    Ngày hết hạn cộng
                </label>
                <div class="w-full">
                    <select v-model="form.vip_expired_at_add"
                        class="block appearance-none w-full  text-black py-3 px-4 pr-8 border border-gray-300 rounded-md leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        <option :value=null>-- Chọn --</option>
                        <option v-for="(option, index) in vipExpiredOptions" :value="option" :key="index">
                            {{ option.name }}
                        </option>
                    </select>

                    <div v-if="$page.props.errors.vip_expired_at_add" v-text="$page.props.errors.vip_expired_at_add"
                        class="text-red-500 mt-1"></div>
                </div>
            </div>

        </div>
        <div class="flex justify-end mt-4 gap-2">
            <button
                class="px-4 py-2 text-gray-200 bg-[#0B1C63] rounded-md hover:opacity-80 focus:outline-none focus:bg-[#0B1C63] font-bold">Thêm</button>
        </div>
    </form>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import { reactive } from "vue";

const { user } = defineProps({ user: Object });

const vipExpiredOptions = [
    {
        day: 5,
        month: 0,
        name: '5 ngày'
    },
    {
        day: 0,
        month: 1,
        name: '1 tháng'
    },
    {
        day: 0,
        month: 3,
        name: '3 tháng'
    },
    {
        day: 0,
        month: 6,
        name: '6 tháng'
    },
    {
        day: 0,
        month: 12,
        name: '12 tháng'
    }
];

const form = reactive({
    user_id: user.id,
    credit_add: null,
    vip_expired_at_add: null,
});

let submit = () => {
    router.post(route("admin.users.add-credit"), form);
};

</script>