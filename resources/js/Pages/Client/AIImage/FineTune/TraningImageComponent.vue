<template>
    <div class="flex justify-center gap-4 w-full flex-wrap md:flex-nowrap border-[#d4d4d4] rounded-[25px] bg-white h-[90vh]">
        <div ref="resultBox" class="bg-white w-full rounded-[10px] md:rounded-[25px] lg:w-full p-[12px] py-4 md:px-24 md:py-10 ">
            <div class="flex flex-col">
                <div class="flex gap-1">
                    <div class="flex justify-start items-center gap-2">
                        <div class="w-[52px] overflow-hidden rounded-full border-[1px] border-[#d4d4d4]">
                            <img class="w-full h-auto object-top" src="/assets/img/ai3goc/logo/circle.svg" alt="Robot" />
                        </div>
                        <h2 class="mt-2 md:mt-0 text-black font-bold text-xl md:text-3xl leading-none">Đào tạo mẫu ảnh của tôi</h2>
                    </div>
                </div>
            </div>
            <form @submit.prevent="handleSubmit" class="">
                <p class="text-ai3goc-sec text-sm md:text-base font-bold my-2 md:my-3">Thực hiện theo các bước sau:</p>
                <div class="relative mt-2">
                    <div class="text-black text-[14px] flex md:items-center flex-col md:flex-row justify-between gap-2">
                        <div class="w-full">
                            <div class="flex flex-col lg:flex-row items-center justify-start lg:justify-between gap-1 w-full">
                                <div class="flex items-center self-start">
                                    <Step :step="1" stepName="Tải hình ảnh của bạn (xem ảnh mẫu và tải ảnh theo yêu cầu)" forName="image-description" />
                                </div>
                                <!-- <div @click="toggleSampleModal" class="text-ai3goc-primary italic underline cursor-pointer">Bấm vào đây để xem ảnh mẫu</div> -->
                            </div>
                            <span class="italic text-sm font-semibold text-black whitespace-pre-line">Lưu ý: <br />- Hình ảnh để đào tạo AI phải là ảnh gốc không qua chỉnh sửa. <br />- Nếu chỉnh sửa thì chỉ xóa những chi tiết quá xấu trên khuôn mặt như mụn trứng cá, không được xóa những đặc trưng khuôn mặt như nốt ruồi... <br />- Phải chọn đủ 10 ảnh đẹp nhất đủ góc cạnh gương mặt, tư thế, biểu cảm, trang phục để đạt được kết quả ảnh đẹp nhất.</span>
                        </div>
                    </div>
                </div>
                <div class="mt-2 font-semibold text-sm text-black">
                Ảnh đã tải lên
                <span>(<span class="text-orion-orange">{{ validImagesCount }}</span>/10)</span>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-5 md:gap-y-4 gap-2 mt-2">
                    <div v-for="(slot, index) in slots" :key="index" class="relative flex items-center justify-center rounded-xl overflow-hidden w-full">
                        <div v-if="uploadedImages[index]" class="relative">
                            <img :src="uploadedImages[index].preview" alt="Uploaded Image" class="aspect-square w-[150px] md:w-[180px] h-[150px] md:h-[180px] object-contain rounded-lg bg-[#E6E6E6]" />
                            <button type="button" @click="removeImage(index)" class="absolute top-1 right-1 bg-white text-white rounded-full w-[24px] h-[24px] flex justify-center items-center hover:bg-red-700">
                                <img src="/assets/img/orion/icon/close_yellow.svg" alt="Close" class="w-6 h-auto" />
                            </button>
                        </div>

                        <div
                            v-else
                            class="relative aspect-square w-[150px] md:w-[180px] h-[150px] md:h-[180px] rounded-xl shadow-lg bg-contain bg-no-repeat bg-[#E6E6E6] bg-center flex flex-col items-center justify-center"
                            :style="images[index] ? `background-image: url(${images[index].url})` : ''"
                            >
                            <div class="absolute inset-0 rounded-lg"></div>

                            <button
                                type="button"
                                @click="triggerInput(index)"
                                class="z-10 text-ai3goc-primary text-sm font-bold absolute top-2 right-2"
                            >
                                <img src="/assets/img/ai3goc/icon/home_upload.svg" class="w-6" alt="" />
                            </button>

                            <div class="z-10 mt-auto mb-2 bg-white bg-opacity-70 mx-2 rounded-lg w-11/12 min-h-[60px] text-center">
                                <p class="text-xs md:text-sm p-1 text-black">{{ slot.label }}</p>
                            </div>

                            <input
                                :ref="(el) => (fileInputs[index] = el)"
                                type="file"
                                @change="handleIndividualUpload($event, index)"
                                accept=".jpg,.png,.jpeg"
                                class="hidden"
                            />
                        </div>
                    </div>
                </div>
                <p v-if="uploadedImages.length === 10" class="text-red-500 text-sm">Bạn đã đạt giới hạn 10 ảnh!</p>
                <div class="my-4 flex flex-col md:flex-row md:items-center gap-2 justify-between">
                    <Step :step="2" stepName="Nhập tên mô hình AI của bạn " forName="modelName" />
                    <input id="modelName" v-model="modelName" type="text" minlength="3" maxlength="50" class="block mt-2 text-[14px] appearance-none md:w-5/12 text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none bg-white border-[#D4D4D4]" placeholder="Đặt tên cho mô hình AI của bạn" />
                </div>
                <div class="flex flex-col md:flex-row text-black md:items-center justify-between gap-2 mt-4 w-full" style="padding-bottom: 20px;">
                    <Step :step="3" stepName="Huấn luyện mô hình AI của bạn" forName="modelName" />
                    <div class="flex justify-start md:w-5/12">
                        <button id="create-image" :disabled="loadingCreateImage" type="submit" class="px-4 py-2 bg-ai3goc-sec text-white rounded-[10px] w-full text-[15px] font-bold disabled:opacity-50 disabled:pointer-events-none hover:scale-105">Gửi yêu cầu</button>
                    </div>
                </div>
            </form>
            <!-- <div v-if="isModalOpen" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex justify-center items-center" @click.self="toggleSampleModal">
                <div class="bg-white w-[90%] md:w-[70%] lg:w-[70%] rounded-lg p-4 relative">
                    <button type="button" @click="toggleSampleModal" class="absolute top-2 right-2 text-gray-600 hover:text-gray-800">
                        <img src="/assets/img/orion/icon/close.svg" class="w-8" alt="" />
                    </button>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mt-12 max-h-[60vh] overflow-y-auto">
                        <div v-for="(image, index) in images" :key="index" class="relative w-32 h-32 md:w-40 md:h-40 flex justify-center bg-gray-300 rounded-lg">
                            <img :src="image.url" :alt="'Hình ' + (index + 1)" class="w-auto h-full object-contain" />
                            <div class="absolute bottom-2 left-2 bg-black bg-opacity-50 text-white text-sm px-2 py-1 rounded">Hình {{ index + 1 }}</div>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
    <div v-if="isTranscribing" class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-50">
        <div class="loader"></div>
    </div>
    <CreditModal :showCreditPopup="showCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleCancel" />
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="acceptUpgrade" />
    <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleBuyCancel" :currentCredit="pageProps.auth.user.credit || 0" :additionalCredit="additionalCredit" />

    <ShowDetailImage :selectedImage="selectedImage" :closeDetail="closeDetail" />
    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink" />
    </Modal>
    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink" />
    </Modal>
    <Popup v-if="showConfirmModal" title="Cảnh báo !" message="Bạn có chắc chắn muốn xóa nội dung này không?" cancelButtonText="Huỷ" acceptButtonText="Xoá" @cancel="cancelDelete" @accept="confirmDelete" />
    <div v-if="isLoading" class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
        <div class="loader"></div>
    </div>
    <PopupN v-if="isShowTrainingModel" title="Xin chào!" :message="'<p>Mẫu ảnh A.I của bạn đang được đào tạo, quá trình đào tạo sẽ diễn ra trong khoảng 30-45 phút.</p><p>Xin vui lòng chờ!</p>'" cancelButtonText="Bỏ qua" @cancel="isShowTrainingModel = false" />

    <Popup v-if="showPopup" :message="message_alert" :type="'error'" @close="showPopup = false" />
