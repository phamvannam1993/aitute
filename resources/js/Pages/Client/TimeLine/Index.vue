<template>
    <Head title="Mốc thời gian - AI 3 GỐC - Cộng đồng AI tử tế" />
    <div class="flex flex-col min-h-screen">
        <div class="w-full bg-cover bg-center p-5 lg:p-10 pt-[50px]" >
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <a :href="route('home.index')">
                        <img src="/assets/img/admin/icon_left_home.png" alt="Trang chủ" class="w-auto h-[19px]"/>
                    </a>
                    <span class="font-medium text-[#2C75E3]">/ Mốc thời gian</span>
                </div>
                <Credit />
            </div>
            <MainMenu />
            <div class="flex flex-col">
                <div class="flex flex-col lg:flex-row text-[#000]">
                    <div class="lg:w-3/4 p-1 lg:p-10">
                        <div class="p-1 lg:p-5 flex flex-col">
                            <div class="flex flex-1 lg:justify-center lg:flex-[0.8]">
                                <div
                                    class="flex flex-col bg-white rounded-xl lg:rounded-3xl pt-0 lg:pt-6 pb-0 lg:pb-6 w-full max-h-[600px] overflow-y-auto"
                                >
                                    <div
                                        class="flex flex-col justify-center items-center p-6 lg:py-10"
                                    >
                                        <div class="w-11/12">
                                            <div
                                                v-for="(item, index) in timeline"
                                                :key="index"
                                                class="flex flex-col my-0 p-0 lg:my-0 lg:p-0"
                                            >
                                                <div class="flex lg:w-full lg:justify-center">
                                                    <div
                                                        class="flex before:content-[''] relative before:absolute before:w-[2px] before:h-[100%] before:bg-[#B1B1B1] before:right-[8%] before:top-[0] before-element"
                                                    >
                                                        <h2
                                                            class="text-lg font-bold text-black mr-3"
                                                        >
                                                            Tháng {{ item.month }} - {{ item.year }}
                                                        </h2>
                                                        <div
                                                            class="bg-blue-500 text-white rounded-full h-8 w-8 flex items-center justify-center z-10"
                                                        >
                                                            {{ item.month }}
                                                        </div>
                                                    </div>
                                                    <div class="ml-8 w-[250px]">
                                                        <h2
                                                            class="text-lg font-bold text-[#0055D3]"
                                                        >
                                                            {{ item.total }} Nghiệp vụ AI mới
                                                        </h2>
                                                        <!-- RENDER DATA TIME - LINE -->
                                                        <ul class="list-none relative">
                                                            <li
                                                                v-for="(
                                                        item, itemIndex
                                                    ) in item.ais"
                                                                :key="itemIndex"
                                                                class="text-gray-700 py-2"
                                                            >
                                                                {{ item.name }}
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
    
                    </div>
                    <div class="lg:w-1/4">
                        <Top10Ai :listAi="listAi" />
                    </div>
                </div>
            </div>
        </div>
        <Footer class="mt-auto"/>
    </div>
</template>

<script setup>
import MainMenu from "@/Components/MainMenu.vue";
import { onMounted, ref } from "vue";
import { Head } from "@inertiajs/vue3";
import Credit from '@/Components/Credit.vue';
import Footer from "../Home/Components/Footer.vue";
import Layout from "@/Layouts/Client/ClientLayout.vue";
import Top10Ai from "./../../../Components/Top10Ai.vue";

const props = defineProps({
    popularAssistants: Array,
    listAi: Array,
    timeline: Array
});

// An array to save state of heart
const heartStates = ref([]);
// handle change ui - pc - mobile
const isShowFee = ref(true);

