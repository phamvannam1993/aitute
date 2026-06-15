<template>
    <div class="relative w-11/12 lg:w-3/5 bg-white p-4 lg:px-12 rounded-2xl">
        <img
            src="/assets/img/close.png"
            @click="closePosting"
            alt="closebtn"
            class="absolute -top-4 -right-4 cursor-pointer"
        />
        <div class="">
            <div
                class="flex text-black items-center justify-between border-b pb-1"
            >
                <h1 class="text-xs hidden xs:block xs:text-xl font-semibold">
                    Lên kế hoạch đăng bài
                </h1>
                <div class="flex items-center gap-2">
                    <div
                        class="flex items-center gap-1 p-2 border-2 rounded-xl bg-green-500 cursor-pointer"
                        @click="toggleMedia"
                    >
                        <img src="/assets/svgs/media-icon.svg" alt="add" />
                        <span class="text-white text-xs md:text-base"
                            >Kho đa phương tiện</span
                        >
                    </div>
                </div>
            </div>
            <div
                class="flex text-black items-center justify-between border-b py-2"
            >
                <div class="flex gap-4">
                    <div
                        class="border-b border-[#0000B4] text-[#0000B4] flex items-center gap-2 cursor-pointer"
                    >
                        <span>Post</span>
                        <img src="/assets/img/image.png" alt="image" />
                    </div>
                    <div class="flex items-center gap-2 cursor-pointer">
                        <span>Reels</span>
                        <img src="/assets/img/video.png" alt="video" />
                    </div>
                </div>
                <div>
                    <Calendar v-model="scheduledAt" />
                </div>
            </div>
            <div class="text-black items-center justify-between py-2">
                <div
                    class="text-editor max-h-[20vh] md:max-h-[35vh] overflow-y-auto"
                >
                    <textarea
                        v-model="postContent"
                        class="w-full h-40 p-2 border rounded-md"
                        placeholder="Nhập văn bản ở đây..."
                        >{{ postContent }}</textarea
                    >

                    <div class="mt-2">
                        <input
                            type="file"
                            multiple
                            @change="handleImageUpload"
                        />
                        <span v-if="isUpload">Đang tải hình ảnh lên...</span>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-lg">Hình ảnh đã upload:</h3>
                        <div class="grid grid-cols-2 gap-2">
                            <div
                                v-for="(item, index) in images"
                                :key="index"
                                class="relative"
                            >
                                <img
                                    :src="item"
                                    class="w-full h-auto rounded-md"
                                />
                                <button
                                    @click="removeImage(index)"
                                    class="absolute top-2 right-2 bg-red-500 text-white w-8 h-8 rounded-full"
                                >
                                    X
                                </button>
                            </div>
                            <div class="relative" v-if="selectedVideo">
                                <video
                                    :src="selectedVideo"
                                    controls
                                    class="w-full h-auto rounded-md"
                                ></video>
                                <button
                                    @click="removeVideo"
                                    class="absolute top-2 right-2 bg-red-500 text-white w-8 h-8 rounded-full"
                                >
                                    X
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div
                v-if="!showFormPrompt"
                class="text-[#0000B4] flex flex-col xl:flex-row items-center justify-around py-2"
            >
                <div
                    @click="showCreateContentAI"
                    class="cursor-pointer flex px-2 py-1 justify-between items-center border-dashed border-2 border-[#0000B4] rounded-2xl"
                >
                    <img src="/assets/img/ai.png" class="w-6 h-6" alt="" />
                    <p>Tạo nội dung bằng AI chuẩn SEO</p>
                </div>
                <div
                    @click="generateVideoByContent"
                    class="flex px-2 py-1 justify-between cursor-pointer items-center border-dashed border-2 border-[#0000B4] rounded-2xl"
                >
                    <img src="/assets/img/ai.png" class="w-6 h-6" alt="" />
                    <p>Tạo video bằng AI</p>
                </div>
                <div
                    @click="generateImageByContent"
                    class="cursor-pointer flex px-2 py-1 justify-between items-center border-dashed border-2 border-[#0000B4] rounded-2xl"
                >
                    <img src="/assets/img/ai.png" class="w-6 h-6" alt="" />
                    <p>Tạo hình ảnh bằng AI</p>
                </div>
            </div>
            <div
                v-else
                class="text-[#0000B4] items-center justify-between py-2"
            >
                <div
                    class="flex px-2 py-1 justify-between items-center border-dashed border-2 bg-[#E5E5FF] border-[#0000B4] rounded-2xl"
                >
                    <img src="/assets/img/ai.png" class="w-6 h-6" alt="" />
                    <input
                        type="text"
                        v-model="prompt"
                        class="border-2 border-[#0000B4] text-sm md:text-base p-1 rounded-lg w-[60%]"
                        placeholder="Nhập prompt để tạo nội dung..."
                    />
                    <div class="flex gap-2">
                        <div>
                            <button
                                @click="showFormPrompt = false"
                                class="px-4 py-2 rounded-xl text-white border-2 bg-[#ff2b2b] border-[#ff2b2b] hidden lg:block"
                            >
                                Hủy
                            </button>
                            <button
                                class="rounded-2xl p-1 text-white bg-[#ff2b2b] lg:hidden"
                            >
                                <img src="/assets/svgs/close2.svg" alt="" />
                            </button>
                        </div>
                        <div>
                            <button
                                @click="submitPrompt"
                                class="px-4 py-2 rounded-xl text-white border-2 bg-[#403ECC] border-[#403ECC] hidden lg:block"
                            >
                                Tạo nội dung
                            </button>
                            <button
                                @click="submitPrompt"
                                class="rounded-2xl p-1 text-white bg-[#403ECC] lg:hidden"
                            >
                                <img src="/assets/svgs/check.svg" alt="" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-end mt-4">
                <button
                    @click="postToSocial"
                    class="px-4 py-2 rounded-xl text-white border-2 bg-[#403ECC] border-[#403ECC]"
                >
                    {{ isFuturePost ? 'Đặt lịch' : 'Đăng' }}
                </button>
            </div>
        </div>
        <div
            v-if="isShowMedia"
            class="bg-white rounded-2xl absolute w-full h-full inset-0"
        >
            <img
                src="/assets/img/close.png"
                @click="toggleMedia"
                alt="closebtn"
                class="absolute -top-4 -right-4 cursor-pointer"
            />
            <div class="w-full h-full p-6">
                <h2 class="text-black font-semibold">
                    Danh sách file đã tạo từ AI của bạn:
                </h2>
                <div class="flex mb-4">
                    <div
                        class="cursor-pointer px-4 py-2 border-b-2"
                        :class="{
                            'border-[#0000B4] text-[#0000B4]': isImageTab,
                            'text-gray-500': !isImageTab,
                        }"
                        @click="toggleTab('image')"
                    >
                        Ảnh
                    </div>
                    <div
                        class="cursor-pointer px-4 py-2 border-b-2"
                        :class="{
                            'border-[#0000B4] text-[#0000B4]': !isImageTab,
                            'text-gray-500': isImageTab,
                        }"
                        @click="toggleTab('video')"
                    >
                        Video
                    </div>
                </div>
                <div
                    v-if="isImageTab"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 max-h-[80%] py-4 overflow-y-scroll gap-2"
                >
                    <div
                        v-for="(item, index) in listImages"
                        :key="index"
                        class="relative bg-gray-500 bg-opacity-30"
                        @click="addToSocial(item)"
                    >
                        <img
                            :src="item.s3_url"
                            :alt="item.id"
                            class="object-fill"
                        />
                        <input
                            type="checkbox"
                            v-model="item.isCheck"
                            class="absolute top-0 right-0 w-8 h-8"
                        />
                    </div>
                </div>
                <div
                    v-else
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 max-h-[80%] py-4 overflow-y-scroll gap-2"
                >
                    <div
                        v-for="(item, index) in listVideos"
                        :key="index"
                        class="relative bg-gray-500 bg-opacity-30"
                    >
                        <video
                            :src="item.s3_url"
                            :alt="item.id"
                            class="object-fill"
                            controls
                            @click="addToSocialVideo(item)"
                        ></video>
                        <img
                            src="/assets/svgs/check-icon.svg"
                            alt="icon"
                            v-if="selectedVideo == item.s3_url"
                            class="w-8 h-8 absolute top-1 right-1"
                        />
                    </div>
                </div>
                <div
                    v-if="hasNextPage && isImageTab"
                    @click="loadMore"
                    class="text-blue-600 text-center cursor-pointer"
                >
                    Hiển thị thêm
                </div>
                <div
                    v-if="hasNextPageVideos && !isImageTab"
                    @click="loadMoreVideos"
                    class="text-blue-600 text-center cursor-pointer"
                >
                    Hiển thị thêm
                </div>
            </div>
        </div>
    </div>
    <div
        v-if="isLoading"
        class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30"
    >
        <div class="loader"></div>
    </div>
