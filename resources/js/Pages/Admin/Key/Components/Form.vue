<template>
    <form @submit.prevent="submit(form)" enctype="multipart/form-data">
        <div class="grid grid-cols-1 gap-6 mt-4 text-black">
            <div v-if="$page.props.errors.message" class="mt-5 text-red-500">
                {{ $page.props.errors.message }}
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="id">
                    Tên
                </label>
                <input v-model="form.name"
                    class="w-full p-2 border border-gray-300 rounded-md focus:border-indigo-600 focus:ring focus:ring-opacity-40 focus:ring-indigo-500"
                    type="text" />
                <div v-if="$page.props.errors.name" v-text="$page.props.errors.name" class="text-red-500 mt-1"></div>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="id">
                    Email
                </label>
                <input v-model="form.email" :disabled="form.id"
                    class="w-full p-2 border disabled:bg-slate-200 border-gray-300 rounded-md focus:border-indigo-600 focus:ring focus:ring-opacity-40 focus:ring-indigo-500"
                    type="text" />
                <div v-if="$page.props.errors.email" v-text="$page.props.errors.email" class="text-red-500 mt-1"></div>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="id">
                    Password
                </label>
                <input v-model="form.password"
                    class="w-full p-2 border border-gray-300 rounded-md focus:border-indigo-600 focus:ring focus:ring-opacity-40 focus:ring-indigo-500"
                    type="text" />
                <div v-if="$page.props.errors.password" v-text="$page.props.errors.password" class="text-red-500 mt-1">
                </div>
            </div>

            <div v-if="!form.id">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="id">
                    Aff
                </label>

                <select v-model="form.config_aff_id"
                    class="block appearance-none w-full  text-black py-3 px-4 pr-8 border border-gray-300 rounded-md leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    id="video-style">
                    <option v-for="(configAff, index) in configAffs" :value="configAff.id" :key="index">
                        {{ configAff.name }}
                    </option>
                </select>

                <div v-if="$page.props.errors.config_aff_id" v-text="$page.props.errors.config_aff_id"
                    class="text-red-500 mt-1"></div>
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

const { user } = defineProps({ user: Object, configAffs: Array, submit: Function });

const form = reactive({
    id: user.id,
    name: user.name,
    email: user.email,
    password: null,
    config_aff_id: user.config_aff_id,
});

</script>
