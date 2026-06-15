<template>
    <Head title="Hình bối cảnh - AI 3 GỐC - Cộng đồng AI tử tế" />

    <Layout :breadcrumbs="breadcrumbsData" :credits="credits" :isBig="true">
        <body class="flex lg:flex-row flex-col gap-4">
            <!-- List Background -->
            <div v-if="isCropping" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
                <div class="bg-white p-4 rounded-md w-full md:w-[80%] lg:w-[40%]">
                    <VueCropper
                        ref="cropper"
                        :src="imageUrl"
                        :zoomable="true"
                        class="max-w-full max-h-[60vh] mx-auto overflow-hidden"
                    />
                    <div class="flex justify-between mt-4">
                        <button @click="cancelCropping" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600">Hủy</button>
                        <button @click="cropImage" class="bg-purple-500 text-white py-2 px-4 rounded hover:bg-purple-600">Cắt ảnh</button>
                    </div>
                </div>
            </div>
            <section class="w-full flex flex-col h-fit lg:w-5/12 bg-white p-3 lg:p-4 2xl:p-6 rounded-3xl text-black drop-shadow-xl">
                <div class="flex flex-col md:mb-2">
                    <div class="flex gap-1">
                        <div class="flex justify-start items-center gap-2">
                            <div class="w-[52px] overflow-hidden rounded-full border-[1px] border-[#d4d4d4]">
                                <img class="w-full h-auto object-top" src="/assets/img/ai3goc/logo/circle.svg" alt="Robot" />
                            </div>

                            <h2 class="text-black font-bold text-xl lg:text-[25px] leading-none">
                                Tạo hình nền A.I sản phẩm
                            </h2>
                        </div>
                    </div>
                    <p class="text-ai3goc-sec text-sm md:text-base font-bold my-2">Thực hiện theo các bước sau:</p>
                </div>
                <div class="flex flex-col gap-2 items-center my-2 mb-4">
                    <div class="flex gap-2 items-center justify-between w-full">
                        <Step :step="1" stepName="Tải hình ảnh" forName="image-description" />
                        <button type="button" @click="handleUploadClick"
                            class="xs:w-1/4 xl:w-1/3 justify-self-end flex flex-nowrap items-center font-bold px-4 py-1 md:py-2 justify-center gap-2 bg-transparent text-ai3goc-primary rounded-xl backdrop-blur-sm transition-colors hover:scale-105 border-2 border-ai3goc-primary">
                            <img src="/assets/img/ai3goc/icon/upload.svg" class="size-6 md:size-5 xl:size-6" />
                            <span class="text-[12px] xl:text-[14px] text-nowrap">Tải ảnh lên</span>
                        </button>

                        <input type="file" ref="fileInput" accept="image/*" @change="handleFileChange" class="hidden" />

                    </div>
                    <div class="bg-gray-300 flex items-center justify-center aspect-square  rounded-lg w-full">
                        <img @click="openDetail(imageGenerate)" v-if="imageUrl" :src="imageUrl" alt="uploaded image" class="h-full w-full max-h-[600px] rounded-lg  cursor-pointer object-contain" />
                        <img v-else src="/assets/svgs/aiwow/image.svg" alt="placeholder" class="mx-auto w-24" />
                    </div>
                </div>

                <!-- <Step :step="2" stepName="Chọn mẫu bối cảnh phù hợp" forName="image-description" />
                <label for="typeGernerate-image" class="flex itmems-center gap-2 my-4">
                    <input id="typeGernerate-image" type="checkbox" @input="handleChangeTypeGernerate" :checked="typeGernerate === 'image'" value="image"
                        class="rounded-full w-[18px] h-[18px] flex-shrink-0 checked:bg-orion-orange text-orion-orange">
                    <span class="text-sm">Tải hình nền của bạn hoặc chọn hình bối cảnh A.I</span>
                </label>
                <div class="w-fit max-h-[600px] md:max-h-[810px] overflow-y-auto rounded-lg relative text-center mt-2" @mousedown.prevent>
                    <div class="grid grid-cols-2 gap-1 w-full">
                        <label for="image-file" v-if="!backgroundTemp"
                            class="h-[200px] md:h-[270px] flex items-center justify-center relative cursor-pointer w-full rounded-xl bg-[#E6E6E6]  text-orion-orange border-2 border-orion-orange"
                            :class="backgroundImageUrl == null ? 'border-4' : ''">
                            <div class="flex flex-col gap-2 inset-0 m-auto items-center">
                                <img src="/assets/img/orion/icon/upload-orange.svg" class="size-6 md:size-8 xl:size-12" />
                                <span class="text-sm md:text-[20px]">Tải ảnh lên</span>
                            </div>
                            <input type="file" id="image-file" @change="handleBackgroundImageFileChange" accept="image/*" class="hidden" />
                        </label>
                        <div v-else class="h-[200px] md:h-[270px] relative w-full rounded-lg border-4 border-orion-orange">
                            <img src="/assets/img/close.png" @click.stop="backgroundTemp = null" class="absolute top-2 right-2 w-6 h-6 cursor-pointer" />
                            <img :src="backgroundTemp" alt="" class="w-full h-full object-cover" />
                        </div>
                        <div @click="handleChangeTemplate(template)" v-for="template in themes" :key="template.id" class="h-[200px] md:h-[270px] relative rounded-xl cursor-pointer transition-all "
                            :class="backgroundImageUrl == template.thumbnail ? 'border-4 border-ai3goc-primary' : 'border-2 border-white'">
                            <img
                            v-lazy="template.thumbnail"
                            alt="" class="w-full h-full rounded-lg object-cover" />
                        </div>
                    </div>
                </div>
                <label for="typeGernerate-prompt" class="flex itmems-center gap-2 my-4">
                    <input id="typeGernerate-prompt" type="checkbox" @input="handleChangeTypeGernerate" :checked="typeGernerate === 'prompt'" value="prompt"
                        class="rounded-full w-[18px] h-[18px] flex-shrink-0 checked:bg-orion-orange text-orion-orange">
                    <span class="text-sm">Hoặc mô tả hình nền bạn muốn tạo</span>
                </label>
                <div class="relative ">
                    <SuggestionPrompt v-model="imageDescription" :type="'image'" :screenId="21" />
                    <textarea id="image-description" v-model="imageDescription" type="text" placeholder="Nhập nội dung cần tạo ảnh dưới đây..."
                        :disabled="loadingCreateImage || typeGernerate !== 'prompt'"
                        class="w-full p-3 h-[200px] border text-black border-ai3goc-primary rounded-[10px] shadow-sm focus:outline-none focus:ring-2 focus:ring-ai3goc-primary"></textarea>
                </div> -->
                <div class="flex items-center justify-between mt-4">
                    <Step :step="2" stepName="Bấm vào đây" forName="image-description" />
                    <!-- gọi function `generateAiBackground` nếu có bước chọn ảnh background / hiện tại đã bỏ bước này nên gọi function `generateAiBackgroundV2` -->
                    <button type="button" :disabled="isLoading" @click="generateAiBackgroundV2"
                        class="flex items-center w-1/2 justify-center gap-2 px-3 py-2 bg-ai3goc-sec text-white rounded-xl backdrop-blur-sm transition-colors hover:scale-105 border-2 border-ai3goc-sec">
                        <span class="text-[12px] xl:text-[15px]">Tạo nền bối cảnh</span>
                    </button>
                </div>
            </section>

            <section ref="resultBox" class="w-full lg:w-7/12 bg-white rounded-3xl text-black drop-shadow-xl p-3 lg:p-4 2xl:p-6">
                <p class="text-ai3goc-primary italic font-thin text-center">Hiển thị kết quả</p>
                <div id="result" class=" flex flex-col gap-3 mt-2">
                    <div class="h-full">
                        <div class="bg-[#E6E6E6] flex items-center justify-center rounded-lg overflow-hidden h-[600px] md:h-[800px]">
                            <button v-if="isLoading" class="h-32 w-32 rounded-md flex-shrink-0">
                                <div class="flex flex-col items-center">
                                    <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                                </div>
                            </button>
                            <div class="w-full h-full flex items-center justify-center " v-else>
                                <img @click="openDetail(imageGenerate)" v-if="imageGenerate?.s3_url" :src="imageGenerate?.s3_url" alt="uploaded image"
                                class="w-full h-full object-contain rounded-lg max-h-[800px] cursor-pointer bg-black" />
                                <img v-else src="/assets/svgs/aiwow/image.svg" alt="placeholder" class="mx-auto w-24" />
                            </div>
                        </div>
                    </div>
                    <!-- <TaskBar :isActive="imageGenerate" :selectedImage="imageGenerate" :shareLinkableType="'PebblelyVideo'" :features="['video']" /> -->
                    <ButtonTaskBar :isActive="imageGenerate" :selectedImage="imageGenerate" type="image" :shareLinkableType="'PebblelyVideo'" :features="['video']"/>
                    <div class="flex justify-center">
                        <a :href="route('ai-background.history')" class="bg-ai3goc-sec px-12 py-1.5 text-white rounded-[10px] w-1/2 text-center lg:w-fit">Lịch sử</a>
                    </div>
                </div>

                <div class="p-2 flex flex-col gap-2">
                    <p class="font-light text-sm md:text-base">Ảnh tạo gần đây</p>
                    <div ref="scrollContainer" class="flex gap-4 overflow-y-auto max-w-[320px] lg:max-w-[600px]" style="scroll-behavior: smooth">
                        <button v-if="isLoading" class="h-32 w-32 bg-gray-300 rounded-md flex-shrink-0">
                            <div class="flex flex-col items-center">
                                <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                            </div>
                        </button>
                        <div v-for="(item, index) in listHistory" :key="index" class="h-32 w-32 bg-gray-300 rounded-md flex-shrink-0 cursor-pointer">
                            <img @click="openDetail(item)" v-lazy="item.s3_url" alt="Generated Background" class="mx-auto w-full h-full rounded-lg object-contain bg-black" />
                        </div>
                    </div>
                </div>
            </section>
        </body>
    </Layout>
    <ShowDetailImage :selectedImage="selectedImage" :closeDetail="closeDetail" :routerDelete="'ai-background.delete'" :hiddenFeature="['all', 'share']" />

    <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleBuyCancel" :currentCredit="pageProps.auth.user.credit || 0"
        :additionalCredit="additionalCredit" />
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="acceptUpgrade" />
</template>
<script setup>
import TaskBar from '@/Components/TaskBar.vue';
import ButtonTaskBar from "@/Components/ButtonTaskBar.vue";
import Layout from "@/Layouts/Client/Layout.vue";
import { usePage, Head } from "@inertiajs/vue3";
import { computed, onMounted, onUnmounted, ref } from "vue";
import CreditBuyModal from "../../../Components/ModalBuyCredit/BuyCredit.vue";

