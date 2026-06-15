<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Forgot Password - AI 3 GỐC - Cộng đồng AI tử tế" />

    <div data-v-e4ebae0a="" class="flex items-center mt-20">
        <div class="md:w-[800px] w-full mx-auto flex flex-col justify-between overflow-y-auto bg-gradient-to-b from-[#2d96e585] to-transparent p-4 rounded-3xl">
            <div class="forgot-password">
                <div class="bg-gradient-to-b from-[#76b0dd] to-transparent p-5 rounded-3xl"><div class="text-center sm:mb-[30px] mb-[20px]">
                <img src="/assets/img/ai3goc/logo/image-text.svg" alt="logo" class="w-20 sm:w-32 mx-auto"><h3 class="font-bold text-2xl text-ai3goc-primary font-lexend mt-2 sm:text-3xl">ĐẶT LẠI MẬT KHẨU</h3></div>
                <div class="box-content rounded-3xl bg-white p-10">
                    <div class="mb-4 text-sm text-gray-600">
                        Bạn quên mật khẩu? Không sao cả. Chỉ cần cho chúng tôi biết địa chỉ email của bạn, chúng tôi sẽ gửi cho bạn một liên kết đặt lại mật khẩu để bạn có thể tạo mật khẩu mới.
                    </div>

                    <div
                        v-if="status"
                        class="mb-4 text-sm font-medium text-green-600"
                    >
                        {{ status }}
                    </div>
            
                    <form @submit.prevent="submit">
                        <div>
                            <InputLabel for="email" value="Email" />

                            <TextInput
                                id="email"
                                type="email"
                                placeholder="Nhập email của bạn"
                                class="mt-1 block w-full p-3 border-[#CECECE] rounded-xl  focus:ring-ai3goc-primary focus:border-ai3goc-primary"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                            />

                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div class="mt-4 flex items-center justify-end">
                            <button class="bg-ai3goc-sec lg:bg-ai3goc-primary text-white  w-2/5 rounded-xl p-2 text-sm hover:opacity-90">Quên mật khẩu?</button>
                        </div>
                    </form>
                 </div>
            </div>
          </div>
        </div>
    </div>
</template>