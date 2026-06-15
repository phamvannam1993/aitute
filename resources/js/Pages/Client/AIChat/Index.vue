<template>
    <Head title="Chat - AI 3 GỐC - Cộng đồng AI tử tế" />
    <!-- <div
        class="flex flex-col min-h-screen bg-[url('../../public/assets/img/aiwow/layout_background.png')] bg-cover bg-center ">
        <div class="flex flex-col md:gap-16 pb-12 lg:gap-0 justify-between">
            <div class="flex items-center justify-between mt-8 px-[10px]">
                <div class="flex items-center space-x-2">
                    <a :href="route('home.index')">
                        <img src="/assets/img/admin/icon_left_home.png" alt="Trang chủ" class="w-auto h-[19px]" />
                    </a>
                    <span class="font-medium text-[#2C75E3]"> / Chat</span>
                </div>
                <Credit :credits="credits" />
            </div>
            <MainMenu />
            <div class="flex flex-col lg:flex-row">
                <div class="w-full px-5">
                    <div class="w-full bg-white rounded-[25px] min-w-2/3 py-2 px-3">
                        <div class="flex justify-start gap-2 items-center w-full border-b-2 pb-1">
                            <div class="bg-[#2C75E3] w-10 h-10 rounded-full flex justify-center p-1">
                                <img src="/assets/svgs/robot-chat.svg" />
                            </div>
                            <h2 class="font-semibold text-[#092A99] text-xl">
                                Xin chào! Hãy chat cùng AI<span class="text-[#FFB800]">WOW</span>
                            </h2>
                        </div>
                        <div ref="messagesContainer" class="p-4 h-[30vh] lg:h-[50vh] overflow-y-scroll">
                            <div class="text-black flex justify-center">
                                <button v-if="hasNextPage" @click="loadMoreMessage"
                                    class="text-center border-[1px] border-gray-300 px-4 py-2 w-fit rounded-full">
                                    <span v-if="isLoading">Đang tải...</span>
                                    <span v-else>Hiển thị tin nhắn cũ hơn</span>
                                </button>
                            </div>
                            <div v-for="(message, index) in messages" :key="index" class="my-4">
                                <div v-if="message.sender === 'user'"
                                    class="flex flex-row-reverse justify-start items-start gap-1">
                                    <img src="/assets/svgs/client-icon.svg" />
                                    <div
                                        class="text-white bg-[#2C75E3] px-[17px] py-[12px] rounded-[15px] pb-7 relative min-w-[100px]">
                                        <div v-if="message.file_url">
                                            <div v-if="
                                                message.file_type.includes('image')
                                            ">
                                                <img :src="message.file_url" class="mb-2 max-w-xs rounded-lg" />
                                            </div>
                                            <div v-else-if="
                                                message.file_type === 'video/webm'
                                            ">
                                                <audio controls class="mt-2 min-w-[200px]">
                                                    <source :src="message.file_url" type="audio/mpeg" />
                                                    Trình duyệt của bạn không hỗ trợ
                                                    audio.
                                                </audio>
                                            </div>
                                            <div v-else>
                                                <a :href="message.file_url" target="_blank" class="text-xs underline">
                                                    (Tải file về máy)
                                                </a>
                                            </div>
                                        </div>
                                        {{ message.content }}
                                        <div class="absolute bottom-1 right-2">
                                            <button class="text-white flex justify-start items-center gap-2" @click="
                                                copyToClipboard(message.content)
                                                ">
                                                <img src="/assets/svgs/copy-icon-white.svg" />
                                                <span class="text-xs">Copy</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div v-else class="flex justify-start items-start gap-1">
                                    <img src="/assets/img/robot-chat.png" />
                                    <div class="bg-[#EDEDED] px-[17px] py-[12px] rounded-[15px]">
                                        <div class="text-black pb-7 relative min-w-[330px]">
                                            <div class="break-words" v-html="message.content"></div>

                                            <div class="absolute bottom-1 w-full flex justify-between items-center">
                                                <div class="flex justify-center items-center gap-4">
                                                    <img src="/assets/svgs/like-icon.svg" />
                                                    <img src="/assets/svgs/dislike-icon.svg" />
                                                    <button class="text-[#2C75E3] flex justify-start items-center gap-2"
                                                        @click="
                                                            prepareDeleteMessage(
                                                                message.id
                                                            )
                                                            ">
                                                        <img src="/assets/svgs/trash-icon.svg" />
                                                    </button>
                                                </div>
                                                <div class="flex justify-center items-center gap-4">
                                                    <button
                                                        class="text-[#2C75E3] flex justify-start items-center gap-2">
                                                        <img src="/assets/svgs/share-icon.svg" />
                                                        <span class="text-xs">Chia sẻ</span>
                                                    </button>
                                                    <button
                                                        class="text-[#2C75E3] flex justify-start items-center gap-2">
                                                        <img src="/assets/svgs/heart-icon.svg" />
                                                        <span class="text-xs">Yêu thích</span>
                                                    </button>
                                                    <button class="text-[#2C75E3] flex justify-start items-center gap-2"
                                                        @click="
                                                            copyToClipboard(
                                                                message.content
                                                            )
                                                            ">
                                                        <img src="/assets/svgs/copy-icon.svg" />
                                                        <span class="text-xs">Copy</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-3 py-[10px] bg-white rounded-[25px] mt-4">
                        <form>
                            <div class="relative">
                                <div class="">
                                    <textarea v-model="chatMessage" type="text" placeholder="Chat với Orion AI"
                                        class="w-full p-3 border text-black border-gray-300 rounded-[13px] shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 pr-32"
                                        @keydown.enter.exact.prevent="sendChat" :disabled="isProcessing"></textarea>
                                </div>
                                <div class="absolute flex justify-start gap-4 top-4 right-4">
                                    <div v-if="file"
                                        class="absolute flex items-center gap-2 bg-gray-800 bg-opacity-50 w-6 h-6 rounded-full justify-center">
                                        <button @click="removeFile" class="hover:opacity-75">
                                            <img src="/assets/img/close-icon.png" alt="remove icon" class="w-4 h-4" />
                                        </button>
                                    </div>
                                    <label for="file-upload" class="cursor-pointer hover:opacity-75">
                                        <img src="/assets/svgs/upload-icon.svg" alt="upload icon" class="w-6 h-6" />
                                    </label>
                                    <input id="file-upload" type="file" class="hidden"
                                        accept=".jpg,.png,.jpeg,.pdf,.doc,.docx" @change="handleFileUpload" />
                                    <div @click="toggleRecording" class="cursor-pointer">
                                        <img :src="isRecording
                                                ? '/assets/svgs/stop-icon.svg'
                                                : '/assets/svgs/micro-icon.svg'
                                            " alt="icon" class="h-[20px]" />
                                    </div>
                                    <button @click="sendChat" :disabled="isProcessing" class="send-button">
                                        <img src="/assets/svgs/send-icon.svg" alt="icon" />
                                        {{ isProcessing ? 'Đang xử lý...' : 'Gửi' }}
                                    </button>
                                </div>
                            </div>
                            <div class="flex justify-between md:justify-end items-center gap-5">
                                <div
                                    class="w-full md:w-fit bg-[#E8F7FF] mb-4 flex justify-start items-center gap-1 md:gap-3 bg-[E8F7FF] rounded-[14px]">
                                    <img src="/assets/svgs/noise-icon.svg" alt="icon" class="pl-[5px]" />
                                    <div class="relative w-full">
                                        <select v-model="model"
                                            class="block appearance-none w-full md:w-fit bg-[#E8F7FF] border border-[#E8F7FF] text-gray-700 py-3 px-1 md:px-4 text-[12px] md:text-[16px] pr-6 md:pr-8 rounded-[15px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500 max-w-[120px] lg:max-w-none"
                                            id="choose-job">
                                            <option v-for="(model, index) in models" :value="model" :key="index">
                                                {{ model }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div
                                    class="w-full md:w-fit mb-4 flex justify-start items-center gap-1 md:gap-3 bg-[E8F7FF] rounded-[14px] bg-[#E8F7FF] ml-3">
                                    <img src="/assets/svgs/lang-icon.svg" alt="icon" class="pl-[5px]" />
                                    <div class="relative w-full">
                                        <select v-model="lang"
                                            class="block appearance-none w-full border bg-[#E8F7FF] border-[#E8F7FF] text-gray-700 py-3 px-1 md:px-4 text-[12px] md:text-[16px] pr-6 md:pr-8 rounded-[15px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                            id="choose-job">
                                            <option v-for="langCode in Object.keys(
                                                languages
                                            )" :value="langCode" :key="langCode">
                                                {{ languages[langCode] }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div
                    class="w-full flex flex-col lg:w-1/4 p-4 h-full mt-[10%] lg:mt-0 bg-gray-100 border-l rounded-bl-[25px] rounded-br-[25px] px-5">
                    <a :href="route('ai-chat.index')"
                        class="px-[20px] py-[10px] md:px-[30px] md:py-[15px] rounded-[15px] bg-[#2C75E3] flex justify-center items-center w-full gap-2">
                        <img src="/assets/svgs/new-chat-icon.svg" alt="icon" />
                        <h2 class="font-bold text-xl">Chat mới</h2>
                    </a>
                    <h2 class="text-xl text-black font-semibold my-4 border-b-2 border-black">
                        Lịch sử
                    </h2>
                    <ul class="space-y-2 max-h-[480px] md:max-h-[504px] xl:max-h-[60vh] overflow-y-auto">
                        <li v-for="(item, index) in history" :key="index"
                            class="p-2 bg-white text-black shadow rounded-lg flex justify-between gap-2">
                            <a :href="route('ai-chat.index', { id: item.id })" v-html="item.oldest_message?.content || 'Đoạn chat trống'
                                " class="line-clamp-1 md:flex-1"></a>
                            <div class="flex justify-end items-center gap-1">
                                <img src="/assets/svgs/share-icon-2.svg" alt="icon" height="12" width="12" />
                                <a :href="route('ai-chat.index', { id: item.id })">
                                    <img src="/assets/svgs/edit-icon.svg" alt="icon" height="12" width="12" />
                                </a>
                                <button @click="prepareDeleteSession(item.id)" class="flex items-center justify-center">
                                    <img src="/assets/svgs/trash-icon.svg" alt="icon" height="12" width="12" />
                                </button>
                            </div>
                        </li>
                    </ul>
                    <div class="text-black flex justify-center mt-4">
                        <button v-if="hasNextPageSessions" @click="loadMoreSession"
                            class="text-center border-[1px] border-gray-300 px-4 py-2 w-fit rounded-full">
                            <span v-if="isLoading">Đang tải...</span>
                            <span v-else>Đoạn chat cũ hơn</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <div class="w-full min-h-full m-auto">
        <div class="flex gap-2 items-center mb-4">
            <img src="/assets/img/orion/logo/logo.svg" alt="icon" class="size-6" />
            <h1 class="font-bold text-xl">Chuyên viên AI tư vấn sẵn sàng giúp bạn!</h1>
        </div>

        <div class="w-full min-h-[50vh]">
            <div v-if="messages.length > 0" class="w-full bg-white rounded-t-[25px] min-w-2/3 px-3 border border-[#D4D4D4]">
                <div ref="messagesContainer" class="px-4 pt-4 h-[30vh] lg:h-[50vh] overflow-y-scroll">
                    <div class="text-black flex justify-center">
                        <button v-if="hasNextPage" @click="loadMoreMessage" class="text-center border-[1px] border-gray-300 px-4 py-2 w-fit rounded-full">
                            <span v-if="isLoading">Đang tải...</span>
                            <span v-else>Hiển thị tin nhắn cũ hơn</span>
                        </button>
                    </div>
                    <div v-for="(message, index) in messages" :key="index" class="mt-4">
                        <div v-if="message.sender === 'user'" class="flex flex-row-reverse justify-start items-start gap-1">
                            <div class="text-white bg-[#207A91] px-[21px] py-2 rounded-[30px] relative">
                                <div v-if="message.file_url">
                                    <div v-if="message.file_type.includes('image')">
                                        <img :src="message.file_url" class="mb-2 max-w-xs rounded-lg" />
                                    </div>
                                    <div v-else-if="message.file_type === 'video/webm'">
                                        <audio controls class="mt-2 min-w-[200px]">
                                            <source :src="message.file_url" type="audio/mpeg" />
                                            Trình duyệt của bạn không hỗ trợ audio.
                                        </audio>
                                    </div>
                                    <div v-else>
                                        <a :href="message.file_url" target="_blank" class="text-xs underline"> (Tải file về máy) </a>
                                    </div>
                                </div>
                                {{ message.content }}
                            </div>
                        </div>

                        <div v-else class="flex w-full">
                            <div class="bg-[#EDEDED] px-[17px] py-[12px] rounded-2xl">
                                <div class="text-black pb-7 relative">
                                    <div v-if="message.aiId == aiId" class="break-words" v-html="marked(convertText(animatedText))"></div>
                                    <div v-else class="break-words" v-html="marked(message.content)"></div>

                                    <div class="absolute bottom-1 w-full flex justify-between items-center">
                                        <div class="flex justify-center items-center gap-4">
                                            <button class="flex justify-start items-center gap-2" @click="copyToClipboard(message.content)">
                                                <img src="/assets/svgs/ai3goc-copy.svg" />
                                            </button>
                                            <img src="/assets/svgs/ai3goc-like.svg" />
                                            <img src="/assets/svgs/ai3goc-dislike.svg" />
                                            <img src="/assets/svgs/ai3goc-speaker.svg" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rounded-[25px]">
                <form>
                    <div class="relative">
                        <div class="">
                            <textarea v-model="chatMessage" type="text" rows="3" placeholder="Bạn cần tư vấn gì?" :class="messages.length > 0 ? 'rounded-b-3xl' : 'rounded-3xl'" class="w-full p-5 border text-black border-[#D4D4D4] [box-shadow: 0px 4px 7.1px 0px #00000033] shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 pr-32 disabled:bg-white/90" @keydown.enter.exact.prevent="sendChat" :disabled="isProcessing"></textarea>
                        </div>
                        <div class="absolute flex justify-start gap-4 top-4 right-4">
                            <button @click="sendChat" :disabled="isProcessing" class="send-button">
                                <svg class="size-7" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="28" height="28" rx="14" fill="#207A91" />
                                    <path d="M14 22.1666V5.83325" stroke="white" stroke-width="2.33333" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M5.8335 13.9999L14.0002 5.83325L22.1668 13.9999" stroke="white" stroke-width="2.33333" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <Popup v-if="showConfirmModal" title="Cảnh báo !" message="Bạn có chắc chắn muốn xóa đoạn chat này không?" cancelButtonText="Huỷ" acceptButtonText="Xoá" @cancel="cancelDelete" @accept="confirmDelete" />
    <div v-if="isLoading" class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
        <div class="loader"></div>
    </div>
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="acceptUpgrade" />
    <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleBuyCancel" :currentCredit="pageProps.auth.user.credit || 0" :additionalCredit="additionalCredit" />
</template>
<script setup>
import { computed, nextTick, onBeforeMount, onMounted, ref, watch } from "vue";
import Layout from "@/Layouts/Client/Layout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import Footer from "../Home/Components/Footer.vue";
import Credit from "@/Components/Credit.vue";
import MainMenu from "@/Components/MainMenu.vue";
import CreditBuyModal from "../../../Components/ModalBuyCredit/BuyCredit.vue";
import axios from "axios";
import { toast } from "vue3-toastify";
import PopupAff from "@/Components/PopupAff.vue";
import Popup from "@/Components/Popup/Index.vue";
import { marked } from "marked";

defineOptions({ layout: Layout });

// Props
const props = defineProps({
    session_id: Number,
    session_histories: Array,
});
const { props: pageProps } = usePage();
const auth = computed(() => pageProps.value.auth);
const additionalCredit = ref(0);
const showAffKeyPopup = ref(false);
const credits = ref(0);

// Reactive variables
const messages = ref([]);
const sessionIdToDelete = ref(null);
const messageIdToDelete = ref(null);
const isLoading = ref(false);
const history = ref(props.session_histories || []);
const chatMessage = ref("");
const model = ref("GPT-4o");
const file = ref(null);
const hasNextPage = ref(false);
const currentPage = ref(1);
const messagesContainer = ref(null);
const showConfirmModal = ref(false);
const currentPageSessions = ref(1);
const hasNextPageSessions = ref(false);
const aiId = ref(0)
const chatId = ref(0)
const session_id = ref(null);

const popupMessage = ref("Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!");
const acceptUpgrade = ref(true);

// Language and Model options
const languages = {
    vi: "Tiếng Việt",
    en: "Tiếng Anh",
    zh: "Tiếng Trung",
    ja: "Tiếng Nhật",
    ko: "Tiếng Hàn",
    fr: "Tiếng Pháp",
    de: "Tiếng Đức",
};

const lang = ref("vi");
const models = ["GPT-4o", "GPT-4o mini"];

// File handling
const handleFileUpload = (event) => {
    const selectedFile = event.target.files[0];
    file.value = selectedFile || null;
};

const removeFile = () => {
    file.value = null;
    document.getElementById("file-upload").value = "";
};

// Convert file to base64
const toBase64 = (file) =>
    new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });

