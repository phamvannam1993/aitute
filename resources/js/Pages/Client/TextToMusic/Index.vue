<template>

    <Head title="Âm Nhạc - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData">
        <div class="w-full bg-white text-black rounded-[25px] min-w-2/3 py-2 px-3">
            <div class="flex justify-start items-center gap-2">
                <img class="bg-[#D4D4D4] p-2 rounded-2xl" src="/assets/img/aiwow/robot.png" />
                <h2 class="text-black font-bold text-[30px] leading-[32px] line-clamp-1">Âm nhạc từ văn bản</h2>
            </div>
            <p class="text-xs lg:text-sm text-[#092A99] font-bold mb-2">Thực hiện theo các bước sau:</p>
            <form @submit.prevent="submit">
                <div class="w-full pb-12">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="md:w-1/2">
                            <div class="">
                                <p class="font-semibold line-clamp-1 mb-4">
                                    {{ ai_assistant?.description }}
                                </p>
                            </div>

                            <div class="border border-[#5FB2FF] p-2 rounded-lg mb-4 md:mb-0">
                                <div>
                                    <label for="music-description"
                                        class="text-xs lg:text-sm flex items-center gap-1 mb-1 w-full lg:w-fit">
                                        <div
                                            class="flex items-center py-1 px-2 rounded-2xl text-white bg-primary-color h-fit">
                                            <img class="h-2 w-4" src="/assets/img/aiwow/right_arrow.png" />
                                            <p class="text-xs lg:text-sm font-semibold">Bước 1</p>
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <p class="text-xs lg:text-sm font-semibold">Mô tả nội dung âm nhạc</p>
                                        </div>
                                    </label>
                                    <div  class="relative">
                                        <SuggestionPrompt v-model="content" :type="'music'" :screenId="9" />
                                        <textarea v-model="content" id="music-description" rows="3"
                                            placeholder="Nhập chủ đề âm nhạc..."
                                            class="resize-none w-full border-[#ccc] rounded-lg mt-2"></textarea>
                                        <div class="object-mic relative ml-auto">
                                            <div v-if="isRecording"
                                                class="outline-mic right-3 bottom-3 flex items-center justify-center"></div>
                                            <div v-if="isRecording"
                                                class="outline-mic right-3 bottom-3 flex items-center justify-center"
                                                id="delayed"></div>
                                            <div v-if="isRecording"
                                                class="button-mic right-3 bottom-3 flex items-center justify-center"></div>
                                            <button
                                                class="button-mic icon-mic absolute right-3 bottom-3 flex items-center justify-center"
                                                @click="startRecording" type="button">
                                                <svg class="h-6 w-6" version="1.1" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 1000 1000">
                                                    <g>
                                                        <path
                                                            d="M500,683.8c84.6,0,153.1-68.6,153.1-153.1V163.1C653.1,78.6,584.6,10,500,10c-84.6,0-153.1,68.6-153.1,153.1v367.5C346.9,615.2,415.4,683.8,500,683.8z M714.4,438.8v91.9C714.4,649,618.4,745,500,745c-118.4,0-214.4-96-214.4-214.4v-91.9h-61.3v91.9c0,141.9,107.2,258.7,245,273.9v124.2H346.9V990h306.3v-61.3H530.6V804.5c137.8-15.2,245-132.1,245-273.9v-91.9H714.4z" />
                                                    </g>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div :class="content.length >= 100 ? 'text-red-600' : 'text-gray-700'"
                                        class="text-end ">{{ content.length }}/100</div>
                                </div>

                                <label for="music-description"
                                    class="text-xs lg:text-sm flex items-center gap-1 mb-1 w-full lg:w-fit">
                                    <div
                                        class="flex items-center py-1 px-2 rounded-2xl text-white bg-primary-color h-fit">
                                        <img class="h-2 w-4" src="/assets/img/aiwow/right_arrow.png" />
                                        <p class="text-xs lg:text-sm font-semibold">Bước 2</p>
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <p class="text-xs lg:text-sm font-semibold">Độ dài âm thanh (giây)</p>
                                    </div>
                                </label>
                                <div class="mb-2 mt-3">
                                    <input v-model="duration" class="w-full border-[#ccc] rounded-lg " placeholder="60"
                                        type="text">
                                </div>
                                <div class="flex items-center flex-col  justify-between">
                                    <div class="mb-2 mt-3">
                                        <input type="checkbox" id="createFromExistingFile"
                                            v-model="createFromExistingFile" class="mr-2" />
                                        <label for="createFromExistingFile" class="text-xs lg:text-sm font-semibold">
                                            Hoặc tạo mới dựa trên tập tin có sẵn
                                        </label>
                                    </div>

                                    <!-- Chọn File âm thanh - Hiển thị khi createFromExistingFile được chọn -->
                                    <div v-if="createFromExistingFile">
                                        <div class="flex items-center space-x-4 rounded-lg">
                                            <button type="button" @click="triggerFileInput"
                                                class="flex items-center px-5 py-1.5 justify-center gap-2 bg-transparent text-primary-color rounded-xl backdrop-blur-sm transition-colors hover:scale-105 border-2 border-primary-color">
                                                <img src="/assets/svgs/aiwow/upload.svg"
                                                    class="size-6 md:size-5 xl:size-6" />
                                                <span class="text-[15px] font-bold sm:hidden md:block">Chọn tài
                                                    liệu</span>
                                            </button>

                                        </div>
                                    </div>
                                </div>
                                <div class=" text-center">
                                    <input type="file" class="hidden " ref="fileInput" @change="handleFileUpload" />
                                    <p v-if="file" class="line-clamp-1 pr-2 text-blue-500 text-xs lg:text-sm">{{ file.name }}</p>
                                </div>
                                <div class="flex flex-col justify-between gap-2 mt-4">
                                    <label for="music-description" class="text-xs lg:text-sm flex items-center gap-1 w-full lg:w-fit">
                                        <div class="flex items-center py-1 px-2 rounded-2xl text-white bg-primary-color h-fit">
                                            <img class="h-2 w-4" src="/assets/img/aiwow/right_arrow.png" />
                                            <p class="text-xs lg:text-sm font-semibold">Bước 3</p>
                                        </div>
                                        <p class="text-xs lg:text-sm font-semibold">Bấm vào đây</p>

                                    </label>
                                    <button @click="sendData(step, index)" :disabled="isLoading"
                                        class=" text-white py-2 rounded-xl w-full text-center bg-[#2C75E3] text-xs lg:text-sm">
                                        <span v-if="!isLoading">Tạo âm nhạc</span>
                                        <div v-else role="status" class="w-full">
                                            <svg aria-hidden="true"
                                                class="w-6 h-6 mx-auto text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                                                viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                                    fill="currentColor" />
                                                <path
                                                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                                    fill="currentFill" />
                                            </svg>
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </button>
                                </div>

                            </div>
                        </div>
                        <div class="w-full">
                            <div class="w-full p-4 rounded-lg relative">
                                <p class="text-primary-color italic font-normal text-xs lg:text-sm mb-1">Hiển thị kết quả</p>
                                <!-- Waveform -->
                                <div ref="waveform" class="rounded-2xl border-2 mb-4 p-1"></div>

                                <!-- Audio Player -->
                                <audio controls class="w-full rounded-md shadow-sm" ref="audioPlayer" @play="handlePlay"
                                    @pause="handlePause" @timeupdate="handleTimeUpdate" @ended="handleEnd">
                                    <source :src="resAudio" type="audio/mpeg" />
                                    Trình duyệt của bạn không hỗ trợ phần tử âm thanh.
                                </audio>
                                <div v-if="resAudio" class="flex justify-end gap-4 my-2">
                                    <button @click="shareAIGeneratedMedia(musicId)" class="flex gap-2 items-center">
                                        <img src="/assets/svgs/aiwow/share.svg" class="w-6 h-6" />
                                        <span class="font-medium hover:scale-105 rounded-md">Chia sẻ</span>
                                    </button>
                                    <button class="flex gap-2 items-center" @click="downloadMusic()">
                                        <img src="/assets/svgs/aiwow/download.svg" alt="Download" class="w-6 h-6" />
                                        <span class="font-medium hover:scale-105 rounded-md">Tải xuống</span>
                                    </button>

                                </div>
                                <!-- Lyrics -->
                                <div v-if="resLyric" class="text-center h-60 overflow-y-auto mt-4">
                                    <h2 class="font-bold">Lyrics</h2>
                                    <p v-html="formatLyrics(resLyric)"></p>
                                </div>
                            </div>
                            <div class="flex items-center justify-end">
                                <a :href="route('text-to-music.history')"
                                    class="px-4 py-3 bg-[#2C75E3] text-white font-semibold rounded-[15px]  flex justify-center items-center gap-1 w-1/2">Lịch
                                    sử</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </Layout>

    <Popup 
        v-if="showConfirmModal"
        title="Cảnh báo !"
        message="Bạn có chắc chắn muốn xóa bài hát này không?"
        cancelButtonText="Huỷ"
        acceptButtonText="Xoá"
        @cancel="cancelDelete"
        @accept="confirmDelete"
    />

    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink" />
    </Modal>
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true"
        :acceptUpgrade="acceptUpgrade" />
    <CreditModal :showCreditPopup="showCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleCancel" />
    <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleBuyCancel"
        :currentCredit="pageProps.auth.user.credit || 0" :additionalCredit="additionalCredit" />
