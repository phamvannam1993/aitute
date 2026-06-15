<template>
    <div
        v-if="isVisible"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white w-[90%] lg:w-[524px] rounded-3xl p-6 shadow-lg relative">
            <!-- Nút đóng -->
            <button
                @click="closePopup"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-xl"
            >
                <img src="/assets/img/orion/icon/close-active-account.svg" alt="Close" class="w-[32px] h-auto" />
            </button>

            <!-- Tiêu đề -->
            <div class="text-center mb-6">
                <h2 class="font-bold text-[24px] lg:text-3xl text-ai3goc-primary mt-4">
                    Kích hoạt tài khoản
                </h2>
            </div>

            <!-- Form kích hoạt -->
            <form >
                <!-- name -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Tên
                    </label>
                    <input
                        v-model="form.name"
                        type="text"
                        id="name"
                        class="w-full border p-3 rounded-xl focus:ring-ai3goc-sec focus:border-ai3goc-sec text-black"
                        placeholder="Nhập tên của bạn"
                        required
                    />
                </div>
                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email
                    </label>
                    <input
                        v-model="form.email"
                        type="email"
                        id="email"
                        class="w-full border p-3 rounded-xl focus:ring-ai3goc-sec focus:border-ai3goc-sec text-black"
                        placeholder="Nhập email của bạn"
                        required
                    />
                </div>

                <!-- Mật khẩu -->
                <div class="mb-4 relative">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Mật khẩu
                    </label>
                    <input
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        id="password"
                        class="w-full border p-3 rounded-xl focus:ring-ai3goc-sec focus:border-ai3goc-sec text-black"
                        placeholder="Nhập mật khẩu của bạn"
                        required
                    />
                    <span
                        class="absolute top-[60%] right-4 transform -translate-y-1/2 cursor-pointer"
                        @click="togglePasswordVisibility"
                    >
                        <span v-if="showPassword">
                            <img src="/assets/svgs/aiwow/eye_open.svg" alt="show password" />
                        </span>
                        <span v-else>
                            <img src="/assets/svgs/aiwow/eye_close.svg" alt="show password" />
                        </span>
                    </span>
                </div>

                <!-- Keys kích hoạt -->
                <div class="mb-6 relative">
                    <label for="activationKey" class="block text-sm font-medium text-gray-700 mb-1">
                        Key kích hoạt
                    </label>
                    <input
                        v-model="form.activationKey"
                        :type="showKey ? 'text' : 'password'"
                        id="activationKey"
                        class="w-full border p-3 rounded-xl focus:ring-ai3goc-sec focus:border-ai3goc-sec text-black"
                        placeholder="Nhập key kích hoạt"
                        required
                    />
                    <span
                        class="absolute top-[60%] right-4 transform -translate-y-1/2 cursor-pointer"
                        @click="toggleKeyVisibility"
                    >
                        <span v-if="showKey">
                            <img src="/assets/svgs/aiwow/eye_open.svg" alt="show key" />
                        </span>
                        <span v-else>
                            <img src="/assets/svgs/aiwow/eye_close.svg" alt="show key" />
                        </span>
                    </span>
                </div>

                <button
                    type="button"
                    @click="activateAccount"
                    :disabled="isLoading"
                    class="w-full py-3 bg-ai3goc-sec text-white rounded-xl hover:bg-ai3goc-sec/90 focus:outline-none disabled:opacity-70 disabled:cursor-not-allowed relative"
                >
                    <span v-if="!isLoading">Kích hoạt</span>
                    <div v-else class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Đang xử lý...
                    </div>
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
    import { reactive, ref } from "vue";

    const props = defineProps({
        isVisible: {
            type: Boolean,
            default: false,
        },
        isLoading: {
            type: Boolean,
            default: false,
        },
    });

    const emit = defineEmits(["close", "activate"]);
    const isLoading = ref(false);
    const form = reactive({
        name: "",
        email: "",
        password: "",
        activationKey: "",
    });

    const showPassword = ref(false);

    const togglePasswordVisibility = () => {
        showPassword.value = !showPassword.value;
    };

    const showKey = ref(false);

    const toggleKeyVisibility = () => {
        showKey.value = !showKey.value;
    };

    const closePopup = () => {
        emit("close");
    };

    const activateAccount = async () => {
        try {
            const payload = {
                name: form.name,
                email: form.email,
                password: form.password,
                activationKey: form.activationKey,
            };
            console.log("Activating account with:", payload);
            await emit("activate", payload);
        } finally {
        }
    };
</script>

<style scoped>
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
</style>
