<template>
    <form @submit.prevent="submit(form)" enctype="multipart/form-data">
        <div class="grid grid-cols-1 gap-6 mt-4 text-black">
            <div v-if="$page.props.errors.message" class="mt-5 text-red-500">
                {{ $page.props.errors.message }}
            </div>

            <div class="flex-col md:flex-row flex items-left gap-5">
                <label class="block text-gray-700 text-sm font-bold w-4/12" for="id">
                    Tên
                </label>
                <div class="w-full">
                    <input v-model="form.name"
                        class="w-full p-2 border border-gray-300 rounded-md focus:border-indigo-600 focus:ring focus:ring-opacity-40 focus:ring-indigo-500"
                        type="text" />
                    <div v-if="$page.props.errors.name" v-text="$page.props.errors.name" class="text-red-500 mt-1">
                    </div>
                </div>
            </div>

            <div class="flex-col md:flex-row flex items-left gap-5">
                <label class="block text-gray-700 text-sm font-bold w-4/12 " for="id">
                    Email
                </label>
                <div class="w-full">
                    <input v-model="form.email" :disabled="form.id"
                        class="w-full p-2 border disabled:bg-slate-200 border-gray-300 rounded-md focus:border-indigo-600 focus:ring focus:ring-opacity-40 focus:ring-indigo-500"
                        type="text" />
                    <div v-if="$page.props.errors.email" v-text="$page.props.errors.email" class="text-red-500 mt-1">
                    </div>
                </div>
            </div>

            <div class="flex-col md:flex-row flex items-left gap-5">
                <label class="block text-gray-700 text-sm font-bold w-4/12" for="id">
                    Password
                </label>
                <div class="w-full">
                    <input v-model="form.password"
                        class="w-full p-2 border border-gray-300 rounded-md focus:border-indigo-600 focus:ring focus:ring-opacity-40 focus:ring-indigo-500"
                        type="text" />
                    <div v-if="$page.props.errors.password" v-text="$page.props.errors.password"
                        class="text-red-500 mt-1">
                    </div>
                </div>
            </div>

            <div class="flex-col md:flex-row flex items-left gap-5">
                <label class="block text-gray-700 text-sm font-bold w-4/12" for="id">
                    Phone
                </label>
                <div class="w-full">
                    <input v-model="form.phone"
                        class="w-full p-2 border border-gray-300 rounded-md focus:border-indigo-600 focus:ring focus:ring-opacity-40 focus:ring-indigo-500"
                        type="text" />
                    <div v-if="$page.props.errors.phone" v-text="$page.props.errors.phone"
                        class="text-red-500 mt-1">
                    </div>
                </div>
            </div>

            <div v-if="user.id" class="flex-col md:flex-row flex items-left gap-5">
                <label class="block text-gray-700 text-sm font-bold w-4/12" for="id">
                    Credit
                </label>
                <div class="w-full">
                    <input v-model="form.credit"
                        class="w-full p-2 border border-gray-300 rounded-md focus:border-indigo-600 focus:ring focus:ring-opacity-40 focus:ring-indigo-500"
                        type="number" />
                    <div v-if="$page.props.errors.credit" v-text="$page.props.errors.credit"
                        class="text-red-500 mt-1">
                    </div>
                </div>
            </div>

            <div class="flex-col md:flex-row flex items-left gap-5">
                <label class="block text-gray-700 text-sm font-bold w-4/12" for="id">
                    Aff
                </label>
                <div class="w-full">
                    <select v-model="form.config_aff_id"
                        class="block appearance-none w-full  text-black py-3 px-4 pr-8 border border-gray-300 rounded-md leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                        @change="handleConfigAffId">
                        <option :value=null>-- Chọn Aff --</option>
                        <option v-for="(configAff, index) in configAffs" :value="configAff.id" :key="index">
                            {{ configAff.name }} - {{configAff.month}} month
                        </option>
                    </select>
                    <div v-if="$page.props.errors.config_aff_id" v-text="$page.props.errors.config_aff_id"
                        class="text-red-500 mt-1"></div>
                </div>
            </div>

            <div v-if="user.config_aff_id != form.config_aff_id && form.config_aff_id"
                class="flex justify-center items-center gap-5">
                <label class="block text-gray-700 text-sm font-bold w-4/12" for="id">
                    Credit cộng
                </label>
                <p
                    class="w-full p-2 border bg-slate-200 border-gray-300 rounded-md focus:border-indigo-600 focus:ring focus:ring-opacity-40 focus:ring-indigo-500">
                    {{ pageData.credit_add }}
                </p>
            </div>
            <div v-if="user.config_aff_id != form.config_aff_id && form.config_aff_id"
                class="flex justify-center items-center gap-5">
                <label class="block text-gray-700 text-sm font-bold w-4/12" for="id">
                    Ngày hết hạn cộng
                </label>
                <p
                    class="w-full p-2 border bg-slate-200 border-gray-300 rounded-md focus:border-indigo-600 focus:ring focus:ring-opacity-40 focus:ring-indigo-500">
                    {{ pageData.vip_expired_at_add }}
                </p>
            </div>

            <div class="flex-col md:flex-row flex items-left gap-5">
                <label class="block text-gray-700 text-sm font-bold w-4/12" for="id">
                    Cho phép gửi thông tin đăng nhập đến email
                </label>
                <div class="w-full">
                    <input type="checkbox" v-model="form.isSendMail" class="w-fit p-2 border border-gray-300 rounded-md focus:border-indigo-600 focus:ring focus:ring-opacity-40 focus:ring-indigo-500"/>
                    <div v-if="$page.props.errors.isSendMail" v-text="$page.props.errors.isSendMail"
                        class="text-red-500 mt-1">
                    </div>
                </div>
            </div>
            <div class="flex-col md:flex-row flex items-left gap-5">
                <label class="block text-gray-700 text-sm font-bold w-4/12" for="id">
                     Tài khoản tặng
                </label>
                <div class="w-full">
                    <input type="checkbox" v-model="form.is_gift" class="w-fit p-2 border border-gray-300 rounded-md focus:border-indigo-600 focus:ring focus:ring-opacity-40 focus:ring-indigo-500"/>
                    <div v-if="$page.props.errors.is_gift" v-text="$page.props.errors.is_gift"
                        class="text-red-500 mt-1">
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-end mt-4 gap-2">
            <button
                class="px-4 py-2 text-gray-200 bg-[#0B1C63] rounded-md hover:opacity-80 focus:outline-none focus:bg-[#0B1C63] font-bold">Lưu</button>
        </div>
    </form>
</template>

<script setup>
import { reactive } from "vue";

const { user, configAffs } = defineProps({ user: Object, configAffs: Array, submit: Function });

const pageData = reactive({
    credit_add: null,
    vip_expired_at_add: null,
});

const form = reactive({
    id: user.id,
    name: user.name,
    email: user.email,
    password: null,
    config_aff_id: user.config_aff_id,
    phone: user.phone,
    credit: user.credit,
    isSendMail: false,
    is_gift: Boolean(user.is_gift)
});

const handleConfigAffId = () => {
    const configAffSelected = configAffs.find(aff => aff.id === form.config_aff_id);
    pageData.credit_add = configAffSelected.credit.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    let monthStr = configAffSelected.month ? configAffSelected.month + ' tháng' : '';
    let dayStr = configAffSelected.day ? configAffSelected.day + ' ngày' : '';
    pageData.vip_expired_at_add = monthStr + ' ' + dayStr;
}

</script>