</template>

<script setup>
import SuggestionPrompt from "@/Components/AIVirtual/SuggestionPrompt.vue";
import FormShareLink from '@/Components/FormShareLink.vue';
import Modal from '@/Components/Modal.vue';
import { Head, usePage } from "@inertiajs/vue3";
import axios from "axios";
import 'suneditor/dist/css/suneditor.min.css'; // Import CSS của
import { computed, onMounted, ref, watch } from "vue";
import { toast } from "vue3-toastify";
import Footer from "../Home/Components/Footer.vue";
import CreditModal from '../../../Components/ModalBuyCredit/Index.vue';
import CreditBuyModal from '../../../Components/ModalBuyCredit/BuyCredit.vue';
import PopupAff from '@/Components/PopupAff.vue';
import Layout from "@/Layouts/Client/AiLayout.vue";
import Popup from '@/Components/Popup/Index.vue'

import WaveSurfer from 'wavesurfer.js'

const props = defineProps({
    ai_assistant: Object,
    job: Object,
    operation: Object,
    credits: Number,
    credits: Number,
});
const breadcrumbsData = [
    { text: "Âm nhạc", link: "text-to-music.index" },
];
const { props: pageProps } = usePage();
const auth = computed(() => pageProps.value.auth);
const additionalCredit = ref(0);
const isShowShareLinkModal = ref(false);
const shareLink = ref(null);
const credits = ref(props.credits);
const showAffKeyPopup = ref(false);
const duration = ref('');

