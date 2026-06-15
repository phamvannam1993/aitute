<template>
    <Head title="Video từ link sản phẩm - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData" :credits="credits">
        <div class="w-full flex flex-col gap-6 lg:grid grid-cols-2 text-black">
            <section class="flex flex-col gap-2.5 justify-between w-full bg-white drop-shadow-xl rounded-2xl p-4">
                <div class="flex flex-col">
                    <div class="flex gap-1">
                        <div class="flex justify-start items-center gap-2">
                            <div class="">
                                <img class="w-[41px] h-[41px] object-top" src="/assets/img/ai3goc/logo/circle.svg" alt="Robot" />
                            </div>
                            <h2 class="text-black font-bold text-xl lg:text-xl 2xl:text-3xl leading-none">
                                Video từ link sản phẩm
                            </h2>
                        </div>
                    </div>
                </div>

                <p class="font-semibold text-sm mt-6">Tên video</p>
                <input class="w-full rounded-xl border-[#D4D4D4] hover:border-solid hover:border-gray-500" type="text" v-model="videoName" />
                <div class="max-h-[70vh] overflow-y-auto w-full">
                    <div v-for="(content, index) in summaryContent" :key="index" class="bg-white drop-shadow-sm rounded-xl p-4 mb-4 border border-[#D4D4D4]">
                        <div class="flex flex-col gap-1.5 relative">
                            <div class="flex w-full justify-between">
                                <div class="text-white font-semibold text-sm md:text-base flex items-center gap-2 rounded-full bg-[#207A91] px-2.5 py-1">
                                    <img src="/assets/img/orion/icon/scene.svg" alt="step" class="w-[16px] h-auto" />
                                    <span class="text-white leading-none font-semibold">Cảnh {{ index + 1 }}</span>
                                </div>
                                <div class="flex gap-1.5">
                                    <button class="bg-[#149CBE] rounded-xl py-1 lg:px-3 px-2 flex gap-1 justify-center items-center hover:scale-105 transform transition-transform" @click="handleAddScene(index, content)">
                                        <img class="size-4" src="/assets/svgs/aiwow/duplicate.svg" />
                                        <p class="text-white text-sm lg:text-base font-semibold">Nhân bản</p>
                                    </button>
                                    <button class="bg-[#A0A0A0] rounded-xl py-1 px-3 flex gap-1 justify-center items-center hover:scale-105 transform transition-transform" @click="handleRemoveScreen(index)">
                                        <img class="size-5" src="/assets/svgs/aiwow/delete.svg" />
                                        <p class="text-white text-sm lg:text-base font-semibold">Xóa</p>
                                    </button>
                                </div>
                            </div>
                            <div class="flex gap-1 items-center mt-1.5">
                                <p class="font-semibold text-sm md:text-base">Nội dung:</p>
                            </div>
                            <textarea required class="font-light text-sm border-none focus:outline-none focus:ring-0 h-fit bg-[#F6F6F6] rounded-[10px]" type="text" v-model="content.subtitles[0].text" @input="autoSplitText(content, content.subtitles[0].text)"> </textarea>
                            <span v-if="!content.subtitles[0].text" class="text-red-500 text-sm">Vui lòng nhập nội dung.</span>
                            <!-- <span v-if="content.subtitles[0].text.length > 170" class="text-red-500 text-sm">Nội dung giới hạn 170 ký tự.</span> -->
                            <div class="flex flex-col gap-2 items-center" v-for="background in content.background.src" :key="background">
                                <di v-if="!!background.isLoading" class="flex flex-col items-center">
                                    <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                                    <h3 class="text-black">Hệ thống đang xử lý, xin đợi một chút</h3>
                                </di>
                                <div v-else>
                                    <img v-if="background.type === 'image'" :src="background.url" alt="Background" class="rounded-xl" />
                                    <video v-else :src="background.url" alt="Background" class="object-fill rounded-lg" controls></video>
                                </div>
                                <div class="flex justify-end w-full gap-4">
                                    <button type="button" @click="triggerFileInput(index)" class="flex items-center w-fit px-3 py-1.5 justify-center gap-2 bg-transparent text-[#149CBE] rounded-xl backdrop-blur-sm transition-colors hover:scale-105 border-2 border-[#149CBE]">
                                        <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="13" cy="13" r="13" fill="#149CBE"/>
                                        <path d="M11.0205 11.7192C10.3272 12.1011 9.75566 12.6772 9.37988 13.3755L9.25 13.6392C9.14906 13.864 9.06146 14.0602 8.98438 14.2114C8.93146 14.3152 8.8598 14.4472 8.76074 14.5708L8.65234 14.6899C8.4924 14.8436 8.35313 14.9341 8.14746 15.0171C7.96026 15.0926 7.77639 15.1143 7.62793 15.1235L7.11914 15.1323C5.93239 15.1323 4.9707 16.094 4.9707 17.2808L4.98047 17.4937C5.08405 18.5439 5.94431 19.3723 7.00781 19.4263H7.00684L7.11914 19.4292H18.8809C20.0672 19.429 21.029 18.4672 21.0293 17.2808L21.0186 17.061C20.9085 15.9776 19.9933 15.1325 18.8809 15.1323C18.6949 15.1323 18.5189 15.1325 18.373 15.1235C18.2619 15.1167 18.1305 15.1031 17.9932 15.0649L17.8545 15.0171C17.6998 14.9548 17.5822 14.8886 17.4658 14.7944L17.3477 14.689C17.1904 14.5375 17.087 14.3495 17.0166 14.2114L16.751 13.6401C16.3843 12.8231 15.7585 12.1487 14.9795 11.7192V9.98389C16.4125 10.5161 17.5737 11.6066 18.1953 12.9917L18.4268 13.4927C18.437 13.5127 18.447 13.5287 18.4541 13.5415C18.459 13.5419 18.4649 13.5431 18.4707 13.5435C18.5564 13.5487 18.6751 13.5483 18.8809 13.5483C20.9419 13.5485 22.6133 15.2197 22.6133 17.2808L22.6084 17.4731C22.508 19.4445 20.8773 21.013 18.8809 21.0132H7.10938L7.09863 21.0122L6.94727 21.0083H6.92773C5.01711 20.9114 3.48592 19.3781 3.3916 17.4673L3.38672 17.2808C3.38672 15.2196 5.05798 13.5484 7.11914 13.5483L7.53027 13.5435C7.53577 13.5431 7.54123 13.5419 7.5459 13.5415C7.553 13.5286 7.56293 13.5129 7.57324 13.4927C7.62828 13.3847 7.69777 13.2311 7.80566 12.9907L7.98535 12.6265C8.6372 11.4149 9.71564 10.4663 11.0205 9.98193V11.7192Z" fill="white"/>
                                        <path d="M12.9988 3.55762L12.439 2.99782L12.9988 2.43803L13.5586 2.99782L12.9988 3.55762ZM13.7904 13.3592C13.7904 13.7964 13.436 14.1509 12.9988 14.1509C12.5615 14.1509 12.2071 13.7964 12.2071 13.3592L13.7904 13.3592ZM9.07813 7.47825L8.51833 6.91846L12.439 2.99782L12.9988 3.55762L13.5586 4.11741L9.63792 8.03804L9.07813 7.47825ZM12.9988 3.55762L13.5586 2.99782L17.4792 6.91846L16.9194 7.47825L16.3596 8.03804L12.439 4.11741L12.9988 3.55762ZM12.9988 3.55762L13.7904 3.55762L13.7904 13.3592L12.9988 13.3592L12.2071 13.3592L12.2071 3.55762L12.9988 3.55762Z" fill="white"/>
                                        </svg>


