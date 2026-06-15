<template>

    <Head title="Image to Video - AI 3 GỐC - Cộng đồng AI tử tế" />
        <Layout :breadcrumbs="breadcrumbsData" isBig>
            <div
                    class="flex flex-col lg:flex-row items-start justify-between lg:items-end mb-5 space-y-4 lg:space-y-0 lg:space-x-4">
                    <div class="w-full flex flex-col space-y-2">
                        <div class="flex justify-between gap-4 w-full flex-wrap md:flex-nowrap">
                            <div class="bg-white w-full rounded-[10px] md:rounded-[25px] md:w-2/5 p-[12px] md:p-[25px] drop-shadow-xl">
                                <div class="flex justify-start items-center gap-2">
                                    <img class="p-2 rounded-2xl size-12" src="/assets/img/ai3goc/logo/circle.svg" />
                                    <div>
                                        <h2 class="text-black font-bold text-base lg:text-[30px] leading-[50px] line-clamp-1">Video từ hình ảnh
                                        </h2>
                                    </div>
                                </div>
                                <p class="text-ai3goc-sec text-sm md:text-base font-bold">Thực hiện theo các bước sau:</p>
                                <form @submit.prevent="handleSubmit">
                                    <div class="relative">
                                        <div class="mt-4">
                                            <!-- <div class="flex items-center">
                                                <input type="checkbox" id="use-search-image" v-model="useSearchImage" class="mr-2">
                                                <label for="use-search-image"
                                                    class="text-black font-semibold text-[14px]">Sử dụng hình ảnh từ tìm kiếm</label>
                                            </div> -->
                                            <div class="flex flex-row md:flex-col xl:flex-row xl:items-center xl:gap-8 gap-2 my-4 w-full justify-between  xl:justify-between">
                                               <Step :step="1" stepName="Tải hình ảnh" forName="use-upload-image" />
                                                <input type="file" ref="fileInput" accept="image/*" @change="handleFileUpload" class="hidden" />
                                            </div>
                                            <div class="flex flex-rows gap-2 text-center self-center mb-2">
                                                <div class="flex flex-col w-full relative pointer">
                                                    <img v-if="imageUrl" :src="imageUrl"
                                                        class="w-full md:max-w-[500px] h-[240px] mx-auto object-contain bg-black rounded-md" />
                                                    <div v-else
                                                        class="bg-[#E6E6E6] w-full h-[240px] rounded-2xl flex flex-col items-center justify-center" @click="handleChangeInputImageType(1)">
                                                        <img src="\assets\img\icon\icon_add.png" alt=""
                                                            class="w-10 h-fit">
                                                            <span class="mt-2">Tải ảnh lên</span>
                                                    </div>
                                                    <img src="\assets\img\icon\delete.png" 
                                                            class="w-6 h-fit absolute icon-delete" v-if="imageUrl" @click="deleteImage(1)">
                                                    <span class="absolute lable-img">Ảnh 1</span>
                                                </div>
                                                <div class="flex flex-col w-full relative pointer">
                                                    <img v-if="imageUrl2" :src="imageUrl2"
                                                        class="w-full md:max-w-[500px] h-[240px] mx-auto object-contain bg-black rounded-md" />
                                                    <div v-else
                                                        class="bg-[#E6E6E6] w-full h-[240px] rounded-2xl flex flex-col items-center justify-center"  @click="handleChangeInputImageType(2)">
                                                            <img src="\assets\img\icon\icon_add.png" alt=""
                                                            class="w-10 h-fit">
                                                            <span class="mt-2">Tải ảnh lên</span>
                                                    </div>
                                                    <img src="\assets\img\icon\delete.png" 
                                                        class="w-6 h-fit absolute icon-delete" v-if="imageUrl2" @click="deleteImage(2)">
                                                    <span class="absolute lable-img">Ảnh 2</span>
                                                </div>
                                            </div>
                                            <span class="text-sm md:text-base text-gray-400 self-center inline-block mb-1 md:mb-4 italic">Ảnh 1 là ảnh bắt đầu, ảnh 2 là ảnh kết thúc video.</span>
                                            <div class="flex flex-rows gap-2 text-center self-center mb-2">
                                                <Step :step="2" stepName="Mô tả chuyển động của camera" forName="video_description" />
                                            </div>
                                            <div class="flex flex-rows gap-2 text-center self-center mb-2 relative">
                                                <SuggestionPrompt v-model="video_description" :type="'image-to-video'" :screenId="2" />
                                                <textarea v-model="video_description" placeholder="VD: Máy quay di chuyển từ trái sang phải, máy quay đặt ở vị trí xa, ..."
                                                    rows="5"
                                                    class="mt-1 block w-full border-orion-sec rounded-md shadow-sm focus:outline-none focus:bg-white focus:border-gray-500 min-h-[160px] lg:min-h-0"></textarea>
                                            </div>

                                            <div class="flex flex-row md:flex-col xl:flex-row xl:items-center xl:gap-8 gap-1 justify-between  xl:justify-between">
                                                <Step :step="3" stepName="Thời lượng" forName="video-duration" />
                                                <select id="video-duration" v-model="videoDuration"
                                                    :class="{ 'bg-gray-200 border-gray-200': !videoDuration, 'bg-white border-orion-sec': videoDuration }"
                                                    class="block w-1/3 sm:w-1/2 md:w-full xl:w-2/5 text-[14px] appearance-none col-span-2 text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                                    <option v-for="duration in durations" :value="duration" :key="duration">
                                                        {{ duration }} giây
                                                    </option>
                                                </select>
                                            </div>
                                            <span v-if="errors.duration" class="text-red-500 text-sm">{{ errors.duration
                                                }}</span>
                                        </div>
    
                                        <div class="mt-4">
                                            <div class="flex flex-row md:flex-col xl:flex-row xl:items-center xl:gap-8 gap-1 justify-between  xl:justify-between">
                                                <Step :step="4" stepName="Kích thước" forName="video-dimensions" />

                                                <select id="video-dimensions" v-model="videoDimensions"
                                                    :class="{ 'bg-gray-200 border-gray-200': !videoDimensions, 'bg-white border-orion-sec': videoDimensions }"
                                                    class="block text-[14px] w-1/3 sm:w-1/2 md:w-full xl:w-2/5 appearance-none col-span-2 text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                                    <option v-for="dimension in validDimensions" :value="dimension"
                                                        :key="dimension">
                                                        {{ dimension }}
                                                    </option>
                                                </select>
                                            </div>
                                            <span v-if="errors.dimensions" class="text-red-500 text-sm">{{ errors.dimensions
                                                }}</span>
                                        </div>
                                    </div>
    
                                    <div class="mt-5 flex flex-row md:flex-col xl:flex-row xl:items-center justify-between gap-1">
                                        <Step :step="5" stepName=""/>

                                        <button type="submit"
                                        :class="{
                                        'cursor-not-allowed': !imageUrl,
                                        'bg-ai3goc-sec hover:bg-ai3goc-sec/80': imageUrl
                                        }"
                                            class=" py-2 bg-ai3goc-sec text-white rounded-[10px] w-1/3 sm:w-1/2 md:w-full xl:w-2/5 text-14 font-bold  transition-colors duration-300 hover:scale-105"
                                            :disabled="!imageUrl || isLoading">
                                            {{ isLoading ? 'Đang xử lý...' : 'Gửi yêu cầu' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
    
                            <div class="bg-white rounded-[10px] md:rounded-[25px] w-full lg:w-3/5 p-[12px] md:p-[25px] drop-shadow-xl">
                                <p class="italic text-pribg-ai3goc-sec font-normal mb-2">Hiển thị kết quả</p>
                                <div class="flex justify-center items-center mb-4">
                                    <video v-if="videoRef" :src="videoRef" controls
                                        class="w-full h-auto md:max-w-[500px] object-cover rounded-lg">
                                    </video>
                                    <div v-else
                                        class="bg-[#E6E6E6] w-full h-[300px] md:h-[400px] xl:h-[500px] rounded-2xl flex items-center justify-center">
                                        <img v-if="!isLoading" src="/assets/img/aiwow/ai_image/placeholder.png"
                                                alt="image"/>
                                        <div v-else class="flex flex-col items-center">
                                            <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                                            <h3 class="text-white">Hệ thống đang xử lý, xin đợi một chút</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-black">
                                        <div class="flex justify-evenly lg:justify-end w-full  lg:gap-10">
                                            <ButtonTaskBar 
                                            :is-call-upload-audio="isCallUploadAudio" 
                                            :is-disabled="isCallUploadAudio"
                                            :isActive="resultValue" 
                                            :selectedImage="resultValue" 
                                            :shareLinkableType="'AIGeneratedMedia'" 
                                            :type="'video'"
                                            :features="['lipsync', 'background_audio']"
                                            @handle-audio-file-change="handleAudioFileChange"/>
                                        </div>
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
                                </div>

                                <div class="flex justify-center mt-4 md:mt-10 w-full">
                                        <a :href="route('ai-video.ImgToVideoHistory')"
                                            class="px-4 w-[180px] text-center h-fit py-2 bg-ai3goc-sec text-white font-bold text-[15px] rounded-[10px] hover:bg-green-600 transition-colors duration-300 hover:scale-105">
                                            Lịch sử
                                        </a>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
        </Layout>
    <!-- <div v-if="isLoading" class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
        <div class="loader"></div>
    </div> -->
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="false" @openBuy="openCreditPackageModal()"/>
    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink"/>
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
    <div v-if="isShowMedia" class="inset-0 bg-black/70 fixed z-[999] flex items-center justify-center">
        <div class="w-10/12 bg-white h-5/6 rounded-lg p-4 text-black">
            <section class="flex justify-between">
                <h2 class="font-semibold text-xl">
                    Danh sách file đã tạo từ AI của bạn:
                </h2>
                <button @click="isShowMedia = false" class="w-10 h-10 rounded-full hover:bg-black/20">X</button>
            </section>
            <div class="flex flex-col justify-between gap-0 lg:gap-5 mt-6 h-5/6 overflow-scroll">
                <div class="flex flex-col justify-between ">
                    <div class="columns-4 gap-0.5 ">
                        <div
                            class="mb-0.5 relative group "
                            v-for="(item, index) in listImages"
                            :key="index"
                        >
                            <img :src="item.s3_url" alt="img" class="w-full h-auto object-contain">
                            <div @click="handleImageUpdate(item?.s3_url)" class="absolute cursor-pointer inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-0"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div
                v-if="hasNextPage"
                @click="fetchMedia"
                class="text-blue-600 text-center cursor-pointer"
            >
                Hiển thị thêm
            </div>
        </div>
    </div>
    <CreditPackageModal ref="creditPackageModalRef" />
</template>
<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import {Head, usePage} from '@inertiajs/vue3';
import Footer from '../Home/Components/Footer.vue';
import MainMenu from '@/Components/MainMenu.vue';
import Credit from '@/Components/Credit.vue';
import SearchImage from "@/Pages/Client/AIVideo/EditImage/SearchImage.vue";
import axios from 'axios';
import firebase from "../../../firebase/firebase";
import {toast} from "vue3-toastify";
import FormShareLink from '@/Components/FormShareLink.vue';
import PopupAff from '@/Components/PopupAff.vue';
import Modal from '@/Components/Modal.vue';
import Layout from "@/Layouts/Client/Layout.vue";
import TaskBar from '@/Components/TaskBar.vue'
import Popup from '@/Components/Popup/Index.vue'
import Step from "@/Components/Step/Index.vue";
import CreditPackageModal from "../../Auth/Components/CreditPackageModal.vue";
import SuggestionPrompt from "@/Components/AIVirtual/SuggestionPrompt.vue";
import ButtonTaskBar from '@/Components/ButtonTaskBar.vue';

const props = defineProps({
    ai_assistant: Object,
    images: {
        type: Array,
        default: () => []
    },
    imageUrl: String
});

const creditPackageModalRef = ref(null);
const openCreditPackageModal = () => {
    showAffKeyPopup.value = false
    creditPackageModalRef.value.openModal();
};

const { props: pageProps } = usePage();
const auth = computed(() => pageProps.value.auth);
const credits = ref(0);
props.images[0] = "/assets/img/img2Vid-default.jpg";
const additionalCredit = ref(0);
const videoRef = ref('');
const durations = computed(() => {
    videoDuration.value = 5;
    return [5, 10];
});

const breadcrumbsData = [
    { text: "Ảnh", link: "ai-video.ImgToVideoHistory" },
    { text: "Ảnh thành video", link: "ai-video.img2Video" },
];
const videoDuration = ref(5);
const videoDimensions = ref('16:9');
const isLoading = ref(false);
const errors = ref({});

const isShowDes = ref(true);

const imageUrl = ref(props?.imageUrl || '');
const imageUrl2 = ref("")
const video_description = ref("")
const imagePreview = ref('');
const searchKeyword = ref('');

const selectedImage = ref(props.imageUrl);
const useSearchImage = ref(false);
const fileInput = ref(null);
const fileUpload = ref({ file: "" });
const showSubmit = ref(true);
const validDimensions = computed(() => ['16:9', '9:16']);
const audioFile = ref(null);
const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
const showConfirmModal = ref(false);
const imageDeleteId = ref(null);
const resultValue = ref(null);

const isShowMedia = ref(false);
const listImages = ref([]);
const currentPage = ref(1);
const hasNextPage = ref(false);

const DEFAULT_SEARCH_IMAGE = props.imageUrl || 'https://c.pxhere.com/photos/ef/9f/flying_bird_bird_flight_wing_sky-30166.jpg!s2';

const popupMessage = ref('Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!')
const acceptUpgrade = ref(true)

const searchImages = async () => {
    try {
        const response = await axios.post('/find-images', {
            params: {
                keyword: searchKeyword.value,
            },
        });
        const images = response.data.images;
        if (images.length > 0) {
            imageUrl.value = images[0];
            imagePreview.value = images[0];
        } else {
            imageUrl.value = '';
            imagePreview.value = '';
        }
    } catch (error) {
        console.error('Lỗi tìm kiếm ảnh:', error);
        imageUrl.value = '';
        imagePreview.value = '';
    }
};

const handleVideoFileChange = (event) => {
    videoFile.value = event.target.files[0];
};

const validateForm = () => {
    errors.value = {};
    if (!videoDuration.value) {
        errors.value.duration = "Độ dài video là bắt buộc.";
    }
    if (!videoDimensions.value) {
        errors.value.dimensions = "Kích thước là bắt buộc.";
    }
    if (!imageUrl.value) {
        errors.value.imageUrl = "URL hình ảnh là bắt buộc.";
    }
    return Object.keys(errors.value).length === 0;
};
let showAffKeyPopup = ref(false);
const handleBuyCancel = () => {
    showAffKeyPopup.value = false;
};
const showBuyCreditModal = () => {
    popupMessage.value = "Tài khoản của bạn không đủ credit để sử dụng chức năng này."
    showAffKeyPopup.value = true;
};

const audioName = ref('');
const isLoadingAudio = ref(false);
const handleAudioFileChange = async (event) => {
    var type = event.target.files[0].type;
    const allowedAudioTypes = ["audio/mpeg", "audio/wav"];
    if (!allowedAudioTypes.includes(type)) {
        alert("Xin vui lòng tải lên các tệp âm thanh có định dạng .mp3 hoặc .wav.");
        audioInput.value.value = "";
        return;
    }
    audioFile.value = event.target.files[0];
    audioName.value = audioFile.value ? audioFile.value.name : 'Chưa có tệp nào được chọn';
    isLoadingAudio.value = true;
    isCallUploadAudio.value = true
    const formData = new FormData();
    formData.append('ai_media_id', aiMediaId.value);
    if (audioFile.value) {
        formData.append('audio_file', audioFile.value);
    }
    formData.append('type', "image-to-video");
    try {
        const response = await axios.post(
        route("ai-video.mergeAudioVideoQueue"),
            formData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            }
        );
        if(response.data.success) {
            isCallUploadAudio.value = true
            callApiDetail()
        } else {
            isCallUploadAudio.value = false
        }
    } catch (error) {
        isLoadingVideo.value = false
    } finally {
        isLoadingAudio.value = false;
    }

};

const checkCredit = async () => {
    try {
        const formData = new FormData();
        formData.append('type', 'image_to_video');

        // Gọi API để kiểm tra credit của user
        const response = await axios.post(route("credit.check_credit"), formData);
        if (response.data.success) {
            // Đủ credit để tiếp tục
            return true;
        } else if (response.data.is_vip_expired){
            showAffKeyPopup.value = true
            if(response.data.affCode == 'aff_trial') {
                popupMessage.value = " Gói của bạn đã hết hạn. Xin vui lòng gia hạn tài khoản để tiếp tục sử dụng tính năng này."
                acceptUpgrade.value = false
            }
            return false;
        }  else {
            additionalCredit.value = response?.data?.data?.total_price - pageProps.auth.user.credit;
            // Không đủ credit, hiển thị modal yêu cầu nạp thêm
            popupMessage.value = "Tài khoản của bạn không đủ credit để sử dụng chức năng này."
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
const handleSubmit = async () => {
    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        isLoading.value = false;
        return; // Dừng nếu không đủ credit
    }
    if (useSearchImage.value) {
        await generateVideo();
        return;
    }

    if (!validateForm()) {
        return;
    }

    await generateVideo();
};
const isCallApi = ref(false)
const isCallUploadAudio  = ref(false)
const aiMediaId = ref(null)
const callApiDetail = async () => {
    var url =  route("ai-video.detail", { id: aiMediaId.value })
    try {
        const response = await axios.get(url)
        if(response.data.is_upload_audio) {
            aiMediaId.value =  response?.data?.id
            videoRef.value = response.data.s3_url;
            resultValue.value = response.data;
            isLoadingVideo.value = false
            isCallUploadAudio.value = false
        } else if(!response.data.success) {
            alert(response.data.message);
            isCallUploadAudio.value = false
        }
    } catch (error) {
        isCallUploadAudio.value = false
    }
}

const generateVideo = async () => {
    console.log('Generating video...');
    isLoading.value = true;

    try {
        let finalImageUrl;

        if (useSearchImage.value) {
            // Nếu sử dụng tìm kiếm ảnh, ưu tiên imagePreview, nếu không có thì dùng ảnh mặc định
            finalImageUrl = imagePreview.value || DEFAULT_SEARCH_IMAGE;
            console.log('Using search image:', finalImageUrl);
        } else {
            finalImageUrl = imageUrl.value;
            console.log('Using URL image:', finalImageUrl);
        }

        const payload = {
            duration: videoDuration.value,
            resolution: videoDimensions.value,
            imageUrl: finalImageUrl,
            imageUrl2:imageUrl2.value,
            video_description:video_description.value
        };

        console.log('Sending payload:', payload);

        const response = await axios.post(
            route("ai-video.generate-img-to-video"),
            payload
        );
        console.log("🚀 ~ generateVideo ~ response:", response)


        if (response.status === 200) {
            videoRef.value = response?.data?.url || "";
            if(!!response.data.thumbnail){
                saveData(response.data.thumbnail, response.data.path);
            }else{
                saveData(useSearchImage.value ? imagePreview.value  : imageUrl.value, response.data.path);
            }
        } else {
            videoRef.value = "";
            toast.error("Có lỗi xảy ra khi tạo video. Vui lòng thử lại.");
        }
    } catch (error) {
        console.error("Có lỗi xảy ra:", error);
        videoRef.value = "";
        toast.error("Có lỗi xảy ra khi tạo video. Vui lòng thử lại.");
    } finally {
        isLoading.value = false;
    }
};

const saveData = async (thumbnail, path) => {
    const response = await axios.post(route("ai-video.store"), {
        type: "video",
        s3_url: path,
        duration: videoDuration.value,
        model: "img-video",
        thumbnail: thumbnail ? thumbnail : DEFAULT_SEARCH_IMAGE,
    });
    if(response.status === 200){
        resultValue.value = response.data.response;
        aiMediaId.value = response.data.response.id;
    }
}

watch(useSearchImage, (newValue) => {
    if (newValue) {
        imageUrl.value = "";
        imagePreview.value = '';
    } else {
        imagePreview.value = "";
    }
});

const downloadVideo = () => {
    if (!videoRef.value) {
        toast.error("Chưa có video");
        return;
    }
    const link = document.createElement("a");
    link.href = videoRef.value;
    link.download = "video.mp4";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
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
    fetchMedia();
});

const fetchMedia = async () => {
    try {
        const response = await axios.get(
            route("ai-image.list-media", {
                page: currentPage.value,
                type: "image",
                per_page: 10,
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

const imageId = ref(1)
const handleChangeInputImageType = (id) => {
    imageId.value = id
    fileInput.value.click();
}
const deleteImage = (id) => {
    if(id == 1) {
        imageUrl.value = ""
    } else {
        imageUrl2.value = ""
    }
}

let allowedExtension = ['image/jpeg', 'image/jpg', 'image/png'];
const handleFileUpload = async (event) => {
    let type =  event.target.files[0].type;
    if(allowedExtension.indexOf(type) < 0){
        toast.error('Vui lòng chọn lại ảnh có định dạng .png, .jpg, .jpeg')
        return false;
    }
    const {width, height } = await getImageDimensions(event.target.files[0])
    var imageRatio = (width/height)
    if(imageRatio < 0.5 || imageRatio > 2) {
        toast.error('Ảnh của bạn không hợp lệ. Vui lòng tải ảnh có kích thước tỷ lệ chiều dài/chiều rộng nằm trong khoảng 0.5 đến 2.')
        return false;
    }
    fileUpload.value.file = event.target.files[0];
    if (fileUpload) {
        const reader = new FileReader();
        reader.onload = (e) => {
            selectedImage.value = e.target.result;
            imagePreview.value = e.target.result;
            if(imageId.value == 1) {
                imageUrl.value = '';
            } else {
                imageUrl2.value = '';
            }
           
            useSearchImage.value = false;
        };
        reader.readAsDataURL(fileUpload.value.file);

        firebase
        .uploadImage(fileUpload.value.file)
        .then((url) => {
            console.log("Ảnh đã được upload:", url);
            selectedImage.value = url;
            if(imageId.value == 1) {
                imageUrl.value = url;
            } else {
                imageUrl2.value = url;
            }
        })
        .catch((error) => {
            console.error("Lỗi upload ảnh lên Firebase:", error);
        });
    }
    fileInput.value.value = ""
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
    }
}

const handleImageUpdate = (val) => {
    isShowMedia.value = false;
    selectedImage.value = val;
    imageUrl.value = val;
    imagePreview.value = val;
    fileUpload.value.file = ""
};

const handleUrlSubmit = (url) => {
    console.log('URL submitted:', url);
    selectedImage.value = url;
    imageUrl.value = url;
    useSearchImage.value = false;
    imagePreview.value = url;
};


const prepareDeleteFile = (id) => {
    imageDeleteId.value = id;
    showConfirmModal.value = true;
};

const confirmDelete = async () => {
    isLoading.value = true;

    try {
        const response = await axios.post(
            route("ai-image.delete", { id: imageDeleteId.value })
        );
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
    var url = route(("ai-video.downloadFile"), {url:image, name:"video.mp4"})
    window.open(url, '_blank');
};

const createPost = (imageUrl) => {
    if (imageUrl) {
        let image = {
            s3_url: imageUrl,
            type: 'image',
        };
        localStorage.setItem("aiImage",JSON.stringify(image) );
        window.location.href = route('calendar');
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
            share_linkable_type: 'AIGeneratedMedia',
        });

        shareLink.value = route("share-link.show", { 'token': response.data.data.link_token })
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
.loaderAudio {
    width: 20px !important;
    height: 20px !important;
    border-radius: 50%;
    margin-left: 0px !important;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #24AA69;
    animation: spin 2s linear infinite;
}
.rt-10 {
    right:10px;
    top: 10px;
}
.pointer {
    cursor:pointer
}
.lable-img {
    left: 10px;
    margin-top: 10px;
    background: #cacaca;
    padding: 2px 10px;
    color: #fff;
    border-radius: 15px;
    font-size: 12px;
}
.icon-delete {
    right: 5px;
    margin-top: 10px;
}
</style>
