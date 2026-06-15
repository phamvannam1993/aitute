<template>
    <Head title="Video - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData">
        <div class="w-full text-black px-3">
            <div class="grid gap-cols-1 lg:grid-cols-2 gap-8 mt-8">
                <div class="grid grid-cols-1 w-full">
                    <form ref="form" @submit.prevent="handleSubmit" class="bg-white rounded-md p-4 drop-shadow-xl">
                        <div class="flex items-center gap-4 ">
                            <img src="/assets/img/aiwow/video_creatomate/logo.png" alt="logo">
                            <div>
                                <p class="font-semibold text-base uppercase">Video mẫu:</p>
                                <p class="text-[#092A99] font-bold text-lg md:text-2xl mb-1 uppercase">Mẫu {{ props.template.title }}</p>
                            </div>
                        </div>
                        <p class="block font-medium text-xs lg:text-sm mb-1 pb-3 text-[#092A99]">Tạo video theo các bước sau:</p>
                        <div class="flex items-center gap-2 mb-2 text-xs lg:text-sm">
                            <div class="flex items-center gap-2 bg-[#0E68EF] text-white w-fit px-2 py-1 rounded-full">
                                <img src="/assets/img/aiwow/right_arrow.png" alt="" class="h-2 w-4 ">
                                <label :for="key" class="block font-bold lg:text-sm capitalize text-nowrap ">
                                    Bước 1
                                </label>
                            </div>
                            <span class="lowercase block font-bold text-nowrap">Chọn hình ảnh</span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div v-for="(field, key) in template.composition" :key="key" class="relative">
                                <div v-if="field.type === 'image'" class="">
                                    <div class=" gap-2 text-start h-auto w-full">
                                        <div v-if="imageUrls[key]" class="min-h-[200px] flex items-center">
                                            <img :src="imageUrls[key]" alt="Uploaded Image" class=" rounded mx-auto" />
                                        </div>
                                        <img v-else src="/assets/img/aiwow/video_creatomate/default_image.png" alt="" class="w-full">
                                        <input
                                            type="file"
                                            :id="key"
                                            @change="handleFileUpload($event, key)"
                                            accept=".jpg,.png,.jpeg"
                                            class="hidden"
                                            :ref="'fileInputImage-' + key"
                                        />
                                        <label :for="key" class="absolute top-1 left-2 text-[#909090] font-bold text-base">
                                            {{ field.label }}
                                        </label>
                                        <button
                                            type="button"
                                            @click="$refs['fileInputImage-' + key][0].click()"
                                            class="absolute bottom-2 w-[100px] md:w-[120px] left-1/2 -translate-x-1/2 flex items-center justify-center bg-white gap-1 text-[15px] rounded-full text-sm font-semibold text-[#0E68EF] border border-[#0290FF] py-1 px-2">
                                            <img src="/assets/img/aiwow/video_creatomate/upload_image.png" alt="">
                                            <span class="line-clamp-1 text-[10px] md:text-[14px]">
                                                {{ 'Chọn ảnh' }}
                                            </span>
                                        </button>
                                    </div>

                                    <div v-if="isCropping[key]" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
                                        <div class="bg-white p-4 rounded-md w-full md:w-[80%] lg:w-[40%]">
                                            <VueCropper ref="cropper" :src="imageUrls[key]" :aspect-ratio="aspectRatio" :zoomable="true" class="max-w-full max-h-[60vh] mx-auto overflow-hidden"/>
                                            <div class="flex justify-between mt-4">
                                                <button @click="cancelCropping(key)" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600">
                                                    Hủy
                                                </button>
                                                <button type="button" @click="cropImage(key)" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                                                    Cắt ảnh
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-for="(field, key) in template.composition" :key="key" class="mb-4"> 
                            <div v-if="field.type === 'audio'">
                                <div class="flex flex-col md:flex-row justify-between gap-4">
                                    <div class="gap-2 flex items-center self-start text-xs lg:text-sm">
                                        <div class="flex items-center gap-2 bg-[#0E68EF] text-white w-fit px-2 py-1 rounded-full">
                                            <img src="/assets/img/aiwow/right_arrow.png" alt="" class="h-2 w-4 ">
                                            <label v-if="containVideo" :for="key" class="block font-bold capitalize text-nowrap ">
                                                Bước 3
                                            </label>
                                            <label v-else :for="key" class="block font-bold capitalize text-nowrap ">
                                                Bước 2
                                            </label>
                                        </div>
                                        <span class="lowercase block font-bold text-nowrap">Chọn nhạc nền</span>
                                    </div>
                                    <div class="w-full md:w-1/2 self-center">
                                        <div class="flex gap-2 w-full items-center justify-center">
                                            <input id="background-color" disabled :checked="isUpload" type="radio" name="background" value="color">
                                            <button type="button"  @click="handleUploadClick(key)"
                                                class="flex items-center w-full md:w-2/3 justify-center gap-2 px-3 py-2 bg-[#DEECFF] text-black border border-[#0E68EF] rounded-lg backdrop-blur-sm transition-colors">
                                                <img src="/assets/img/aiwow/ai_text/upload.png" class="size-6 md:size-5 xl:size-6" />
                                                <span class="text-[12px] ">Tải lên</span>
                                            </button>
                                        </div>
                                        <input :id="key" type="file" accept="audio/*" class="hidden" :ref="el => (fileInputRefs[key] = el)"  @change="handleFileUpload($event, key)" />
                                        <div class="flex items-center justify-center gap-2 mt-4">
                                            <input id="background-color" disabled :checked="!isUpload" type="radio" name="background" value="color">
                                            <button type="button"  @click="startRecording(key)"
                                                class="flex items-center w-full md:w-2/3 justify-center gap-2 px-3 py-2 bg-[#DEECFF] text-black border border-[#0E68EF] rounded-lg backdrop-blur-sm transition-colors">
                                                <img src="/assets/img/aiwow/ai_text/mic.png" class="size-6 md:size-5 xl:size-6" />
                                                <span class="text-[12px] ">{{ isRecording? 'Dừng thu âm':'Thu âm' }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="formData[key].value" class="text-sm text-gray-600 mt-2">
                                    <audio controls class="w-full md:w-[70%] mx-auto" ref="audioPreview"
                                        :key="audioPreviewKey">
                                        <source
                                            :src="uploadedAudioPreview"
                                            type="audio/wav">
                                        Browser không hỗ trợ audio.
                                    </audio>
                                    <p class="text-[#0E68EF] italic font-medium text-15px text-center mx-auto mt-1 w-full md:w-[70%]">
                                        {{ recordedFileName }}
                                    </p>
                                </div>
                            </div>
                            <div v-else-if="field.type === 'video'">
                            <div class="flex items-center gap-2 mb-2 text-xs lg:text-sm">
                                <div class="flex items-center gap-2 bg-[#0E68EF] text-white w-fit px-2 py-1 rounded-full">
                                    <img src="/assets/img/aiwow/right_arrow.png" alt="" class="h-2 w-4 ">
                                    <label for="video-description" class="block font-bold capitalize text-nowrap ">
                                        Bước 2
                                    </label>
                                </div>
                                <span class="lowercase block font-bold text-nowrap">Chọn video</span>
                            </div>
                                <input :id="key" type="file" accept="video/*" class="w-full p-2 border border-gray-300 rounded" @change="handleFileUpload($event, key)" />
                            </div>
                        </div>
                        <div class="mb-4 w-full">
                            <div class="flex items-center gap-2 mb-2 text-xs lg:text-sm">
                                <div class="flex items-center gap-2 bg-[#0E68EF] text-white w-fit px-2 py-1 rounded-full">
                                    <img src="/assets/img/aiwow/right_arrow.png" alt="" class="h-2 w-4 ">
                                    <label v-if="containVideo" for="video-description" class="block font-bold capitalize text-nowrap ">
                                        Bước 4
                                    </label>
                                    <label v-else for="video-description" class="block font-bold capitalize text-nowrap ">
                                        Bước 3
                                    </label>
                                </div>
                                <span class="lowercase block font-bold w-7/12 md:w-1/2">Nhập nội dung mô tả video</span>
                            </div>
                            <div class="relative">
                                <SuggestionPrompt v-model="videoDescription" :screenId="0" />
                                <textarea
                                    id="video-description"
                                    v-model="videoDescription"
                                    type="text"
                                    placeholder="Nhập mô tả cho nội dung bạn muốn tạo..."
                                    class="w-full p-3 mt-1 h-[200px] border text-black border-[#2C75E3] rounded-[10px] shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                ></textarea>
                                <div class="object-mic relative ml-auto">
                                    <div
                                        v-if="isRecordingSuggestion"
                                        class=" right-3 bottom-3 flex items-center justify-center"
                                    ></div>
                                    <div
                                        v-if="isRecordingSuggestion"
                                        class=" right-3 bottom-3 flex items-center justify-center"
                                        id="delayed"
                                    ></div>
                                    <div
                                        v-if="isRecordingSuggestion"
                                        class="button-mic right-3 bottom-3 flex items-center justify-center"
                                    ></div>
                                    <button
                                        class="button-mic icon-mic absolute right-3 bottom-3 flex items-center justify-center"
                                        @click="startRecordingSuggestion()"
                                        :disabled="disabledRecord"
                                        type="button"
                                    >
                                        <svg class="h-6 w-6" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000">
                                            <g>
                                                <path
                                                    d="M500,683.8c84.6,0,153.1-68.6,153.1-153.1V163.1C653.1,78.6,584.6,10,500,10c-84.6,0-153.1,68.6-153.1,153.1v367.5C346.9,615.2,415.4,683.8,500,683.8z M714.4,438.8v91.9C714.4,649,618.4,745,500,745c-118.4,0-214.4-96-214.4-214.4v-91.9h-61.3v91.9c0,141.9,107.2,258.7,245,273.9v124.2H346.9V990h306.3v-61.3H530.6V804.5c137.8-15.2,245-132.1,245-273.9v-91.9H714.4z"
                                                />
                                            </g>
                                        </svg>
                                    </button>
                                </div>
                                <div :class="videoDescription.length>=2000 ? 'text-red-600'  : 'text-gray-700'" class="text-end self-end">{{videoDescription.length}}/2000</div>
                            </div>
                            <div class="mt-4 flex justify-between md:items-center gap-2 flex-col md:flex-row">
                                <div class="flex items-center gap-2 text-xs lg:text-sm">
                                    <div class="flex items-center gap-2 bg-[#0E68EF] text-white w-fit px-2 py-1 rounded-full">
                                        <img src="/assets/img/aiwow/right_arrow.png" alt="" class="h-2 w-4 ">
                                        <label v-if="containVideo" for="video-description" class="block font-bold capitalize text-nowrap ">
                                            Bước 5
                                        </label>
                                        <label v-else for="video-description" class="block font-bold capitalize text-nowrap ">
                                            Bước 4
                                        </label>
                                    </div>
                                    <label class="block font-bold capitalize text-nowrap ">
                                        Bấm vào đây
                                    </label>
                                </div>
                                <button type="button" @click="createContent" class="bg-green-500 text-white md:px-2 py-2 text-xs lg:text-sm rounded-[10px] hover:bg-green-600 w-full lg:w-1/3 md:w-[200px] font-bold">Tạo nội dung</button>
                            </div>
                        </div>
                        <div v-if="isCreateContent" v-for="(field, key) in template.composition" :key="key" class="mb-4">
                            <div v-if="field.type === 'text'">
                                <div class="flex justify-between items-center">
                                    <label :for="key" class="block font-bold text-xs lg:text-sm capitalize">
                                        {{ field.label }}
                                    </label>
                                    <div v-if="formData[key].type == 'text'" class="flex justify-start items-center gap-2">
                                        <button type="button" class="text-xs lg:text-sm text-blue-500 font-bold" @click="toggleEdit(key)">
                                            {{ isEditing[key] ? "Bỏ chọn màu" : `Chọn màu chữ` }}
                                        </button>
                                        <input v-if="isEditing[key]" type="color" v-model="formData[`${key}.fill_color`]" class="w-[36px] h-[36px] cursor-pointer border border-gray-300 rounded mt-2" />
                                    </div>
                                </div>
                                <div class="relative">
                                <SuggestionPrompt v-model="formData[key].value" :type="'creatomate'" :screenId="0" />
                                <textarea rows="4" :id="key" v-model="formData[key].value" required :maxlength="field.length || 100" type="text" class="w-full p-2 border border-gray-300 rounded pr-12" placeholder="Nhập nội dung" />
                                <div :class="formData[key].value.length >= field.length ? 'text-red-600'  : 'text-gray-700'" class="text-end ">{{formData[key].value.length}}/{{field.length}}</div>
                                <div class="object-mic relative ml-auto">
                                    <div
                                        v-if="isRecordingSuggestion"
                                        class=" right-3 bottom-3 flex items-center justify-center"
                                    ></div>
                                    <div
                                        v-if="isRecordingSuggestion"
                                        class=" right-3 bottom-3 flex items-center justify-center"
                                        id="delayed"
                                    ></div>
                                    <div
                                        v-if="isRecordingSuggestion"
                                        class="button-mic right-3 bottom-3 flex items-center justify-center"
                                    ></div>
                                    <button
                                        class="button-mic icon-mic absolute right-0 bottom-3 flex items-center justify-center"
                                        @click="startRecordingSuggestion(key)"
                                        :disabled="disabledRecord"
                                        type="button"
                                    >
                                        <svg class="h-6 w-6" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000">
                                            <g>
                                                <path
                                                    d="M500,683.8c84.6,0,153.1-68.6,153.1-153.1V163.1C653.1,78.6,584.6,10,500,10c-84.6,0-153.1,68.6-153.1,153.1v367.5C346.9,615.2,415.4,683.8,500,683.8z M714.4,438.8v91.9C714.4,649,618.4,745,500,745c-118.4,0-214.4-96-214.4-214.4v-91.9h-61.3v91.9c0,141.9,107.2,258.7,245,273.9v124.2H346.9V990h306.3v-61.3H530.6V804.5c137.8-15.2,245-132.1,245-273.9v-91.9H714.4z"
                                                />
                                            </g>
                                        </svg>
                                    </button>
                                </div>
                                <!-- <div :class="formData[key].value.length>=2000 ? 'text-red-600'  : 'text-gray-700'" class="text-end self-end">{{formData[key].value.length}}/2000</div> -->
                            </div>
                            </div>
                        </div>
                        <div v-if="isCreateContent" class="flex gap-2 md:items-center md:justify-between flex-col md:flex-row">
                            <div class="flex items-center gap-2 text-xs lg:text-sm">
                                <div class="flex items-center gap-2 bg-[#0E68EF] text-white w-fit px-2 py-1 rounded-full">
                                    <img src="/assets/img/aiwow/right_arrow.png" alt="" class="h-2 w-4 ">
                                    <label v-if="containVideo" for="video-description" class="block font-bold capitalize text-nowrap ">
                                        Bước 6
                                    </label>
                                    <label v-else for="video-description" class="block font-bold capitalize text-nowrap ">
                                        Bước 5
                                    </label>
                                </div>
                                <label class="block font-bold capitalize text-nowrap ">
                                    Bấm vào đây
                                </label>
                            </div>
                            <button id="submit-form-button" type="submit" class="bg-blue-500 self-center  w-full lg:w-1/3 text-white py-2 rounded-[10px] hover:bg-blue-600 md:w-[200px] text-xs lg:text-sm font-bold ">Tạo video</button>
                        </div>
                    </form>
                </div>
                <div>
                    <div class="bg-white rounded-[10px] md:rounded-[25px] w-full p-[12px] md:p-[25px] drop-shadow-xl">
                        <div class="flex flex-col justify-center items-center mb-4">
                            <p class="self-start ml-2 mb-3 text-[#0E68EF] italic">Hiển thị kết quả</p>
                            <video v-if="videoRef" :src="videoRef" alt="video-generate-by-AI" controls="" :poster="videoPoster" preload="auto" class="w-full h-auto md:max-w-[500px]  object-cover" />
                            <div v-else class="bg-[#E6E6E6] flex items-center justify-center w-full md:w-[400px] xl:w-[500px] h-[300px] md:h-[400px] xl:h-[500px] rounded-2xl showLoaderVideo">
                                <div v-if="isLoadingVideo">
                                    <div class="loaderVideo"></div>
                                    <p class="text-loading">Video đang được tạo. Vui lòng chờ !</p>
                                </div>
                                <img v-else src="/assets/img/aiwow/video_creatomate/play.png" alt="" class="w-24 h-24">
                            </div>
                        </div>
                        <TaskBar :isActive="videoRef" :selectedImage="videoResult" type="video" :shareLinkableType="'CreatomateVideo'" />
                        <div class="flex justify-end mt-4 w-full btn-history">
                            <a :href="route('creatomate.history')" class="px-4 w-[180px] text-center py-2 bg-[#2C75E3] text-white font-bold  text-lg rounded-[10px]">Lịch sử</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
    
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
    <div v-if="isLoading" class="fixed inset-0 flex items-center justify-center z-[104] bg-black bg-opacity-30">
        <div class="loader"></div>
    </div>
    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink" />
    </Modal>
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="acceptUpgrade"/>
    <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup"
                    @buyCredit="handleBuyCredit"
                    @cancel="handleBuyCancel"
                    :currentCredit = "pageProps.auth.user.credit || 0"
                    :additionalCredit = "additionalCredit"
    />
</template>
<script setup>
// import Layout from "@/Layouts/Client/ClientLayout.vue";
import Layout from "@/Layouts/Client/AiLayout.vue";

import { Head, usePage } from "@inertiajs/vue3";
import axios from "axios";
import MainMenu from "@/Components/MainMenu.vue";
import { ref, reactive, computed, onMounted, watch } from "vue";
import Credit from "@/Components/Credit.vue";
import Footer from "../Home/Components/Footer.vue";
import Modal from '@/Components/Modal.vue';
import PopupAff from '@/Components/PopupAff.vue';
import CreditBuyModal from '../../../Components/ModalBuyCredit/BuyCredit.vue';
import {toast} from "vue3-toastify";
import SuggestionPrompt from "@/Components/AIVirtual/SuggestionPrompt.vue";
import FormShareLink from "@/Components/FormShareLink.vue";

import VueCropper from 'vue-cropperjs';
import 'cropperjs/dist/cropper.css';
import { values } from "lodash";
import TaskBar from '@/Components/TaskBar.vue'
import Popup from '@/Components/Popup/Index.vue'

const props = defineProps({
    credits: Number,
    template: Object,
});
const credits = ref(0);
const template = ref(JSON.parse(props.template.structure) || {});
const videoDescription = ref('');
const videoRef = ref();
const videoPoster = ref();
const isLoading = ref(false);
const duration = ref(0);
const { props: pageProps } = usePage();
const auth = computed(() => pageProps.value.auth);
const additionalCredit = ref(0);
const showAffKeyPopup = ref(false);
const popupMessage = ref('Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!')
const acceptUpgrade = ref(true)
const finalStep = ref(0)
const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
const showConfirmModal = ref(false);
const videoDeleteId = ref(null);
const resultValue = ref(null);
const formData = reactive({});
const recordedFileName = ref("");
const form = ref(null);
const isUpload = ref(true)
const isEditing = reactive({});

const imageUrls = reactive({});
const imageDescriptions = reactive({});
const imageInputs = reactive({});
const disabledRecord = ref(false)
const audioPreviewKey = ref(0);
const uploadedAudioPreview = ref(null);
const videoResolution = ref(props.template.resolution || '1:1')
const step = ref(0)
const isCropping = reactive({});
const cropper = ref(null);
const croppedImageUrls = reactive({});
const videoResult = ref({
    id: '',
    s3_url: ''
})
const breadcrumbsData = [
    { text: "Danh sách template", link: "creatomate.index" },
    { text: "Tạo video", link: "ai-background.indexV2" },
];

const aspectRatio = computed(() => {
  const resolution = props.template.resolution || "1:1";
  const [width, height] = resolution.split(':');
  return parseFloat(width) / parseFloat(height);
});

const cancelCropping = (key) => {
    isCropping[key] = false;
};

const cropImage = (key) => {
    if (cropper.value) {
        const croppedCanvas = cropper.value[0].cropper.getCroppedCanvas()
        const croppedImageUrl = croppedCanvas.toDataURL();
        const croppedFile = dataURLtoFile(croppedImageUrl, `${key}_cropped.png`);
        formData[key].value = croppedFile;
        croppedImageUrls[key] = croppedImageUrl;
        imageUrls[key] = croppedImageUrl;
        isCropping[key] = false;
    }
};

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
        formData.append('model', "creatomate");
        formData.append('type', 'creatomate');
        formData.append('number_result', 30);
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

Object.keys(template.value.composition).forEach((key) => {
    formData[key] = {
        value: "",
        type: template.value.composition[key].type,
    };
    isEditing[key] = false;
});

const toggleEdit = (key) => {
    isEditing[key] = !isEditing[key];
    if (isEditing[key]) {
        if (!formData[`${key}.fill_color`]) {
            formData[`${key}.fill_color`] = '#000000';
        }
    } else {
        delete formData[`${key}.fill_color`];
    }
};

const handleFileUpload = async (event, key) => {
    const file = event.target.files[0];
    if (file) {
        event.target.value = '';
        imageInputs[key] = false;
        if (formData[key].type == 'image') {
            const validExtensions = ['.png', '.jpg', '.jpeg'];
            const fileExtension = file.name.slice(((file.name.lastIndexOf(".") - 1) >>> 0) + 2).toLowerCase();
            console.log('abc')
            if (!validExtensions.includes(`.${fileExtension}`)) {
                toast.error('Vui lòng chọn file hình ảnh có định dạng PNG, JPG hoặc JPEG.');
                return;
            }
            formData[key].value = file;
            imageUrls[key] = URL.createObjectURL(file);
            isCropping[key] = true;
        }

        if (formData[key].type === 'audio') {
            const validAudioExtensions = ['.mp3', '.wav', '.ogg'];
            const fileExtension = file.name.slice(((file.name.lastIndexOf(".") - 1) >>> 0) + 2).toLowerCase();
            console.log('debug: fileExtension for audio', fileExtension);

            if (!validAudioExtensions.includes(`.${fileExtension}`)) {
                toast.error('Vui lòng chọn file âm thanh có định dạng MP3, WAV hoặc OGG.');
                return;
            }
            formData[key].value = file
            uploadedAudioPreview.value = URL.createObjectURL(file);
            audioPreviewKey.value += 1;
            recordedFileName.value = file.name;
            isUpload.value=true;
        }

        if (formData[key].type === 'video') {
            const validVideoExtensions = ['.mp4', '.avi', '.mov'];
            const fileExtension = file.name.slice(((file.name.lastIndexOf(".") - 1) >>> 0) + 2).toLowerCase();
            console.log('debug: fileExtension for video', fileExtension);

            if (!validVideoExtensions.includes(`.${fileExtension}`)) {
                toast.error('Vui lòng chọn file video có định dạng MP4, AVI hoặc MOV.');
                return;
            }

            formData[key].value = file
            videoPreviewUrls[key] = URL.createObjectURL(file);  // Đảm bảo bạn đã định nghĩa videoPreviewUrls
        }
    }
};
const handleDescriptionChange = (key, value) => {
    imageDescriptions[key] = value;
};

const calculateDimensions = (aspectRatio) => {
  const [width, height] = aspectRatio.split(':').map(Number);
  const fixedHeight = 1360;
  const calculatedWidth = (fixedHeight * width) / height;
  return { width: calculatedWidth, height: fixedHeight };
};

const handleCreateImage = async (key) => {
    try {
        isLoading.value = true;

        if (
            (!imageDescriptions[key] || imageDescriptions[key].trim() === "") &&
            (!videoDescription.value || videoDescription.value.trim() === "")
        ) {
            toast.info("Vui lòng nhập mô tả nội dung hình ảnh cần tạo");
            imageInputs[key] = true;
            isLoading.value = false;
            return;
        }

        const { width, height } = calculateDimensions(videoResolution.value);

        const response = await axios.post(route("ai-image.generate"), {
            description: imageDescriptions[key] || videoDescription.value,
            style: "",
            artist: "",
            height: height,
            width: width,
            model: "Flux Schnell",
            aspect_ratio: videoResolution.value
        });

        if (response.status === 200 && response.data.url) {
            imageInputs[key] = false;
            imageUrls[key] = response.data.url;
            formData[key].value = await convertURLtoFile(response.data.url, 'image');
            credits.value = response.data.credits;
            toast.success("Tạo hình ảnh thành công.");
        } else {
            console.error("Không có URL hình ảnh trong phản hồi.");
            toast.error("Không thể tạo hình ảnh. Vui lòng thử lại.");
        }
    } catch (error) {
        console.error("Lỗi khi tạo hình ảnh:", error);
        toast.error("Đã xảy ra lỗi khi tạo hình ảnh. Vui lòng thử lại sau.");
    } finally {
        isLoading.value = false;
    }
};

const convertURLtoFile = async (url, fileType) => {
        try {
            const response = await axios.get(route('posts.show', { s3_url: url }), {
                responseType: 'blob',
            });

            let contentType = response.headers['content-type'] || getContentTypeFromExtension(url);

            if (fileType === 'audio' && !contentType.startsWith('audio/')) {
                contentType = 'audio/mpeg';
            }
            if (fileType === 'image' && !contentType.startsWith('image/')) {
                contentType = 'image/png';
            }

            const randomString = Math.random().toString(36).substring(2, 12);
            const fileName = fileType === 'audio'
                ? `audio_${randomString}.mp3`
                : `image_${randomString}.png`;

            return new File([response.data], fileName, { type: contentType });
        } catch (error) {
            console.error("Error in convertURLtoFile:", error.message || error);
            throw error;
        }
    };

    const fileInputRefs = ref({});
    const handleUploadClick = (key) => {
        if (isRecording.value) {
            startRecording();
        }
        // Sau khi dừng ghi âm, kích hoạt upload
        if (fileInputRefs.value[key]) {
            fileInputRefs.value[key].click(); // Sử dụng ref để kích hoạt file input
        }
    };

const handleSubmit = async () => {
    for (const key in formData) {
        if (key.endsWith('.fill_color')) {
            continue;
        }
        if (formData[key].value === "" || formData[key].value === null || formData[key].value === undefined) {
            console.log(key)
            alert(`Trường "${template.value.composition[key].label}" không được để trống. Vui lòng kiểm tra lại!`);
            return;
        }
    }

    if (!formData['Audio'].value) {
        alert('Vui lòng tải lên tệp âm thanh trước khi tạo video!');
        return;
    }

    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        return; // Dừng nếu không đủ credit
    }

    isLoading.value = true;

    const formDataToSubmit = new FormData();
    formDataToSubmit.append("template_id", props.template.template_id);
    formDataToSubmit.append('duration', duration.value);

    Object.keys(formData).forEach((key) => {
        if (formData[key].type === "image" || formData[key].type === "audio" || formData[key].type === "video") {
            formDataToSubmit.append(key, formData[key].value);
        } else if (key.includes('fill_color')) {
            formDataToSubmit.append(key, formData[key]);
        } else {
            formDataToSubmit.append(key, formData[key].value);
        }
    });

    try {
        const response = await axios.post(route("creatomate.create-video"), formDataToSubmit, {
            headers: { "Content-Type": "multipart/form-data" },
        });

        resultValue.value = response.data.data;
        videoResult.value.s3_url = resultValue.value.video_url;
        videoResult.value.id = resultValue.value.id;
        videoRef.value = response.data.data.video_url;
        videoPoster.value = response.data.data.snapshot_url;
        credits.value = response.data.credits;
        alert("Tạo video thành công!");
    } catch (error) {
        console.error("Error creating video:", error);
        alert("Tạo video thất bại!");
    }
    isLoading.value = false;
};

