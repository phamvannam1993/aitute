<template>
    <Head title="Hình ảnh - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData">
        <div class="flex justify-between gap-4 w-full flex-wrap md:flex-nowrap drop-shadow-xl">
            <div ref="resultBox" class="bg-white flex flex-col w-full rounded-[10px] md:rounded-[25px] lg:w-2/5 p-[12px] md:p-[25px] h-fit">
                <div class="flex justify-start items-center gap-4">
                    <img class="bg-[#D4D4D4] p-2 rounded-2xl" src="/assets/img/aiwow/robot.png" />
                    <div>
                        <h2 class="text-black font-bold text-base lg:text-[30px] leading-[32px] line-clamp-1">Tạo ảnh từ ảnh gốc</h2>
                    </div>
                </div>
                <p class="text-xs lg:text-sm text-[#092A99] font-bold">Thực hiện theo các bước sau:</p>
                <form @submit.prevent="generateImage" class="text-black mt-4">
                    <div class="relative flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <label for="image-description" class="text-xs lg:text-sm flex gap-1 items-center mb-1 font-semibold">
                                <div class="flex items-center py-1 px-2 rounded-2xl text-white bg-primary-color">
                                    <img class="h-2 w-4" src="/assets/img/aiwow/right_arrow.png" />
                                    <p>Bước 1</p>
                                </div>
                                <p>Tải hình ảnh gốc</p>
                            </label>
                            <input type="file" id="image-file" @change="handleImageFileChange" accept="image/*" class="hidden" ref="fileInputImage" />
                            <button type="button" @click="$refs.fileInputImage.click()" class="font-bold flex items-center px-3 py-1.5 justify-center gap-2 bg-transparent text-primary-color rounded-xl backdrop-blur-sm transition-colors hover:scale-105 border-2 border-primary-color">
                                <img src="/assets/svgs/aiwow/upload.svg" class="size-6 md:size-5 xl:size-6" />
                                <span class="text-[12px] md:hidden xl:block xl:text-[15px]">Tải ảnh</span>
                            </button>
                        </div>
                        <div class="mt-4 image-url" v-if="imageUrl">
                            <img :src="imageUrl" />
                        </div>
                        <div class="">
                            <label for="image-description" class="text-xs lg:text-sm flex gap-1 items-center mb-1 font-semibold">
                                <div class="flex items-center py-1 px-2 rounded-2xl text-white bg-primary-color">
                                    <img class="h-2 w-4" src="/assets/img/aiwow/right_arrow.png" />
                                    <p>Bước 2</p>
                                </div>
                                <p>Mô tả nội dung hình ảnh</p>
                            </label>
                            <div class="relative">
                                <SuggestionPrompt v-model="imageDescription" :type="'image'" :screenId="2" />
                                <textarea id="image-description" v-model="imageDescription" type="text" placeholder="Nhập mô tả ảnh bạn muốn tạo..." :disabled="loadingCreateImage" class="w-full p-3 mt-1 h-[200px] border text-black border-[#2C75E3] rounded-[10px] shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>

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

                            <span v-if="errors.description" class="text-red-500 text-sm">{{ errors.description }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <label for="submit-swapface" class="text-xs lg:text-sm flex items-center gap-1 mb-1 font-semibold">
                                <div class="flex items-center py-1 px-2 rounded-2xl text-white bg-primary-color h-fit">
                                    <img class="h-2 w-4" src="/assets/img/aiwow/right_arrow.png" />
                                    <p>Bước 3</p>
                                </div>
                                <p>Bấm vào đây</p>

                            </label>
                            <button id="submit-swapface" type="submit" class="font-bold px-4 py-2 bg-[#2C75E3] text-white rounded-[10px] text-14 hover:bg-blue-600 transition-colors duration-300 hover:scale-105" :disabled="loadingCreateImage">
                                {{ loadingCreateImage ? "Đang xử lý..." : "Tạo ảnh" }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="bg-white rounded-[10px] md:rounded-[25px] w-full lg:w-3/5 p-[12px] md:p-[25px]">
                <p class="text-primary-color italic font-thin">Hiển thị kết quả</p>
                <div class="flex flex-col justify-between items-center h-fit text-black">
                    <div v-if="imageRef" class="relative">
                        <img @click="openDetail(imageRef)" :src="imageRef.s3_url" alt="image generated by AI" class="w-full h-auto object-contain cursor-pointer rounded-2xl" />
                    </div>
                    <div class="flex justify-center items-center mb-4 w-full">
                        <div v-if="!imageResponse" class="bg-[#E6E6E6] w-full h-[40vh] rounded-2xl showLoaderImage">
                            <div v-if="isLoadingImage">
                                <div class="loaderImage"></div>
                                <p class="text-loading">Ảnh đang được tạo. Vui lòng chờ !</p>
                            </div>
                            <div v-else>
                                <img src="/assets/svgs/aiwow/image.svg" alt="placeholder" class="mx-auto w-24" />
                            </div>
                        </div>
                    </div>
                    <TaskBar :isActive="!!imageResponse" :selectedImage="imageRef" :shareLinkableType="'AIGeneratedMedia'" :features="['video', 'swap_face']" />
                    <div class="w-full justify-end items-center flex flex-col md:flex-row mt-2 md:w-full xl:w-full lg:pb-[4px]">
                        <div class="flex mt-4" ref="resultBox">
                            <a :href="route('ai-image.history')" class="px-4 w-[180px] text-center py-2 bg-[#2C75E3] text-white font-bold text-[15px] rounded-[10px] h-fit"> Lịch sử </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>

    <div v-if="isTranscribing" class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-50">
        <div class="loader"></div>
        <!-- You can use a spinner here -->
    </div>
    <CreditModal :showCreditPopup="showCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleCancel" />
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="acceptUpgrade" />
    <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleBuyCancel" :currentCredit="pageProps.auth.user.credit || 0" :additionalCredit="additionalCredit" />

    <ShowDetailImage :selectedImage="selectedImage" :closeDetail="closeDetail" />
</template>
<script setup>
import SuggestionPrompt from "@/Components/AIVirtual/SuggestionPromptImage.vue";
import FormShareLink from "@/Components/FormShareLink.vue";
import MainMenu from "@/Components/MainMenu.vue";
import Modal from "@/Components/Modal.vue";
import Layout from "@/Layouts/Client/AiLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { computed, nextTick, onMounted, ref, watch } from "vue";
import { toast } from "vue3-toastify";
import Credit from "@/Components/Credit.vue";
import Footer from "../Home/Components/Footer.vue";
import CreditModal from "../../../Components/ModalBuyCredit/Index.vue";
import CreditBuyModal from "../../../Components/ModalBuyCredit/BuyCredit.vue";
import axios from "axios";
import ShowDetailImage from "@/Components/ShowDetailImage.vue";
import PopupAff from "@/Components/PopupAff.vue";
import TaskBar from "@/Components/TaskBar.vue";

const props = defineProps({
    ai_assistant: Object,
});
const { props: pageProps } = usePage();
const auth = computed(() => pageProps.value.auth);
const additionalCredit = ref(0);

const breadcrumbsData = [
    { text: "Ảnh", link: "ai-image.history" },
    { text: "Tạo ảnh từ ảnh", link: "ai-image.imageToImage" },
];

const isLoadingImage = ref(false);
const disabledRecord = ref(false);
const credits = ref(0);
const imageRef = ref(null);
const currentImgSelect = ref(null);
const imageFile = ref(null);
const imageUrl = ref(null);

const popupMessage = ref("Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!");
const acceptUpgrade = ref(true);

let allowedExtension = ["image/jpeg", "image/jpg", "image/png"];
const handleImageFileChange = (event) => {
    let type = event.target.files[0].type;
    if (allowedExtension.indexOf(type) < 0) {
        alert("Vui lòng chọn lại ảnh có định dạng .png, .jpg, .jpeg");
        return false;
    }
    imageFile.value = event.target.files[0];
    imageUrl.value = URL.createObjectURL(imageFile.value);
};

const models = ["Consistent Character"];
const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
// setDefault Value For Model = Consistent Character
const imageModel = ref("Consistent Character");
const imageDescription = ref("");
const isLoading = ref(false);
const isShowDes = ref(true);
const resultBox = ref(null);
const imageResponse = ref(null);
const errors = ref({});
const numberImageSelect = ref(1);
const loadingCreateImage = ref(false);
const loadingImageState = ref(false);
const selectedImage = ref(null);
const openDetail = (item) => {
    selectedImage.value = item;
    currentImgSelect.value = item;
};

const showConfirmModal = ref(false);
const imageDeleteId = ref(null);

const closeDetail = () => {
    selectedImage.value = null;
};

const validateForm = () => {
    errors.value = {}; // Reset lỗi
    if (!imageDescription.value.trim()) {
        errors.value.description = "Mô tả hình ảnh là bắt buộc.";
    } else if (imageDescription.value.length > 2000) {
        errors.value.description = "Mô tả hình ảnh không được vượt quá 2000 ký tự.";
    }
    return Object.keys(errors.value).length === 0;
};

const showAffKeyPopup = ref(false);
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
        formData.append("model", "consistent-character");
        formData.append("type", "consistent-character");
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

const generateImage = async () => {
    if (!validateForm()) {
        return;
    }
    // isLoading.value = true;
    let currentDescription = imageDescription.value;
    imageResponse.value = "";
    imageRef.value = "";
    credits.value = "";

    try {
        const hasEnoughCredit = await checkCredit();
        if (!hasEnoughCredit) {
            isLoading.value = false;
            return; // Dừng nếu không đủ credit
        }
        loadingImageState.value = true;
        let finalDescription = currentDescription;
        const formData = new FormData();
        formData.append("description", currentDescription);
        formData.append("model", "consistent-character");
        if (imageFile.value) {
            formData.append("image_file", imageFile.value);
        } else {
            alert("Vui lòng chọn ảnh");
            return false;
        }
        isLoadingImage.value = true;
        loadingCreateImage.value = true;
        const response = await axios.post(route("ai-image.generateImageToImage"), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        if (response.status === 200 && response.data.s3_url) {
            // selectedImage.value = response.data.generate_file;
            imageResponse.value = response.data.s3_url;
            imageRef.value = response.data.generate_file;
            credits.value = response.data.credits;
            isLoadingImage.value = false;
            loadingCreateImage.value = false;
        } else {
            alert(response.data.message);
            isLoadingImage.value = false;
            loadingCreateImage.value = false;
        }
    } catch (error) {
        isLoadingImage.value = false;
        alert(error.response?.data?.message || "Unknown error occurred");
        loadingCreateImage.value = false;
    }
};

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

onMounted(() => {
    handleCheckSieze();
    window.addEventListener("resize", handleCheckSieze);
});

const handleCheckSieze = () => {
    if (window.innerWidth >= 1024) {
        isShowDes.value = true;
    } else {
        isShowDes.value = false;
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
                    imageDescription.value = response?.data?.text || "Vui lòng thử lại";
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
</script>

<style scoped>
.loader {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #24AA69;
    border-radius: 50%;
    width: 60px;
    height: 60px;
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
.loaderImage {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #24AA69;
    border-radius: 50%;
    width: 120px;
    height: 120px;
    margin-left: 40%;
    animation: spin 2s linear infinite;
}

.showLoaderImage {
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
</style>