import SuggestionPrompt from "@/Components/AIVirtual/SuggestionPrompt.vue";
import PopupAff from "@/Components/PopupAff.vue";
import "cropperjs/dist/cropper.css";
import axios from 'axios';
import ShowDetailImage from '@/Components/ShowDetailImage.vue';
import { toast } from "vue3-toastify";
import VueCropper from 'vue-cropperjs';
import Step from "@/Components/Step/Index.vue";

const props = defineProps({
    history: Array,
    imageUrl: String,
});

const breadcrumbsData = [
    { text: "Tạo sản phẩm MEDIA", link: "ai-background.history" },
    { text: "Hình bối cảnh", link: "ai-background.indexV2" },
];

const { props: pageProps } = usePage();
const additionalCredit = ref(0);
const showAffKeyPopup = ref(false);
const popupMessage = ref("Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!");
const acceptUpgrade = ref(true);
const themes = ref([]);

// Handle Action
const isLoading = ref(false);

const selectedImage = ref(null);

// Props
const listHistory = ref(props.history.data || []);
const imageGenerate = ref(null);
const credits = ref(0);

// Image
let allowedExtension = ["image/jpeg", "image/jpg", "image/png"];
const imageFile = ref(null);
const imageUrl = ref(props.imageUrl || null);
const fileInput = ref(null);