const resetFormData = () => {
    recordedFileName.value = "";
    form.value.reset();
};

const isCreateContent = ref(false)
const createContent = async () => {
    if (!videoDescription.value) {
        toast.error("Vui lòng nhập nội dung mô tả video")
        return;
    }
    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        return; // Dừng nếu không đủ credit
    }
    isLoading.value = true;
    // resetFormData();
    try {
        const response = await axios.post(route("creatomate.create-content"), {
            description: videoDescription.value,
            structure: props.template.structure,
        });

        if (response.data && response.data.result) {
            template.value = JSON.parse(response.data.result);

            Object.keys(template.value.composition).forEach((key) => {
                if (template.value.composition[key].type === 'video' ||
                    template.value.composition[key].type === 'image' ||
                    template.value.composition[key].type === 'audio') {
                    return;
                }

                let value = template.value.composition[key].value;
                const maxLength = template.value.composition[key]?.length;
                if (maxLength && value.length > maxLength) {
                    value = value.slice(0, maxLength);
                }

                formData[key] = {
                    value: value,
                    type: template.value.composition[key].type,
                    length: maxLength || 50,
                };
            });
            sortData(template.value.composition);
        }

        credits.value = response.data.credits;
        console.log("Content created:", response.data);
        isCreateContent.value = true;
        toast.success('Tạo nội dung thành công.')
    } catch (error) {
        console.error("Error creating content:", error);
        alert("Tạo nội dung thất bại!");
    }
    isLoading.value = false;
};

