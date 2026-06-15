<template>
    <Head title="Làm đẹp ảnh - AI 3 GỐC - Cộng đồng AI tử tế" />
        <Layout :breadcrumbs="breadcrumbsData" :credits="credits" :isBig="true">
            <div class="flex justify-between gap-4 w-full flex-wrap md:flex-nowrap">
                <div ref="resultBox"
                    
                    class="bg-white w-full rounded-[10px] md:rounded-[25px] lg:w-1/2 p-[12px] md:p-[25px] drop-shadow-xl flex flex-col">
                    <div class="flex flex-col mb-2">
                        <div class="flex gap-1">
                            <div class="flex justify-start items-center gap-2">
                                <div class="w-[52px] overflow-hidden rounded-full border-[1px] border-[#d4d4d4]">
                                    <img class="w-full h-auto object-top" src="/assets/img/ai3goc/logo/circle.svg" alt="Robot" />
                                </div>

                                <h2 class="text-black font-bold text-xl lg:text-[25px] leading-none">
                                    Làm đẹp ảnh
                                </h2>
                            </div>
                        </div>
                        <p class="text-ai3goc-sec font-bold text-sm md:text-base my-1 md:my-4">Thực hiện theo các bước sau:</p>
                    </div> 
                    <form @submit.prevent="generateImage" class="md:mt-6">
                        <div class="flex flex-col gap-2 items-center my-2 mb-4">
                    <div class="flex gap-2 items-center justify-between w-full">
                        <Step :step="1" stepName="Tải hình ảnh" forName="image-description" />
                        <button type="button" @click="$refs.fileInputImage.click()"
                            class="w-1/2 md:w-1/3 justify-self-end flex flex-nowrap items-center font-bold px-1 py-1 md:py-1.5 justify-center gap-2 bg-transparent text-ai3goc-primary rounded-xl backdrop-blur-sm transition-colors hover:scale-105 border-2 border-ai3goc-primary">
                            <img src="/assets/img/ai3goc/icon/upload.svg" class="size-6 md:size-5 xl:size-6" />
                            <span class="text-[12px] xl:text-[14px] text-nowrap">Tải ảnh </span>
                        </button>

                        <input type="file" ref="fileInputImage" accept="image/*" @change="handleImageFileChange" class="hidden" />

                    </div>
                    <div class="bg-gray-300 flex items-center justify-center aspect-square  rounded-lg w-full">
                        <img v-if="imageUrl" :src="imageUrl" alt="uploaded image" class="h-full w-full max-h-[600px] rounded-lg  cursor-pointer object-contain" />
                        <img v-else src="/assets/svgs/aiwow/image.svg" alt="placeholder" class="mx-auto w-24" />
                    </div>
                </div>
                <div class="flex items-center justify-between mt-4">
                    <Step :step="2" stepName="Bấm vào đây" forName="image-description" />
                    <button type="submit" :disabled="loadingCreateImage" 
                        class="font-bold flex items-center w-1/2 justify-center gap-2 px-3 py-2 bg-ai3goc-sec text-white rounded-xl backdrop-blur-sm transition-colors hover:scale-105 border-2 border-ai3goc-sec">
                        <span class="text-[12px] xl:text-base">Làm đẹp ảnh</span>
                    </button>
                </div>
                                </form>
                            </div>
                            <section  class="bg-white rounded-[10px] md:rounded-[25px] w-full lg:w-1/2 p-[12px] md:p-[25px] drop-shadow-xl">
                <p class="text-orion-sec italic font-thin mb-1 text-center text-sm md:text-base">Hiển thị kết quả</p>
                <div class="flex flex-col justify-between items-center h-fit">
                     <div  v-if="imageRef">
                         <ImageComparison
                                                v-if="isMobile"
                                                        :beforeImage="imageUrl || defaultImage"
                                                        :afterImage="imageRef?.s3_url_mobile || defaultImage"
                                                    />
                                            <ImageComparison
                                                v-else
                                                :beforeImage="imageUrl || defaultImage"
                                                :afterImage="imageRef?.s3_url || defaultImage"
                                            />
        
                     </div>
                     <div class="flex justify-center items-center mb-4 flex-1 w-full">
                                        <div v-if="!imageResponse"
                                            class="bg-gray-300 w-full min-h-[200px] md:min-h-[400px] rounded-2xl showLoaderImage flex items-center justify-center">
                                            <div v-if="isLoadingImage" class="flex items-center justify-center flex-col">
                                                <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                                        <h3 class="text-white">Hệ thống đang xử lý, xin đợi một chút</h3>
                                            </div>
                                            <div v-else class="">
                                                <img src="/assets/svgs/aiwow/image.svg" class="mx-auto w-24" >
                                            </div>
                                        </div>
                                    </div>
                                             <ButtonTaskBar :isActive="imageRef" :selectedImage="imageRef" type="image" :shareLinkableType="'AIGeneratedMedia'" :features="['background','video', 'swap_face']"/>

                    <div class="w-full justify-center items-center flex flex-col md:flex-row mt-2 md:w-full xl:w-full lg:pb-[4px]">
                        <div class="flex mt-4" ref="resultBox">
                            <a :href="route('ai-image.historyBeautyImage')" class="px-4 w-[180px] text-center py-2 bg-ai3goc-sec text-white font-bold text-[15px] rounded-[10px] h-fit"> Lịch sử </a>
                        </div>
                    </div>
                </div>
            </section>
                            </div>
                        
                    </Layout>
    <!-- <div
        v-if="isLoading"
        class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30"
    >
        <div class="loader"></div>
    </div> -->
    <div v-if="isTranscribing" class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-50">
        <div class="loader"></div> <!-- You can use a spinner here -->
    </div>
    <CreditModal :showCreditPopup="showCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleCancel" />
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="acceptUpgrade"/>
   <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup"
                    @buyCredit="handleBuyCredit"
                    @cancel="handleBuyCancel"
                    :currentCredit = "pageProps.auth.user.credit || 0"
                    :additionalCredit = "additionalCredit"
    />

    <ShowDetailImage :selectedImage="selectedImage" :closeDetail="closeDetail" />
    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink"/>
    </Modal>
    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink"/>
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
    <div
        v-if="isLoading"
        class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30"
    >
        <div class="loader"></div>
    </div>