const isRecording = ref(false);
let mediaRecorder = null;
let audioChunks = [];
const audioUrl = ref(null);
const audioBlob = ref(null);

const toggleRecording = async () => {
    if (!isRecording.value) {
        const stream = await navigator.mediaDevices.getUserMedia({
            audio: true,
        });
        mediaRecorder = new MediaRecorder(stream);

        mediaRecorder.ondataavailable = (event) => {
            audioChunks.push(event.data);
        };

        mediaRecorder.onstop = () => {
            if (audioChunks.length > 0) {
                // Tạo audioBlob từ audioChunks
                audioBlob.value = new Blob(audioChunks, { type: "audio/mp3" });
                audioChunks = [];
                audioUrl.value = URL.createObjectURL(audioBlob.value); // Lưu trữ URL cho việc phát lại
                sendChat();
            } else {
                console.error("Không có dữ liệu âm thanh để tạo Blob.");
            }
        };

        mediaRecorder.start();
        isRecording.value = true;
    } else {
        mediaRecorder.stop();
        isRecording.value = false;
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
let showBuyCreditPopup = ref(false);
const handleBuyCancel = () => {
    showBuyCreditPopup.value = false;
};
const showBuyCreditModal = () => {
    showBuyCreditPopup.value = true;
};
const checkCredit = async (messages) => {
    try {
        const formData = new FormData();
        formData.append("type", "text");
        formData.append("message", messages);
        formData.append("model", model.value);
        // Gọi API để kiểm tra credit của user
        const response = await axios.post(route("credit.check_credit"), formData);
        if (response.data.success) {
            // Đủ credit để tiếp tục
            return true;
        } else if (response.data.is_vip_expired) {
            if (response.data.affCode == "aff_trial") {
                popupMessage.value = " Gói của bạn đã hết hạn. Xin vui lòng gia hạn tài khoản để tiếp tục sử dụng tính năng này.";
                acceptUpgrade.value = false;
            }
            showAffKeyPopup.value = true;
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
// Send chat message
const sendChat1 = async () => {
    if (chatMessage.value.trim() === "" && !audioBlob.value) {
        alert("Bạn cần nhập tin nhắn trước khi gửi.");
        return;
    }

    const tempMsg = chatMessage.value;
    let fileData = null;
    let fileType = null;
    let fileUrl = null;

    // Process file if exists
    if (file.value) {
        const fileFormat = file.value.type.split("/")[0];
        fileUrl = fileFormat === "image" ? await toBase64(file.value) : file.value.name;
        fileType = file.value.type;
        fileData = file.value;
        file.value = null; // Reset file after processing
    }

    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        return; // Dừng nếu không đủ credit
    }

    if (audioBlob.value) {
        const audioFile = new File([audioBlob.value], "audio_record.webm", {
            type: "video/webm",
        });
        fileData = audioFile;
        fileType = audioFile.type;
        audioBlob.value = null;
        fileUrl = URL.createObjectURL(audioFile);
    }
    // Add user message
    messages.value.push({
        sender: "user",
        content: tempMsg,
        file_type: fileType,
        file_url: fileUrl,
    });

    chatMessage.value = ""; // Reset chat input

    // Prepare form data
    const formData = new FormData();
    formData.append("message", tempMsg);
    formData.append("model", model.value);
    formData.append("languageKey", lang.value);
    formData.append("lang", languages[lang.value]);
    formData.append("session_id", props.session_id);
    if (fileData) formData.append("file", fileData);
    console.log(formData);
    // Send message via API
    try {
        const response = await axios.post(route("ai-chat.chat"), formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });
        messages.value.push({
            sender: "AI",
            content: response.data.response,
        });
        credits.value = response.data.credits;
    } catch (error) {
        if (error.response && error.response.status === 403) {
            console.log("error.response ", error.response);
            showBuyCreditModal();
        } else {
            console.error("Đã xảy ra lỗi khi gửi tin nhắn:", error.message);
        }
    }
};
const isProcessing = ref(false);
const sendChat = async () => {
    if (isProcessing.value) {
        return;
    }

    isProcessing.value = true;
    if (chatMessage.value.trim() === "" && !audioBlob.value) {
        alert("Bạn cần nhập tin nhắn trước khi gửi.");
        return;
    }
    await nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop =
                messagesContainer.value.scrollHeight;
        }
    });
    const tempMsg = chatMessage.value;
    let fileData = null;
    let fileType = null;
    let fileUrl = null;
    // Process file if exists
    if (file.value) {
        const fileFormat = file.value.type.split("/")[0];
        fileUrl =
            fileFormat === "image"
                ? await toBase64(file.value)
                : file.value.name;
        fileType = file.value.type;
        fileData = file.value;
        file.value = null; // Reset file after processing
    }
    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        return; // Dừng nếu không đủ credit
    }
    if (audioBlob.value) {
        const audioFile = new File([audioBlob.value], "audio_record.webm", {
            type: "video/webm",
        });
        fileData = audioFile;
        fileType = audioFile.type;
        audioBlob.value = null;
        fileUrl = URL.createObjectURL(audioFile);
    }
    // **Thêm tin nhắn từ người dùng vào danh sách**
    messages.value.push({
        sender: "user",
        content: tempMsg,
        file_type: fileType,
        file_url: fileUrl,
    });
    chatMessage.value = ""; // Reset chat input
    // Chuẩn bị dữ liệu gửi lên API
    const formData = new FormData();
    formData.append("message", tempMsg);
    formData.append("model", model.value);
    formData.append("chat_id", chatId.value);
    formData.append("languageKey", lang.value);
    formData.append("lang", languages[lang.value]);
    formData.append("session_id", session_id.value);
    if (fileData) formData.append("file", fileData);
    const response = await axios.post(route("ai-text.send-chat"), formData, {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    });
    let streamResponse = "";
    // Thêm container HTML cho response
    aiId.value = aiId.value + 1
    if(!chatId.value) {
        fetchChat()
    }
    chatId.value = response.data.chat_id
    let aiMessage = {
        sender: "AI",
        content: convertText(response.data.data.choices[0].message.content),
        aiId:aiId.value,
        animatedText:""
    }
    messages.value.push(aiMessage);
    animateText(response.data.data.choices[0].message.content)
};