const ai_assistant = ref(props.ai_assistant);
const musicId = ref(null);
const fileInput = ref(null);

let messageIdToDelete = ref(null);
const message_id = ref("");
const message = ref("");
const css = ref("");
const isLoading = ref(false);
const isDeleteLoading = ref(false);
const history = ref([]);
const content = ref("");
const file = ref(null);
const createFromExistingFile = ref(false);

const resAudio = ref(null);
const resLyric = ref('');
const waveform = ref(null);
const wavesurfer = ref(null);
const isPlaying = ref(false);
const durationSuffer = ref('0:00');
const currentTime = ref("0:00");

onMounted(() => {

    wavesurfer.value = WaveSurfer.create({
        container: waveform.value,
        waveColor: '#4B9CFF',
        progressColor: '#1D78FF',
        barWidth: 3,
        barGap: 1,
        cursorColor: '#FF5500',
        height: 150,
        responsive: true,
    });

    wavesurfer.value.load('/assets/audio/default_music.wav');

    wavesurfer.value.on('ready', () => {
        durationSuffer.value = formatTime(wavesurfer.value.getDuration());
    });

    wavesurfer.value.on('audioprocess', () => {
        currentTime.value = formatTime(wavesurfer.value.getCurrentTime());
    });

    wavesurfer.value.on('finish', () => {
        isPlaying.value = false;
    });

    audioPlayer.value.addEventListener('play', () => wavesurfer.play());
    audioPlayer.value.addEventListener('pause', () => wavesurfer.pause());
    audioPlayer.value.addEventListener('ended', () => wavesurfer.stop());
});