</template>
<script setup>
import Credit from '@/Components/Credit.vue';
import FormShareLink from '@/Components/FormShareLink.vue';
import ImageComparison from "@/Components/ImageComparison.vue";
import MainMenu from "@/Components/MainMenu.vue";
import Modal from '@/Components/Modal.vue';
import PopupAff from '@/Components/PopupAff.vue';
import ShowDetailImage from "@/Components/ShowDetailImage.vue";
import Layout from "@/Layouts/Client/Layout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { computed, onMounted, ref } from "vue";
import { toast } from "vue3-toastify";
import CreditBuyModal from '../../../Components/ModalBuyCredit/BuyCredit.vue';
import CreditModal from '../../../Components/ModalBuyCredit/Index.vue';
import Footer from "../Home/Components/Footer.vue";
import { useMobile } from '@/helper/useMobile';
import ButtonTaskBar from "@/Components/ButtonTaskBar.vue";
import Popup from '@/Components/Popup/Index.vue'
import Step from "@/Components/Step/Index.vue";

const props = defineProps({
    ai_assistant: Object,
});

const breadcrumbsData = [
    { text: "Làm đẹp ảnh" },
];
const { isMobile } = useMobile();
const { props: pageProps } = usePage();
const auth = computed(() => pageProps.value.auth);
const additionalCredit = ref(0);
const isCallApi = ref(false)
const isLoadingImage = ref(false);
const credits = ref(0);
const imageRef = ref(null);
const currentImgSelect = ref(null);
const imageFile = ref(null);
const imageUrl = ref(null)
const defaultImage = ref('https://images.unsplash.com/flagged/photo-1562088315-f56d842060b6?q=80&w=2967&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D')
const popupMessage = ref('Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!')
const acceptUpgrade = ref(true)

