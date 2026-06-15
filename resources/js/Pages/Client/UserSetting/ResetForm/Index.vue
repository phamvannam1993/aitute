<template>
    <Head title="Reset Password - AI 3 GỐC - Cộng đồng AI tử tế" />
    <div class="flex flex-col">
        <div class="w-full bg-cover bg-center min-h-screen lg:p-10 pt-[50px] lg:pt[25px]" >
            <div class="flex items-center space-x-2 px-[10px]">
                <a :href="route('home.index')">
                    <img src="/assets/img/admin/icon_left_home.png" alt="Trang chủ" class="w-auto h-[19px]"/>
                </a>
                <span class="font-medium text-[#2C75E3]">/ Thay đổi mật khẩu</span>
            </div>
            <!-- Main Content -->
            <main class="flex-grow flex items-center justify-center py-12">
                <div class="w-full max-w-md bg-gradient-to-b from-[#76b0dd] shadow-lg rounded-lg p-6">
                    <!-- Tiêu đề -->
                    <h2 class="text-2xl font-bold text-ai3goc-primary text-center mb-4">Đặt lại mật khẩu</h2>
                    <p class="text-sm text-gray-500 text-center mb-6">
                        Nhập mật khẩu mới để hoàn tất việc đặt lại mật khẩu.
                    </p>

                    <!-- Form Reset Password -->
                    <form @submit.prevent="resetPassword" class="space-y-6">
                        <!-- Email (readonly) -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input
                                type="email"
                                id="email"
                                v-model="form.email"
                                class="text-black mt-1 block w-full border-[#CECECE] rounded-md shadow-sm px-4 py-2 focus:ring-ai3goc-primary focus:border-ai3goc-primary bg-gray-100"
                                readonly
                            />
                        </div>

                        <!-- Mật khẩu mới -->
                        <div class="relative">
                            <label for="password" class="block text-sm font-medium text-gray-700">Mật khẩu mới</label>
                            <input
                                :type="!showPassword ? 'password' : 'text'"
                                id="password"
                                v-model="form.password"
                                class="text-black mt-1 block w-full border-[#CECECE] rounded-md shadow-sm px-4 py-2 focus:ring-ai3goc-primary focus:border-ai3goc-primary"
                                placeholder="Nhập mật khẩu mới"
                                required
                            />
                            <div class="absolute top-[.9rem] right-5 cursor-pointer mt-5" @click="toggleShow(1)">
                                <span>
                                    <svg v-if="showPassword" width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><g id="Edit / Show"><g id="Vector"><path d="M3.5868 13.7788C5.36623 15.5478 8.46953 17.9999 12.0002 17.9999C15.5308 17.9999 18.6335 15.5478 20.413 13.7788C20.8823 13.3123 21.1177 13.0782 21.2671 12.6201C21.3738 12.2933 21.3738 11.7067 21.2671 11.3799C21.1177 10.9218 20.8823 10.6877 20.413 10.2211C18.6335 8.45208 15.5308 6 12.0002 6C8.46953 6 5.36623 8.45208 3.5868 10.2211C3.11714 10.688 2.88229 10.9216 2.7328 11.3799C2.62618 11.7067 2.62618 12.2933 2.7328 12.6201C2.88229 13.0784 3.11714 13.3119 3.5868 13.7788Z" stroke="#A1A6B2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M10 12C10 13.1046 10.8954 14 12 14C13.1046 14 14 13.1046 14 12C14 10.8954 13.1046 10 12 10C10.8954 10 10 10.8954 10 12Z" stroke="#A1A6B2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></g></g></g></svg>
                                    <svg v-else width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><g id="Edit / Hide"><path id="Vector" d="M3.99989 4L19.9999 20M16.4999 16.7559C15.1473 17.4845 13.6185 17.9999 11.9999 17.9999C8.46924 17.9999 5.36624 15.5478 3.5868 13.7788C3.1171 13.3119 2.88229 13.0784 2.7328 12.6201C2.62619 12.2933 2.62616 11.7066 2.7328 11.3797C2.88233 10.9215 3.11763 10.6875 3.58827 10.2197C4.48515 9.32821 5.71801 8.26359 7.17219 7.42676M19.4999 14.6335C19.8329 14.3405 20.138 14.0523 20.4117 13.7803L20.4146 13.7772C20.8832 13.3114 21.1182 13.0779 21.2674 12.6206C21.374 12.2938 21.3738 11.7068 21.2672 11.38C21.1178 10.9219 20.8827 10.6877 20.4133 10.2211C18.6338 8.45208 15.5305 6 11.9999 6C11.6624 6 11.3288 6.02241 10.9999 6.06448M13.3228 13.5C12.9702 13.8112 12.5071 14 11.9999 14C10.8953 14 9.99989 13.1046 9.99989 12C9.99989 11.4605 10.2135 10.9711 10.5608 10.6113" stroke="#A1A6B2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></g></g></svg>
                                </span>
                            </div>
                        </div>

                        <!-- Nhập lại mật khẩu -->
                        <div class="relative">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Nhập lại mật khẩu</label>
                            <input
                                :type="!showConfirmPassword ? 'password' : 'text'"
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                class="text-black mt-1 block w-full border-[#CECECE] rounded-md shadow-sm px-4 py-2 focus:ring-ai3goc-primary focus:border-ai3goc-primary"
                                placeholder="Xác nhận mật khẩu mới"
                                required
                            />

                            <div class="absolute top-[.9rem] right-5 cursor-pointer mt-5" @click="toggleShow(2)">
                                <span>
                                    <svg v-if="showConfirmPassword" width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><g id="Edit / Show"><g id="Vector"><path d="M3.5868 13.7788C5.36623 15.5478 8.46953 17.9999 12.0002 17.9999C15.5308 17.9999 18.6335 15.5478 20.413 13.7788C20.8823 13.3123 21.1177 13.0782 21.2671 12.6201C21.3738 12.2933 21.3738 11.7067 21.2671 11.3799C21.1177 10.9218 20.8823 10.6877 20.413 10.2211C18.6335 8.45208 15.5308 6 12.0002 6C8.46953 6 5.36623 8.45208 3.5868 10.2211C3.11714 10.688 2.88229 10.9216 2.7328 11.3799C2.62618 11.7067 2.62618 12.2933 2.7328 12.6201C2.88229 13.0784 3.11714 13.3119 3.5868 13.7788Z" stroke="#A1A6B2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M10 12C10 13.1046 10.8954 14 12 14C13.1046 14 14 13.1046 14 12C14 10.8954 13.1046 10 12 10C10.8954 10 10 10.8954 10 12Z" stroke="#A1A6B2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></g></g></g></svg>
                                    <svg v-else width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><g id="Edit / Hide"><path id="Vector" d="M3.99989 4L19.9999 20M16.4999 16.7559C15.1473 17.4845 13.6185 17.9999 11.9999 17.9999C8.46924 17.9999 5.36624 15.5478 3.5868 13.7788C3.1171 13.3119 2.88229 13.0784 2.7328 12.6201C2.62619 12.2933 2.62616 11.7066 2.7328 11.3797C2.88233 10.9215 3.11763 10.6875 3.58827 10.2197C4.48515 9.32821 5.71801 8.26359 7.17219 7.42676M19.4999 14.6335C19.8329 14.3405 20.138 14.0523 20.4117 13.7803L20.4146 13.7772C20.8832 13.3114 21.1182 13.0779 21.2674 12.6206C21.374 12.2938 21.3738 11.7068 21.2672 11.38C21.1178 10.9219 20.8827 10.6877 20.4133 10.2211C18.6338 8.45208 15.5305 6 11.9999 6C11.6624 6 11.3288 6.02241 10.9999 6.06448M13.3228 13.5C12.9702 13.8112 12.5071 14 11.9999 14C10.8953 14 9.99989 13.1046 9.99989 12C9.99989 11.4605 10.2135 10.9711 10.5608 10.6113" stroke="#A1A6B2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></g></g></svg>
                                </span>
                            </div>
                        </div>
                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="w-full bg-ai3goc-sec lg:bg-ai3goc-primary text-white text-lg font-semibold rounded-md px-4 py-2 hover:opacity-90"
                        >
                            Đặt lại mật khẩu
                        </button>
                    </form>
                </div>
            </main>

        </div>
    </div>