const popupMessage = ref('Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!')
const acceptUpgrade = ref(true)

const audioPlayer = ref(null);

const formatTime = (time) => {
    const minutes = Math.floor(time / 60);
    const seconds = Math.floor(time % 60).toString().padStart(2, '0');
    return `${minutes}:${seconds}`;
};

const handlePlay = () => {
    wavesurfer.value.play(audioPlayer.value.currentTime);
};

const handlePause = () => {
    wavesurfer.value.pause();
};

const handleEnd = () => {
    wavesurfer.value.stop();
};

const handleTimeUpdate = () => {
    if (wavesurfer.value && audioPlayer.value) {
        wavesurfer.value.seekTo(audioPlayer.value.currentTime / audioPlayer.value.duration);
    }
};

const downloadMusic = () => {
    if (resAudio.value) {
        // Tạo một thẻ <a> động để tải xuống
        const link = document.createElement('a');
        link.href = resAudio.value;
        link.download = 'audio-file.mp3'; // Tên file bạn muốn tải về
        link.click();
        // Dọn dẹp thẻ <a> sau khi sử dụng
        link.remove();
    } else {
        alert('Không có tệp âm thanh để tải xuống!');
    }
};

const shareAIGeneratedMedia = async (id) => {
    try {
        const response = await axios.post(route("share-link.store"), {
            share_linkable_id: id,
            share_linkable_type: 'Music',
        });
        shareLink.value = route("share-link.show", { token: response.data.data.link_token })
        openShareLink();
    } catch (error) {
        toast.error("Chia sẻ ảnh thất bại");
    }
};

const triggerFileInput = () => {
    if (fileInput.value) {
        fileInput.value.click();
    }
};

const openShareLink = () => {
    isShowShareLinkModal.value = true;
};

const closeShareLink = () => {
    isShowShareLinkModal.value = false;
};

const showConfirmModal = ref(false);
const confirmDelete = async () => {
    let response;
    isDeleteLoading.value = true;

    response = await axios.post(
        route("ai-text.delete-text", { id: messageIdToDelete.value }),
        { id: messageIdToDelete.value }
    );
    if (response.status === 200) {
        const index = history.value.data.findIndex(
            (item) => item.id === messageIdToDelete.value
        );
        if (index !== -1) {
            history.value.data.splice(index, 1);
        }
        toast.success("Xóa thành công");
    }
    isDeleteLoading.value = false;
    showConfirmModal.value = false;
};

const cancelDelete = () => {
    showConfirmModal.value = false;
};

const createPost = (message) => {
    if (message) {
        localStorage.setItem("postContent", message);
        window.location.href = "/page-management";
    } else {
        alert("Vui lòng tạo một hình ảnh trước khi tạo bài đăng.");
    }
};
let showCreditPopup = ref(false);
const handleBuyCredit = () => {
    window.location.href = '/pricing';
};

const handleCancel = () => {
    showCreditPopup.value = false;
};

const showCreditModal = () => {
    showCreditPopup.value = true;
};