let allowedExtension = ['image/jpeg', 'image/jpg', 'image/png'];
const handleImageFileChange = (event) => {
    let type =  event.target.files[0].type;
    if(allowedExtension.indexOf(type) < 0){
        alert('Vui lòng chọn lại ảnh có định dạng .png, .jpg, .jpeg')
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
const aiMediaId = ref(null)
const numberImageSelect = ref(1)
const loadingCreateImage = ref(false)
const loadingImageState = ref(false)
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
        formData.append('model', 'photo-beauty');
        formData.append('type', 'photo-beauty');
        // Gọi API để kiểm tra credit của user
        const response = await axios.post(route("credit.check_credit"), formData);
        if (response.data.success) {
            // Đủ credit để tiếp tục
            return true;
        } else if (response.data.is_vip_expired) {
            showAffKeyPopup.value = true
            if(response.data.affCode == 'aff_trial') {
                popupMessage.value = " Gói của bạn đã hết hạn. Xin vui lòng gia hạn tài khoản để tiếp tục sử dụng tính năng này."
                acceptUpgrade.value = false
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
            Object.values(errors).forEach(errorMessages => {
                if (Array.isArray(errorMessages)) {
                    errorMessages.forEach(error => {
                        toast.error(error);
                    });
                } else {
                    toast.error(errorMessages);
                }
            });
        } else {
            if (error.response) {
                toast.error((error.response.data.message || 'Đã xảy ra lỗi.'));
            } else if (error.request) {
                toast.error('Không thể kết nối đến server.');
            } else {
                toast.error('Lỗi: ' + error.message);
            }
        }
        return false;
    }
};

var intervalid1 = setInterval(async () => {
    if(aiMediaId.value && !isCallApi.value) {
        isCallApi.value = true
        var url = route("ai-image.getImage", { id: aiMediaId.value })
        try {
            const response = await axios.get(url)
            isCallApi.value = false
            if (response.status === 200 && response.data.s3_url) {
                imageResponse.value = response.data.s3_url
                imageRef.value = response.data.generate_file
                credits.value = response.data.credits;
                isLoadingImage.value = false;
                loadingCreateImage.value = false;
                aiMediaId.value = ""
            } else if(response.status === 500) {
                aiMediaId.value = ""
                alert(response.data.message);
                isLoadingImage.value = false;
                loadingCreateImage.value = false;
            }
        } catch (error) {
            isCallApi.value = false
        }
    }
}, 10000);

const generateImage = async () => {
    // isLoading.value = true;
    imageResponse.value = ""
    imageRef.value = ""
    try {
        const hasEnoughCredit = await checkCredit();
        if (!hasEnoughCredit) {
            isLoading.value = false;
            return; // Dừng nếu không đủ credit
        }
        loadingImageState.value = true;
        const formData = new FormData();
        formData.append('model', 'photo-beauty');
        if (imageFile.value) {
            formData.append('image_file', imageFile.value);
        } else {
            alert("Vui lòng chọn ảnh")
            return false
        }
        isLoadingImage.value = true;
        loadingCreateImage.value = true;
        const response = await axios.post(
            route("ai-image.generatePhotoBeauty"),
            formData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            }
        );

        if (response.status === 200 && response.data.id) {
            aiMediaId.value = response.data.id
        } else {
            alert(response.data.message);
            isLoadingImage.value = false;
            loadingCreateImage.value = false;
        }
    } catch (error) {
        isLoadingImage.value = false;
        loadingCreateImage.value = false;
    }
};


let showCreditPopup = ref(false);
const handleBuyCredit = () => {
    window.location.href = '/pricing';
};

const handleCancel = () => {
    showCreditPopup.value = false;
};

const confirmDelete = async () => {
    isLoading.value = true;
    showConfirmModal.value = false;
    try {
        const response = await axios.post(
            route("ai-image.delete", { id: imageDeleteId.value })
        );
        if (response.status === 200){
            alert("Xóa thành công")
            window.location.href = ""
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
    var url = route(("ai-video.downloadFile"), {url:url, name:"image.png"})
    window.open(url, '_blank');
};

const createPost = (url) => {
    if (url) {
        let image = {
            s3_url: url,
            type: 'image',
        };
        localStorage.setItem("aiImage",JSON.stringify(image) );
        window.location.href = route('calendar');
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
            share_linkable_type: 'AIGeneratedMedia',
        });

        shareLink.value = route("share-link.show", { 'token': response.data.data.link_token })
        openShareLink();

    } catch (error) {
        toast.error("Chia sẻ ảnh thất bại");
    }
};

onMounted(() => {
    handleCheckSieze();
    window.addEventListener("resize", handleCheckSieze);
});
const isTranscribing = ref(false);

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
        color:black;
        text-align:center;
        margin-top: 20px;
    }
</style>