</template>

<script setup>
import Footer from "../../Home/Components/Footer.vue";
import Layout from "@/Layouts/Client/ClientLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import axios from "axios";
import {toast} from "vue3-toastify";
defineOptions({ layout: Layout });

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = ref({
    email: "",
    password: "",
    password_confirmation: "",
    token: "",
});

onMounted(() => {
    const pathname = window.location.pathname;
    const segments = pathname.split("/");
    form.value.token = segments[segments.length - 1] || "";

    const urlParams = new URLSearchParams(window.location.search);
    form.value.email = urlParams.get("email") || "";

    if (!form.value.token || !form.value.email) {
        alert("Liên kết không hợp lệ. Vui lòng thử lại!");
        window.location.href = "/";
    }
});



const resetPassword = async () => {
    try {
        const response = await axios.post("/password/reset", {
            token: form.value.token,
            email: form.value.email,
            password: form.value.password,
            password_confirmation: form.value.password_confirmation,
        });

        window.location.href = "/login";
    } catch (error) {
        if (error.response && error.response.status === 422) {
            const errors = error.response.data.errors;
            for (let key in errors) {
                if (errors.hasOwnProperty(key)) {
                    toast.error(errors[key][0]);
                }
            }
        } else {
            toast.error('Không thể gửi yêu cầu đặt lại mật khẩu!');
        }
        console.error('Lỗi:', error.response?.data || error);
    }
};

const toggleShow = (type) => {
    if(type == 1) {
        showPassword.value = showPassword.value ? false : true
    }
    if(type == 2) {
        showConfirmPassword.value = showConfirmPassword.value ? false : true
    }
}
</script>