const createPost = (imageUrl) => {
    if (imageUrl) {
        let image = {
            s3_url: imageUrl,
            type: 'video',
        };
        localStorage.setItem("aiImage",JSON.stringify(image) );
        window.location.href = route('calendar');
    }
};

const prepareDeleteFile = (id) => {
    videoDeleteId.value = id;
    showConfirmModal.value = true;
};
const confirmDelete = async () => {
    isLoading.value = true;

    try {
        const response = await axios.post(
            route("creatomate.delete", { id: videoDeleteId.value })
        );
        if (response.status === 200) {
            isLoading.value = false;
            window.location.reload();
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
            share_linkable_type: 'CreatomateVideo',
        });

        shareLink.value = route("share-link.show", { 'token': response.data.data.link_token })
        openShareLink();

    } catch (error) {
        toast.error("Chia sẻ tin thất bại");
    }
};

const downloadVideo = async () => {
    if (!videoRef.value) {
        alert("Chưa có video");
        return;
    }
    var url = route(("ai-video.downloadFile"), {url:videoRef.value, name:"video.mp4"})
    window.location.href = url
};

const isRecording = ref(false);
const isRecordingSuggestion = ref(false);
const recordingKey = ref(null);
let mediaRecorder = null;
const audioChunks = ref([]);
const recordedAudioBlob = ref(null);
let stream = ref(null);
const audioBlob = ref(null);
const audioUrl = ref(null);
let device = ref(null);