const chatHistory = ref([]);
const displayedHistoryChat = ref([]);
const totalHistoryChatPages = ref(1);
const currentHistoryChatPage = ref(1);

const fetchChat = async (page = 1) => {
    const response = await axios.get(
        route("ai-text.list-chat", {
            page
        })
    );
    if(response) {
        chatHistory.value = response.data.data.data;
        displayedHistoryChat.value = response.data.data.data;
        totalHistoryChatPages.value = response.data.data.last_page;
        currentHistoryChatPage.value = response.data.data.current_page;
    }
}

const animatedText = ref("")
const animateText = (fullText) => {
    animatedText.value = ""; // Xóa nội dung cũ
    let index = 0;
    const interval = setInterval(() => {
        if (index < fullText.length) {
            animatedText.value += fullText[index];
        index++;
        // nextTick(() => {
        //     if (messagesContainer.value) {
        //         messagesContainer.value.scrollTop =
        //             messagesContainer.value.scrollHeight;
        //     }
        // });
        } else {
            clearInterval(interval);
        }
    }, 10); // Tốc độ gõ (50ms mỗi ký tự)
   isProcessing.value = false; // Kết thúc quá trình xử lý
}

const convertText = (inputText) => {
   const replacements = {
    "\\times": "×",
    "\\div": "÷",
    "\\pm": "±",
    "\\sqrt": "√",
    "\\pi": "π",
    "\\theta": "θ",
    "\\alpha": "α",
    "\\beta": "β",
    "\\gamma": "γ",
    "\\delta": "δ",
    "\\sum": "∑",
    "\\int": "∫",
    "\\infty": "∞",
    "\\text{cm}": "cm",
    "\\text{ cm}": "cm",
    "\\neq": "≠",
    "\\leq": "≤",
    "\\geq": "≥",
    "\\approx": "≈",
    "\\sin": "sin",
    "\\cos": "cos",
    "\\tan": "tan",
    "\\log": "log",
    "\\ln": "ln",
    "\\frac": "/", // Đơn giản hóa phân số
    "\\left(": "(",
    "\\right)": ")",
    "\\^": "^",
    "_": ""
  };

  inputText = inputText.replace(/\\text\{([^}]*)\}/g, "$1");
  inputText = inputText.replace(/\/\{([^}]*)\}\{([^}]*)\}/g, "$1/$2");
  // Thay thế các ký hiệu inputText
  return inputText.replace(/\\[a-zA-Z]+|\^|_/g, match => replacements[match] || match);
}

