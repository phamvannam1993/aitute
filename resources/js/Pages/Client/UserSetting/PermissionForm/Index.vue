<template>
    <Head title="Ngành nghề - AI 3 GỐC - Cộng đồng AI tử tế" />
    <div class="flex flex-col">
        <div class="w-full bg-cover bg-center min-h-screen lg:p-10 pt-[50px] lg:pt[25px]">
            <div class="flex items-center space-x-2 px-[10px]">
                <a :href="route('home.index')">
                    <img src="/assets/img/admin/icon_left_home.png" alt="Trang chủ" class="w-auto h-[19px]" />
                </a>
                <span class="font-medium text-[#2C75E3]">/ Permission</span>
            </div>
            <!-- Permission Content -->
            <div class="flex items-center justify-center px-4 lg:px-0 mt-10">
                <div class="bg-white rounded-xl shadow-lg p-6 lg:p-10 max-w-2xl w-full">
                    <div class="text-center">
                        <!-- Lock Icon -->
                        <div class="mx-auto w-20 h-20 lg:w-24 lg:h-24 bg-red-50 rounded-full flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 lg:w-12 lg:h-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>

                        <!-- Title -->
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 mb-4">Không có quyền truy cập</h1>

                        <!-- Description -->
                        <p class="text-gray-600 mb-8 text-sm lg:text-base">
                            Rất tiếc, bạn không có quyền truy cập tính năng này.<br />
                            Vui lòng nâng cấp tài khoản để sử dụng đầy đủ các tính năng.
                        </p>

                        <!-- Buttons -->
                        <div class="flex flex-col lg:flex-row items-center justify-center space-y-4 lg:space-y-0 lg:space-x-4">
                            <a :href="route('pricing.index')" class="w-full lg:w-auto px-6 py-3 bg-[#2C75E3] text-white rounded-lg hover:bg-[#2361c0] transition duration-300 text-center"> Nâng cấp tài khoản </a>
                            <a :href="route('home.index')" class="w-full lg:w-auto px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-300"> Quay lại </a>
                        </div>

                        <!-- Additional Info -->
                        <!-- <div class="mt-8 text-sm text-gray-500">
                            <p>Nếu bạn cần hỗ trợ, vui lòng liên hệ với chúng tôi qua:</p>
                            <p class="mt-2">
                                <a href="mailto:vuontrithucviet@gmail.com" class="text-[#2C75E3] hover:underline">vuontrithucviet@gmail.com</a>
                            </p>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
        <Footer class="mt-auto" />
    </div>
</template>

<script setup>
import Footer from "../../Home/Components/Footer.vue";
import Layout from "@/Layouts/Client/ClientLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import axios from "axios";
import { toast } from "vue3-toastify";
defineOptions({ layout: Layout });
</script>
