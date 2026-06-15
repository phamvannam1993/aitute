<template>
    <div class="p-6 bg-white rounded-[35px] shadow text-black mx-auto mb-w-340px">
        <div class="flex flex-wrap gap-2 justify-between items-center mb-4">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 overflow-hidden rounded-full border border-gray-300">
                    <img class="w-full h-auto object-top" src="/assets/img/ai3goc/logo/circle.svg" alt="Robot" />
                </div>
                <h2 class="text-black font-bold text-[14px] md:text-[16px] lg:text-[18px] leading-none">LỊCH SỬ BÀI VIẾT ĐÃ TẠO</h2>
            </div>

            <div class="flex flex-wrap gap-2 md:gap-4">
                <select v-model="selectedCategory" class="w-[200px] border p-2 rounded-full text-sm md:text-base">
                    <option value="all">Tất cả</option>
                    <option v-for="category in categories" :key="category" :value="category">
                        {{ category }}
                    </option>
                </select>
                <div class="relative">
                    <input type="text" v-model="search" placeholder="Tìm kiếm..." class="border p-2 rounded-full text-sm md:text-base w-full sm:w-auto min-w-[200px]" />
                    <div class="flex absolute right-3 top-2.5 text-gray-400">| <img src="/assets/svgs/search-icon.svg" /></div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-400">
                        <th class="p-2 text-center">STT</th>
                        <th class="p-2 text-center">Bài viết</th>
                        <th class="p-2 text-center w-[100px]">Dự án</th>
                        <th class="p-2 text-center">Ngày tạo</th>
                        <th class="p-2 text-center w-[100px]">Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(post, index) in paginatedPosts" :key="post.id" class="border">
                        <td class="p-2 border text-center">{{ index + 1 }}</td>
                        <td class="p-2 border">
                            <p class="line-clamp-2 sm:line-clamp-3 md:line-clamp-4" v-html="post.project"></p>
                        </td>
                        <td class="p-2 border text-center">{{ post.title }}</td>
                        <td class="p-2 border text-center">{{ dayjs(post.date).format('MM-DD-YYYY HH:mm:ss') }}</td>
                        <td class="p-2 border text-center">
                            <button @click="viewDetails(post)" class="text-ai3goc-primary underline"><i>Xem</i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex justify-center mt-4 space-x-2">
            <button @click="prevPage" :disabled="currentPage === 1" class="px-3 py-1 border rounded-full">❮</button>
            <span v-for="page in totalPages" :key="page" class="px-3 py-1 border rounded cursor-pointer" @click="changePage(page)" :class="{ 'bg-[#FFA411] text-white': currentPage === page }">
                {{ page }}
            </span>
            <button @click="nextPage" :disabled="currentPage === totalPages" class="px-3 py-1 border rounded-full">❯</button>
        </div>
    </div>
    <div class="w-full h-[2px] border my-4 md:my-8"></div>
    <div v-if="selectedPost" id="details-section" class="p-6 bg-white rounded-[35px] shadow text-black">
        <h2 class="text-lg font-bold">
           <span class="text-ai3goc-primary">Dự án:</span> <span class="font-extrabold">{{ selectedPost?.title }}</span>
        </h2>
        <h2 class="text-lg font-bold">
            <span class="text-ai3goc-primary">Ngày tạo:</span> <span class="font-medium">{{ selectedPost?.date ? dayjs(selectedPost.date).format('MM-DD-YYYY HH:mm:ss') : '' }}</span>
        </h2>
        <h2 class="text-lg font-bold text-ai3goc-primary">Nội dung đã tạo:</h2>

        <div class="mt-4 border rounded-2xl p-4 whitespace-pre-line" v-html="selectedPost?.project"></div>

        <h2 class="mt-4 font-bold text-ai3goc-primary">Hình ảnh/Video bài đăng:</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-2">
            <div v-for="(image, index) in displayedImages" :key="index" class="w-full h-48 bg-gray-300 flex items-center justify-center rounded-lg overflow-hidden">
                <img :src="image ? image : '/assets/svgs/aiwow/image.svg'" alt="loading" class="w-full h-full object-contain" :style="!image ? 'width: 100px;' : ''" />
            </div>
        </div>
        <div class="w-full md:w-1/2 bg-gray-300 flex items-center justify-center rounded-lg mx-auto mt-4">
            <video v-if="selectedPost?.video_url" :src="selectedPost?.video_url" controls class="w-full h-full object-cover rounded-lg max-h-[600px]"></video>
            <img v-else src="/assets/svgs/play_button.svg" alt="loading" class="w-16" />
        </div>
    </div>
</template>
<script setup>
import { ref, computed, onMounted, nextTick } from "vue";
import dayjs from 'dayjs';
import axios from "axios";

const posts = ref([]);
const search = ref("");
const selectedCategory = ref("all");
const currentPage = ref(1);
const itemsPerPage = 5;

const getListProjects = async () => {
    try {
        const response = await axios.get(route("ai-business.get-list-project-history"), {
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
        });

        if (response.status === 200) {
            posts.value = response.data.data.data.map((item, index) => ({
                id: item.id,
                title: item.title || "Không có tiêu đề",
                project: item.content || "Chưa xác định",
                date: item.updated_at || "N/A",
                image_urls: item.image_urls || [],
                video_url: item.video_url || null,
            }));
        } else {
            console.error("Không thể lấy danh sách sản phẩm:", response.data.message);
        }
    } catch (error) {
        console.error("Lỗi khi gọi API danh sách sản phẩm:", error.response?.data || error.message);
    }
};

onMounted(() => {
    getListProjects();
});
const selectedPost = ref(null);
const viewDetails = async (post) => {
    console.log("Selected post:", post);
    selectedPost.value = post;
    await nextTick();

    // Cuộn xuống phần chi tiết
    const detailsSection = document.getElementById("details-section");
    if (detailsSection) {
        detailsSection.scrollIntoView({ behavior: "smooth" });
    }
};

const filteredPosts = computed(() => {
    return posts.value.filter((post) => post.title.toLowerCase().includes(search.value.toLowerCase()) || post.project.toLowerCase().includes(search.value.toLowerCase()) || post.date.includes(search.value));
});

const paginatedPosts = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    return filteredPosts.value.slice(start, start + itemsPerPage);
});

const totalPages = computed(() => Math.ceil(filteredPosts.value.length / itemsPerPage));

const displayedImages = computed(() => {
    if (selectedPost.value?.image_urls) {
        try {
            const images = JSON.parse(selectedPost.value.image_urls);
            if (Array.isArray(images) && images.length > 0) {
                return [...images.slice(0, 4), ...Array(4 - images.length).fill("/assets/svgs/aiwow/image.svg")];
            }
        } catch (error) {
            console.error("Lỗi khi parse JSON image_urls:", error);
        }
    }
    return Array(4).fill("/assets/svgs/aiwow/image.svg");
});

const prevPage = () => {
    if (currentPage.value > 1) currentPage.value--;
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) currentPage.value++;
};

const changePage = (page) => {
    currentPage.value = page;
    selectedPost.value = null;
};

//   const categories = computed(() => [...new Set(posts.value.map(post => post.title))]);
</script>
<style>
    @media only screen and (max-width: 600px) {
        .mb-w-340px {
            width: 340px;
        }
    }
</style>