// Delete session/message preparation
const prepareDeleteSession = (sessionId) => {
    sessionIdToDelete.value = sessionId;
    showConfirmModal.value = true;
};

const formatResponseToHTML = (text) => {
    if (text.includes("|")) {
        const lines = text.split("\n");
        let isTable = false;
        let tableHTML = `<div style='background:#f8f9fa; padding:15px; border-radius:8px; margin:8px 0;'>`; // Giảm padding
        let normalText = "";
        for (let line of lines) {
            if (line.includes("|")) {
                if (!isTable) {
                    isTable = true;
                    if (normalText) {
                        tableHTML += `<p style='margin-bottom:8px;'>${normalText}</p>`; // Giảm margin
                        normalText = "";
                    }
                    tableHTML += `<table style='width:100%; border-collapse:collapse; background:white; margin:0;'>`; // Thêm margin:0
                }
                const cells = line.split("|").filter((cell) => cell.trim() !== "");
                if (line.includes("---")) continue;
                const isHeader = !tableHTML.includes("</thead>");
                tableHTML += "<tr" + (isHeader ? " style='background:#f0f2f5;'" : "") + ">";
                cells.forEach((cell) => {
                    const tag = isHeader ? "th" : "td";
                    // Giảm padding của cells
                    tableHTML += `<${tag} style='padding:8px 10px; border:1px solid #ddd; text-align:left; line-height:1.2;'>${cell.trim()}</${tag}>`;
                });
                tableHTML += "</tr>";
                if (isHeader) {
                    tableHTML += "</thead><tbody>";
                }
            } else {
                if (isTable) {
                    tableHTML += "</tbody></table>";
                    isTable = false;
                }
                if (line.trim()) {
                    normalText += line + "<br/>";
                }
            }
        }
        if (isTable) {
            tableHTML += "</tbody></table>";
        }
        if (normalText) {
            tableHTML += `<p style='margin-top:8px;'>${normalText}</p>`;
        }
        tableHTML += "</div>";
        return tableHTML;
    }
    return `<div style='background:#f8f9fa; padding:15px; border-radius:8px; margin:8px 0;'>
              <p style='margin:8px 0; line-height:1.4;'>${text}</p>
            </div>`;
};