const startRecording = async (key) => {
    if (!isRecording.value) {
        try {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                mediaRecorder.stop();
                stream.getTracks().forEach((track) => track.stop());
            }

            stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(stream);

            mediaRecorder.addEventListener("dataavailable", (event) => {
                audioChunks.value.push(event.data);
            });

            mediaRecorder.addEventListener("stop", () => {
                formData[key].value = null;
                const blob = new Blob(audioChunks.value, { type: "audio/mp3" });
                recordedAudioBlob.value = blob;
                audioPreviewKey.value += 1;
                formData[key].value = new File([blob], "recording.mp3", { type: "audio/mp3" });
                recordedFileName.value = formData[key].value.name;
                uploadedAudioPreview.value = URL.createObjectURL(blob);
            });

            mediaRecorder.onstart = () => {
                audioChunks.value = [];
            };
            mediaRecorder.start();
            isRecording.value = true;
            recordingKey.value = key;
            isUpload.value = false;
        } catch (error) {
            console.error("Lỗi khi ghi âm:", error);
            isRecording.value = false;
            alert("Không thể bắt đầu ghi âm. Vui lòng kiểm tra micro!");
        }
    } else {
        isRecording.value = false; // Dừng ghi âm
        mediaRecorder.stop(); // Kết thúc ghi âm
        stream.getTracks().forEach((track) => track.stop()); // Dừng tất cả các tracks
    }
};