// Data from BE
const asistants = [
    {
        id: 1,
        name: "Trợ lí nổi bật 1",
        des: "Tạo 10 ý tưởng trong 1 giây",
        course: "Giáo dục và đào tạo",
        price: "Miễn phí",
        image: "/assets/svgs/robo-capa1.svg",
        hex: "#FFD43B",
    },
    {
        id: 2,
        name: "Trợ lí nổi bật 1",
        des: "Tạo 10 ý tưởng trong 1 giây",
        course: "Giáo dục và đào tạo",
        price: "Miễn phí",
        image: "/assets/svgs/robo-capa2.svg",
        hex: "#2C75E3",
    },
    {
        id: 3,
        name: "Trợ lí nổi bật 1",
        des: "Tạo 10 ý tưởng trong 1 giây",
        course: "Giáo dục và đào tạo",
        price: "Miễn phí",
        image: "/assets/svgs/robo-capa3.svg",
        hex: "#FFEBA3",
    },
    {
        id: 4,
        name: "Trợ lí nổi bật 1",
        des: "Tạo 10 ý tưởng trong 1 giây",
        course: "Giáo dục và đào tạo",
        price: "Miễn phí",
        image: "/assets/svgs/robo-capa1.svg",
        hex: "#FFD43B",
    },
];

// Data from BE
const timelineData = [
    {
        number: 4,
        date: "Tháng 4 - 2014",
        title: "15 nghiệp vụ AI mới",
        aiServices: [
            "Phát triển phần mềm",
            "Quản lý nghiệp vụ tiếp thị",
            "Trang Wikipedia",
            "Quản lý email Outlook",
            "Hướng dẫn chơi cờ",
            "Cập nhật phát hành sản phẩm",
            "Phân tích bài đăng trên mạng xã hội",
            "Tìm kiếm nội dung giáo dục",
            "Kế hoạch ăn mừng",
            "Lưu đồ",
            "Nhập vai Anime",
            "Tự động hóa An ninh",
            "Tư vấn định cư",
            "Video Tiktok",
            "Quản lý thu chi",
        ],
    },
    {
        number: 3,
        date: "Tháng 3 - 2014",
        title: "5 nghiệp vụ AI mới",
        aiServices: [
            "Phát triển thời trang",
            "Dự đoán xu hướng bằng giọng nói",
            "Tư vấn thời trang",
            "Phân tích thị trường",
            "Đưa ra gợi ý cá nhân hóa",
        ],
    },
    {
        number: 2,
        date: "Tháng 2 - 2014",
        title: "5 nghiệp vụ AI mới",
        aiServices: [
            "Phát triển thời trang",
            "Dự đoán xu hướng bằng giọng nói",
            "Tư vấn thời trang",
            "Phân tích thị trường",
            "Đưa ra gợi ý cá nhân hóa",
        ],
    },
    {
        number: 1,
        date: "Tháng 4 - 2014",
        title: "15 nghiệp vụ AI mới",
        aiServices: [
            "Phát triển phần mềm",
            "Quản lý nghiệp vụ tiếp thị",
            "Trang Wikipedia",
            "Quản lý email Outlook",
            "Hướng dẫn chơi cờ",
            "Cập nhật phát hành sản phẩm",
            "Phân tích bài đăng trên mạng xã hội",
            "Tìm kiếm nội dung giáo dục",
            "Kế hoạch ăn mừng",
            "Lưu đồ",
            "Nhập vai Anime",
            "Tự động hóa An ninh",
            "Tư vấn định cư",
            "Video Tiktok",
            "Quản lý thu chi",
        ],
    },
];

// handle resize and change layout pc - mobile
const handleReisze = () => {
    if (window.innerWidth >= 1024) {
        isShowFee.value = true;
    } else {
        isShowFee.value = false;
    }
};

// handle active <-> non-active heart
const toggleHeart = (index) => {
    heartStates.value[index] = !heartStates.value[index];
};

onMounted(() => {
    handleReisze();
    window.addEventListener("resize", handleReisze);
    // innitial value for ref hearStates
    heartStates.value = new Array(asistants.length).fill(false);
});

defineOptions({ layout: Layout });
</script>

<style scoped>
/* add more custom style for sidebar */
.Wapper-Layout {
    background-image: url("/assets/img/TimeLineLayout.png");
    background-size: contain;
    background-repeat: space;
}
</style>
