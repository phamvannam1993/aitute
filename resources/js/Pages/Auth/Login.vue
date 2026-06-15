<script setup>
import { Head } from "@inertiajs/vue3";
import LoginForm from "./LoginForm.vue";
import ActivateAccount from "../Client/Home/Components/ActivateAccount.vue";

import {ref} from "vue";
import {toast} from "vue3-toastify";
const showActivatePopup = ref(false);
const isLoading = ref(false);
const openActivateAccount = () => {
    console.log("openActivateAccount")
    showActivatePopup.value = true;
};
const closeActivateAccount = () => {
    showActivatePopup.value = false;
};

const handleActivate = async (payload) => {
    try {
        isLoading.value = true;
        const response = await axios.post('/activate-account', payload);
        console.log(response.data.message);
        window.location.href = response.data.redirect;
        showActivatePopup.value = false;
    } catch (error) {
        console.error("Lỗi kích hoạt tài khoản:", error);
        if (error.response && error.response.data.errors) {
            const errors = error.response.data.errors;
            for (const field in errors) {
                errors[field].forEach((message) => {
                    toast.error(message);
                });
            }
        } else {
            toast.error(error.response.data.message);
            console.error("Đã xảy ra lỗi không xác định. Vui lòng thử lại.");
        }
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <Head title="Log in - AI 3 GỐC - Cộng đồng AI tử tế" />

    <div class="flex items-center bg-white h-screen">
        <div class="w-1/2 h-full lg:flex flex-col items-center justify-end hidden gap-4 lg:bg-ai3goc-bgclr">
            <div class="flex items-center gap-4">
                <img src="/assets/img/ai3goc/logo/image-text.svg" alt="AI 3 GỐC" class="w-[112px]" />
                <img src="/assets/img/ai3goc/logo/bke.svg" alt="AI 3 GỐC" class="w-[112px]" />
                <img src="/assets/img/ai3goc/logo/slogan-image.svg" alt="AI 3 GỐC" class="w-[112px]" />
            </div>
            <div class="flex justify-center items-center">
                <img src="/assets/img/ai3goc/banner/slogan-text.svg" alt="AI 3 GỐC" class="" />
            </div>
            <div class="flex items-start justify-around">
                <img src="/assets/img/ai3goc/banner/login.svg" alt="AI 3 GỐC" class="w-5/6" />
            </div>
        </div>
        <div class="flex w-full lg:w-1/2 items-center h-full">
            <LoginForm @openModalActionActivate = "openActivateAccount" />
        </div>
    </div>
    <ActivateAccount
        :isVisible="showActivatePopup"
        @close="closeActivateAccount"
        @activate="handleActivate"
        :isLoading="isLoading"
    />
</template>