let isUpdatingFromAPI = false;
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
        formData.append('type', 'text_to_music');

        // Gọi API để kiểm tra credit của user
        const response = await axios.post(route("credit.check_credit"), formData);
        if (response.data.success) {
            // Đủ credit để tiếp tục
            return true;
        } else if (response.data.is_vip_expired) {
            showAffKeyPopup.value = true
            if (response.data.affCode == 'aff_trial') {
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
const sendData = async () => {
    isLoading.value = true;
    try {
        const hasEnoughCredit = await checkCredit();
        if (!hasEnoughCredit) {
            isLoading.value = false;
            return; // Dừng nếu không đủ credit
        }
        isUpdatingFromAPI = true;

        // Kiểm tra nếu chưa có nội dung
        if (!content.value) {
            toast.error('Vui lòng nhập nội dung sản phẩm');
            isLoading.value = false;
            return;
        }
        resAudio.value = null;

        const formData = new FormData();
        formData.append('content', content.value);
        formData.append('duration', duration.value || 60);
        formData.append('continue', createFromExistingFile.value);  // độ dài âm thanh mặc định 60 giây nếu trống

        // Nếu người dùng tick "Tạo mới dựa trên file có sẵn" và có file
        if (file.value) {
            formData.append('audioFile', file.value);
        }

        // Gửi request tới backend
        const response = await fetch(route("ai-text.music"), {
            method: 'POST',
            body: formData,
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        resAudio.value = data.audio.url;
        wavesurfer.value.load(data.audio.url);
        if (audioPlayer.value) {
            audioPlayer.value.load();
        }
        musicId.value = data.music_id;
        resLyric.value = data.lyrics;
        saveData();
    } catch (error) {
        // Xử lý lỗi chi tiết hơn
        if (error.response && error.response.status === 403) {
            console.log("error.response ", error.response);
            showCreditModal();
        } else if (error.response && error.response.status === 422) {
            const errors = error.response.data.errors;
            Object.values(errors).forEach(errorMessages => {
                if (Array.isArray(errorMessages)) {
                    errorMessages.forEach(error => toast.error(error));
                } else {
                    toast.error(errorMessages);
                }
            });
        } else {
            console.error("Đã xảy ra lỗi khi gửi dữ liệu:", error);
            toast.error(error.response?.data?.message || 'Đã xảy ra lỗi.');
        }
    } finally {
        isLoading.value = false;
        isUpdatingFromAPI = false;
    }
};

const formatLyrics = (lyrics) => {
    return lyrics.replace(/\n/g, '<br>');
}

const saveData = async (allowToast = true) => {
    try {
        const response = await axios.post(route("ai-text.store"), {
            id: message_id.value,
            ai_assistant: ai_assistant.value,
            message: message.value,
            css: css.value,
            media_link: media_link.value,
            prompt: content.value,
        });
        history.value.push(response.data.response.data);

        if (response) {
            if (allowToast) {
                toast.success("Lưu dữ liệu thành công");
            }
            return response.data.response.data;
        }

        return {};

    } catch (error) {
        console.log(error)
        return {};
    }
};
const checkContentLength = () => {
    if (content.value.length > 100) {
        content.value = content.value.slice(0, 100);
    }
}
const handleFileUpload = (event) => {
    const selectedFile = event.target.files[0];

    if (!selectedFile) {
        return;
    }

    // Chỉ định các định dạng file âm thanh hợp lệ
    const validFileTypes = ['audio/mpeg', 'audio/wav', 'audio/ogg'];

    if (!validFileTypes.includes(selectedFile.type)) {
        toast.error('File phải có định dạng: mp3, wav, ogg.');
        file.value = null;
        return;
    }

    // Giới hạn kích thước file không quá 10MB
    if (selectedFile.size > 10 * 1024 * 1024) {
        toast.error('File không được vượt quá 10MB.');
        file.value = null;
        return;
    }

    // Nếu tất cả điều kiện được đáp ứng, lưu file vào biến `file`
    file.value = selectedFile || null;
};

watch(createFromExistingFile, (newValue) => {
    if (!newValue) {
        file.value = null; // Đặt lại file về null khi checkbox bị bỏ chọn
    }
});
watch(content, (newContent) => {
    if (newContent.length > 100) {
        content.value = newContent.slice(0, 100);
    }
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
                    const response = await axios.post('/speech-to-text', formData, {
                        headers: { "Content-Type": "multipart/form-data" },
                    });
                    // Xử lý văn bản trả về từ API
                    console.log("Transcription Text:", response);
                    content.value = response?.data?.text || 'Vui lòng thử lại';
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
.p-editor {
    --p-editor-content-color: #000000;
    /* Đặt màu mặc định cho văn bản */
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