const startRecordingSuggestion = async (key = null) => {
    console.log(key)
    if (!isRecordingSuggestion.value) {
        // Nếu chưa ghi âm
        try {
            isRecordingSuggestion.value = true; // Bắt đầu ghi âm
            device = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(device);

            // Khi có dữ liệu âm thanh
            mediaRecorder.addEventListener("dataavailable", (event) => {
                audioChunks.value.push(event.data);
            });
            // Khi dừng ghi âm
            if(!key) {
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
                        const response = await axios.post('/speech-to-text', formData, {
                            headers: { "Content-Type": "multipart/form-data" },
                        });
                        // Xử lý văn bản trả về từ API
                        console.log("Transcription Text:", response);
                        videoDescription.value = response?.data?.text || 'Vui lòng thử lại';
                    } catch (error) {
                        toast.error("Không nhận diện được giọng nói, vui lòng thử lại!");
                        console.error("Error in Speech-to-Text:", error);
                    } finally {
                        // isTranscribing.value = false;
                    }

                    isRecordingSuggestion.value = false;
                });
            } else {
                mediaRecorder.addEventListener("stop", async () => {
                    audioBlob.value = new Blob(audioChunks.value, { type: "audio/mp3" });
                    audioUrl.value = URL.createObjectURL(audioBlob.value);

                    // Tạo FormData và gửi yêu cầu API
                    const formDataSuggest = new FormData();

                    // Sửa lại: Gói Blob thành File object trước khi thêm vào FormData
                    const file = new File([audioBlob.value], "audio.mp3", { type: "audio/mp3" });
                    formDataSuggest.append("audio", file);

                    // isTranscribing.value = true;
                    try {
                        const response = await axios.post('/speech-to-text', formDataSuggest, {
                            headers: { "Content-Type": "multipart/form-data" },
                        });

                        formData[key].value = response?.data?.text || "";

                    } catch (error) {
                        toast.error("Không nhận diện được giọng nói, vui lòng thử lại!");
                        console.error("Error in Speech-to-Text:", error);
                    } finally {
                        // isTranscribing.value = false;
                    }

                    isRecordingSuggestion.value = false;
                });
            }

            // Bắt đầu ghi âm
            mediaRecorder.onstart = () => {
                audioChunks.value = []; // Xóa các đoạn âm thanh trước đó
            };

            mediaRecorder.start(); // Bắt đầu ghi
        } catch (error) {
            console.error("Lỗi khi bắt đầu ghi âm:", error);
            isRecordingSuggestion.value = false; // Trở lại trạng thái chưa ghi âm nếu có lỗi
        }
    } else {
        // Nếu đang ghi âm
        isRecordingSuggestion.value = false; // Dừng ghi âm
        mediaRecorder.stop(); // Kết thúc ghi âm
        device.getTracks().forEach((track) => track.stop()); // Dừng tất cả các tracks
    }
};
const containVideo = ref(false);
const sortData = (temp) => {
    let aVideo = [];
    let aImage = [];
    let aAudio = [];
    let aText = [];
    let step = 0;
    Object.keys(temp).forEach((key) => {
        const item = temp[key];
        if (item['type'] === 'video') {
            aVideo.push([key, item]);
            containVideo.value=true;
            step += 1;
        } else if (item['type'] === 'image') {
            aImage.push([key, item]);
            step += 1;
        } else if (item['type'] === 'audio') {
            aAudio.push([key, item]);
            step += 1;
        } else if (item['type'] === 'text') {
            aText.push([key, item]);
        }
    });

    const sortedComposition = [
        ...aVideo,
        ...aImage,
        ...aAudio,
        ...aText
    ];
    finalStep.value = step;
    console.log("Sorted Composition:", sortedComposition);

    let objectStructure = {};

    sortedComposition.forEach((element, index) => {
        element[1]['index'] = index;
        objectStructure[element[0]] = element[1];
    });

    template.value.composition = objectStructure;

    console.log("Updated Composition:", template.value.composition);
}
watch(videoDescription, (newContent) => {
    if (newContent.length > 2000) {
        videoDescription.value = newContent.slice(0, 2000);
    }
});

onMounted(() => {
    sortData(template.value.composition);
});

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
    width: 60px;
    height: 60px;
    border-radius: 50%;
    /* background: #50CDDD; */
    /* box-shadow: 0px 0px 80px #0084f9; */
    position: absolute;
}

@keyframes pulseMic {
    0% {
        transform: scale(0);
        opacity: 0;
        border: 65px solid #0b3082;
    }

    50% {
        border: solid #a3ffc2;
        opacity: 0.8;
    }

    90% {
        transform: scale(3.2);
        opacity: 0.2;
        border: 3px solid #2e3cff;
    }

    100% {
        transform: scale(3.3);
        opacity: 0;
        border: 1px solid #7a89ff;
    }
}

.object-input-mic {
    display: flex;
    flex: 0 1 100%;
    justify-content: center;
    align-items: center;
    align-content: stretch;
}

.outline-input-mic {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: 8px solid #b5a4a4;
    animation: pulseMic 3s;
    animation-timing-function: ease-out;
    animation-iteration-count: infinite;
    position: absolute;
}

.button-input-mic {
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

.bg-primary-color {
        background-color:#2C75E3;
        color:#fff;
    }
</style>