</template>
<script setup>
import FormShareLink from "@/Components/FormShareLink.vue";
import Modal from "@/Components/Modal.vue";
import Layout from "@/Layouts/Client/Layout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { computed, nextTick, onMounted, ref, watch } from "vue";
import { toast } from "vue3-toastify";
import CreditModal from "../../../../Components/ModalBuyCredit/Index.vue";
import CreditBuyModal from "../../../../Components/ModalBuyCredit/BuyCredit.vue";
import axios from "axios";
import ShowDetailImage from "@/Components/ShowDetailImage.vue";
import PopupAff from "@/Components/PopupAff.vue";
import Step from "@/Components/Step/Index.vue";
import PopupN from "@/Components/Popup/Index.vue";

import JSZip from "jszip";
import Popup from "@/Components/Popup.vue";

const props = defineProps({
    ai_assistant: Object,
});
const { props: pageProps } = usePage();
const auth = computed(() => pageProps.value.auth);
const additionalCredit = ref(0);

const credits = ref(0);

const breadcrumbsData = [
    { text: "Tự sướng", link: "ai-image.my-ai-image" },
    { text: "Đào tạo mẫu ảnh", link: "ai-image.training-my-ai-image" },
];

const popupMessage = ref("Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!");
const acceptUpgrade = ref(true);

