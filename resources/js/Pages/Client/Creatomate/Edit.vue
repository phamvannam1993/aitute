<template>
    <Head title="Âm Nhạc - AI 3 GỐC - Cộng đồng AI tử tế" />
    <div class="relative w-full mx-auto bg-[url('../../public/assets/img/aiwow/layout_background.png')] bg-cover bg-center min-h-screen p-5 lg:p-10 text-xs lg:text-sm mb-20">
        <div class="flex items-center justify-between mt-8 px-[10px]">
            <div class="flex items-center space-x-2">
                <a :href="route('home.index')">
                    <img src="/assets/img/admin/icon_left_home.png" alt="Trang chủ" class="w-auto h-[19px]" />
                </a>
                <span class="font-medium text-[#2C75E3]"> / Tạo video</span>
            </div>
            <Credit :credits="credits" />
        </div>
        <MainMenu />
        <div class="w-full text-black px-3">
            <p class="block font-medium text-3xl mb-1 uppercase">{{ props.template.title }}</p>
            <p class="block font-medium text-lg mb-1 uppercase">Tạo video theo các bước sau:</p>
            <div class="grid gap-cols-1 lg:grid-cols-2 gap-8 mt-8">
                <div class="grid grid-cols-1 w-full">
                    <form ref="form" @submit.prevent="handleSubmit" class="bg-white rounded-md p-4 shadow-md">
                        <div v-for="(field, key) in template.composition" :key="key" class="mb-4">
                            <div v-if="field.type === 'image'">
                                <div class="flex justify-between md:flex-row flex-col md:items-center gap-4 text-start mb-4">
                                    <label :for="key" class="block font-bold text-base capitalize text-nowrap">
                                    Bước {{ field.index +1 + ': Chọn' }} <span class="lowercase">{{ field.label }}</span>
                                    </label>
                                    <input
                                        type="file"
                                        :id="key"
                                        @change="handleFileUpload($event, key)"
                                        accept=".jpg,.png,.jpeg"
                                        class="hidden"
                                        :ref="'fileInputImage-' + key"
                                    />
                                    <button
                                        type="button"
                                        @click="$refs['fileInputImage-' + key][0].click()"
                                        class="self-center block min-w-36 w-fit text-[15px] rounded-[10px] text-sm font-semibold text-white bg-green-600 py-3 px-4 border-0">
                                        <span class="line-clamp-1 w-full text-center">
                                            {{ 'Chọn ảnh' }}
                                        </span>
                                    </button>
                                </div>

                                <!-- Hiển thị ảnh đã upload -->
                                <div v-if="imageUrls[key]" class="mt-4">
                                    <img :src="imageUrls[key]" alt="Uploaded Image" class="max-w-full h-auto rounded mx-auto" />
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
                            <div v-else-if="field.type === 'audio'">
                                <label :for="key" class="block font-bold text-base capitalize">
                                    Bước {{ field.index +1 + ': Chọn' }} <span class="lowercase">{{ field.label }}</span>
                                    </label>
                                <input :id="key" type="file" accept="audio/*" class="hidden" :ref="'fileInputAudio-' + key"  @change="handleFileUpload($event, key)" />
                                <div class="flex flex-col md:flex-row gap-2 w-full items-center justify-between md:justify-center my-4">
                                    <button type="button"  @click="$refs['fileInputAudio-' + key][0].click()"
                                        class="flex items-center w-full md:w-1/3 justify-center gap-2 px-3 py-2 bg-black/50 hover:bg-black/70 text-white rounded-lg backdrop-blur-sm transition-colors">
                                        <img src="/assets/img/upload.png" class="size-6 md:size-5 xl:size-6" />
                                        <span class="text-[12px] md:hidden xl:block xl:text-[16px]">Tải lên</span>
                                    </button>
                                </div>
                                <div v-if="formData[key].value" class="text-sm text-gray-600 mt-2">
                                    <audio controls class="w-full md:w-[70%] mx-auto" ref="audioPreview"
                                        :key="audioPreviewKey">
                                        <source
                                            :src="uploadedAudioPreview"
                                            type="audio/wav">
                                        Browser không hỗ trợ audio.
                                    </audio>
                                </div>
                                <div class="flex relative space-x-4 my-4 mt-12">
                                    <div class="object-mic relative ml-auto">
                                        <div v-if="isRecording" class="outline-mic"></div>
                                        <div v-if="isRecording" class="outline-mic" id="delayed"></div>
                                        <div v-if="isRecording" class="button-mic"></div>
                                        <button class="button-mic flex justify-center items-center h-20 w-20 bg-gray-400 disabled:opacity-20" id="circlein" type="button" @click="startRecording(key)" :disabled="disabledRecord">
                                            <svg class="h-6" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 1000 1000" enable-background="new 0 0 1000 1000" xml:space="preserve" style="fill: rgb(0 13 20)">
                                                <g>
                                                    <path d="M500,683.8c84.6,0,153.1-68.6,153.1-153.1V163.1C653.1,78.6,584.6,10,500,10c-84.6,0-153.1,68.6-153.1,153.1v367.5C346.9,615.2,415.4,683.8,500,683.8z M714.4,438.8v91.9C714.4,649,618.4,745,500,745c-118.4,0-214.4-96-214.4-214.4v-91.9h-61.3v91.9c0,141.9,107.2,258.7,245,273.9v124.2H346.9V990h306.3v-61.3H530.6V804.5c137.8-15.2,245-132.1,245-273.9v-91.9H714.4z" />
                                                </g>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-center text-black mt-12">
                                    <p v-if="!isRecording">Hoặc bấm vào đây để thu âm thuyết minh</p>
                                    <p v-else>Bấm vào MIC để dừng thu âm</p>
                                </div>
                            </div>
                            <div v-else-if="field.type === 'video'">
                                Bước {{ field.index +1 + ': ' + field.label }}
                                <input :id="key" type="file" accept="video/*" class="w-full p-2 border border-gray-300 rounded" @change="handleFileUpload($event, key)" />
                            </div>
                        </div>
                        <div class="mb-4 w-full">
                            <label for="video-description" class="mb-1 block font-bold text-base capitalize">Bước {{ finalStep +1 }}: Nhập nội dung mô tả video</label>
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
                                        @click="startRecordingSuggestion"
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
                            <div class="mt-4 flex justify-end">
                                <button type="button" @click="createContent" class="bg-green-500 text-white py-3 px-4 rounded-[10px] hover:bg-green-600 w-[200px] text-lg font-bold">Tạo nội dung</button>
                            </div>
                        </div>
                        <div v-for="(field, key) in template.composition" :key="key" class="mb-4">
                            <div v-if="field.type === 'text'">
                                <div class="flex justify-between items-center">
                                    <label :for="key" class="block font-bold text-base capitalize">
                                        {{ field.label }}
                                    </label>
                                    <div v-if="formData[key].type == 'text'" class="flex justify-start items-center gap-2">
                                        <button type="button" class="text-base text-blue-500 font-bold" @click="toggleEdit(key)">
                                            {{ isEditing[key] ? "Bỏ chọn màu" : `Chọn màu chữ` }}
                                        </button>
                                        <input v-if="isEditing[key]" type="color" v-model="formData[`${key}.fill_color`]" class="w-[36px] h-[36px] cursor-pointer border border-gray-300 rounded mt-2" />
                                    </div>
                                </div>
                                <textarea rows="2" :id="key" v-model="formData[key].value" required type="text" class="w-full p-2 border border-gray-300 rounded" placeholder="Nhập nội dung" />
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center justify-end">
                            <label for="submit-form-button" class="mb-1 block font-bold text-base capitalize">Bấm vào đây để tạo video </label>
                            <button id="submit-form-button" type="submit" class="bg-blue-500 ml-4 text-white py-3 px-4 rounded-[10px] hover:bg-blue-600 w-[200px] text-lg font-bold ">Tạo video</button>
                        </div>
                    </form>
                </div>
                <div>
                    <div class="bg-white rounded-[10px] md:rounded-[25px] w-full p-[12px] md:p-[25px]">
                        <div class="flex justify-center items-center mb-4">
                            <video v-if="videoRef" :src="videoRef" alt="video-generate-by-AI" controls="" :poster="videoPoster" preload="auto" class="w-full h-auto md:max-w-[500px]  object-cover" />
                            <div v-else class="bg-gray-300 w-full md:w-[400px] xl:w-[500px] h-[300px] md:h-[400px] xl:h-[500px] rounded-2xl showLoaderVideo">
                                <div v-if="isLoadingVideo">
                                    <div class="loaderVideo"></div>
                                    <p class="text-loading">Video đang được tạo. Vui lòng chờ !</p>
                                </div>
                            </div>
                        </div>
                        <div v-if="videoRef"  :class="{ 'opacity-50 pointer-events-none': !videoRef }" class="flex justify-evenly gap-4 items-center flex-wrap">
                            <button @click="createPost(videoRef)" class="text-[#2C75E3] flex lg:flex-row flex-col justify-start items-center gap-2 hover:text-blue-600 transition-colors duration-300 mx-auto lg:mx-0">
                                <img src="/assets/svgs/edit-icon-new.svg" class="h-[26px] w-[26px]" />
                                <span class="lg:text-sm lg:block font-medium bg-primary-color hover:scale-105 px-2 lg:px-4 py-1.5 text-xs rounded-lg">Tạo bài đăng</span>
                            </button>
                            <!-- <button @click="shareAIGeneratedMedia(resultValue)" class="text-[#2C75E3] flex items-center gap-2 hover:text-[#1f5bb5] mx-auto lg:mx-0">
                                <img src="/assets/svgs/share-icon-2.svg" class="h-[26px] w-[26px]" />
                                <span class="font-medium">Chia sẻ</span>
                            </button> -->
                            <button @click="downloadVideo(videoRef)" class="text-[#2C75E3] flex lg:flex-row flex-col items-center gap-2 hover:text-[#1f5bb5] mx-auto lg:mx-0">
                                <img src="/assets/svgs/download-icon-new.svg" class="h-[26px] w-[26px]" />
                                <span class="lg:text-sm lg:block font-medium bg-primary-color hover:scale-105 px-2 lg:px-4 py-1.5 text-xs rounded-lg">Tải xuống</span>
                            </button>
                        </div>
                        <div class="flex justify-center mt-4 w-full btn-history">
                            <a :href="route('creatomate.history')" class="px-4 w-[180px] text-center py-2 bg-[#2C75E3] text-white font-bold  text-lg rounded-[10px]">Lịch sử</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="acceptUpgrade"/>
    <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup"
                    @buyCredit="handleBuyCredit"
                    @cancel="handleBuyCancel"
                    :currentCredit = "pageProps.auth.user.credit || 0"
                    :additionalCredit = "additionalCredit"
    />
    <Footer />
