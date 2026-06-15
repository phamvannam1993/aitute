<template>
    <Head title="Phim - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData">
        <div class="flex justify-between gap-4 w-full flex-wrap md:flex-nowrap">
            <div class="bg-white w-full rounded-[10px] md:rounded-[25px] lg:w-2/5 p-[12px] md:p-[25px] drop-shadow-xl">
                <div class="flex justify-start items-center gap-4 mb-1">
                    <img class="p-2 rounded-2xl" src="/assets/img/ai3goc/logo/circle.svg" />
                    <div>
                        <h2 class="text-black font-bold text-base lg:text-[30px] leading-[32px] line-clamp-1">Video từ văn bản</h2>
                    </div>
                </div>
                <p class="text-sm md:text-base text-ai3goc-primary font-bold mb-4">Thực hiện theo các bước sau:</p>
                <form @submit.prevent="handleSubmit" class="text-black">
                    <div class="relative flex flex-col gap-4">
                        <div class="">
                            <Step :step="1" stepName="Nhập nội dung video" forName="image-description" />
                            <div class="relative">
                                <SuggestionPrompt v-model="videoDescription" :type="'film'" :screenId="3" />
                                <textarea id="video-description" v-model="videoDescription" type="text" placeholder="Một con gấu trúc khổng lồ, đeo kính gọng đen, đang đọc sách trong một quán cà phê. Quyển sách đặt trên bàn, bên cạnh là một tách cà phê nghi ngút khói và bên cạnh cửa sổ của quán cà phê." class="w-full p-3 mt-1 h-[160px] border text-black border-ai3goc-sec rounded-[10px] shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"> </textarea>
                                <div class="object-mic relative ml-auto">
                                    <div v-if="isVideoRecording" class="outline-mic right-3 bottom-3 flex items-center justify-center"></div>
                                    <div v-if="isVideoRecording" class="outline-mic right-3 bottom-3 flex items-center justify-center" id="delayed"></div>
                                    <div v-if="isVideoRecording" class="button-mic right-3 bottom-3 flex items-center justify-center"></div>
                                    <button class="button-mic icon-mic absolute right-3 bottom-3 flex items-center justify-center" @click="startVideoRecording" :disabled="disabledRecord" type="button">
                                        <svg class="h-6 w-6" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000">
                                            <g>
                                                <path d="M500,683.8c84.6,0,153.1-68.6,153.1-153.1V163.1C653.1,78.6,584.6,10,500,10c-84.6,0-153.1,68.6-153.1,153.1v367.5C346.9,615.2,415.4,683.8,500,683.8z M714.4,438.8v91.9C714.4,649,618.4,745,500,745c-118.4,0-214.4-96-214.4-214.4v-91.9h-61.3v91.9c0,141.9,107.2,258.7,245,273.9v124.2H346.9V990h306.3v-61.3H530.6V804.5c137.8-15.2,245-132.1,245-273.9v-91.9H714.4z" />
                                            </g>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <span v-if="errors.description" class="text-red-500 text-sm">{{ errors.description }}</span>
                        </div>
                        <div class="flex items-start justify-between">
                            <Step :step="2" stepName="Thêm ảnh" />
                            <div class="flex flex-col gap-2 w-1/2 text-center self-center">
                                <button type="button" @click="$refs.fileInputImage.click()" class="flex items-center font-bold px-3 py-1.5 justify-center gap-2 bg-transparent text-ai3goc-sec rounded-xl backdrop-blur-sm transition-colors hover:scale-105 border-2 border-ai3goc-sec">
                                    <img src="/assets/img/ai3goc/icon/upload.svg" class="size-6 md:size-5 xl:size-6" />
                                    <span class="text-[12px] md:hidden xl:block xl:text-[14px]">Tải ảnh lên</span>
                                </button>
                                <button type="button" @click="handleCreateImage" class="flex items-center font-bold px-3 py-1.5 justify-center gap-2 bg-transparent text-ai3goc-sec rounded-xl backdrop-blur-sm transition-colors hover:scale-105 border-2 border-ai3goc-sec">
                                    <img src="/assets/img/ai3goc/icon/img-ai.svg" class="size-7 md:size-6 xl:size-7" />
                                    <span class="text-[12px] md:hidden xl:block xl:text-[14px]">Tạo ảnh bằng AI</span>
                                </button>
                            </div>
                        </div>
                        <input type="file" id="image-file" @change="handleImageFileChange" accept="image/*" class="hidden" ref="fileInputImage" />
                        <div class="mt-4 image-url w-full" v-if="imageUrl && videoModel == 'Runway/gen3-alphaturbo'">
                            <img :src="imageUrl" class="rounded-lg w-full h-full" />
                        </div>

                        <div class="mt-4">
                            <Step :step="3" stepName="Nhập nội dung thuyết minh" forName="audio-description" />
                            <div class="relative">
                                <SuggestionPrompt v-model="audioDescription" :type="'film-narration'" :screenId="3" />
                                <textarea id="audio-description" v-model="audioDescription" type="text" placeholder="Tiếng chim hót líu lo trong không gian yên tĩnh" class="w-full p-3 mt-1 h-[160px] border text-black border-ai3goc-sec rounded-[10px] shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                                <div class="object-mic relative ml-auto">
                                    <div v-if="isRecording" class="outline-mic right-3 bottom-3 flex items-center justify-center"></div>
                                    <div v-if="isRecording" class="outline-mic right-3 bottom-3 flex items-center justify-center" id="delayed"></div>
                                    <div v-if="isRecording" class="button-mic right-3 bottom-3 flex items-center justify-center"></div>
                                    <button class="button-mic icon-mic absolute right-3 bottom-3 flex items-center justify-center" @click="startRecording" :disabled="disabledRecord" type="button">
                                        <svg class="h-6 w-6" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000">
                                            <g>
                                                <path d="M500,683.8c84.6,0,153.1-68.6,153.1-153.1V163.1C653.1,78.6,584.6,10,500,10c-84.6,0-153.1,68.6-153.1,153.1v367.5C346.9,615.2,415.4,683.8,500,683.8z M714.4,438.8v91.9C714.4,649,618.4,745,500,745c-118.4,0-214.4-96-214.4-214.4v-91.9h-61.3v91.9c0,141.9,107.2,258.7,245,273.9v124.2H346.9V990h306.3v-61.3H530.6V804.5c137.8-15.2,245-132.1,245-273.9v-91.9H714.4z" />
                                            </g>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <span v-if="errors.audioDescription" class="text-red-500 text-sm">{{ errors.audioDescription }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <Step :step="4" stepName="Thời lượng" forName="video-artist" />
                            <select
                                v-model="videoDuration"
                                :class="{
                                    'bg-gray-200 border-gray-200': !videoDuration,
                                    'bg-white border-ai3goc-sec': videoDuration,
                                }"
                                class="block mt-1 text-[14px] appearance-none lg:w-1/2 text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500 w-1/2"
                                id="video-artist"
                            >
                                <option v-for="duration in durations" :value="duration" :key="duration">
                                    {{ duration }}
                                </option>
                            </select>
                            <span v-if="errors.artist" class="text-red-500 text-sm">{{ errors.artist }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <Step :step="5" stepName="Kích thước" forName="video-dimensions" />
                            <select
                                v-model="videoDimensions"
                                id="video-dimensions"
                                :class="{
                                    'bg-gray-200 border-gray-200': !videoDimensions,
                                    'bg-white border-ai3goc-sec': videoDimensions,
                                }"
                                class="block mt-1 text-[14px] appearance-none w-1/2 text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                            >
                                <option v-for="dimension in validDimensions" :value="dimension" :key="dimension">
                                    {{ dimension }}
                                </option>
                            </select>
                            <span v-if="errors.dimensions" class="text-red-500 text-sm">{{ errors.dimensions }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <Step :step="6" stepName="Ngôn ngữ" forName="video-dimensions" />
                            <select
                                v-model="selectedLanguage"
                                id="language"
                                :class="{
                                    'bg-gray-200 border-gray-200': !selectedLanguage,
                                    'bg-white border-ai3goc-sec': selectedLanguage,
                                }"
                                class="block mt-1 text-[14px] appearance-none w-1/2 text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                            >
                                <option v-for="lang in languages" :value="lang.value" :key="lang.value">
                                    {{ lang.label }}
                                </option>
                            </select>
                            <span v-if="errors.language" class="text-red-500 text-sm">{{ errors.language }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <Step :step="7" stepName="Phụ đề" forName="video-dimensions" />
                            <div class="grid grid-cols-2 w-1/2">
                                <div class='flex gap-1 items-center cursor-pointer'>
                                    <input type="radio" id="enableCaptionTrue" name="enableCaption" value="true" v-model="enableCaption" class="ml-0 rounded-full text-orion-orange focus:ring-orion-orange" />
                                    <label for="enableCaptionTrue">Bật</label>
                                </div>
                                <div class='flex gap-1 items-center cursor-pointer'>
                                    <input type="radio" id="enableCaptionFalse" name="enableCaption" value="false" v-model="enableCaption" class="ml-0 rounded-full text-orion-orange focus:ring-orion-orange" />
                                    <label for="enableCaptionFalse">Tắt</label>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <Step :step="8" stepName="" />

                            <div class="relative w-1/2 text-center-mb">
                                <button v-if="isLoadingVideo" type="button" class="px-4 py-2 bg-ai3goc-bgclr text-white rounded-[10px] min-w-36 text-[15px] font-bold disabled:opacity-50 disabled:pointer-events-none w-full hover:scale-105">Tạo phim</button>
                                <button v-else type="submit" class="px-4 py-2 bg-ai3goc-bgclr text-white rounded-[10px] min-w-36 text-[15px] font-bold disabled:opacity-50 disabled:pointer-events-none w-full hover:scale-105">Gửi yêu cầu</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="bg-white rounded-[10px] md:rounded-[25px] w-full lg:w-3/5 p-[12px] md:p-[25px] drop-shadow-xl text-black">
                <!-- Phần hiển thị video/hình ảnh -->
                <p class="text-ai3goc-primary italic mb-1">Hiển thị kết quả</p>
                <div class="flex justify-center items-center mb-4">
                    <video v-if="videoRef" :src="videoRef" alt="video-generate-by-AI" controls="" preload="auto" class="h-auto md:max-w-[500px] md:max-h-[500px] object-cover" />
                    <div v-else class="bg-[#E6E6E6] w-full h-[50vh] rounded-2xl showLoaderVideo items-center">
                        <div v-if="isLoadingVideo">
                            <div class="loaderVideo"></div>
                            <p class="text-loading">Video đang được tạo. Vui lòng chờ !</p>
                        </div>
                        <img v-else src="\assets\img\aiwow\ai_image\placeholder.png" alt="" class="w-20 h-fit">
                    </div>
                </div>
                <TaskBar :isActive="resultValue" :selected-image="resultValue" :isCallUploadAudio="isCallUploadAudio" @handle-audio-file-change="handleAudioFileChange" :shareLinkableType="'AIGeneratedMedia'" :features="['lipsync', 'ai-video']" type="video" />

                <!-- Nút lịch sử -->
                <div class="flex justify-end mt-4 w-full btn-history">
                    <a v-if="isLoadingVideo && !videoRef" href="javascript:void(0)" @click="showAlert()" class="px-4 w-[180px] text-center py-2 bg-ai3goc-bgclr text-white font-bold text-[15px] rounded-[10px] h-fit">Lịch sử</a>
                    <a v-else :href="route('ai-video.history')" class="px-4 w-[180px] text-center py-2 bg-ai3goc-bgclr text-white font-bold text-[15px] rounded-[10px] h-fit">Lịch sử</a>
                </div>
            </div>
        </div>
    </Layout>

    <div v-if="isLoading" class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
        <div class="loader"></div>
    </div>
    <CreditModal :showCreditPopup="showCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleCancel" />
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="acceptUpgrade" />
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
</template>
<script setup>
import Layout from "@/Layouts/Client/Layout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { computed, onMounted, ref } from "vue";
import Footer from "../Home/Components/Footer.vue";
import Credit from "@/Components/Credit.vue";
import SuggestionPrompt from "@/Components/AIVirtual/SuggestionPrompt.vue";
import CreditModal from "../../../Components/ModalBuyCredit/Index.vue";
import axios from "axios";
import CreditBuyModal from "../../../Components/ModalBuyCredit/BuyCredit.vue";
import PopupAff from "@/Components/PopupAff.vue";
import { toast } from "vue3-toastify";
import FormShareLink from "@/Components/FormShareLink.vue";
import Modal from "@/Components/Modal.vue";
import TaskBar from '@/Components/TaskBar.vue'
import Popup from '@/Components/Popup/Index.vue'
import Step from "@/Components/Step/Index.vue";

const props = defineProps({
    ai_assistant: Object,
});

const breadcrumbsData = [
    { text: "Phim", link: "ai-video.history" },
];

const { props: pageProps } = usePage();
const auth = computed(() => pageProps.value.auth);
const additionalCredit = ref(0);
const showAffKeyPopup = ref(false);
const credits = ref(0);
const redirect = ref("/");
const videoRef = ref("");
const isShowAlert = ref(false);
const taskId = ref("");
const models = ["Kling", "Lucataco/animate-diff", "Runway/gen3-alphaturbo"];
const durations = computed(() => {
    if (videoModel.value === "Kling") {
        videoDuration.value = 5;
        return [5, 10];
    }
    return [5, 10];
});
const textToSpeechGoogle = async (audio_des, duration) => {
   try {
        const result = await axios.post(route("ai-audio.text-to-speech-google"), {
            text: audio_des,
            duration: duration,
        });
        if (result.data.success) {
            return result.data.data;
        }
    } catch (error) {
       return "";
    }
};
const videoDescription = ref("");
const audioDescription = ref("");
const videoModel = ref("Runway/gen3-alphaturbo");
const videoDuration = ref(5);
const videoDimensions = ref("16:9");
const isLoading = ref(false);
const isLoadingVideo = ref(false);
const isHistory = ref(false);
const errors = ref({});
const isUploadAI = ref(false);
const isShowDes = ref(true);

const popupMessage = ref("Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!");
const acceptUpgrade = ref(true);

const videoFile = ref(null);
const audioFile = ref(null);
const imageFile = ref(null);
const imageUrl = ref(null);
const s3Url = ref("");
const resultValue = ref(null);
const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
const showConfirmModal = ref(false);
const videoDeleteId = ref(null);
const enableCaption = ref(false);
const aiMediaId = ref(0);
const audioName = ref(null);
const audioPathCut = ref("");
const isTranscription = ref(false);
let showBuyCreditPopup = ref(false);
const handleBuyCancel = () => {
    showBuyCreditPopup.value = false;
};
const showBuyCreditModal = () => {
    showBuyCreditPopup.value = true;
};
const checkCredit = async () => {
    try {
        const formData = new FormData();
        formData.append("model", videoModel.value);
        formData.append("type", "video");
        // Gọi API để kiểm tra credit của user
        const response = await axios.post(route("credit.check_credit"), formData);
        if (response.data.success) {
            // Đủ credit để tiếp tục
            return true;
        } else if (response.data.is_vip_expired) {
            showAffKeyPopup.value = true;
            if (response.data.affCode == "aff_trial") {
                popupMessage.value = " Gói của bạn đã hết hạn. Xin vui lòng gia hạn tài khoản để tiếp tục sử dụng tính năng này.";
                acceptUpgrade.value = false;
            }
            return false;
        } else {
            additionalCredit.value = response?.data?.data?.total_price - pageProps.auth.user.credit;
            // Không đủ credit, hiển thị modal yêu cầu nạp thêm
            showBuyCreditModal();
            return false;
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi khi kiểm tra credit:", error);
        toast.error("Không thể kiểm tra credit. Vui lòng thử lại.");
        if (error.response && error.response.status === 422) {
            const errors = error.response.data.errors;
            Object.values(errors).forEach((errorMessages) => {
                if (Array.isArray(errorMessages)) {
                    errorMessages.forEach((error) => {
                        toast.error(error);
                    });
                } else {
                    toast.error(errorMessages);
                }
            });
        } else {
            if (error.response) {
                toast.error(error.response.data.message || "Đã xảy ra lỗi.");
            } else if (error.request) {
                toast.error("Không thể kết nối đến server.");
            } else {
                toast.error("Lỗi: " + error.message);
            }
        }
        return false;
    }
};

const selectedLanguage = ref("vi-VN");
const languages = [
    { value: "vi-VN", label: "Tiếng Việt" },
    { value: "en-US", label: "Tiếng Anh" },
    { value: "ja-JP", label: "Tiếng Nhật" },
    { value: "ko-KR", label: "Tiếng Hàn" },
    { value: "zh-CN", label: "Tiếng Trung" },
];

const validDimensions = computed(() => {
    if (videoModel.value === "Kling") {
        videoDimensions.value = "9:16";
        return [ "9:16", "16:9", "1:1"];
    }
    if (videoModel.value === "Runway/gen3-alphaturbo") {
        videoDimensions.value = "9:16";
        return ["9:16", "16:9"];
    }
});

const cancelExit = () => {
    history.pushState({}, "");
    isShowAlert.value = false;
};

const confirmExit = () => {
    if (isHistory.value) {
        window.location.href = "/ai-video/history";
    } else {
        var redirect = localStorage.getItem("referrer");
        window.location.href = redirect;
    }
};

const showAlert = () => {
    isShowAlert.value = true;
    isHistory.value = true;
};
const referrer = document.referrer;
if (!referrer.includes("ai-video") || referrer.includes("ai-video/history")) {
    localStorage.setItem("referrer", referrer);
}
for (var i = 0; i < 100; i++) {
    history.pushState({}, "");
}
window.onpopstate = function () {
    if (isLoadingVideo.value && !videoRef.value) {
        isShowAlert.value = true;
        isHistory.value = false;
    }
};

const handleVideoFileChange = (event) => {
    videoFile.value = event.target.files[0];
};

const fileName = ref("");
const handleAudioFileChange = async (event) => {
    var type = event.target.files[0].type;
    const allowedAudioTypes = ["audio/mpeg", "audio/wav"];
    if (!allowedAudioTypes.includes(type)) {
        alert("Xin vui lòng tải lên các tệp âm thanh có định dạng .mp3 hoặc .wav.");
        return;
    }
    audioFile.value = event.target.files[0];
    audioName.value = audioFile.value ? audioFile.value.name : "Chưa có tệp nào được chọn";

    const formData = new FormData();
    formData.append("ai_media_id", aiMediaId.value);
    if (audioFile.value) {
        formData.append("audio_file", audioFile.value);
    }
    formData.append("type", "ai-video");
    try {
        isCallUploadAudio.value = true
        const response = await axios.post(route("ai-video.mergeAudioVideoQueue"), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
        if (response.data.success) {
             isCallUploadAudio.value = false
            callApiDetail()
        }
    } catch (error) {
        isLoadingVideo.value = false;
    } finally {
    }
};

const callApiDetail = async () => {
    var url =  route("ai-video.detail", { id: aiMediaId.value })
    try {
        const response = await axios.get(url)
        aiMediaId.value =  response?.data?.id
        videoRef.value = response.data.s3_url;
        resultValue.value = response?.data;
    } catch (error) {
        isCallUploadAudio.value = false
    }
}

let allowedExtension = ["image/jpeg", "image/jpg", "image/png"];
const handleImageFileChange = async (event) => {
    let type = event.target.files[0].type;
    if (allowedExtension.indexOf(type) < 0) {
        alert("Vui lòng chọn lại ảnh có định dạng .png, .jpg, .jpeg");
        return false;
    }
    const { width, height } = await getImageDimensions(event.target.files[0]);
    var imageRatio = width / height;
    if (imageRatio < 0.5 || imageRatio > 2) {
        alert("Ảnh của bạn không hợp lệ. Vui lòng tải ảnh có kích thước tỷ lệ chiều dài/chiều rộng nằm trong khoảng 0.5 đến 2.");
        return false;
    }
    imageFile.value = event.target.files[0];
    imageUrl.value = URL.createObjectURL(imageFile.value);
    s3Url.value = "";
    isUploadAI.value = false;
};

async function getImageDimensions(file) {
    let img = new Image();
    img.src = URL.createObjectURL(file);
    await img.decode();
    let width = img.width;
    let height = img.height;
    return {
        width,
        height,
    };
}

const validateForm = () => {
    errors.value = {}; // Reset errors
    if (!videoDescription.value.trim()) {
        errors.value.description = "Mô tả video là bắt buộc.";
    }
    if (videoDescription.value.trim().indexOf("[") > -1 || videoDescription.value.trim().indexOf("]") > -1) {
        errors.value.description = 'Mô tả video của chưa ký tự đặc biệt dấu "[ hoặc ]"';
    }
    if (videoDescription.value.trim().indexOf("https") > -1 || videoDescription.value.trim().indexOf("http") > -1) {
        errors.value.description = "Mô tả video của chứa đường dẫn http hoặc https";
    }
    if (videoDescription.value.trim().indexOf("..") > -1 && videoDescription.value.trim().length < 4) {
        errors.value.description = 'Mô tả video của chứa dấu ".."';
    }
    if (videoDescription.value.trim().length < 2) {
        errors.value.description = "Mô tả video tối thiểu 2 ký tự";
    }

    if (audioDescription.value.trim().indexOf("[") > -1 || audioDescription.value.trim().indexOf("]") > -1) {
        errors.value.audioDescription = 'Nội dung thuyết minh đang chưa ký tự đặc biệt dấu "[ hoặc ]"';
    }
    if (audioDescription.value.trim().indexOf("https") > -1 || audioDescription.value.trim().indexOf("http") > -1) {
        errors.value.audioDescription = "Nội dung thuyết minh đang chứa đường dẫn http hoặc https";
    }
    if (audioDescription.value.trim().indexOf("..") > -1 && audioDescription.value.trim().length < 4) {
        errors.value.audioDescription = "Nội dung thuyết minh đang chứa dấu ..";
    }

    if (!videoModel.value) {
        errors.value.style = "Mô hình là bắt buộc.";
    }
    if (!videoDuration.value) {
        errors.value.artist = "Nghệ sĩ là bắt buộc.";
    }
    if (!videoDimensions.value) {
        errors.value.dimensions = "Kích thước là bắt buộc.";
    }
    if (!selectedLanguage.value) {
        errors.value.language = "Ngôn ngữ là bắt buộc.";
    }
    return Object.keys(errors.value).length === 0;
};

const toggleTranscription = async (data) => {
    try {
        const response = await axios.post(route("ai-video.create-video-with-transcription"), {
            ai_media_id: aiMediaId.value,
            duration: videoDuration.value,
            screen_id: 3,
        });
        const transcription = response?.data?.video;
        data.s3_url = transcription
        videoRef.value = transcription;
        videoModel.value == "Runway/gen3-alphaturbo"
        resultValue.value = data;
        isLoadingVideo.value = false;
        isTranscription.value = true;
    } catch (error) {
        console.error("Error creating transcription:", error);
        toast.error("Có lỗi xảy ra khi tạo video. Vui lòng thử lại.");
    } finally {
    }
};

const handleSubmit = async () => {
    console.log(!validateForm(), errors.value);
    if (!validateForm()) {
        return;
    }
    await generateVideo();
};
const isCallApi = ref(false);
const isCallUploadAudio = ref(false);
let showCreditPopup = ref(false);
const handleBuyCredit = () => {
    window.location.href = "/pricing";
};

const handleCancel = () => {
    showCreditPopup.value = false;
};

const showCreditModal = () => {
    showCreditPopup.value = true;
};

const generateVideo = async () => {
    try {
        if (videoDescription.value.length > 255) {
            alert("Mô tả nội dung video không được vượt quá 255 ký tự");
            return;
        }
        if (audioDescription.value.length > 255) {
            alert("Nội dung thuyết minh không được vượt quá 255 ký tự");
            return;
        }
        const hasEnoughCredit = await checkCredit();
        if (!hasEnoughCredit) {
            isLoading.value = false;
            return; // Dừng nếu không đủ credit
        }
        var audioUrl = ""
        if(audioDescription.value.length > 0) {
            audioUrl = await textToSpeechGoogle(audioDescription.value)
        }
        videoRef.value = null;
        const formData = new FormData();
        formData.append("video_description", videoDescription.value);
        formData.append("audio_url", audioUrl);
        formData.append("model", videoModel.value);
        formData.append("duration", videoDuration.value);
        formData.append("number_result", videoDuration.value);
        formData.append("ratio", videoDimensions.value);
        formData.append("language", selectedLanguage.value);
        formData.append("s3_url", s3Url.value);
        if (imageFile.value) {
            formData.append("image_file", imageFile.value);
        }
        isLoadingVideo.value = true;
        taskId.value = ""
        const response = await axios.post(route("ai-video.generate-video-audio"), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        if (response.data.success) {
            aiMediaId.value = response?.data?.id;
            if(enableCaption.value) {
                await toggleTranscription(response?.data.generate_file);
            } else {
                videoRef.value = response?.data?.s3_url || "";
                resultValue.value = response?.data.generate_file;
                isLoadingVideo.value = false;
            }

        } else {
            toast.error(response.data.message)
            isLoadingVideo.value = false;
        }
    } catch (error) {

    } finally {
    }
};

const nextModel = async () => {
    if (videoModel.value == "Runway/gen3-alphaturbo") {
        videoModel.value = "Kling";
        await generateVideo();
    }
};

const downloadVideo = async () => {
    if (!videoRef.value) {
        alert("Chưa có video");
        return;
    }
    var url = route("ai-video.downloadFile", { url: videoRef.value, name: "video.mp4" });
    window.location.href = url;
};

const handleCheckSieze = () => {
    if (window.innerWidth >= 1024) {
        isShowDes.value = true;
    } else {
        isShowDes.value = false;
    }
};

onMounted(() => {
    handleCheckSieze();
    window.addEventListener("resize", handleCheckSieze);
});

const isVideoRecording = ref(false);
const startVideoRecording = async () => {
    if (!isVideoRecording.value) {
        // Nếu chưa ghi âm
        try {
            isVideoRecording.value = true; // Bắt đầu ghi âm
            device = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(device);

            // Khi có dữ liệu âm thanh
            mediaRecorder.addEventListener("dataavailable", (event) => {
                audioChunks.value.push(event.data);
            });

            // Khi dừng ghi âm
            mediaRecorder.addEventListener("stop", async () => {
                audioBlob.value = new Blob(audioChunks.value, { type: "audio/mp3" });
                audioUrl.value = URL.createObjectURL(audioBlob.value);

                // Tạo FormData và gửi yêu cầu API
                const formData = new FormData();

                // Sửa lại: Gói Blob thành File object trước khi thêm vào FormData
                const file = new File([audioBlob.value], "audio.mp3", { type: "audio/mp3" });
                formData.append("audio", file);

                // isTranscribing.value = true;
                try {
                    const response = await axios.post("/speech-to-text", formData, {
                        headers: { "Content-Type": "multipart/form-data" },
                    });
                    // Xử lý văn bản trả về từ API
                    console.log("Transcription Text:", response);
                    videoDescription.value = response?.data?.text || "Vui lòng thử lại";
                } catch (error) {
                    console.error("Error in Speech-to-Text:", error);
                } finally {
                    // isTranscribing.value = false;
                }

                isVideoRecording.value = false;
            });

            // Bắt đầu ghi âm
            mediaRecorder.onstart = () => {
                audioChunks.value = []; // Xóa các đoạn âm thanh trước đó
            };

            mediaRecorder.start(); // Bắt đầu ghi
        } catch (error) {
            console.error("Lỗi khi bắt đầu ghi âm:", error);
            isVideoRecording.value = false; // Trở lại trạng thái chưa ghi âm nếu có lỗi
        }
    } else {
        // Nếu đang ghi âm
        isVideoRecording.value = false; // Dừng ghi âm
        mediaRecorder.stop(); // Kết thúc ghi âm
        device.getTracks().forEach((track) => track.stop()); // Dừng tất cả các tracks
    }
};

const isRecording = ref(false);
const audioBlob = ref(null);
const audioUrl = ref(null);
let mediaRecorder = null;
let audioChunks = [];
const audioResult = ref({});
let device = ref(null);
const isTranscribing = ref(false);
const startRecording = async () => {
    if (!isRecording.value) {
        // Nếu chưa ghi âm
        try {
            isRecording.value = true; // Bắt đầu ghi âm
            device = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(device);

            // Khi có dữ liệu âm thanh
            mediaRecorder.addEventListener("dataavailable", (event) => {
                audioChunks.value.push(event.data);
            });

            // Khi dừng ghi âm
            mediaRecorder.addEventListener("stop", async () => {
                audioBlob.value = new Blob(audioChunks.value, { type: "audio/mp3" });
                audioUrl.value = URL.createObjectURL(audioBlob.value);

                // Tạo FormData và gửi yêu cầu API
                const formData = new FormData();

                // Sửa lại: Gói Blob thành File object trước khi thêm vào FormData
                const file = new File([audioBlob.value], "audio.mp3", { type: "audio/mp3" });
                formData.append("audio", file);

                // isTranscribing.value = true;
                try {
                    const response = await axios.post("/speech-to-text", formData, {
                        headers: { "Content-Type": "multipart/form-data" },
                    });
                    // Xử lý văn bản trả về từ API
                    console.log("Transcription Text:", response);
                    audioDescription.value = response?.data?.text || "Vui lòng thử lại";
                } catch (error) {
                    console.error("Error in Speech-to-Text:", error);
                } finally {
                    // isTranscribing.value = false;
                }

                isRecording.value = false;
            });

            // Bắt đầu ghi âm
            mediaRecorder.onstart = () => {
                audioChunks.value = []; // Xóa các đoạn âm thanh trước đó
            };

            mediaRecorder.start(); // Bắt đầu ghi
        } catch (error) {
            console.error("Lỗi khi bắt đầu ghi âm:", error);
            isRecording.value = false; // Trở lại trạng thái chưa ghi âm nếu có lỗi
        }
    } else {
        // Nếu đang ghi âm
        isRecording.value = false; // Dừng ghi âm
        mediaRecorder.stop(); // Kết thúc ghi âm
        device.getTracks().forEach((track) => track.stop()); // Dừng tất cả các tracks
    }
};

const stopRecording = () => {
    if (mediaRecorder) {
        mediaRecorder.stop();
        isRecording.value = false;
    }
};

const handleCreateImage = async () => {
    if (videoDescription.value.trim() == "") {
        toast.error("Vui lòng nhập mô tả nội dung video");
        isLoading.value = false;
        return;
    }

    isLoading.value = true;
    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        isLoading.value = false;
        return; // Dừng nếu không đủ credit
    }

    isUploadAI.value = true;
    const response = await axios.post(route("ai-image.generate"), {
        description: videoDescription.value,
        style: "",
        artist: "",
        height: 1024,
        width: 1024,
        model: "Flux Schnell",
        aspect_ratio: videoDimensions.value,
    });

    if (response.status === 200 && response.data.url) {
        // Add each response to the array for display
        isLoading.value = false;
        s3Url.value = response.data.url;
        imageUrl.value = response.data.url;
    } else {
        console.error("Không có URL hình ảnh trong phản hồi.");
        alert("Không thể tạo hình ảnh. Vui lòng thử lại.");
    }
};

const prepareDeleteFile = (id) => {
    videoDeleteId.value = id;
    showConfirmModal.value = true;
};

const confirmDelete = async () => {
    isLoading.value = true;

    try {
        const response = await axios.post(route("ai-video.delete", { id: videoDeleteId.value }));
        if (response.status === 200) window.location.reload();
    } catch (error) {
        console.error("Đã xảy ra lỗi khi xóa:", error.message);
    } finally {
        isLoading.value = false;
    }
};

const cancelDelete = () => {
    showConfirmModal.value = false;
};

const downloadImage = (image) => {
    var url = route("ai-video.downloadFile", { url: image, name: "image.png" });
    window.open(url, "_blank");
};

const createPost = (imageUrl) => {
    if (imageUrl) {
        let image = {
            s3_url: imageUrl,
            type: "image",
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
            share_linkable_type: "AIGeneratedMedia",
        });

        shareLink.value = route("share-link.show", { token: response.data.data.link_token });
        openShareLink();
    } catch (error) {
        console.log(error, 'error');
        toast.error("Chia sẻ tin thất bại");
    }
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

.loaderVideo {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #24AA69;
    border-radius: 50%;
    width: 120px;
    height: 120px;
    margin-left: 25%;
    animation: spin 2s linear infinite;
}

.showLoaderVideo {
    display: flex;
    justify-content: center;
    flex-direction: column-reverse;
    justify-content: center;
    margin-left: 50;
}

.text-loading {
    color: black;
    text-align: center;
    margin-top: 20px;
}
@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

.object-mic {
    display: flex;
    flex: 0 1 100%;
    justify-content: center;
    align-items: center;
    align-content: stretch;
}

.outline-mic {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: 8px solid #b5a4a4;
    animation: pulseMic 3s;
    animation-timing-function: ease-out;
    animation-iteration-count: infinite;
    position: absolute;
}

.button-mic {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    /* background: #50CDDD; */
    box-shadow: 0px 0px 80px #0084f9;
    position: absolute;
}

@keyframes pulseMic {
    0% {
        transform: scale(0.5);
        opacity: 1;
    }
    50% {
        transform: scale(1.5);
        opacity: 0.4;
    }

    100% {
        transform: scale(2);
        opacity: 0;
    }
}

.btn-create-image {
    position: relative;
    height: 30px;
    line-height: 12px;
    cursor: pointer;
    width: 165px;
}

@media only screen and (max-width: 599px) {
    .btn-upload-image {
        width: 240px;
    }
    .desktop {
        display: none;
    }
    .text-center-mb {
        text-align: center;
    }
}
.bg-primary-color {
    background-color: #2c75e3;
    color: #fff;
}
@media (min-width: 600px) {
    .mobile {
        display: none;
    }
    .lg\:gap-5 {
        gap: 5.5rem;
    }
}
.text-name-audio {
    max-width: 100px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
.loaderAudio {
    width: 30px !important;
    height: 30px !important;
    margin-left: 0px !important;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #24AA69;
}
</style>