const isTransparentImage = ref(null);
const isCropping = ref(false);
const numberImageSelect = ref(0);
const cropper = ref(null);

const resultBox = ref(null);
const imageDescription = ref(null);
const typeGernerate = ref('image');
const backgroundImageFile = ref(null);
const backgroundImageUrl = ref(null);
const backgroundTemp = ref(null);

const fetchThemes = async () => {
    try {
        const response = await axios.get(route('background-images.index'));
        themes.value = response.data.map(item => ({
            label: item.category,
            thumbnail: item.sample_url,
            originalLabel: item.category
        }));
    } catch (error) {
        console.error('Error fetching themes:', error);
    }
};

onMounted(() => {
    fetchThemes();
    createFileFromUrl();
});
const handleUploadClick = () => {
    fileInput.value?.click();
};

const scrollContainer = ref(null);

const handleWheel = (event) => {
    if (scrollContainer.value) {
        event.preventDefault();
        scrollContainer.value.scrollLeft += event.deltaY;
    }
};

onMounted(() => {
    if (scrollContainer.value) {
        scrollContainer.value.addEventListener("wheel", handleWheel);
    }
});

onUnmounted(() => {
    if (scrollContainer.value) {
        scrollContainer.value.removeEventListener("wheel", handleWheel);
    }
});
const dataURLtoFile = (dataUrl, fileName) => {
    const [header, base64Data] = dataUrl.split(';base64,');
    const mime = header.split(':')[1];
    const binary = atob(base64Data);
    const array = new Uint8Array(binary.length);
    for (let i = 0; i < binary.length; i++) {
        array[i] = binary.charCodeAt(i);
    }
    return new File([array], fileName, { type: mime });
};
const cancelCropping = (key) => {
    if (imageUrl.value) {
        const img = new Image();
        img.src = imageUrl.value;
        img.onload = function () {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const targetSize = 1080;
            const aspectRatio = img.width / img.height;
            if (aspectRatio > 1) {
                canvas.width = targetSize;
                canvas.height = targetSize / aspectRatio;
            } else {
                canvas.height = targetSize;
                canvas.width = targetSize * aspectRatio;
            }
            ctx.drawImage(img, 0, 0, img.width, img.height, 0, 0, canvas.width, canvas.height);
            const resizedImageUrl = canvas.toDataURL();
            imageUrl.value = resizedImageUrl;
            const file = dataURLtoFile(resizedImageUrl, 'resized-image.png');
            imageFile.value = file;
            isCropping.value = false;
        };
    }
};
function checkImageTransparency(imageUrl, callback) {
  const img = new Image();
  img.src = imageUrl;
  img.onload = function () {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = img.width;
    canvas.height = img.height;
    ctx.drawImage(img, 0, 0);
    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const data = imageData.data;
    let hasTransparentPixel = false;
    for (let i = 0; i < data.length; i += 4) {
      if (data[i + 3] < 255) { // Kiểm tra alpha channel (pixel trong suốt)
        hasTransparentPixel = true;
        break;
      }
    }
    callback(hasTransparentPixel);
  };
  img.onerror = function () {
    callback(false);
  };
}
const cropImage = () => {
    if (cropper.value) {
        const croppedCanvas = cropper.value.getCroppedCanvas();
        let width = croppedCanvas.width;
        let height = croppedCanvas.height;
        if (width > 2048 || height > 2048) {
            const resizedCanvas = document.createElement('canvas');
            const ctx = resizedCanvas.getContext('2d');
            const maxDimension = 1080;
            if (width > height) {
                resizedCanvas.width = maxDimension;
                resizedCanvas.height = (height * maxDimension) / width;
            } else {
                resizedCanvas.height = maxDimension;
                resizedCanvas.width = (width * maxDimension) / height;
            }
            ctx.drawImage(croppedCanvas, 0, 0, width, height, 0, 0, resizedCanvas.width, resizedCanvas.height);
            croppedCanvas.width = resizedCanvas.width;
            croppedCanvas.height = resizedCanvas.height;
            croppedCanvas.getContext('2d').drawImage(resizedCanvas, 0, 0);
            width = croppedCanvas.width;
            height = croppedCanvas.height;
        }
        const croppedImage = croppedCanvas.toDataURL();
        imageUrl.value = croppedImage;
        const file = dataURLtoFile(croppedImage, 'cropped-image.png');
        console.log('Image Dimensions:', width, 'x', height);
        imageFile.value = file;
        isCropping.value = false;
    }
};
const handleChangeTypeGernerate = (e) => {
    if (e.target.checked) {
        typeGernerate.value = e.target.value;
    }
};
const handleChangeTemplate = (template) => {
    backgroundImageUrl.value = template.thumbnail
    backgroundTemp.value = null
    backgroundImageFile.value = null
}
const handleBackgroundImageFileChange = (event) => {
    const type = event.target.files[0].type;
    if (allowedExtension.indexOf(type) < 0) {
        alert("Vui lòng chọn lại ảnh có định dạng .png, .jpg, .jpeg");
        return false;
    }
    if (type) {
        backgroundImageFile.value = event.target.files[0];
        backgroundTemp.value = URL.createObjectURL(event.target.files[0]);
        backgroundImageUrl.value = null;
    }
};
const handleFileChange = async (event) => {
    const type = event.target.files[0].type;
    if (allowedExtension.indexOf(type) < 0) {
        alert("Vui lòng chọn lại ảnh có định dạng .png, .jpg, .jpeg");
        return false;
    }
    if (type) {
        isCropping.value = true
        imageGenerate.value = null
        imageFile.value = event.target.files[0];
        imageUrl.value = URL.createObjectURL(event.target.files[0]);
        checkImageTransparency(imageUrl.value, function(isTransparent) {
            isTransparentImage.value = isTransparent;
        });
    }
};

