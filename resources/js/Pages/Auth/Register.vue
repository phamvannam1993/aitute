<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import CodeInput from "../../Components/CodeInput.vue";

const showPassword = ref(false);
const showConfirmPassword = ref(false);
const verifyCode = ref(false);
const successPopup = ref(false);

const form = useForm({
    name: "",
    username: "",
    password: "",
    password_confirmation: "",
    phone: "",
    email: "",
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Head title="Log in" />

    <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
        {{ status }}
    </div>

    <div class="md:grid grid-cols-2 font-lexend">
        <div class="banner w-full h-screen hidden md:block">
            <div class="lg:w-[540px] px-2 mx-auto mt-[70px] font-lexend">
                <h1 class="text-[77px] text-[#00D1FF] font-bold">AIWOW</h1>
                <h3 class="text-xl font-semibold text-white">
                    TRỢ LÝ AI - TƯƠNG LAI BỨC PHÁ
                </h3>
                <p class="text-white">
                    Giải pháp giúp bạn trở nên hạnh phúc và giàu có <br />
                    Luôn cập nhật các công cụ AI mới nhất, bởi công cụ tổng hợp
                    AI số 1.
                </p>
            </div>
        </div>
        <div class="flex items-center">
            <div
                v-if="!verifyCode"
                class="md:w-[524px] h-full mx-auto flex flex-col justify-around overflow-hidden"
            >
                <div class="login">
                    <div
                        class="bg-gradient-to-b from-[#7dd6ff48] to-transparent p-5 rounded-3xl"
                    >
                        <div class="text-center mb-[44px] mt-[12px]">
                            <img
                                :src="'/assets/img/login_icon/logo.png'"
                                alt="logo"
                                class="w-[215px] h-[68px] mx-auto"
                            />
                            <h3
                                class="font-bold text-3xl text-[#2C75E3] font-lexend mt-3"
                            >
                                Đăng ký tài khoản
                            </h3>
                        </div>
                        <form @submit.prevent="submit" class="">
                            <div>
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full p-3 border-[#5FB2FF] rounded-xl"
                                    v-model="form.username"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="Nhập họ và tên*"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.username"
                                />
                            </div>
                            <div class="mt-5">
                                <TextInput
                                    id="username"
                                    type="text"
                                    class="mt-1 block w-full p-3 border-[#5FB2FF] rounded-xl"
                                    v-model="form.username"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="Nhập tên đăng nhập*"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.username"
                                />
                            </div>

                            <div class="flex justify-between mt-5">
                                <div class="relative w-[48%]">
                                    <TextInput
                                        id="password"
                                        :type="
                                            showPassword ? 'text' : 'password'
                                        "
                                        class="mt-1 block w-full p-3 border-[#5FB2FF] rounded-xl pr-16"
                                        v-model="form.password"
                                        required
                                        autocomplete="current-password"
                                        placeholder="Nhập mật khẩu*"
                                        lang="en"
                                    />
                                    <div
                                        class="absolute top-[1rem] right-5 cursor-pointer"
                                        @click="showPassword = !showPassword"
                                    >
                                        <span v-if="showPassword">
                                            <svg
                                                width="24px"
                                                height="24px"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <g
                                                    id="SVGRepo_bgCarrier"
                                                    stroke-width="0"
                                                ></g>
                                                <g
                                                    id="SVGRepo_tracerCarrier"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                ></g>
                                                <g id="SVGRepo_iconCarrier">
                                                    <g id="Edit / Show">
                                                        <g id="Vector">
                                                            <path
                                                                d="M3.5868 13.7788C5.36623 15.5478 8.46953 17.9999 12.0002 17.9999C15.5308 17.9999 18.6335 15.5478 20.413 13.7788C20.8823 13.3123 21.1177 13.0782 21.2671 12.6201C21.3738 12.2933 21.3738 11.7067 21.2671 11.3799C21.1177 10.9218 20.8823 10.6877 20.413 10.2211C18.6335 8.45208 15.5308 6 12.0002 6C8.46953 6 5.36623 8.45208 3.5868 10.2211C3.11714 10.688 2.88229 10.9216 2.7328 11.3799C2.62618 11.7067 2.62618 12.2933 2.7328 12.6201C2.88229 13.0784 3.11714 13.3119 3.5868 13.7788Z"
                                                                stroke="#A1A6B2"
                                                                stroke-width="2"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                            ></path>
                                                            <path
                                                                d="M10 12C10 13.1046 10.8954 14 12 14C13.1046 14 14 13.1046 14 12C14 10.8954 13.1046 10 12 10C10.8954 10 10 10.8954 10 12Z"
                                                                stroke="#A1A6B2"
                                                                stroke-width="2"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                            ></path>
                                                        </g>
                                                    </g>
                                                </g>
                                            </svg>
                                        </span>
                                        <span v-else>
                                            <svg
                                                width="24px"
                                                height="24px"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <g
                                                    id="SVGRepo_bgCarrier"
                                                    stroke-width="0"
                                                ></g>
                                                <g
                                                    id="SVGRepo_tracerCarrier"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                ></g>
                                                <g id="SVGRepo_iconCarrier">
                                                    <g id="Edit / Hide">
                                                        <path
                                                            id="Vector"
                                                            d="M3.99989 4L19.9999 20M16.4999 16.7559C15.1473 17.4845 13.6185 17.9999 11.9999 17.9999C8.46924 17.9999 5.36624 15.5478 3.5868 13.7788C3.1171 13.3119 2.88229 13.0784 2.7328 12.6201C2.62619 12.2933 2.62616 11.7066 2.7328 11.3797C2.88233 10.9215 3.11763 10.6875 3.58827 10.2197C4.48515 9.32821 5.71801 8.26359 7.17219 7.42676M19.4999 14.6335C19.8329 14.3405 20.138 14.0523 20.4117 13.7803L20.4146 13.7772C20.8832 13.3114 21.1182 13.0779 21.2674 12.6206C21.374 12.2938 21.3738 11.7068 21.2672 11.38C21.1178 10.9219 20.8827 10.6877 20.4133 10.2211C18.6338 8.45208 15.5305 6 11.9999 6C11.6624 6 11.3288 6.02241 10.9999 6.06448M13.3228 13.5C12.9702 13.8112 12.5071 14 11.9999 14C10.8953 14 9.99989 13.1046 9.99989 12C9.99989 11.4605 10.2135 10.9711 10.5608 10.6113"
                                                            stroke="#A1A6B2"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                        ></path>
                                                    </g>
                                                </g>
                                            </svg>
                                        </span>
                                    </div>
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.password"
                                    />
                                </div>
                                <div class="relative w-[48%]">
                                    <TextInput
                                        id="password_confirmation"
                                        :type="
                                            showConfirmPassword
                                                ? 'text'
                                                : 'password'
                                        "
                                        class="mt-1 block w-full p-3 border-[#5FB2FF] rounded-xl pr-16"
                                        v-model="form.password_confirmation"
                                        required
                                        autocomplete="current-password_confirmation"
                                        placeholder="Nhập lại mật khẩu*"
                                        lang="en"
                                    />
                                    <div
                                        class="absolute top-[1rem] right-5 cursor-pointer"
                                        @click="
                                            showConfirmPassword =
                                                !showConfirmPassword
                                        "
                                    >
                                        <span v-if="showConfirmPassword">
                                            <svg
                                                width="24px"
                                                height="24px"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <g
                                                    id="SVGRepo_bgCarrier"
                                                    stroke-width="0"
                                                ></g>
                                                <g
                                                    id="SVGRepo_tracerCarrier"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                ></g>
                                                <g id="SVGRepo_iconCarrier">
                                                    <g id="Edit / Show">
                                                        <g id="Vector">
                                                            <path
                                                                d="M3.5868 13.7788C5.36623 15.5478 8.46953 17.9999 12.0002 17.9999C15.5308 17.9999 18.6335 15.5478 20.413 13.7788C20.8823 13.3123 21.1177 13.0782 21.2671 12.6201C21.3738 12.2933 21.3738 11.7067 21.2671 11.3799C21.1177 10.9218 20.8823 10.6877 20.413 10.2211C18.6335 8.45208 15.5308 6 12.0002 6C8.46953 6 5.36623 8.45208 3.5868 10.2211C3.11714 10.688 2.88229 10.9216 2.7328 11.3799C2.62618 11.7067 2.62618 12.2933 2.7328 12.6201C2.88229 13.0784 3.11714 13.3119 3.5868 13.7788Z"
                                                                stroke="#A1A6B2"
                                                                stroke-width="2"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                            ></path>
                                                            <path
                                                                d="M10 12C10 13.1046 10.8954 14 12 14C13.1046 14 14 13.1046 14 12C14 10.8954 13.1046 10 12 10C10.8954 10 10 10.8954 10 12Z"
                                                                stroke="#A1A6B2"
                                                                stroke-width="2"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                            ></path>
                                                        </g>
                                                    </g>
                                                </g>
                                            </svg>
                                        </span>
                                        <span v-else>
                                            <svg
                                                width="24px"
                                                height="24px"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <g
                                                    id="SVGRepo_bgCarrier"
                                                    stroke-width="0"
                                                ></g>
                                                <g
                                                    id="SVGRepo_tracerCarrier"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                ></g>
                                                <g id="SVGRepo_iconCarrier">
                                                    <g id="Edit / Hide">
                                                        <path
                                                            id="Vector"
                                                            d="M3.99989 4L19.9999 20M16.4999 16.7559C15.1473 17.4845 13.6185 17.9999 11.9999 17.9999C8.46924 17.9999 5.36624 15.5478 3.5868 13.7788C3.1171 13.3119 2.88229 13.0784 2.7328 12.6201C2.62619 12.2933 2.62616 11.7066 2.7328 11.3797C2.88233 10.9215 3.11763 10.6875 3.58827 10.2197C4.48515 9.32821 5.71801 8.26359 7.17219 7.42676M19.4999 14.6335C19.8329 14.3405 20.138 14.0523 20.4117 13.7803L20.4146 13.7772C20.8832 13.3114 21.1182 13.0779 21.2674 12.6206C21.374 12.2938 21.3738 11.7068 21.2672 11.38C21.1178 10.9219 20.8827 10.6877 20.4133 10.2211C18.6338 8.45208 15.5305 6 11.9999 6C11.6624 6 11.3288 6.02241 10.9999 6.06448M13.3228 13.5C12.9702 13.8112 12.5071 14 11.9999 14C10.8953 14 9.99989 13.1046 9.99989 12C9.99989 11.4605 10.2135 10.9711 10.5608 10.6113"
                                                            stroke="#A1A6B2"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                        ></path>
                                                    </g>
                                                </g>
                                            </svg>
                                        </span>
                                    </div>
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.password"
                                    />
                                </div>
                            </div>

                            <div class="mt-5">
                                <TextInput
                                    id="phone"
                                    type="text"
                                    class="mt-1 block w-full p-3 border-[#5FB2FF] rounded-xl"
                                    v-model="form.phone"
                                    required
                                    autofocus
                                    autocomplete="phone"
                                    placeholder="Nhập số điện thoại*"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.username"
                                />
                            </div>
                            <div class="mt-5">
                                <TextInput
                                    id="email"
                                    type="text"
                                    class="mt-1 block w-full p-3 border-[#5FB2FF] rounded-xl"
                                    v-model="form.email"
                                    required
                                    autofocus
                                    autocomplete="email"
                                    placeholder="Nhập Email*"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.username"
                                />
                            </div>
                            <div
                                class="mt-4 flex text-xs items-center gap-2 font-light"
                            >
                                <span>
                                    <svg
                                        width="24px"
                                        height="24px"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        stroke="#2C75E3"
                                    >
                                        <g
                                            id="SVGRepo_bgCarrier"
                                            stroke-width="0"
                                        ></g>
                                        <g
                                            id="SVGRepo_tracerCarrier"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        ></g>
                                        <g id="SVGRepo_iconCarrier">
                                            <path
                                                d="M12 17V11"
                                                stroke="#2C75E3"
                                                stroke-width="1.5"
                                                stroke-linecap="round"
                                            ></path>
                                            <circle
                                                cx="1"
                                                cy="1"
                                                r="1"
                                                transform="matrix(1 0 0 -1 11 9)"
                                                fill="#2C75E3"
                                            ></circle>
                                            <path
                                                opacity="0.5"
                                                d="M2 12C2 7.28595 2 4.92893 3.46447 3.46447C4.92893 2 7.28595 2 12 2C16.714 2 19.0711 2 20.5355 3.46447C22 4.92893 22 7.28595 22 12C22 16.714 22 19.0711 20.5355 20.5355C19.0711 22 16.714 22 12 22C7.28595 22 4.92893 22 3.46447 20.5355C2 19.0711 2 16.714 2 12Z"
                                                stroke="#2C75E3"
                                                stroke-width="1.5"
                                            ></path>
                                        </g>
                                    </svg>
                                </span>
                                <p class="text-[#999999]">
                                    Mã xác nhận sẽ được gửi đến số điện thoại
                                    này để kích hoạt tài khoản.
                                </p>
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <button
                                    :disabled="form.processing"
                                    class="bg-[#2C75E3] text-white w-full rounded-xl p-3 hover:opacity-90"
                                >
                                    Đăng nhập
                                </button>
                            </div>
                        </form>
                        <div class="flex gap-3 justify-center mt-4">
                            <p>Đã có tài khoản?</p>
                            <a :href="route('login')" class="text-[#2C75E3]">Đăng nhập ngay</a>
                        </div>
                    </div>
                </div>
            </div>
            <div
                v-else
                class="w-[524px] h-full mx-auto flex flex-col justify-around overflow-hidden"
            >
                <div class="login">
                    <div
                        class="bg-gradient-to-b from-[#7dd6ff48] to-transparent p-5 rounded-3xl"
                    >
                        <div class="text-center mb-[44px] mt-[12px]">
                            <img
                                :src="'/assets/img/login_icon/logo.png'"
                                alt="logo"
                                class="w-[215px] h-[68px] mx-auto"
                            />
                            <h3
                                class="font-bold text-3xl text-[#2C75E3] font-lexend mt-3"
                            >
                                Xác thực tài khoản
                            </h3>
                            <img
                                :src="'/assets/img/login_icon/confirm.png'"
                                alt="logo"
                                class="mt-4 mx-auto"
                            />
                        </div>
                        <div class="text-center">
                            <p>
                                Nhập mã xác thực được gửi đến số điện thoại của
                                bạn
                            </p>
                            <p
                                class="my-4 font-semibold text-2xl text-[#2C75E3]"
                            >
                                01:35
                            </p>
                        </div>
                        <code-input
                            @complete="completed = true"
                            :fields="6"
                            :fieldWidth="56"
                            :fieldHeight="56"
                            :required="true"
                        />
                        <div class="flex gap-3 justify-center mt-4">
                            <p>Không nhận được mã?</p>
                            <a href="" class="text-[#2C75E3]">Gửi lại mã</a>
                        </div>
                        <button
                            class="btn p-4 bg-[#2C75E3] w-full text-center text-white rounded-2xl mt-12"
                            :disabled="!completed"
                        >
                            Xác nhận
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-if="successPopup" class="login w-[600px] h-[400px] shadow-[0px_0px_0px_1px_rgba(0,0,0,0.06),0px_1px_1px_-0.5px_rgba(0,0,0,0.06),0px_3px_3px_-1.5px_rgba(0,0,0,0.06),_0px_6px_6px_-3px_rgba(0,0,0,0.06),0px_12px_12px_-6px_rgba(0,0,0,0.06),0px_24px_24px_-12px_rgba(0,0,0,0.06)] rounded-[2rem]">
        <div
            class="rounded-[2rem] bg-gradient-to-b from-[#7dd6ff48] to-transparent text-center px-10"
        >
            <img
                :src="'/assets/img/login_icon/success.png'"
                class="mx-auto"
                alt=""
            />
            <h4>Xin chào, <span class="font-bold text-xl">Nguyễn Văn A!</span></h4>
            <p class="mt-4">
                Bạn đã đăng ký tài khoản thành công. <br>
                Bạn có thể cập nhật và chỉnh sửa thông tin trong phần thông tin cá nhân.
            </p>
            <div class="flex gap-6 justify-center mt-6">
                <button class="p-3 font-semibold border-2 rounded-2xl border-[#2C75E3] w-[180px]">Bỏ qua</button>
                <button class="p-3 font-semibold border-2 rounded-2xl border-[#2C75E3] text-white bg-[#2C75E3] w-[180px]">Cập nhật ngay</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.banner {
    background-image: url("/public/assets/img/login_icon/register.png");
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
}

.login {
    background-image: url("/public/assets/img/login_icon/dec.png");
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
}
</style>
