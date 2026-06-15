<template>
    <Head title="Đổi khuôn mặt - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData" :credits="credits" isBig>
        <div class="flex justify-between gap-4 w-full flex-wrap md:flex-nowrap">
            <div class="bg-white w-full rounded-[10px] md:rounded-[25px] lg:w-1/2 p-[12px] md:p-[25px] drop-shadow-xl flex flex-col">
                <div class="flex flex-col mb-2">
                    <div class="flex gap-1">
                        <div class="flex justify-start items-center gap-2">
                            <div class="w-[52px] overflow-hidden rounded-full border-[1px] border-[#d4d4d4]">
                                <img class="w-full h-auto object-top" src="/assets/img/ai3goc/logo/circle.svg" alt="Robot" />
                            </div>

                            <h2 class="text-black font-bold text-xl lg:text-[25px] leading-none">
                                Đổi khuôn mặt
                            </h2>
                        </div>
                    </div>
                    <p class="text-ai3goc-sec font-bold text-xs md:text-base my-1 md:my-4">Thực hiện theo các bước sau:</p>
                </div> 

                <div v-if="apiError" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">{{ apiError.title }}</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>{{ apiError.message }}</p>
                                <p v-if="apiError.details" class="mt-1 text-sm text-red-600">
                                    {{ typeof apiError.details === "string" ? apiError.details : JSON.stringify(apiError.details) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <form @submit.prevent="handleSubmit" class="flex flex-col gap-4 md:mt-3">
                    <div class="text-black">
                        <div class="flex flex-col md:flex-row items-center">
                            <div class="flex gap-1 w-full">
                                <Step :step="1" stepName="Tải hình ảnh của bạn" forName="upload-image" />
                            </div>

                            <div class="flex items-center gap-2 my-2 max-w-[340px] mx-auto w-full">
                                <button type="button" @click="triggerFileInput"
                                    class="flex-1 flex items-center w-fit border-[2px] border-ai3goc-primary justify-center gap-2 p-1 bg-white hover:bg-black/10 text-ai3goc-primary rounded-lg backdrop-blur-sm transition-colors">
                                    <img src="/assets/img/ai3goc/icon/upload.svg" class="size-6 md:size-5 xl:size-6" />
                                    <span class="text-[12px]  xl:text-[16px]">Tải ảnh</span>
                                </button>
                                <button type="button" @click="initializeCamera"
                                    class="flex-1 flex items-center w-fit border-[2px] border-ai3goc-primary justify-center gap-2 p-1 bg-white hover:bg-black/10 text-ai3goc-primary rounded-lg backdrop-blur-sm transition-colors">
                                    <img src="/assets/img/ai3goc/icon/camera.svg" class="size-6 md:size-5 xl:size-6" />
                                    <span class="text-[12px]  xl:text-[16px]">Camera</span>
                                </button>
                            </div>
                        </div>
                        <p class="text-ai3goc-primary font-light text-xs md:text-base italic mb-2">Bạn có thể tạo 1 hoặc nhiều ảnh đổi khuôn mặt cùng lúc (tải lên tối đa 4 ảnh). Chú ý: Sử dụng hình ảnh rõ khuôn mặt.</p>

                        <input id="upload-image" type="file" ref="fileInput" @change="handleFileUpload" accept="image/*" class="hidden" multiple />
                        <div v-if="showCamera" class="relative w-full h-[30rem] bg-black rounded-lg overflow-hidden">
                            <video ref="videoRef" autoplay playsinline class="w-full h-full object-contain" style="transform: scaleX(-1)" />
                            <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-4">
                                <button @click="capturePhoto" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">Chụp ảnh</button>
                                <button @click="stopCamera" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">Hủy</button>
                            </div>
                        </div>
                        <div v-if="uploadedImages.length > 0" class="mt-4">
                            <h4 class="text-black font-semibold">Ảnh đã tải lên:</h4>
                            <div :class="uploadedImages.length <= 1 ? 'grid-cols-1' : 'lg:grid-cols-2'" class="grid gap-1">
                                <div v-for="(image, index) in uploadedImages" :key="index" class="relative">
                                    <img :src="image" class="w-full h-[30vh] object-contain rounded-lg border" alt="Uploaded image" />
                                    <img @click="removeImage(index)" src="/assets/svgs/aiwow/remove_icon.svg" class="absolute size-6 md:size-5 xl:size-6 top-2 right-2 cursor-pointer" />
                                    <!-- <button type="button" @click="removeImage(index)" class="absolute w-[20px] h-[20px] flex justify-center items-center top-2 right-2 bg-red-500 text-white rounded-full p-1">X</button> -->
                                </div>
                            </div>
                        </div>
                        <div v-else class="w-full flex items-center justify-center ">
                            <div  class="flex items-center justify-center md:aspect-square md:h-[40vh] rounded-lg">
                                <img :src="defaultImagePath" class="h-full w-auto" alt="Uploaded image" />
                            </div>
                        </div>
                        <div v-if="errorMessage" class="mt-4 mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">{{ errorMessage }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <HandleImage step="2" stepName="Tải hình ảnh sẽ thay thế" v-model="selectedImage2"
                                        @fileChange="handleFile2Change" />
                    <div v-if="errorMessage" class="mt-4 mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">{{ errorMessage }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <Step :step="3" stepName="" forName="submit-swapface" />

                        <button id="submit-swapface" type="submit" class="px-2 py-2 w-[45%] justify-self-end bg-ai3goc-sec text-white rounded-[10px] text-[15px] font-bold hover:bg-ai3goc-sec/50 transition-colors duration-300 hover:scale-105" :disabled="isLoading">
                            {{ isLoading ? "Đang xử lý..." : "Gửi yêu cầu" }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Result Section -->
            <div class="bg-white rounded-[10px] md:rounded-[25px] w-full lg:w-1/2 p-[12px] md:p-[25px] drop-shadow-xl">
                <p class="text-ai3goc-primary italic font-thin mb-1 text-center">Hiển thị kết quả</p>
                <div class="flex justify-center items-center mb-4">
                    <div :class="`${numberImageSelect > 1 ? 'grid lg:grid-cols-2 grid-cols-1' : 'grid grid-cols-1 text-sm'}`" class="gap-0.5 w-full">
                        <div v-for="index in numberImageSelect" :key="index">
                            <div v-if="resultList[index - 1]" class="relative rounded-xl">
                                <img @click="openDetail(resultList[index - 1])" :src="resultList[index - 1].image" alt="image generated by AI" class="w-full h-auto object-contain cursor-pointer rounded-xl" />
                            </div>
                            <div v-else class="bg-[#E6E6E6] flex items-center justify-center rounded-xl w-full h-[40vh]">
                                <img v-if="!loadingImageState[index - 1]" src="/assets/svgs/aiwow/image.svg" alt="loading" class="mx-auto w-24" />
                                <div v-else class="flex flex-col items-center">
                                    <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                                    <h3 class="text-white">Hệ thống đang xử lý, xin đợi một chút</h3>
                                </div>
                            </div>
                            <ButtonTaskBar :isActive="resultList[index - 1]" @open-detail="openDetail" :selectedImage="resultList[index - 1]?.value" type="image" :shareLinkableType="'AIGeneratedMedia'" :features="['video']"/>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center lg:justify-center mt-4 w-full">
                    <a :href="route('ai-image.historyfaceSwap')" class="px-4 w-[180px] text-center h-fit py-2 bg-ai3goc-sec text-white font-bold text-[15px] rounded-[10px] hover:bg-green-600 transition-colors duration-300"> Lịch sử </a>
                </div>
            </div>
        </div>
    </Layout>

    <!-- Loading Overlay -->
    <!-- <div v-if="isLoading" class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
        <div class="loader"></div>
    </div> -->
    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink" />
    </Modal>
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="acceptUpgrade" />
    <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleBuyCancel" :currentCredit="pageProps.auth.user.credit || 0" :additionalCredit="additionalCredit" />
</template>

<script setup>
import { ref, onMounted, watchEffect, computed, onUnmounted, nextTick } from "vue";
import Layout from "@/Layouts/Client/Layout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import HandleImage from "@/Pages/Client/AIImage/HandleImage.vue";
import axios from "axios";
import CreditBuyModal from "../../../Components/ModalBuyCredit/BuyCredit.vue";
import { toast } from "vue3-toastify";
import PopupAff from "@/Components/PopupAff.vue";
import Modal from "@/Components/Modal.vue";
import FormShareLink from "@/Components/FormShareLink.vue";
import ButtonTaskBar from "@/Components/ButtonTaskBar.vue";
import Step from "@/Components/Step/Index.vue";

const props = defineProps({
    ai_assistant: Object,
    images: {
        type: Array,
        default: () => [],
    },
    imageUrl: String,
    targetUrl: String
});

const breadcrumbsData = [
    { text: "Tạo sản phẩm MEDIA", link: "ai-image.historyfaceSwap" },
    { text: "Đổi khuôn mặt", link: "ai-image.faceSwap" },
];

const numberImageSelect = ref(1);
const loadingImageState = ref([]);


const { props: pageProps } = usePage();
const auth = computed(() => pageProps.value.auth);
const additionalCredit = ref(0);
const showAffKeyPopup = ref(false);
const credits = ref(0);
const resultImage = ref(null);
const resultValue = ref(null);
const isLoading = ref(false);
const apiError = ref(null);
const isShowDes = ref(true);

const popupMessage = ref("Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!");
const acceptUpgrade = ref(true);

const fileImage1 = ref([]);
const fileImage2 = ref(null);
const selectedImage = ref(null);

const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
const showConfirmModal = ref(false);
const imageDeleteId = ref(null);

// Image refs
const selectedImage1 = ref(props.targetUrl || '/assets/img/orion/bg/your-face-sample.svg');
const selectedImage2 = ref(props.imageUrl || "/assets/img/orion/bg/target-face-sample.svg");
const defaultImagePath = "/assets/img/orion/bg/your-face-sample.svg";
const errorMessage = ref("");
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
        formData.append("type", "image_to_image");

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

const error = ref("");
let mediaStream = null;
const videoRef = ref(null);
const showCamera = ref(false);
const uploadedImages = ref([]);
let allowedExtension = ["image/jpeg", "image/jpg", "image/png"];
const handleFileUpload = (event) => {
    let type = event.target.files[0].type;
    if (allowedExtension.indexOf(type) < 0) {
        alert("Vui lòng chọn lại ảnh có định dạng .png, .jpg, .jpeg");
        return false;
    }

    const files = event.target.files;
    if (files.length > 4) {
        errorMessage.value = "Không thể tải lên quá 4 ảnh.";
        return;
    }

    fileImage1.value = [];
    uploadedImages.value = [];

    for (let i = 0; i < files.length; i++) {
        if (uploadedImages.value.length < 4) {
            uploadedImages.value.push(URL.createObjectURL(files[i]));
            fileImage1.value.push(files[i]);
        }
    }
    numberImageSelect.value = files.length;
    errorMessage.value = ""; // Reset thông báo lỗi
};

const fileInput = ref(null);

const triggerFileInput = () => {
    if (fileInput.value) {
        fileInput.value.click();
    }
};

const removeImage = (index) => {
    uploadedImages.value.splice(index, 1);
    if(numberImageSelect.value > 1){
        numberImageSelect.value -= 1;
    }
};

const handleSubmit = async () => {
    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        isLoading.value = false;
        return; // Dừng nếu không đủ credit
    }
    // Check if images are still default
    // if (selectedImage1.value === defaultImagePath && selectedImage2.value === defaultImagePath) {
    //     errorMessage.value = 'Xin vui lòng chọn cả hai hình ảnh';
    //     image1Ref.value?.$el.scrollIntoView({ behavior: 'smooth' });
    //     return;
    // }

    // if (selectedImage1.value === defaultImagePath) {
    //     errorMessage.value = 'Xin vui lòng chọn hình ảnh của bạn';
    //     image1Ref.value?.$el.scrollIntoView({ behavior: 'smooth' });
    //     return;
    // }

    // if (selectedImage2.value === defaultImagePath) {
    //     errorMessage.value = 'Xin vui lòng chọn hình ảnh sẽ thay vào';
    //     image2Ref.value?.$el.scrollIntoView({ behavior: 'smooth' });
    //     return;
    // }
    await generateVideo();
};

const handleFile1Change = (file) => {
    if (file) {
        fileImage1.value = file;
    }
};

const handleFile2Change = (file) => {
    if (file) {
        fileImage2.value = file;
    }
};

const resultList = ref([]);

const generateVideo = async () => {
    isLoading.value = true;
    apiError.value = null;
    let index = 0;
    try {
        loadingImageState.value = [];
        resultList.value = [];
        do {
            loadingImageState.value[index] = true;
            const formData = new FormData();
            formData.append("source_img", fileImage1.value[index]);
            formData.append("target_img", fileImage2.value);
            formData.append("source_url", selectedImage1.value);
            formData.append("target_url", selectedImage2.value);

            try {
                // Gửi yêu cầu API
                const response = await axios.post(route("ai-image.generate-swap-face"), formData, {
                    headers: { "Content-Type": "multipart/form-data" },
                });

                if (response.data.success) {
                    const base64Image = response.data.data.image;
                    const resultData = {
                        image: base64Image.startsWith("data:image") ? base64Image : `data:image/jpeg;base64,${base64Image}`,
                        value: response.data?.data?.aiGeneratedMedia,
                    };
                    resultList.value[index] = resultData;
                    resultImage.value = resultData.image;
                    resultValue.value = resultData.value;
                } else {
                    showErrorMessage(response.data); // Hiển thị lỗi nhưng không dừng
                }
            } catch (error) {
                handleApiError(error); // Xử lý lỗi cụ thể cho từng lần gọi API
            } finally {
                loadingImageState.value[index] = false; // Đảm bảo trạng thái được cập nhật
            }

            index++;
        } while (index < fileImage1.value.length);
    } catch (error) {
        console.error("Error in generateVideo:", error);
        apiError.value = error.message;
    } finally {
        isLoading.value = false;
    }
};


const selectImage = (resultValue) => {
    selectedImage.value = resultValue;
};

const showErrorMessage = (errorData) => {
    apiError.value = {
        title: "Lỗi xử lý",
        message: errorData.message || "Có lỗi xảy ra khi xử lý hình ảnh",
        details: errorData.error,
    };
    resultImage.value = null;
};

const handleApiError = (error) => {
    let errorInfo = {
        title: "Lỗi hệ thống",
        message: "Có lỗi xảy ra khi xử lý yêu cầu",
        details: null,
    };

    if (error.response) {
        const errorData = error.response.data;
        if (errorData && errorData.error === "No face found in Target") {
            errorInfo = {
                title: "Không tìm thấy khuôn mặt trong ảnh",
                message: "Vui lòng thử lại với ảnh khác.",
            };
            toast.error("Không tìm thấy khuôn mặt trong ảnh. Vui lòng thử lại với ảnh khác.");
        } else {
            errorInfo = {
                title: "Lỗi xử lý",
                message: errorData.message || "Hình ảnh dính bản quyền, vui lòng chọn hình ảnh khác",
            };
            toast.error("Lỗi xử lý. Vui lòng thử lại với ảnh khác.");
        }
    } else if (error.request) {
        errorInfo = {
            title: "Lỗi kết nối",
            message: "Không thể kết nối đến máy chủ. Vui lòng kiểm tra kết nối mạng của bạn hoặc chờ trong giây lát và thử lại sau",
            details: null,
        };
    }

    apiError.value = errorInfo;
    console.error("Error details:", errorInfo.details);
    resultImage.value = null;
};

const downloadVideo = () => {
    if (!resultImage.value) {
        apiError.value = {
            title: "Lỗi tải xuống",
            message: "Chưa có hình ảnh kết quả",
            details: null,
        };
        return;
    }
    const link = document.createElement("a");
    link.href = resultImage.value;
    link.download = "processed-image.jpg";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

const handleCheckSize = () => {
    isShowDes.value = window.innerWidth >= 1024;
};

onMounted(() => {
    handleCheckSize();
    window.addEventListener("resize", handleCheckSize);
});

const initializeCamera = async () => {
    try {
        showCamera.value = true;
        await nextTick();
        await startCamera();
    } catch (err) {
        console.error("Failed to initialize camera:", err);
        error.value = "Không thể khởi tạo camera";
        showCamera.value = false;
    }
};

const startCamera = async () => {
    try {
        if (!navigator.mediaDevices?.getUserMedia) {
            throw new Error("Browser không hỗ trợ truy cập camera");
        }

        mediaStream = await navigator.mediaDevices.getUserMedia({
            video: {
                width: { ideal: 1280 },
                height: { ideal: 720 },
                facingMode: "user",
            },
            audio: false,
        });

        if (!videoRef.value) {
            throw new Error("Video element không tồn tại");
        }

        videoRef.value.srcObject = mediaStream;
        await videoRef.value.play();
        error.value = "";
    } catch (err) {
        console.error("Camera error:", err);
        error.value = `Không thể truy cập camera: ${err.message}`;
        await stopCamera();
        throw err;
    }
};

const stopCamera = async () => {
    if (mediaStream) {
        mediaStream.getTracks().forEach((track) => track.stop());
        mediaStream = null;
    }

    if (videoRef.value) {
        videoRef.value.srcObject = null;
    }

    showCamera.value = false;
    error.value = "";
};

const capturePhoto = () => {
    if (!videoRef.value) return;

    const canvas = document.createElement("canvas");
    const video = videoRef.value;
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    const ctx = canvas.getContext("2d");
    // Flip the image horizontally to match the preview
    ctx.scale(-1, 1);
    ctx.translate(-canvas.width, 0);
    ctx.drawImage(video, 0, 0);

    const imageUrl = canvas.toDataURL("image/jpeg", 0.9);

    uploadedImages.value.push(imageUrl);
    // Convert base64 to File object
    fetch(imageUrl)
        .then((res) => res.blob())
        .then((blob) => {
            const file = new File([blob], "camera-capture.jpg", { type: "image/jpeg" });
            fileImage1.value.push(file);
        });

    stopCamera();
};

onMounted(() => {
    window.addEventListener("beforeunload", stopCamera);
});

onUnmounted(() => {
    stopCamera();
    window.removeEventListener("beforeunload", stopCamera);
});

const prepareDeleteFile = (id) => {
    imageDeleteId.value = id;
    showConfirmModal.value = true;
};

const confirmDelete = async () => {
    isLoading.value = true;

    try {
        const response = await axios.post(route("ai-image.delete", { id: imageDeleteId.value }));
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

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}
</style>
