<template>
    <div class="flex flex-col text-[#000] my-7 space-y-8">
        <div class="settings-page container mx-auto p-6 bg-white rounded-lg shadow-lg">
            <!-- Tiêu đề -->
            <h1 class="text-3xl font-bold mb-8 text-center text-gray-800">Cài đặt Tài Khoản</h1>

            <!-- Nội dung chính -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Hiển thị ngành nghề -->
                <div class="bg-gray-50 p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Hiển thị ngành nghề yêu thích khi đăng nhập</h2>
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div class="flex items-center space-x-10">
                            <label class="flex items-center space-x-3">
                                <input
                                    type="radio"
                                    name="industry"
                                    value="1"
                                    v-model="form.first_login"
                                    class="form-radio h-5 w-5 text-blue-600 focus:ring-blue-500"
                                />
                                <span class="text-gray-700 text-lg">Hiển thị</span>
                            </label>
                            <label class="flex items-center space-x-3">
                                <input
                                    type="radio"
                                    name="industry"
                                    value="0"
                                    v-model="form.first_login"
                                    class="form-radio h-5 w-5 text-blue-600 focus:ring-blue-500"
                                />
                                <span class="text-gray-700 text-lg">Không hiển thị</span>
                            </label>
                        </div>
                        <button
                            type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-green-500 to-green-700 text-white text-lg font-semibold rounded-md hover:bg-gradient-to-l transition-all duration-300 ease-in-out shadow-md hover:shadow-lg w-full sm:w-auto mt-[54px] "
                        >
                            Cập nhật
                        </button>

                    </form>
                </div>

                <!-- Đổi mật khẩu -->
                <div class="bg-gray-50 p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Đổi mật khẩu</h2>
                    <form @submit.prevent="sendResetPasswordLink" class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input
                                type="email"
                                id="email"
                                v-model="resetForm.email"
                                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Nhập email của bạn"
                                required
                            />
                        </div>
                        <button
                            type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-green-500 to-green-700 text-white text-lg font-semibold rounded-md hover:bg-gradient-to-l transition-all duration-300 ease-in-out shadow-md hover:shadow-lg w-full sm:w-auto"
                        >
                            Gửi yêu cầu đặt lại mật khẩu
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Lịch sử sử dụng -->
        <div class="container mx-auto p-6 bg-white rounded-lg shadow-lg text-center">
            <h3 class="text-2xl font-bold mb-6">Lịch Sử Sử Dụng</h3>
            <a
                href="/credit/history"
                class="px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-700 text-white text-lg font-semibold rounded-md hover:bg-gradient-to-l transition-all duration-300 ease-in-out shadow-md hover:shadow-lg"
            >
                Xem
            </a>
        </div>
    </div>
</template>


<script setup>
    import {computed, ref} from 'vue';
    import axios from 'axios';
    import { toast } from "vue3-toastify";
    import 'vue3-toastify/dist/index.css';
    import {usePage} from "@inertiajs/vue3";

    const props = defineProps({
        firstLogin: Number,
    });

    const { props: pageProps } = usePage();
    const auth = computed(() => pageProps.value.auth);

    const form = ref({
        first_login: props.firstLogin
    });

    const resetForm = ref({
        email: pageProps.auth.user.email ,
    });

    const isLoadingMore = ref(false);

    const submitForm = async () => {
        isLoadingMore.value = true;

        try {
            const response = await axios.post('/settings/account/update', form.value);
            toast.success(response.data.message || 'Cập nhật thành công!');
        } catch (error) {
            if (error.response && error.response.status === 422) {
                const errors = error.response.data.errors;
                for (let key in errors) {
                    if (errors.hasOwnProperty(key)) {
                        toast.error(errors[key][0]);
                    }
                }
            } else {
                toast.error('Cập nhật thất bại!');
            }
            console.error('Cập nhật thất bại:', error.response.data);
        } finally {
            isLoadingMore.value = false;
        }
    };

    const sendResetPasswordLink = async () => {
        isLoadingMore.value = true;

        try {
            const response = await axios.post('/settings/password/forgot', resetForm.value);
            toast.success(response.data.message || 'Liên kết đặt lại mật khẩu đã được gửi!');
        } catch (error) {
            if (error.response && error.response.status === 422) {
                const errors = error.response.data.errors;
                for (let key in errors) {
                    if (errors.hasOwnProperty(key)) {
                        toast.error(errors[key][0]);
                    }
                }
            } else {
                toast.error('Không thể gửi liên kết đặt lại mật khẩu. Vui lòng thử lại sau 60 giây!');
            }
            console.error('Lỗi:', error.response?.data || error);
        } finally {
            isLoadingMore.value = false;
        }
    };
</script>

<style scoped>
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .spinner {
        border: 6px solid rgba(255, 255, 255, 0.3);
        border-left-color: #ffffff;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        100% {
            transform: rotate(360deg);
        }
    }
</style>