</template>

<script setup>
import Calendar from '@/Components/Calendar.vue';
import { computed, ref } from "vue";

const props = defineProps({
    togglePosting: {
        type: Function,
        required: true,
    },
    postingImageUrl: {
        type: String,
        default: "",
    },
    postingContent: {
        type: String,
        default: "",
    },
    date: {
        type: String,
        default: null,
    },
    activePage: {
        type: Object,
    }
});

// const tags = ref(["Label 1", "Label 2", "Label 3"]);
const postContent = ref(props.postingContent);
const showFormPrompt = ref(false);
const aiContent = ref("");

const prompt = ref("");
const isLoading = ref(false);
const isUpload = ref(false);
const selectedVideo = ref("");

const showCreateContentAI = () => {
    showFormPrompt.value = true;
};

const submitPrompt = async () => {
    if(prompt.value == '') {
        alert("Vui lòng nhập yêu cầu của bạn");
        return;
    }

    const imageUrls = images.value[0];
    isLoading.value = true;

    const payload = {
        prompt: prompt.value,
        imageUrl: imageUrls || null,
        postContent: postContent.value || null,
    };
    try {
        const response = await axios.post(
            route("generate-content-with-ai"),
            payload,
            {
                headers: {
                    "Content-Type": "application/json",
                },
            }
        );

        postContent.value = response.data.content;
    } catch (error) {
        console.error("Error generating AI content:", error);
    } finally {
        showFormPrompt.value = false;
        isLoading.value = false;
    }
};