<!--                                        <img src="/assets/img/orion/icon/upload.svg" class="size-6 md:size-5 xl:size-6" />-->
                                        <span class="text-[12px] md:hidden xl:block xl:text-[14px]">Tải ảnh hoặc video</span>
                                    </button>
                                </div>
                                <input type="file" :ref="setFileInputRef(index)" accept="image/*,video/*" @change="(event) => handleFileChange(event, background)" class="hidden" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full justify-center flex my-6">
                    <button @click="createVideo" class="px-14 py-1.5 w-fit font-bold text-white bg-[#1E405A] h-fit rounded-lg hover:scale-105 transform transition-transform">Tạo Video</button>
                </div>
            </section>
            <section class="flex flex-col h-fit gap-6 justify-between w-full bg-white drop-shadow-xl rounded-2xl p-5">
                <p class="text-[#1E405A] italic mb-1 text-center">Hiển thị kết quả</p>
                <div class="flex justify-center items-center mb-4 ">
                    <iframe v-if="videoPreview && !videoUrlPreview && !isLoading" class="w-full h-[45vh] rounded-xl" :src="videoPreview" frameborder="0" allow="autoplay; fullscreen" allowfullscreen> </iframe>
                    <video v-if="videoUrlPreview && !isLoading" :src="videoUrlPreview" controls class="w-full h-[45vh] rounded-lg mx-auto"></video>
                    <div v-if="isLoading" class="bg-[#E6E6E6] w-full h-[45vh] rounded-2xl showLoaderVideo items-center flex flex-col justify-center">
                        <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                        <p class="text-loading">Video đang được tạo. Vui lòng chờ !</p>
                        <div class="w-10/12 bg-gray-200 rounded-full dark:bg-gray-700">
                            <div class="bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full"  :style="{ width: loadingPercent + '%'}">{{loadingPercent}}%</div>
                        </div>
                    </div>
                </div>
                <div v-if="resultVideo" :class="['flex justify-center gap-2 lg:gap-6', { 'pointer-events-none opacity-75': !resultVideo }]">
                    <button @click="openPostDetail()" class="flex gap-1 bg-[#149CBE] px-3 py-1.5 rounded-3xl items-center justify-center hover:scale-105 transform transition-transform">
                        <img v-if="isLoadingPostDetail" class="w-8 h-8" src="/assets/img/dashboard/loading.gif" alt="" />
                        <img v-else src="/assets/img/orion/icon/create_post-white.svg" class="lg:block hidden size-5" />
                        <p class="text-white text-sm lg:text-base font-bold">Đăng bài</p>
                    </button>
                    <button @click="shareAIGeneratedMedia(resultVideo?.video)" class="flex gap-1 bg-[#149CBE] px-3 py-1.5 rounded-3xl items-center justify-center hover:scale-105 transform transition-transform">
                        <img src="/assets/img/ai3goc/icon/share.svg" class="lg:block hidden size-5" />
                        <p class="text-white text-sm lg:text-base font-bold">Chia sẻ</p>
                    </button>
                    <button @click="downloadVideo(resultVideo?.data?.videoURL)" class="flex gap-1 bg-[#149CBE] px-3 py-1.5 rounded-3xl items-center justify-center hover:scale-105 transform transition-transform">
                        <img src="/assets/img/ai3goc/icon/download.svg" class="lg:block hidden size-5" />
                        <p class="text-white text-sm lg:text-base font-bold">Tải xuống</p>
                    </button>
                </div>
                <div class="w-full justify-center flex">
                    <a :href="route('ai-video.url-to-video.history')" class="px-14 py-1.5 w-fit font-bold text-white bg-[#1E405A] h-fit rounded-lg hover:scale-105 transform transition-transform">Lịch sử</a>
                </div>
            </section>
        </div>
    </Layout>

    <!-- <div v-if="isLoading" class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
        <div class="loader"></div>
    </div> -->
    <CreditModal :showCreditPopup="showCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleCancel" />
    <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleBuyCancel" :currentCredit="pageProps.auth.user.credit || 0" :additionalCredit="additionalCredit" />

    <div v-if="isShowAlert" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-xl font-bold text-[#092A99]">Xác nhận thoát màn hình</h3>
            <p class="mt-4 text-gray-600">Video đang được tạo. Bạn có chắc chắn muốn thoát không?</p>
            <div class="mt-6 flex justify-end space-x-4">
                <button @click="cancelExit" class="px-4 py-2 bg-gray-300 text-black rounded">Hủy</button>
                <button @click="confirmExit" class="px-4 py-2 bg-red-500 text-white rounded">Đồng ý</button>
            </div>
        </div>
    </div>

    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink" />
    </Modal>
    <Popup
        v-if="showConfirmModal"
        title="Cảnh báo !"
        message="Bạn có chắc chắn muốn xóa video này không?"
        cancelButtonText="Huỷ"
        acceptButtonText="Xoá"
        @cancel="cancelDelete"
        @accept="confirmDelete"
    />

    <PostForm ref="postFormRef" :templatePostForm="'ModelFullForm'" @beforeSubmit="beforeSubmitPostForm"  @onSuccess="onSuccessPostForm" />

</template>

<script setup>
import Layout from "@/Layouts/Client/Layout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { computed, onMounted, ref, watch, nextTick } from "vue";
import Credit from "@/Components/Credit.vue";
import axios from "axios";
import { toast } from "vue3-toastify";
import FormShareLink from "@/Components/FormShareLink.vue";
import Modal from "@/Components/Modal.vue";
import { franc } from "franc-min";
import { isEqual, debounce } from "lodash";
import Popup from '@/Components/Popup/Index.vue'
import PostForm from "@/Components/ShareAiText/PostForm.vue";

const { props: pageProps } = usePage();
const props = defineProps({
    video_preview: Object,
    credit: Number,
});

const credits = ref(props.credit || 0);
const fileInputRefs = ref([]);

const breadcrumbsData = [
    { text: "Ảnh", link: "ai-image.history" },
    { text: "Tạo ảnh từ văn bản", link: "ai-image.index" },
];

const setFileInputRef = (index) => (el) => {
    fileInputRefs.value[index] = el;
};
const postFormRef = ref(null);
const videoPreview = ref(props.video_preview.preview_url);
const videoName = ref(props.video_preview.title || "");
const renderParams = ref(JSON.parse(props.video_preview.renderParams));
const summaryContent = ref(renderParams.value.scenes || []);
const originalSummaryContent = ref(summaryContent.value);
const translateSummaryContent = ref(null);
const newSumaryContent = ref(renderParams.value.scenes);
const isLoading = ref(false);
const isLoadingPostDetail = ref(false);
const videoUrlPreview = ref(props.video_preview?.s3_url ? JSON.parse(props.video_preview.s3_url).videoURL || null : null);
const resultVideo = ref(null);
const canCreateVideo = ref(true);

// Handle action
const imageDeleteId = ref(null);
const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
const selectedVideo = ref(null);
const showConfirmModal = ref(false);
const defaultLanguage = ref("vie");
const translateActive = ref(false);
const uploadCurrentType = ref(null);
const charLimit = 70;
const loadingPercent = ref(0);

const handleRemoveScreen = (index) => {
    if (confirm("Bạn có chắc chắn muốn xóa phân cảnh này không?")) {
        summaryContent.value.splice(index, 1);
    }
}

const handleAddScene = (index, content) => {

    const newScene = JSON.parse(JSON.stringify(content));

    summaryContent.value.splice(index + 1, 0, newScene);

    summaryContent.value = [...summaryContent.value];
};

const autoSplitText = (content, text) => {
  content.sub_scenes[0].text_lines = splitTextByLimit(text, charLimit);
// 1038 is the base position of subtitle
// 63 is the heihgt of each subtitile
// To get the subtitle position need to divied the number of the line
  content.sub_scenes[0].location.start_y = 1038 - (63 * content.sub_scenes[0].text_lines.length);
};

const createTextLine = (text) => ({
  text,
  text_animation: [
    {
      animation: "slide-in-left",
      source: "templates",
      speed: 1.4,
      type: "start",
    },
    {
      animation: "none",
      source: "templates",
      speed: 0.5,
      type: "end",
    },
  ],
  text_bg_animation: [
    {
      animation: "slide-in-left",
      source: "templates",
      speed: 1.4,
      type: "start",
    },
    {
      animation: "none",
      source: "templates",
      speed: 0.5,
      type: "end",
    },
  ],
});

// Hàm chia văn bản thành các đoạn nhỏ theo giới hạn ký tự
const splitTextByLimit = (text, limit) => {
  const words = text.split(" "); // Tách văn bản thành các từ
  const result = [];
  let currentLine = "";

  words.forEach((word) => {
    // Kiểm tra nếu thêm từ hiện tại vào dòng có vượt quá giới hạn
    if ((currentLine + word).length <= limit) {
      currentLine += (currentLine.length > 0 ? " " : "") + word; // Thêm từ vào dòng
    } else {
      // Đưa dòng hiện tại vào kết quả
      result.push(createTextLine(currentLine));
      currentLine = word; // Bắt đầu dòng mới với từ hiện tại
    }
  });

  // Đưa dòng cuối cùng vào kết quả nếu không rỗng
  if (currentLine.length > 0) {
    result.push(createTextLine(currentLine));
  }

  return result;
};

onMounted(() => {
    const detectedLanguage = detectLanguage(summaryContent.value);
    if (detectedLanguage !== "vie") {
        defaultLanguage.value = detectedLanguage;
    }
});

// watch(translateActive, async (newValue) => {
//     translateActive.value = newValue;
//     if (!translateSummaryContent.value) {
//         isLoading.value = true;
//         canCreateVideo.value = false;
//         translateSummaryContent.value = await translateContent(summaryContent.value, 'vi');
//         newSumaryContent.value = translateSummaryContent.value;
//     }
//     if (translateActive.value) {
//         summaryContent.value = translateSummaryContent.value;
//         newSumaryContent.value = translateSummaryContent.value;
//         canCreateVideo.value = true;
//     } else {
//         summaryContent.value = originalSummaryContent.value;
//         newSumaryContent.value = originalSummaryContent.value;
//         canCreateVideo.value = true;
//     }
// });

watch(translateActive, async (newValue) => {
    translateActive.value = newValue;
    isLoading.value = true;
    if(translateActive.value){
        summaryContent.value = await translateContent(summaryContent.value, 'vi');
    }else{
        summaryContent.value = await translateContent(summaryContent.value, 'en');
    }
    isLoading.value = false;
})

// watch(
//     newSumaryContent,
//     () => {
//         checkChanges();
//     },
//     { deep: true }
// );

// const checkChanges = debounce(() => {
//     const isDifferent = newSumaryContent.value == summaryContent.value;
//     if (isDifferent) {
//         canCreateVideo.value = false;
//     } else {
//         canCreateVideo.value = true;
//     }
//     console.log("🚀 ~ isDifferent:", isDifferent);
// }, 500);

const combinedText = computed(() => {
    return summaryContent.value.map((scene) => scene.subtitles[0].text).join("\n");
});

const createVideo = async () => {

    const allValid = summaryContent.value.every((content) =>
        content.subtitles[0].text.trim() !== ""
    );

    if (allValid) {
        const paramsData = {
            videoId: props.video_preview.id,
            videoName: videoName.value,
            summaryContent: summaryContent.value,
            renderParams: renderParams.value,
        };

        isLoading.value = true;
        loadingPercent.value = 0; // Initialize loading percentage

        // Function to simulate loading progress
        const simulateLoading = () => {
            if (loadingPercent.value < 99) {
                const increment = Math.floor(Math.random() * 4) + 2; // Random increment between 2-5%
                loadingPercent.value = Math.min(loadingPercent.value + increment, 99); // Cap at 99%
                setTimeout(simulateLoading, 10000); // Update every 10 seconds
            }
        };

        simulateLoading();

        try {
            const response = await axios.post(route("ai-video.url-to-video.create-video"), paramsData);

            if (response.data.success) {
                videoPreview.value = "";
                videoUrlPreview.value = response.data.data.videoURL;
                resultVideo.value = response.data;
                toast.success("Video được tạo thành công!");
                credits.value = response.data.credit;
                loadingPercent.value = 100;
            } else {
                toast.error("Có lỗi xảy ra khi tạo video!");
            }
        } catch (error) {
            toast.error("Có lỗi xảy ra khi tạo video!");
        } finally {
            isLoading.value = false;
        }
    }else{
        toast.error('Vui lòng kiểm tra lại thông tin')
    }
};


const previewStoryboard = async () => {
    const allValid = summaryContent.value.every((content) => content.subtitles[0].text.trim() !== "");
    if (allValid) {
        renderParams.value.scenes = summaryContent.value;
        const paramsData = {
            videoId: props.video_preview.id,
            videoName: videoName.value,
            renderParams: renderParams.value,
            combinedText: combinedText.value,
        };
        console.log(paramsData);
        isLoading.value = true;

        try {
            const response = await axios.post(route("ai-video.url-to-video.update-story-board"), paramsData);

            if (response.data.success) {
                videoPreview.value = response.data.video.preview_url;
                renderParams.value = response.data.renderParams;
                summaryContent.value = response.data.renderParams.scenes;
                canCreateVideo.value = true;
            } else {
                toast.error("Có lỗi xảy ra khi tạo video!");
            }
        } catch (error) {
            toast.error("Có lỗi xảy ra khi gửi yêu cầu!");
        } finally {
            isLoading.value = false;
        }
    } else {
        toast.error("Vui lòng điền toàn bộ nội dung!");
    }
};

// Handle action

const openDetail = (item) => {
    selectedVideo.value = item;
};

const closeDetail = () => {
    selectedVideo.value = null;
};

const prepareDeleteFile = (id) => {
    imageDeleteId.value = id;
    showConfirmModal.value = true;
};

const confirmDelete = async (id) => {
    isLoading.value = true;

    try {
        const response = await axios.post(route("ai-video.url-to-video.delete", { id: imageDeleteId.value }));
        if (response.status === 200) window.location.href = route("home/index");
    } catch (error) {
        console.error("Đã xảy ra lỗi khi xóa:", error.message);
    } finally {
        isLoading.value = false;
    }
};

const cancelDelete = () => {
    showConfirmModal.value = false;
};

const downloadVideo = async (videoUrl) => {
    if (!videoUrl) {
        alert("Chưa có video");
        return;
    }
    window.location.href = videoUrl;
};

const createPost = (videoUrl) => {
    if (videoUrl) {
        let image = {
            s3_url: videoUrl,
            type: "video",
        };
        localStorage.setItem("aiImage", JSON.stringify(image));
        window.location.href = route("calendar");
    }
};

const openShareLink = () => {
    isShowShareLinkModal.value = true;
};

const closeShareLink = () => {
    isShowShareLinkModal.value = false;
};

const shareAIGeneratedMedia = async (item) => {
    try {
        const response = await axios.post(route("share-link.store"), {
            share_linkable_id: item.id,
            share_linkable_type: "PictoryVideo",
        });

        shareLink.value = route("share-link.show", { token: response.data.data.link_token });
        openShareLink();
    } catch (error) {
        toast.error("Chia sẻ tin thất bại");
    }
};

const detectLanguage = (data) => {
    let combinedText = "";

    // Check if data.scenes exists
    if (data && Array.isArray(data)) {
        for (const scene of data) {
            // Check if subtitles exist
            if (scene.subtitles && Array.isArray(scene.subtitles)) {
                combinedText += scene.subtitles.map((subtitle) => subtitle.text).join(" ") + " ";
            }
        }
    }
    if (combinedText) {
        const langCode = franc(combinedText);
        if (langCode === "und") {
            return "unknown";
        }
        return langCode;
    }
    return "unknown";
};

const translateContent = async (content, language) => {
    try {
        const response = await axios.post(route("ai-video.url-to-video.translate-content"), { content: content, language: language });
        if (response.status === 200) {
            isLoading.value = false;
            return response.data;
        }
    } catch (error) {
        console.log("🚀 ~ translateContent ~ error:", error);
    }
};
const triggerFileInput = (index, type) => {
    uploadCurrentType.value = type; // Cập nhật giá trị ngay lập tức
    nextTick(() => {
        const fileInput = fileInputRefs.value[index];
        if (fileInput) {
            fileInput.click();
        } else {
            console.error(`File input with index ${index} not found.`);
        }
    });
};

const handleFileChange = async (event, background) => {
    const file = event.target.files[0];
    let type = "image";

    if (file) {
        const fileTypes = {
            image: {
                validTypes: ["image/jpeg", "image/png", "image/jpg", "image/gif"],
                errorMessage: "File ảnh phải có định dạng: jpeg, png, jpg, gif.",
            },
            video: {
                validTypes: ["video/mp4", "video/webm", "video/quicktime"],
                errorMessage: "File video phải có định dạng: mp4, webm, mov.",
            },
        };

        const fileCategory = file.type.startsWith("image/") ? "image" : file.type.startsWith("video/") ? "video" : null;

        if (fileCategory && fileTypes[fileCategory].validTypes.includes(file.type)) {
            type = fileCategory;
        } else {
            toast.error(fileCategory ? fileTypes[fileCategory].errorMessage : "Loại file không hợp lệ. Vui lòng chọn file ảnh hoặc video.");
            return;
        }

        const maxFileSize = 500 * 1024 * 1024; // 500MB
        if (file.size > maxFileSize) {
            toast.error("Kích thước file không được vượt quá 500MB.");
            return;
        }

        if (type === "video") {
            const videoValidation = await new Promise((resolve) => {
                const video = document.createElement("video");
                video.src = URL.createObjectURL(file);

                video.onloadedmetadata = () => {
                    const duration = video.duration; // Duration in seconds
                    URL.revokeObjectURL(video.src); // Clean up the object URL
                    resolve(duration <= 60);
                };

                video.onerror = () => {
                    resolve(false);
                };
            });

            if (!videoValidation) {
                toast.error("Thời lượng video không được vượt quá 60 giây.");
                return;
            }
        }

        background.isLoading = true;
        try {
            const formData = new FormData();
            formData.append("upload", file); // Đưa từng file vào FormData với tên là "file"
            const response = await axios.post(route("slide.uploadImage"), formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });
            if (response.status == 200) {
                background.url = response.data.url;
                background.type = type;
                background.asset_id = null;
            }
        } catch (error) {
            toast.error(error);
        } finally {
            background.isLoading = false;
        }
    }
};


const openPostDetail = async () => {

    if (postFormRef.value && !isLoadingPostDetail.value) {
        const file = await getFileByUrl(videoUrlPreview.value);

        let postForm = {
            content: null,
            published: 1,
            scheduled_publish_time: null,
            files: [file],
        };
        postFormRef.value.openPostDetail(postForm);
    }
}

const getFileByUrl = async (videoURL) => {
    isLoadingPostDetail.value = true;
    const response = await fetch(videoURL);
    const blob = await response.blob();
    const file = new File([blob], "video.mp4", { type: 'video/mp4' });
    isLoadingPostDetail.value = false;
    return file;
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
