<template>
    <Head title="Hình ảnh - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData">
        <div class="flex justify-between gap-4 w-full flex-wrap md:flex-nowrap">
            <div ref="resultBox" class="bg-white w-full rounded-[10px] md:rounded-[25px] lg:w-2/5 p-[12px] md:p-[18px] h-fit drop-shadow-xl">
                <div class="flex gap-1">
                    <div class="flex justify-start items-center gap-2">
                            <div class="w-[52px] h-[75%] overflow-hidden rounded-[10px] border-[1px] border-[#d4d4d4]">
                            <img class="w-full h-auto object-top" src="/assets/img/orion/big-robot.png" alt="Robot" />
                        </div>
                        <h2 class="text-black font-bold text-xl lg:text-xl 2xl:text-3xl leading-none">
                            Thiết kế banner/poster
                        </h2>
                    </div>
                </div>
                <p class="text-xs lg:text-sm text-orion-primary font-bold my-4">Thực hiện theo các bước sau:</p>

                <form @submit.prevent="generateImage" class="text-black">
                    <div class="relative">
                        <div class="">
                            <Step :step="1" stepName="Nhập nội dung mô tả"/>
                            <div class="relative">
                                <SuggestionPrompt v-model="imageDescription" :type="'image'" :screenId="2" />
                                <textarea id="image-description" v-model="imageDescription" type="text" placeholder="Nhập mô tả ảnh bạn muốn tạo..." :disabled="loadingCreateImage" class="w-full p-3 mt-1 h-[200px] border text-black border-[#D4D4D4] rounded-[10px] shadow-sm focus:outline-none focus:ring-2 focus:ring-orion-sec focus:border-orion-sec"></textarea>

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
                        <div class="mt-4 flex items-center justify-between">
                            <Step :step="2" stepName="Chọn thể loại"/>
                            <div class="grid grid-cols-1 xs:grid-cols-2 gap-1 xs:gap-0 xs:space-x-4 w-1/3 xs:w-1/2">
                                <label class="flex items-center xs:space-x-2 cursor-pointer">
                                    <input type="radio" id="banner" value="banner" v-model="selectedOption" class="text-[#FFA411] focus:ring-[#FFA411] focus:ring-2 h-4 w-4" />
                                    <span class="text-black text-sm">Banner</span>
                                </label>
                                <label class="flex items-center xs:space-x-2 cursor-pointer">
                                    <input type="radio" id="poster" value="poster" v-model="selectedOption" class="text-[#FFA411] focus:ring-[#FFA411] focus:ring-2 h-4 w-4" />
                                    <span class="text-black text-sm">Poster</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <Step :step="3" stepName="Chọn số lượng"/>

                            <select
                                v-model="numberImageSelect"
                                :disabled="loadingCreateImage"
                                :class="{
                                    'bg-gray-200': !numberImageSelect,
                                    'bg-white': numberImageSelect,
                                }"
                                class="w-2/5 block mt-1 text-[14px] appearance-none text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500 border-gray-200"
                                id="image-model"
                            >
                                <option v-for="(model, index) in 4" :value="model" :key="index">
                                    {{ model }}
                                </option>
                            </select>
                            <span v-if="errors.number" class="text-red-500 text-sm">{{ errors.number }}</span>
                        </div>

                        <div v-for="(image, index) in numberImageSelect" :key="index" class="mt-4 flex items-center justify-between">
                            <Step :step="4" stepName="Chọn kích thước"/>

                            <select
                                v-model="imageDimensions[index]"
                                :id="`image-dimensions-${index}`"
                                :disabled="loadingCreateImage"
                                :class="{
                                    'bg-gray-200 ': !imageDimensions[index],
                                    'bg-white ': imageDimensions[index],
                                }"
                                class="block mt-1 text-[14px] appearance-none w-2/5 text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none bg-white border-gray-200"
                            >
                                <option v-for="dimension in validDimensions" :value="dimension" :key="dimension.key">
                                    {{ dimension.key }}
                                </option>
                            </select>
                            <span v-if="errors.dimensions && errors.dimensions[index]" class="text-red-500 text-sm">
                                {{ errors.dimensions[index] }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col xs:flex-row justify-between w-full xs:items-center">
                        <Step :step="5" stepName="Bấm vào đây"/>

                        <button :disabled="loadingCreateImage" type="submit" class="py-2 bg-orion-primary w-full xs:w-2/5 text-white rounded-[10px] text-[15px] font-bold disabled:opacity-50 disabled:pointer-events-none hover:scale-105 max-sm:mt-2">Gửi yêu cầu</button>
                    </div>
                </form>
            </div>
            <div class="bg-white rounded-[10px] md:rounded-[25px] shadow-lg w-full lg:w-3/5 p-[12px] md:p-[25px]">
                <p class="text-[10px] text-sm text-left text-orion-sec w-full italic mb-4">Hiển thị kết quả</p>

                <div class="flex flex-col justify-between items-center h-fit">
                    <div class="w-full grid grid-cols-1 text-sm gap-6">
                        <div v-for="index in numberImageSelect" :key="index">
                            <div v-if="imageRef[index - 1]" class="relative border-2 rounded-md">
                                <img @click="openDetail(imageRef[index - 1])" :src="imageRef[index - 1].s3_url" alt="image generated by AI" class="w-full h-auto object-contain cursor-pointer" />

                                <ButtonTaskBar :isActive="imageRef" @open-detail="openDetail" :selectedImage="imageRef[index - 1]" type="image" :shareLinkableType="'AiGeneratedBannerPoster'" />
                            </div>
                            <!-- <div v-if="imageRef[index - 1]" class="relative">
                                            <img
                                                :src="imageRef[index - 1].s3_url"
                                                alt="image generated by AI"
                                                class="w-full h-auto object-contain cursor-pointer"
                                            />
                                        </div> -->
                            <div v-else class="bg-[#E6E6E6] w-full h-[300px] md:h-[400px] xl:h-[415px] flex items-center justify-center rounded-xl">
                                <img v-if="!loadingImageState[index - 1]" src="/assets/img/aiwow/image_placeholder.png" alt="loading" class="mx-auto w-24" />
                                <div v-else class="flex flex-col items-center">
                                    <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                                    <h3 class="text-white">Hệ thống đang xử lý, xin đợi một chút</h3>
                                </div>
                            </div>
                        </div>
                        <div class="w-full justify-end items-center flex flex-col md:flex-row md:w-full xl:w-full lg:pb-[4px]">
                            <div class="flex" ref="resultBox">
                                <a :href="route('ai-image-advertisement.history')" class="px-4 w-[180px] text-center py-2 bg-orion-primary text-white font-bold text-[15px] rounded-[10px] h-fit"> Lịch sử </a>
                            </div>
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

    <ShowDetailImage :selectedImage="selectedImage" :closeDetail="closeDetail" :hiddenFeature="['all']" />
    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink" />
    </Modal>
    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink" />
    </Modal>
    <Popup 
        v-if="showConfirmModal"
        title="Cảnh báo !"
        message="Bạn có chắc chắn muốn xóa ảnh này không?"
        cancelButtonText="Huỷ"
        acceptButtonText="Xoá"
        @cancel="cancelDelete"
        @accept="confirmDelete"
    />
    <div v-if="isLoading" class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
        <div class="loader"></div>
    </div>
</template>
<script setup>
import SuggestionPrompt from "@/Components/AIVirtual/SuggestionPrompt.vue";
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
import ButtonTaskBar from "@/Components/ButtonTaskBar.vue";
import Popup from '@/Components/Popup/Index.vue'
import Step from "@/Components/Step/Index.vue";

const props = defineProps({
    ai_assistant: Object,
});

const breadcrumbsData = [{ text: "Tạo Banner - Poster", link: "ai-image-advertisement.history" }];

const { props: pageProps } = usePage();
const auth = computed(() => pageProps.value.auth);
const additionalCredit = ref(0);

const disabledRecord = ref(false);
const credits = ref(0);
const imageRef = ref([]);
const currentImgSelect = ref(null);
const selectedOption = ref("banner");
watch(imageRef, (newValue) => {
    // Set currentImgSelect to the first element of imageRef if it exists
    currentImgSelect.value = newValue.length > 0 ? newValue[0] : null;
});

const popupMessage = ref("Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!");
const acceptUpgrade = ref(true);

const styles = ["3D", "Art Deco", "Bút chì", "Cảnh quan", "Cổ điển", "Cubism", "Digital Art", "Hoạt hình", "Impressionism", "Lãng mạn", "Low poly", "Màu nước", "Neon", "Thiên nhiên", "Nhật Bản (Anime)", "Trung Quốc (Ink painting)", "Pop art", "Retro", "Siêu thực", "Sơn dầu", "Tối giản", "Trừu tượng", "Vintage", "Surrealism", "Tương lai", "Thực tế"];

const artistes = ["Leonardo da Vinci", "Vincent van Gogh", "Pablo Picasso", "Claude Monet", "Rembrandt van Rijn", "Salvador Dalí", "Michelangelo Buonarroti", "Johannes Vermeer", "Henri Matisse", "Frida Kahlo"];

const validDimensions = computed(() => {
    return [
        { key: "1280x320", value: [1280, 320] },
        { key: "1440x360", value: [1440, 360] },
        { key: "1440x300", value: [1440, 300] },
        { key: "323x468", value: [323, 468] },
    ];
});

const models = ["flux-1.1-pro", "flux-1.1-pro"];
const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
// setDefault Value For Model = Flux Schnell
const imageModel = ref("flux-1.1-pro");
const videoDimensions = ref("16:9");
const imageDimensions = ref([validDimensions.value[2]]);
const imageDescription = ref("");
const imageStyle = ref("Thực tế");
const imageArtist = ref("");
const imageHeight = ref(1024);
const imageWidth = ref(1024);
const isLoading = ref(false);
const isShowDes = ref(true);
const resultBox = ref(null);
const imageResponse = ref([]);
const errors = ref({});
const numberImageSelect = ref(1);
const loadingCreateImage = ref(false);
const loadingImageState = ref([]);
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

watch(imageModel, (newModel) => {
    videoDimensions.value = newModel === "Flux Schnell" ? "16:9" : "1024x1024";
    videoDimensions.value = newModel === "Aesthetic" ? "16:9" : "1024x1024";
    // Change all the size based on new model
    imageDimensions.value = Array(numberImageSelect.value).fill(validDimensions.value[0]);
});

watch(numberImageSelect, (newVal) => {
    imageDimensions.value = Array(newVal).fill(validDimensions.value[0]);
});

const validateForm = () => {
    errors.value = {}; // Reset lỗi
    if (!imageDescription.value) {
        errors.value.description = "Mô tả hình ảnh là bắt buộc.";
    } else if (imageDescription.value.length > 2000) {
        errors.value.description = "Mô tả hình ảnh không được vượt quá 2000 ký tự.";
    }
    if (!imageModel.value) {
        errors.value.model = "Mô hình là bắt buộc.";
    }
    // if (imageModel.value != "Flux Schnell" && imageModel.value != "Aesthetic") {
    //     if (!imageStyle.value) {
    //         errors.value.style = "Phong cách là bắt buộc.";
    //     }
    //     if (!imageArtist.value) {
    //         errors.value.artist = "Nghệ sĩ là bắt buộc.";
    //     }
    //     if (!imageWidth.value) {
    //         errors.value.width = "Chiều rộng là bắt buộc.";
    //     }
    //     if (!imageHeight.value) {
    //         errors.value.height = "Chiều cao là bắt buộc.";
    //     }
    // }
    return Object.keys(errors.value).length === 0;
};

const updateImageDimensions = (event) => {
    console.log("🚀 ~ updateImageDimensions ~ event:", event);
    const selectedKey = event.target.value;
    const selectedDimension = validDimensions.value.find((dimension) => dimension.key === selectedKey);

    if (selectedDimension) {
        imageWidth.value = selectedDimension.value[0];
        imageHeight.value = selectedDimension.value[1];
    }
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
        formData.append("model", imageModel.value);
        formData.append("type", "image");
        // Gọi API để kiểm tra credit của user
        const response = await axios.post(route("credit.check_credit"), formData);
        if (response.data.success) {
            // Đủ credit để tiếp tục
            return true;
        } else if (response.data.is_vip_expired) {
            showAffKeyPopup.value = true;
            if (response.data.affCode == "aff_trial") {
                popupMessage.value = "Tài khoản của bạn đã hết thời gian trải nghiệm, vui lòng đăng ký tài khoản chính thức ở đây";
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
    loadingCreateImage.value = true;
    imageRef.value = [];
    loadingImageState.value = [];
    let currentDescription = imageDescription.value;
    console.log("numberImageSelect", numberImageSelect.value);
    try {
        for (let index = 0; index < numberImageSelect.value; index++) {
            const hasEnoughCredit = await checkCredit();
            if (!hasEnoughCredit) {
                isLoading.value = false;
                return; // Dừng nếu không đủ credit
            }
            loadingImageState.value[index] = true;
            try {
                const response = await callGenerateImage(currentDescription, index, models);

                if (response.status === 200 && response.data.s3_url) {
                    isLoading.value = false;
                    imageResponse.value = [...imageResponse.value, response.data];
                    imageRef.value = [...imageRef.value, response.data.generate_file];
                    credits.value = response.data.credits;
                }
            } catch (error) {
                if (error.response && error.response.status === 403) {
                    console.log("error.response ", error.response);
                    showCreditModal();
                } else if (error.response && error.response.status === 500) {
                    toast.error("Đã có lỗi xảy ra khi tạo ảnh. Xin vui lòng tạo lại!");
                    loadingImageState.value[index] = false;
                    console.error("Có lỗi xảy ra: 500 Internal Server Error.");
                    continue;
                } else {
                    console.error("Có lỗi xảy ra:", error);
                    toast.error(error.response?.data?.message || "Đã có lỗi xảy ra khi tạo ảnh. Xin vui lòng tạo lại!");
                }
            }

            loadingImageState.value[index] = false;

            await new Promise((resolve) => setTimeout(resolve, 3000));
        }
        loadingCreateImage.value = false;

        if (!isShowDes.value) {
            await nextTick(() => {
                resultBox.value.scrollIntoView({ behavior: "smooth" });
            });
        }
    } catch (error) {
        console.error("Có lỗi xảy ra:", error);
        toast.error(error.response?.data?.message || "Unknown error occurred");
    } finally {
        isLoading.value = false;
    }
};

const callGenerateImage = async (finalDescription, index, models) => {
    if (models.length === 0) {
        throw new Error("Tất cả các model đều không thành công.");
    }

    try {
        const currentModel = models[0];
        const response = await axios.post(route("ai-image-advertisement.generate"), {
            description: finalDescription,
            // style: imageStyle.value,
            // artist: imageArtist.value || 'Leonardo da Vinci',
            height: imageDimensions.value[index].value[1],
            width: imageDimensions.value[index].value[0],
            model: currentModel,
            typeOutput: selectedOption.value,
            aspect_ratio: "custom",
        });
        console.log("🚀 ~ response ~ response:", response);

        return response;
    } catch (error) {
        if (error.response && error.response.status === 403) {
            showCreditModal();
            throw error;
        } else {
            console.error(`Model ${models[0]} thất bại, thử model tiếp theo...`);
            // Call back
            return callGenerateImage(finalDescription, index, models.slice(1));
        }
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

const prepareDeleteFile = (id) => {
    imageDeleteId.value = id;
    showConfirmModal.value = true;
};

const confirmDelete = async () => {
    isLoading.value = true;
    showConfirmModal.value = false;
    try {
        const response = await axios.post(route("ai-image.delete", { id: imageDeleteId.value }));
        if (response.status === 200) {
            const index = imageRef.value.findIndex((image) => image?.id === imageDeleteId.value);
            if (index !== -1) {
                imageRef.value[index] = null;
            }
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi khi xóa:", error.message);
    } finally {
        isLoading.value = false;
    }
};

const cancelDelete = () => {
    showConfirmModal.value = false;
};

const downloadImage = (url) => {
    if (!url) {
        alert("Chưa có hình ảnh");
        return;
    }
    var url = route("ai-video.downloadFile", { url: url, name: "image.png" });
    window.open(url, "_blank");
};

const createPost = (url) => {
    if (url) {
        let image = {
            s3_url: url,
            type: "image",
        };
        localStorage.setItem("aiImage", JSON.stringify(image));
        window.location.href = route("calendar");
    } else {
        alert("Vui lòng tạo một hình ảnh trước khi tạo bài đăng.");
    }
};

const handleCheckSieze = () => {
    if (window.innerWidth >= 1024) {
        isShowDes.value = true;
    } else {
        isShowDes.value = false;
    }
};

const openShareLink = () => {
    isShowShareLinkModal.value = true;
};

const closeShareLink = () => {
    isShowShareLinkModal.value = false;
};

const shareAIGeneratedMedia = async (id) => {
    try {
        const response = await axios.post(route("share-link.store"), {
            share_linkable_id: id,
            share_linkable_type: "AiGeneratedBannerPoster",
        });

        shareLink.value = route("share-link.show", { token: response.data.data.link_token });
        openShareLink();
    } catch (error) {
        toast.error("Chia sẻ ảnh thất bại");
    }
};

onMounted(() => {
    handleCheckSieze();
    window.addEventListener("resize", handleCheckSieze);
});

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
</style>