</template>
<script setup>
import Layout from "@/Layouts/Client/ClientLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import axios from "axios";
import MainMenu from "@/Components/MainMenu.vue";
import { ref, reactive, onMounted, computed } from "vue";
import Credit from "@/Components/Credit.vue";
import Footer from "../Home/Components/Footer.vue";
import Modal from '@/Components/Modal.vue';
import PopupAff from '@/Components/PopupAff.vue';
import {toast} from "vue3-toastify";
import CreditBuyModal from '../../../Components/ModalBuyCredit/BuyCredit.vue';
import Popup from '@/Components/Popup/Index.vue'

import VueCropper from 'vue-cropperjs';
import 'cropperjs/dist/cropper.css';

defineOptions({ layout: Layout });

const props = defineProps({
    credits: Number,
    template: Object,
    record: Object,
});
const credits = ref(props.credits);
const template = ref(JSON.parse(props.template.structure) || {});
const video = ref(JSON.parse(props.record.content) || {});
const videoDescription = ref('');
const videoRef = ref(props.record?.result);
const videoPoster = ref(props.record?.snapshot_url);
const isLoading = ref(false);
const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
const showConfirmModal = ref(false);
const videoDeleteId = ref(null);
const resultValue = ref(null);
const formData = reactive({});
const recordedFileName = ref("");
const form = ref(null);
const { props: pageProps } = usePage();
const auth = computed(() => pageProps.value.auth);
const finalStep = ref(0)
const imageUrls = reactive({});
const imageDescriptions = reactive({});
const imageInputs = reactive({});