const prepareDeleteMessage = (id) => {
    messageIdToDelete.value = id;
    showConfirmModal.value = true;
};

// Load messages on mount
onBeforeMount(async () => {
    try {
        fetchChat();
    } catch (error) {
        console.error("Đã xảy ra lỗi khi lấy lịch session:", error.message);
    }
});

// Load more messages
const loadMoreMessage = async () => {
    if (hasNextPage.value) {
        currentPage.value++;
        isLoading.value = true;

        try {
            const response = await axios.get(
                route("ai-chat.message-histories", {
                    sessionId: props.session_id,
                    page: currentPage.value,
                })
            );
            hasNextPage.value = response?.data?.data?.next_page_url || null;
            const newMessages = response.data.data.data || [];
            if (newMessages.length > 0) {
                messages.value = [...newMessages, ...messages.value];
            }
        } catch (error) {
            console.error("Đã xảy ra lỗi khi lấy lịch sử tin nhắn:", error.message);
        } finally {
            isLoading.value = false;
        }
    } else {
        console.log("Không còn trang tiếp theo.");
    }
};

// Auto-scroll to the bottom of messages
watch(messages, () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
});

// Confirm delete session/message
const confirmDelete = async () => {
    let response;
    isLoading.value = true;

    try {
        if (sessionIdToDelete.value !== null && messageIdToDelete.value === null) {
            response = await axios.get(route("ai-chat.delete-session", { id: sessionIdToDelete.value }));
            if (response.status === 200) window.location.reload();
        } else if (messageIdToDelete.value !== null) {
            response = await axios.get(route("ai-chat.delete-message", { id: messageIdToDelete.value }));
            if (response.status === 200) {
                messages.value = messages.value.filter((message) => message.id !== messageIdToDelete.value);
                showConfirmModal.value = false;
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

// Copy to clipboard
const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text);
        alert("Nội dung đã được sao chép vào bộ nhớ đệm!");
    } catch (err) {
        console.error("Có lỗi xảy ra khi sao chép:", err);
        alert("Có lỗi xảy ra khi sao chép nội dung.");
    }
};

// Load chat history sessions on mount
onMounted(async () => {
    try {
        const response = await axios.get("/ai-chat/chat/history?page=" + currentPageSessions.value);
        if (response?.data?.data?.next_page_url) {
            hasNextPageSessions.value = response.data.data.next_page_url;
        }
        history.value = response.data.data.data;
    } catch (error) {
        console.error("Đã xảy ra lỗi khi lấy lịch sử các phiên trò chuyện:", error.message);
    }
});

// Load more chat sessions
const loadMoreSession = async () => {
    if (hasNextPageSessions.value) {
        currentPageSessions.value++;
        isLoading.value = true;

        try {
            const response = await axios.get("/ai-chat/chat/history?page=" + currentPageSessions.value);
            hasNextPageSessions.value = response?.data?.data?.next_page_url || null;
            const newSessions = response.data.data.data || [];
            if (newSessions.length > 0) {
                history.value = [...history.value, ...newSessions];
            }
        } catch (error) {
            console.error("Đã xảy ra lỗi khi lấy lịch sử tin nhắn:", error.message);
        } finally {
            isLoading.value = false;
        }
    } else {
        console.log("Không còn trang tiếp theo.");
    }
};
</script>

<style scoped>
.send-button[disabled] {
    opacity: 0.6;
    cursor: not-allowed;
}

textarea[disabled] {
    background-color: #f5f5f5;
    cursor: not-allowed;
}

.loader {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #24aa69;
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
