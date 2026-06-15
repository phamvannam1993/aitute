<template>
    <div class="flex max-sm:mt-12 sm:items-center justify-center sm:h-screen">
        <div class="bg-white w-[90%] lg:w-[524px] rounded-3xl p-6 shadow-lg relative">

            <!-- Tiêu đề -->
            <div class="text-center mb-6">
                <img src="/assets/img/orion/logo/slogan.svg" alt="Logo" class="w-[240px] h-auto mx-auto" />
                <h2 class="font-bold text-[24px] lg:text-3xl text-orion-sec mt-4">
                    Kích hoạt tài khoản
                </h2>
            </div>

            <!-- Form kích hoạt -->
            <form>
                <!-- name -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Tên <span class="text-red-500">*</span>
                    </label>
                    <input v-model="form.name" type="text" id="name"
                        class="w-full border p-3 rounded-xl focus:ring-orion-primary focus:border-orion-primary text-black"
                        placeholder="Nhập tên của bạn" required />
                </div>
                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input v-model="form.email" type="email" id="email"
                        class="w-full border p-3 rounded-xl focus:ring-orion-primary focus:border-orion-primary text-black"
                        placeholder="Nhập email của bạn" required />
                </div>

                <!-- Mật khẩu -->
                <div class="mb-4 relative">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Mật khẩu <span class="text-red-500">*</span>
                    </label>
                    <input v-model="form.password" :type="showPassword ? 'text' : 'password'" id="password"
                        class="w-full border p-3 rounded-xl focus:ring-orion-primary focus:border-orion-primary text-black"
                        placeholder="Nhập mật khẩu của bạn" required />
                    <span class="absolute top-[60%] right-4 transform -translate-y-1/2 cursor-pointer"
                        @click="togglePasswordVisibility">
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
                        Key kích hoạt <span class="text-red-500">*</span>
                    </label>
                    <input v-model="form.activationKey" :type="showKey ? 'text' : 'password'" id="activationKey"
                        class="w-full border p-3 rounded-xl focus:ring-orion-primary focus:border-orion-primary text-black"
                        placeholder="Nhập key kích hoạt" required />
                    <span class="absolute top-[60%] right-4 transform -translate-y-1/2 cursor-pointer"
                        @click="toggleKeyVisibility">
                        <span v-if="showKey">
                            <img src="/assets/svgs/aiwow/eye_open.svg" alt="show key" />
                        </span>
                        <span v-else>
                            <img src="/assets/svgs/aiwow/eye_close.svg" alt="show key" />
                        </span>
                    </span>
                </div>

                <button type="button" @click="handleActivate" :disabled="isLoading"
                    class="w-full py-3 bg-orion-primary text-white rounded-xl hover:bg-orion-primary/90 focus:outline-none disabled:opacity-70 disabled:cursor-not-allowed relative">
                    <span v-if="!isLoading">Kích hoạt</span>
                    <div v-else class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
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

const handleActivate = async () => {
    try {
        const payload = {
            name: form.name,
            email: form.email,
            password: form.password,
            activationKey: form.activationKey,
        };
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