const isDropdown = ref(false);
const isSongTab = ref(true);
const filteredSongs = ref(null)
const filteredMusics = ref(null)
const currentAudio = ref(null);
const currentAudioUrl = ref(null);
const audioPreviewKey = ref(0);
const uploadedAudioPreview = ref(null);
const videoResolution = ref(props.template.resolution || '1:1')
const isEditing = reactive({});

const isCropping = reactive({});
const cropper = ref(null);
const croppedImageUrls = reactive({});
const showAffKeyPopup = ref(false);
const popupMessage = ref('Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!')
const acceptUpgrade = ref(true);

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

Object.keys(template.value.composition).forEach((key) => {
    if (template.value.composition[key].type === 'audio' || template.value.composition[key].type === 'image' || template.value.composition[key].type === 'video') {
        formData[key] = {
            value: '',
            type: template.value.composition[key].type,
        };
    } else {
        formData[key] = {
            value: video.value[key],
            type: template.value.composition[key].type,
        };
    }
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
const toggleDropdown = () => {
    isDropdown.value = !isDropdown.value;
}
const toggleTab = (tab) => {
    if (tab === "song") {
        isSongTab.value = true;
    } else {
        isSongTab.value = false;
    }
};
const fetchSong = async () => {
    const response = await axios.get(route('text-to-song.getAllMusic'));
    filteredSongs.value = response.data.data;
}
const fetchMusic = async () => {
    const response = await axios.get(route('text-to-music.getAllMusic'));
    filteredMusics.value = response.data.data;
}
function toggleAudio(key, audioUrl) {
    if (currentAudio.value && currentAudioUrl.value === audioUrl) {
        // Nếu audio đang phát là audio hiện tại, thì dừng
        currentAudio.value.pause();
        currentAudio.value = null;
        currentAudioUrl.value = null;
    } else {
        // Nếu có audio khác đang phát, tắt nó
        if (currentAudio.value) {
            currentAudio.value.pause();
        }
        // Phát audio mới
        currentAudio.value = new Audio(audioUrl);
        currentAudio.value.play();
        currentAudioUrl.value = audioUrl;
    }
}
const addSong = async (key ,url) => {
    try {
        formData[key].value = await convertURLtoFile(url, 'audio');
        uploadedAudioPreview.value = url;
        audioPreviewKey.value += 1;
    } catch (error) {
        console.log('Error when adding song!',error);
    }
};
const handleFileUpload = (event, key) => {
    const file = event.target.files[0];
    if (file) {
        imageInputs[key] = false;
        formData[key].value = file;
        if (formData[key].type == 'audio') {
            uploadedAudioPreview.value = URL.createObjectURL(file);
            audioPreviewKey.value += 1;
            recordedFileName.value = file.name;
        }
        imageUrls[key] = URL.createObjectURL(file);
        isCropping[key] = true;
    }
};

const calculateDimensions = (aspectRatio) => {
  const [width, height] = aspectRatio.split(':').map(Number);
  const fixedHeight = 1360;
  const calculatedWidth = (fixedHeight * width) / height;
  return { width: calculatedWidth, height: fixedHeight };
};

const handleSubmit = async () => {
    for (const key in formData) {
        if (formData[key].value === "" || formData[key].value === null || formData[key].value === undefined) {
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
    formDataToSubmit.append("creatomate_video_id", props.record.id);

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
        videoRef.value = response.data.data.video_url;
        videoPoster.value = response.data.data.snapshot_url;
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

const isCreateContent = ref(false)
const createContent = async () => {
    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        return; // Dừng nếu không đủ credit
    }
    isLoading.value = true;
    resetFormData();
    try {
        const response = await axios.post(route("creatomate.create-content"), {
            description: videoDescription.value,
            structure: props.template.structure,
        });

        if (response.data && response.data.result) {
            template.value = JSON.parse(response.data.result);

            Object.keys(template.value.composition).forEach((key) => {
                formData[key] = {
                    value: template.value.composition[key].value,
                    type: template.value.composition[key].type,
                };
            });
            sortData(template.value.composition);
        }

        console.log("Content created:", response.data);
        isCreateContent.value = true;
        alert("Tạo nội dung thành công!");
    } catch (error) {
        console.error("Error creating content:", error);
        alert("Tạo nội dung thất bại!");
    }
    isLoading.value = false;
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
const handleDescriptionChange = (key, value) => {
    imageDescriptions[key] = value;
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
            share_linkable_type: 'AIGeneratedMedia',
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
const recordingKey = ref(null);
let mediaRecorder = null;
const audioChunks = ref([]);
const recordedAudioBlob = ref(null);
let stream = ref(null);

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
    box-shadow: 0px 0px 80px #0084f9;
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
</style>