const showBuyCreditPopup = ref(false);

const handleBuyCancel = () => {
    showBuyCreditPopup.value = false;
};

const showBuyCreditModal = () => {
    showBuyCreditPopup.value = true;
};

const checkCredit = async () => {
    try {
        const formData = new FormData();
        formData.append("model", "pebblely");
        formData.append("type", "pebblely");
        const response = await axios.post(route("credit.check_credit"), formData);
        if (response.data.success) {
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

const getPromptBackground = async (imageFile) => {
    try {
        let formData = new FormData();
        formData.append("image_url", imageFile);
        const response = await axios.post(route("ai-business.generate-prompt-image"), formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });
        if (response.data.status === "success") {
            return response.data.response;
        } else {
            toast.error("Lỗi khi tạo phối cảnh: " + response.data.message);
            toast.error('Tạo ảnh không thành công. Vui lòng thử lại.');
            return null; // Return null if the response is not successful
        }
    } catch (error) {
        toast.error("Lỗi khi gọi API tạo phối cảnh!");
        toast.error('Tạo ảnh không thành công. Vui lòng thử lại.');
        return null; // Return null on error
    }
};

const createFileFromUrl = async () => {
    const urlParams = new URLSearchParams(window.location.search);
    const imageUrl = urlParams.get('imageUrl');
    if (!imageUrl) return;
    const response = await fetch(imageUrl);
    const blob = await response.blob();
    imageFile.value = new File([blob], 'image.webp', { type: blob.type });
}

const generateAiBackgroundV2 = async () => {
    if (!imageFile.value) {
        toast.error("Vui lòng chọn ảnh sản phẩm.");
        return;
    }
    try {
        isLoading.value = true;
        const hasEnoughCredit = await checkCredit();
        if (!hasEnoughCredit) {
            isLoading.value = false;
            return;
        }
        const prompt =  await getPromptBackground(imageFile.value)
        if (prompt != null) {
            let formData = new FormData();
            formData.append("imageFile", imageFile.value);
            formData.append("prompt_background", prompt);
            const res = await axios.post(route("ai-background.generate-ai-background-v2"), formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });
            if (res.status === 200) {
                isLoading.value = false;
                imageGenerate.value = res.data;
                listHistory.value.unshift(res.data);
                credits.value = res.data.credits;
            }
        }
    } catch (error) {
        if(error.response.data.errors) {
            toast.error(error.response.data.errors.imageFile[0]);
        }
        console.log(error.response.data.errors.imageFile[0]);
    } finally {
        isLoading.value = false;
    }
};

const generateAiBackground = async () => {
    try {
        isLoading.value = true;
        const hasEnoughCredit = await checkCredit();
        if (!hasEnoughCredit) {
            isLoading.value = false;
            return;
        }
        const formData = new FormData();
        if (imageFile.value) {
            formData.append("imageFile", imageFile.value);
        } else if (imageUrl.value) {
            formData.append("imageUrl", imageUrl.value);
        }
        if (typeGernerate.value === 'image') {
            if (!backgroundImageUrl.value && !backgroundImageFile.value) {
                alert("Vui lòng tải ảnh nền hoặc chọn ảnh nền có sẵn")
                isLoading.value = false;
                return;
            }
            if (backgroundImageUrl.value) {
                formData.append("backgroundImageUrl", backgroundImageUrl.value);
            }
            else {
                formData.append("backgroundImageFile", backgroundImageFile.value);
            }
        } else {
            if (!imageDescription.value) {
                alert("Vui lòng nhập mô tả hình nền")
                isLoading.value = false;
                return;
            }
            formData.append("prompt_background", imageDescription.value);
        }
        //scroll to top
        const resultElement = document.getElementById("result");
        resultElement.scrollIntoView({ behavior: "smooth", block: "start" });
        const res = await axios.post(route("ai-background.generate-ai-background-v2"), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
        console.log(res.data.s3_url);
        if (res.status === 200) {
            isLoading.value = false;
            imageGenerate.value = res.data;
            listHistory.value.unshift(res.data);
            console.log(listHistory.value);
            credits.value = res.data.credits;
        }
    } catch (error) {
        if(error.response.data.errors) {
            toast.error(error.response.data.errors.imageFile[0]);
        }
        console.log(error.response.data.errors.imageFile[0]);
    } finally {
        isLoading.value = false;
    }
};

const openDetail = (item) => {
    selectedImage.value = item;
};

const closeDetail = () => {
    selectedImage.value = null;
};
</script>