const postingContent = ref("");

const images = ref([]);

if (props.postingImageUrl) {
    images.value.push(props.postingImageUrl);
}

const setContent = () => {
    postContent.value = aiContent.value;
    aiContent.value = "";
};

const scheduledAt = ref(props.date);
const isFuturePost = computed(() => {
    if (!scheduledAt.value) {
        return false;
    }

    return new Date(scheduledAt.value) > new Date();
});
const postToSocial = async () => {
    isLoading.value = true;
    const formData = new FormData();
    formData.append("content", postContent.value);
    formData.append("files", JSON.stringify(images.value));
    formData.append("scheduled_at", scheduledAt.value);
    formData.append("video", selectedVideo.value || "");
    formData.append("facebook_fanpage_id", props.activePage.id)
    try {
        const response = await axios.post(route('social.post-to-facebook'), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        if (response.status === 200) {
            alert("Đăng bài thành công!");
        } else {
            alert("Đăng bài thất bại!");
        }
    } catch (error) {
        alert(
            "Vui lòng nhập nội dung bài đăng hoặc tải lên file đa phương tiện của bạn."
        );
        console.error("Lỗi khi đăng bài:", error);
    } finally {
        isLoading.value = false;
    }
};

const handleImageUpload = async (event) => {
    const files = event.target.files; // Lấy tất cả các file ảnh
    if (files.length > 0) {
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const formData = new FormData();
            formData.append("file", file); // Đưa từng file vào FormData với tên là "file"
            isUpload.value = true;
            try {
                // Gửi request POST lên server để upload ảnh
                const response = await axios.post(
                    route("social.upload-image"),
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    }
                );

                if (response.status === 200) {
                    // Khi upload thành công, thêm URL của ảnh vào mảng images
                    const imageUrl = response.data.url.url; // Giả sử response.data.url chứa URL ảnh
                    images.value.push(imageUrl);
                } else {
                    console.error("Upload ảnh thất bại!", response);
                }
            } catch (error) {
                console.error("Lỗi khi upload ảnh:", error);
            } finally {
                isUpload.value = false;
            }
        }
    }
};