const showAffKeyPopup = ref(false);
let showBuyCreditPopup = ref(false);
const isShowTrainingModel = ref(false);

// const isModalOpen = ref(false);
const images = ref([{ url: "/assets/img/orion/training_image/1.jpg" }, { url: "/assets/img/orion/training_image/5.jpg" }, { url: "/assets/img/orion/training_image/2.jpg" }, { url: "/assets/img/orion/training_image/6.jpg" }, { url: "/assets/img/orion/training_image/4.jpg" }, { url: "/assets/img/orion/training_image/8.jpg" }, { url: "/assets/img/orion/training_image/10.jpg" }, { url: "/assets/img/orion/training_image/7.jpg" }, { url: "/assets/img/orion/training_image/9.jpg" }, { url: "/assets/img/orion/training_image/3.jpg" }]);

// const toggleSampleModal = () => {
//     isModalOpen.value = !isModalOpen.value;
// };

const handleBuyCancel = () => {
    // showBuyCreditPopup.value = false;
    window.location.href = route("home.index");
};
const showBuyCreditModal = () => {
    showBuyCreditPopup.value = true;
};
const checkCredit = async () => {
    try {
        const formData = new FormData();
        formData.append("model", "my_ai_image");
        formData.append("type", "my_ai_image");
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

const errors = ref({});

const uploadedImages = ref([]);

const handleAvatarChange = (event) => {
    let files = Array.from(event.target.files);

    const validExtensions = ["image/png", "image/jpeg", "image/jpg"];
    files = files.filter((file) => validExtensions.includes(file.type));

    if (files.length === 0) {
        toast.error("Chỉ chấp nhận các định dạng PNG, JPG, hoặc JPEG!");
        return;
    }

    const totalImages = uploadedImages.value.length + files.length;

    if (totalImages > 10) {
        toast.error("Bạn chỉ được tải lên tối đa 10 ảnh!");
        const remainingSlots = 10 - uploadedImages.value.length;
        files = files.slice(0, remainingSlots);
    }

    files.forEach((file) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            uploadedImages.value.push({
                file,
                preview: e.target.result,
            });
        };
        reader.readAsDataURL(file);
    });

    event.target.value = null;
};

const slots = ref([
    { label: "1: Ảnh chụp cận mặt, góc chính diện", condition: (file) => true },
    { label: "2: Ảnh cận mặt, góc chính diện (kiểu tóc khác)", condition: (file) => true },
    { label: "3: Ảnh chụp cận mặt, góc 1/2", condition: (file) => true },
    { label: "4: Ảnh chụp cận mặt, góc 3/4", condition: (file) => true },
    { label: "5: Ảnh chụp nửa thân trên, góc chính diện", condition: (file) => true },
    { label: "6. Ảnh nửa thân trên, góc chính diện (kiểu tóc và trang phục khác)", condition: (file) => true },
    { label: "7: Ảnh chụp nửa thân trên, góc 3/4", condition: (file) => true },
    { label: "8. Ảnh nửa thân trên, góc 3/4 (kiểu tóc và trang phục khác)", condition: (file) => true },
    { label: "9: Ảnh chụp toàn thân, góc chính diện", condition: (file) => true },
    { label: "10: Ảnh chụp toàn thân, góc nghiêng 3/4", condition: (file) => true },
]);

const fileInputs = ref([]);

const validImagesCount = computed(() => {
    return uploadedImages.value.filter(image => image !== null).length;
});

const handleIndividualUpload = (event, index) => {
    console.log(index);
    const file = event.target.files[0];

    if (!file) return;

    const validExtensions = ["image/png", "image/jpeg", "image/jpg"];
    if (!validExtensions.includes(file.type)) {
        toast.error("Chỉ chấp nhận các định dạng PNG, JPG, hoặc JPEG!");
        return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
        uploadedImages.value[index] = {
            file,
            preview: e.target.result,
        };
    };
    reader.readAsDataURL(file);

    event.target.value = null;
};

const triggerInput = (index) => {
    const input = fileInputs.value[index];
    if (input) {
        input.click();
    } else {
        console.error(`No input found for index ${index}`);
    }
};

const removeImage = (index) => {
    uploadedImages.value[index] = null;
};

const isLoading = ref(false);
const modelName = ref("");
const loadingCreateImage = ref(false);
const showPopup = ref(false);
const message_alert = ref("Kích thương file ảnh quá lớn, vui lòng chọn ảnh có kích thước nhỏ hơn 10MB.");

