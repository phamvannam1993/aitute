<template>
    <div class="w-11/12 lg:w-8/12 flex flex-col lg:gap-4 items-center justify-center mx-auto min-h-[80vh] h-full lg:max-h-[90vh] relative ">
        <div class="bg-white rounded-[50px] py-6 lg:py-12 px-4 lg:px-8 w-full flex flex-col gap-4 items-center justify-center mx-auto">
            <div class="lg:hidden flex items-center justify-between w-full gap-2">
                <img src="/assets/img/ai3goc/logo/image-text.svg" alt="AI 3 GỐC" class="w-1/4" />
                <img src="/assets/img/ai3goc/logo/bke.svg" alt="AI 3 GỐC" class="w-1/3" />
                <img src="/assets/img/ai3goc/logo/slogan-image.svg" alt="AI 3 GỐC" class="w-1/4" />
            </div>
            <section>
                <h1 class="text-[30px] font-bold text-ai3goc-primary">Đăng nhập</h1>
            </section>
            <section class="w-full">
                <form @submit.prevent="submit" class="">
                    <div>
                        <TextInput id="username" type="text" class="mt-1 block w-full p-3 border-[#CECECE] rounded-2xl focus:ring-ai3goc-primary focus:border-ai3goc-primary" v-model="form.email" required autofocus autocomplete="username" placeholder="Nhập tên đăng nhập" />
                        <InputError v-for="email in form?.errors?.email" :key="email" class="mt-0.5" :message="email" />
                    </div>
    
                    <div class="mt-4 relative">
                        <TextInput id="password" :type="pageData.showPassword ? 'text' : 'password'" class="mt-1 block w-full p-3 border-[#CECECE] rounded-2xl focus:ring-ai3goc-primary focus:border-ai3goc-primary" v-model="form.password" required autocomplete="current-password" placeholder="Nhập mật khẩu" lang="en" />
                        <div class="absolute top-[.9rem] right-5 cursor-pointer" @click="pageData.showPassword = !pageData.showPassword">
                            <span v-if="pageData.showPassword">
                                <img src="/assets/svgs/aiwow/eye_open.svg" alt="show password" />
                            </span>
                            <span v-else>
                                <img src="/assets/svgs/aiwow/eye_close.svg" alt="show password" />
                            </span>
                        </div>
                        <InputError v-for="email in form?.errors?.password" :key="email" class="mt-0.5" :message="password" />
                    </div>
    
                    <div class="mt-4 flex justify-between">
                        <label class="flex items-center cursor-pointer">
                            <Checkbox name="remember" v-model:checked="form.remember" />
                            <span class="ms-2 text-sm text-[#A7A7A7]">Ghi nhớ</span>
                        </label>
                    </div>
    
                    <div class="flex items-center justify-end mt-2 lg:mt-12">
                        <button :disabled="form.processing" class="bg-ai3goc-sec lg:bg-ai3goc-primary text-white w-full rounded-2xl p-3 hover:opacity-90">Đăng nhập</button>
                    </div>
                </form>
                <div class="flex gap-3 justify-center mt-4">
                    <p>Bạn chưa có tài khoản?</p>
                </div>
                <div class="flex items-center justify-end mt-2 sm:block">
                    <button @click="openCreditPackageModal" class="bg-ai3goc-sec lg:bg-ai3goc-primary text-white w-full rounded-2xl p-3 hover:opacity-90">Mua tài khoản</button>
                </div>
                <div class="flex gap-3 justify-center mt-4">
                    <p>Nếu bạn có key kích hoạt?</p>
                </div>
                <div class="flex items-center justify-end mt-2 sm:block">
                    <button @click="handleActivateAccountOpen()" class="bg-ai3goc-sec lg:bg-ai3goc-primary text-white w-full rounded-2xl p-3 hover:opacity-90">Nhập mã kích hoạt</button>
                </div>
                <div class="flex items-center justify-end mt-2 sm:block">
                    <button @click="handleForgotPassword()" class="bg-ai3goc-sec lg:bg-ai3goc-primary text-white w-full rounded-2xl p-3 hover:opacity-90">Quên mật khẩu?</button>
                </div>
            </section>
        </div>
        <section class="mx-auto hidden lg:block">
            <p class="text-white text-center pb-5 sm:text-md text-sm">Bản quyền thuộc về AI 3 Gốc</p>
        </section>
        <div class="flex justify-center items-center lg:hidden">
            <img src="/assets/img/ai3goc/banner/slogan-text-mobile.svg" alt="AI 3 GỐC" class="" />
        </div>
        <div class="flex items-start justify-around lg:hidden">
                <img src="/assets/img/ai3goc/banner/login.svg" alt="AI 3 GỐC" class="w-1/2" />
        </div>
    </div>
    <CreditPackageModal ref="creditPackageModalRef" />
</template>

<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { reactive, ref, defineEmits } from "vue";
import CreditPackageModal from "./Components/CreditPackageModal.vue";

const creditPackageModalRef = ref(null);

const pageData = reactive({
    showPassword: false,
});
const emit = defineEmits(['openModalActionActivate']);

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = async () => {
    try {
        form.errors = null;
        const response = await axios.post(route("login"), {
            email: form.email,
            password: form.password,
            remember: form.remember,
        });

        if (response.status === 200) {
            const { status, redirect } = response.data;

            if (status === "user") {
                window.location.reload();
            } else {
                window.location.href = redirect;
            }
        }
    } catch (error) {
        if (error.response && error.response.data) {
            form.errors = error.response.data.errors;
        }
    }
};

const openCreditPackageModal = () => {
    creditPackageModalRef.value.openModal();
};
const handleActivateAccountOpen = () => {
    emit('openModalActionActivate');
};

const handleForgotPassword = () => {
    window.location.href = route('password.request');
};
</script>
