<template>

<Master>
    <div class="flex w-full flex-col">
        <div class="w-full bg-cover bg-center min-h-screen lg:p-10 pt-[50px] lg:pt[25px]"
            style="background-image: url('/assets/svgs/bg-home.svg');">
            <div class="flex items-center space-x-2 px-[10px]">
                <a :href="route('home.index')">
                    <img src="/assets/img/admin/icon_left_home.png" alt="Trang chủ" class="w-auto h-[19px]" />
                </a>
                <span class="font-medium text-[#2C75E3]">/ Permission</span>
            </div>
            <div class="flex items-center justify-center px-4 lg:px-0 mt-10">
                <div class="bg-white rounded-xl shadow-lg p-6 lg:p-10 max-w-2xl w-full">
                    <div class="text-center">
                        <div
                            class="mx-auto w-20 h-20 lg:w-24 lg:h-24 bg-red-50 rounded-full flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 lg:w-12 lg:h-12 text-red-500"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>

                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 mb-4">
                            Không có quyền truy cập
                        </h1>

                        <p class="text-gray-600 mb-8 text-sm lg:text-base">
                            Rất tiếc, bạn không có quyền truy cập tính năng này.<br>
                        </p>

                        <div
                            class="flex flex-col lg:flex-row items-center justify-center space-y-4 lg:space-y-0 lg:space-x-4">
                            <a :href="route('admin.index')"
                                class="w-full lg:w-auto px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-300">
                                Quay lại
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <Footer class="mt-auto" />
    </div>
</Master>
</template>

<script setup>
import Master from "../../../Layouts/Admin/Master.vue";

</script>