const handleSubmit = async () => {
    if (loadingCreateImage.value) return;
    loadingCreateImage.value = true;

    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        return;
    }

    if (uploadedImages.value.length === 0) {
        toast.error("Chưa có hình ảnh để nén.");
        loadingCreateImage.value = false;
        return;
    }

    if (uploadedImages.value.length < 10) {
        toast.error("Vui lòng cung cấp 10 hình ảnh.");
        loadingCreateImage.value = false;
        return;
    }

    if (modelName.value.trim() === "") {
        toast.error("Hãy nhập tên cho mô hình AI của bạn");
        loadingCreateImage.value = false;
        return;
    }

    const regex = /^[a-zA-ÀÁÂÃÈÉÊẾÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêếìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳýỵỷỹ\s\d]+$/u;
    if (!regex.test(modelName.value.trim())) {
        toast.error("Tên mô hình AI không được chứa ký tự đặc biệt");
        loadingCreateImage.value = false;
        return;
    }

    if (/\s{2,}/.test(modelName.value)) {
        toast.error("Tên mô hình AI không được chứa khoảng trắng thừa!");
        loadingCreateImage.value = false;
        return;
    }

    const trimmedModelName = modelName.value.trim();
    if (trimmedModelName.length < 3) {
        toast.error("Tên mô hình AI phải có ít nhất 3 ký tự!");
        loadingCreateImage.value = false;
        return;
    }

    if (trimmedModelName.length > 50) {
        toast.error("Tên mô hình AI không được vượt quá 50 ký tự!");
        loadingCreateImage.value = false;
        return;
    }

    if (uploadedImages.value.length != 10) {
        toast.error("Tải lên 10 ảnh để tiến hành đào tạo hình ảnh A.I của bạn.");
        loadingCreateImage.value = false;
        return;
    }

    const response = await axios.get(route("ai-image.has-fine-tune-model"));
    const hasImage = response.data.exists;

    if (hasImage) {
        isShowTrainingModel.value = true;
        return;
    }

    const zip = new JSZip();

    uploadedImages.value.forEach((image, index) => {
        const file = image.file;
        zip.file(`original_image_${index + 1}.${file.name.split(".").pop()}`, file);
    });

    const processedImages = await Promise.all(
        uploadedImages.value.map(async (image, index) => {
            const processedImageFiles = await handleImageProcessing(image);
            return processedImageFiles.map((processedImage, i) => ({
                file: processedImage,
                index,
                subIndex: i,
            }));
        })
    );

    processedImages.flat().forEach(({ file, index, subIndex }) => {
        zip.file(`processed_image_${index + 1}_${subIndex + 1}.${file.name.split(".").pop()}`, file);
    });

    const zipFile = await zip.generateAsync({ type: "blob" });

    const formData = new FormData();
    formData.append("zip", zipFile, "images.zip");
    formData.append("modelName", modelName.value);

    try {
        isLoading.value = true;
        const response = await axios.post(route("ai-image.fine-tune"), formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });
        console.log(response);
        if (response.status == 200) {
            toast.success(response?.data?.message);
            uploadedImages.value = [];
            modelName.value = "";
        } else {
            toast.error("Đã xảy ra lỗi khi gửi ảnh.");
        }
    } catch (error) {
        if (error.status == 403) {
            isShowTrainingModel.value = true;
        } else if (error.status == 413) {
            showPopup.value = true;
        } else {
            toast.error("Lỗi khi gửi ảnh. Vui lòng thử lại.");
        }
    } finally {
        isLoading.value = false;
    }
};

// Hàm tiền xử lý ảnh
const handleImageProcessing = (image) => {
    return new Promise((resolve) => {
        const img = new Image();
        img.src = URL.createObjectURL(image.file);

        img.onload = () => {
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");

            const processedImages = [];

            // Lật ảnh ngang
            const createFlippedImage = () => {
                canvas.width = img.width;
                canvas.height = img.height;
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.scale(-1, 1);
                ctx.drawImage(img, -img.width, 0);
                canvas.toBlob((blob) => {
                    processedImages.push(new File([blob], `${image.file.name.split(".")[0]}_flipped.${image.file.name.split(".").pop()}`));
                });
            };

            createFlippedImage();

            // Giải quyết vấn đề đồng bộ blob
            setTimeout(() => {
                resolve(processedImages);
            }, 1000);
        };
    });
};

onMounted(async () => {
    checkCredit();
    const response = await axios.get(route("ai-image.has-fine-tune-model"));
    const hasImage = response.data.exists;
    if (hasImage) {
        isShowTrainingModel.value = true;
        return;
    }
});
</script>

<style scoped>
.loader {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #24aa69;
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
</style>