const generateImageByContent = async () => {
    if (postContent.value == null) {
        alert("Hãy nhập nội dung bài đăng của bạn.");
        return;
    }
    isLoading.value = true;
    const formData = new FormData();
    formData.append("description", postContent.value);
    try {
        const response = await axios.post(
            route("ai-image.generate-image-for-post"),
            formData,
            {
                headers: {
                    "Content-Type": "application/json",
                },
            }
        );

        images.value.push(response.data.url);
    } catch (error) {
        console.error("Error generating AI content:", error);
    } finally {
        isLoading.value = false;
    }
};

const isShowMedia = ref(false);
const listImages = ref([]);
const currentPage = ref(1);
const hasNextPage = ref(false);

const isImageTab = ref(true);
const toggleTab = (tab) => {
    if (tab === "image") {
        isImageTab.value = true;
    } else {
        isImageTab.value = false;
        if (isShowMedia.value && currentPageVideos.value == 1) {
            loadMoreVideos();
        }
    }
};

const addToSocial = (item) => {
    if (typeof item.isCheck === "undefined") {
        item.isCheck = false;
    }
    item.isCheck = !item.isCheck;
    if (item.isCheck) {
        images.value.push(item.s3_url);
    } else {
        const index = images.value.indexOf(item.s3_url);
        if (index !== -1) {
            images.value.splice(index, 1);
        }
    }
};

const addToSocialVideo = (item) => {
    if (selectedVideo.value != item.s3_url) {
        selectedVideo.value = item.s3_url;
    } else {
        selectedVideo.value = null;
    }
};

const toggleMedia = () => {
    isShowMedia.value = !isShowMedia.value;
    if (isShowMedia.value && currentPage.value == 1) {
        loadMore();
    }
};
const loadMore = async () => {
    try {
        const response = await axios.get(
            route("ai-image.list-media", {
                page: currentPage.value,
                type: "image",
            })
        );

        hasNextPage.value = response?.data?.data?.next_page_url || null;
        let list = response.data.data.data || [];
        list = list.map((item) => ({
            ...item,
            isCheck: false,
        }));
        if (list.length > 0) {
            listImages.value = [...list, ...listImages.value];
        }
    } catch (error) {
        console.error("Error generating AI content:", error);
    } finally {
        isLoading.value = false;
        currentPage.value++;
    }
};

const listVideos = ref([]);
const currentPageVideos = ref(1);
const hasNextPageVideos = ref(false);
const loadMoreVideos = async () => {
    try {
        const response = await axios.get(
            route("ai-image.list-media", {
                page: currentPageVideos.value,
                type: "video",
            })
        );

        hasNextPageVideos.value = response?.data?.data?.next_page_url || null;
        let list = response.data.data.data || [];
        list = list.map((item) => ({
            ...item,
            isCheck: false,
        }));
        if (list.length > 0) {
            listVideos.value = [...list, ...listVideos.value];
        }
    } catch (error) {
        console.error("Error generating AI content:", error);
    } finally {
        isLoading.value = false;
        currentPageVideos.value++;
    }
};

const isImage = (fileUrl) => {
    return fileUrl.includes("/images/");
};

const removeImage = (index) => {
    let removedUrl = images.value[index];

    images.value.splice(index, 1);

    const imageItem = listImages.value.find(
        (item) => item.s3_url === removedUrl
    );

    if (imageItem) {
        imageItem.isCheck = false;
    }
};

const removeVideo = () => {
    selectedVideo.value = null;
};

const generateVideoByContent = async () => {
    if (postContent.value == null) {
        alert("Hãy nhập nội dung bài đăng của bạn.");
        return;
    }
    isLoading.value = true;
    const formData = new FormData();
    formData.append("description", postContent.value);
    formData.append("style", "Siêu thực");
    formData.append("author", "Vincent van Gogh");
    formData.append("resolution", "1280x720");
    try {
        const response = await axios.post(
            route("ai-video.generate-video-audio"),
            formData,
            {
                headers: {
                    "Content-Type": "application/json",
                },
            }
        );

        listVideos.value.push(response.data.url);
        selectedVideo.value = response.data.url
    } catch (error) {
        console.error("Error generating AI content:", error);
    } finally {
        isLoading.value = false;
    }
};
const closePosting = () => {
    images.value = []; 
    props.togglePosting(); 
};
</script>
<style scoped>
.loader {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #24AA69;
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>
