<template>
    <Head title="Giọng nói - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData" isBig>
        <div v-if="menuItems.length === 0" class="flex items-center mx-auto gap-2 justify-center bg-white p-6 rounded-full shadow-md sm:w-[500px] w-[300px] mt-32">
            <img src="/assets/img/aiwow/homepage/logo.png" alt="No Projects" class="sm:w-32 w-12 h-auto" />
            <!-- Message Content -->
            <div class="text-center">
                <h2 class="sm:text-xl font-semibold text-orion-primary">Bạn chưa có dự án nào!</h2>
                <p class="sm:text-sm text-gray-600 mt-2">Vui lòng tạo dự án mới để bắt đầu trải nghiệm tính năng!</p>
            </div>
        </div>
        <div v-else>
            <ProjectMusic />
        </div>
        <div v-if="isNewProject" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50">
            <div class="bg-white rounded-2xl shadow-lg p-6 w-full max-w-lg">
                <div class="flex items-center">
                    <img src="/assets/img/aiwow/homepage/logo.png" alt="Project Icon" class="w-12 h-12 mr-4" />
                    <h2 class="text-xl font-semibold text-orion-primary">Tạo dự án mới</h2>
                </div>

                <div class="mt-6">
                    <input type="text" placeholder="Nhập tên dự án của bạn..." class="w-full p-3 border border-gray-300 rounded-lg focus:ring-orion-sec focus:border-orion-sec" />

                    <label class="block text-sm font-medium text-gray-700 mt-4 mb-2">Dự án là gì?</label>
                    <p class="text-[#8A8A8A]">Dự án lưu giữ các cuộc trò chuyện, tệp và hướng dẫn tùy chỉnh ở một nơi. Sử dụng chúng cho công việc đang diễn ra hoặc chỉ để giữ mọi thứ gọn gàng.</p>
                </div>

                <!-- Buttons -->
                <div class="flex justify-center mt-6 space-x-4">
                    <button @click="closeModal" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">Hủy</button>
                    <button @click="confirm" class="px-6 py-2 bg-orion-sec text-white rounded-lg hover:bg-orion-primary">Xác nhận</button>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import "suneditor/dist/css/suneditor.min.css";
import { computed, onMounted, ref, watch } from "vue";
import Footer from "../Home/Components/Footer.vue";
import Credit from "@/Components/Credit.vue";
import ProjectMusic from "./ProjectMusic.vue";
import Layout from "@/Layouts/Client/Layout.vue";

const isLoggedIn = ref(false);
const credits = ref(0);
const isNewProject = ref(false);

const menuItems = ref([
    {
        image: "/assets/img/my_assistant/p_red.png",
        name: "Fanpage Hello!",
        isActive: true,
    },
    {
        image: "/assets/img/my_assistant/p_green.png",
        name: "Dự án 2",
        isActive: false,
    },
    {
        image: "/assets/img/my_assistant/p_blue.png",
        name: "Dự án 3",
        isActive: false,
    },
]);

const breadcrumbsData = [
    { text: "Tạo nhạc doanh nghiệp" },
];

const checkAuthStatus = async () => {
    try {
        const response = await axios.get("/auth-check");
        isLoggedIn.value = response.data.authenticated;
    } catch (error) {
        console.error("Failed to check authentication status:", error);
    }
};

const createNewProject = () => {
    isNewProject.value = true;
};
const closeModal = () => {
    isNewProject.value = false;
};

onMounted(() => {
    checkAuthStatus();
});
</script>

<style scoped></style>