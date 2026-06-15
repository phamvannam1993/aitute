<template>
    <div class="absolute -left-[2px] top-[5%] lg:top-[8%] border-[2px] bg-white border-[#d4d4d4] px-4 py-2.5 shadow-lg z-50"
        :class="showHistory ? 'w-full lg:w-1/5 rounded-tr-[20px] rounded-br-[20px]' : 'w-auto rounded-tr-[8px] rounded-br-[8px]'"
    >
        <div class="w-full flex items-center justify-between">
            <button @click="toggleHistory" class=" bg-white">
                <img src="/assets/img/orion/icon/guru-history.svg" alt="history icon" class="w-[28px]" />
            </button>
            <button v-if="showHistory" class="" @click="createNewChat">
                <img src="/assets/img/orion/icon/guru-new-chat.svg" alt="history icon" class="w-[28px]" />
            </button>
        </div>
        <div v-if="showHistory" class="w-full mt-2 lg:mt-4">
            <p class="text-[1x8p] font-bold pb-2 border-b-[2px] border-[#B9B9B9] mb-2">Lịch sử Chatbot GURU</p>
            <ul v-if="chatHistory.length > 0" class="w-full bg-white overflow-y-auto max-h-[50vh]">
                <li v-for="(history, index) in chatHistory" 
                    :key="index" 
                    @click="loadChat(history)" 
                    class="cursor-pointer p-2 hover:bg-[#F2F2F2] rounded-[14px] flex items-center justify-between group gap-1">
                    
                    <span class="line-clamp-1 text-black text-[18px] flex-1">{{ history.content }}</span>

                    <button @click.stop="removeChat(history)" 
                        class="opacity-100 lg:opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-1 rounded-full hover:bg-gray-200">
                        <img src="/assets/img/orion/icon/guru-remove-chat.svg" alt="remove icon" class="w-[14px]" />
                    </button>
                </li>
                <Pagination
                    v-if="displayedHistoryChat?.length"
                        v-model="currentHistoryChatPage"
                        :page-count="totalHistoryChatPages"
                        :click-handler="handleHistoryChatPageClick"
                        :container-class="'pagination'"
                        :prev-text="'<'"
                        :next-text="'>'"
                    />
            </ul>
            <div v-else class="w-full bg-white h-[50vh] flex items-center justify-center">
                <p class="text-black">Không có lịch sử chat nào</p>
            </div> 
        </div>
    </div>
    <div class="flex flex-col lg:flex-row" :class="showHistory ? 'w-4/5' : 'w-full'">
        <div class="w-full px-5">
            <p class="text-3xl text-center font-bold my-4">Chuyên gia GURU có thể giúp gì cho bạn?</p>
            <div class="w-full bg-white rounded-[35px] border border-[#D4D4D4] shadow-lg min-w-2/3 py-4 chat-response mt-16 lg:mt-0">
                <div v-if="messages.length > 0" ref="messagesContainer"
                    class="p-4 h-[30vh] lg:h-[50vh] overflow-y-scroll rounded-[35px]">
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
                            <div
                                class="text-white bg-[#24AA69] px-[17px] py-[12px] rounded-[30px] relative min-w-[100px]">
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
                            </div>
                        </div>

                        <div v-else class="flex justify-start items-start gap-1">
                            <div class="bg-[#EDEDED] px-[17px] py-[12px] rounded-[15px]">
                                <div class="text-black pb-7 relative min-w-[330px]">
                                    <div v-if="message.aiId == aiId" class="break-words" v-html="marked(convertText(animatedText))"></div>
                                    <div v-else class="break-words" v-html="marked(message.content)"></div>
                                    <div class="absolute bottom-1 w-full flex justify-between items-center">
                                        <div class="flex justify-center items-center gap-4">
                                            <button class="text-[#2C75E3] flex justify-start items-center gap-2" @click="
                                                copyToClipboard(
                                                    message.content
                                                )
                                                ">
                                                <img src="/assets/img/orion/icon/copy.svg" />
                                            </button>
                                            <img src="/assets/svgs/like-icon.svg" />
                                            <img src="/assets/svgs/dislike-icon.svg" />
                                            <img src="/assets/img/orion/icon/speaker.svg" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <form :class="messages.length > 0 ? 'border-t-2 pt-2' : ''">
                    <div class="relative">
                        <div class="pb-10">
                            <textarea v-model="chatMessage" rows="3" type="text" placeholder="Hỏi bất kỳ điều gì"
                                class="w-full resize-none p-3 px-6 border-none text-black !bg-transparent rounded-[13px] shadow-sm focus:outline-none focus:ring-0 focus:border-transparent"
                                @keydown.enter.exact.prevent="sendChat" :disabled="isProcessing"></textarea>
                            <img src="/assets/img/icon/icon_btn_create_chat.png" alt="icon" class="w-8 absolute r-20 mt-[-90px] cursor-pointer" @click="sendChat"/>
                        </div>
                        <div class="absolute flex justify-start gap-4 top-[6.5rem] right-4">
                            <input id="file-upload" type="file" class="hidden" accept=".jpg,.png,.jpeg,.pdf,.doc,.docx"
                                @change="handleFileUpload" />
                            <div @click="toggleRecording" class="cursor-pointer">
                                <img :src="isRecording
                                    ? '/assets/svgs/stop-icon.svg'
                                    : '/assets/img/orion/icon/mic-orange.svg'
                                    " alt="icon" class="w-8" />
                            </div>
                        </div>
                        <div class="absolute flex justify-start gap-4 top-[6.5rem] left-4">
                            <img src="/assets/img/orion/icon/more-chat.svg" alt="">
                            <!-- <div v-if="file"
                                class="absolute flex items-center gap-2 bg-gray-800 bg-opacity-50 w-6 h-6 rounded-full justify-center">
                                <button @click="removeFile" class="hover:opacity-75">
                                    <img src="/assets/img/close-icon.png" alt="remove icon" class="w-4 h-4" />
                                </button>
                            </div>
                            <label for="file-upload" class="cursor-pointer hover:opacity-75">
                                <img src="/assets/svgs/upload-icon.svg" alt="upload icon" class="w-6 h-6" />
                            </label> -->
                        </div>
                    </div>
                    <!-- <div class="flex justify-between md:justify-end items-center gap-5">
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
                    </div> -->
                </form>
            </div>
        </div>
    </div>
    <div class="flex flex-1 flex-col mt-[12px] md:mt-[8px] " :class="showHistory ? 'w-4/5' : 'w-full'">
        <div class="flex flex-col items-center my-2 xl:space-x-6 text-black">
            <div class="px-[14px] md:px-0 w-full mb-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <img src="/assets/img/orion/icon/label-circle.svg" alt="">
                        <h1 class="font-bold text-xs md:text-base text-black my-4">CÁC TRỢ LÝ</h1>
                    </div>
                    <div class="flex flex-1 justify-center items-center space-x-2 md:space-x-4 px-[20px] md:px-0 ">
                        <div class="w-full relative text-black max-w-[400px]">
                            <input type="text" placeholder="Tìm kiếm trợ lí..." v-model="searchQuery"
                                @keyup.enter="searchAI"
                                class="w-full text-[10px] md:text-[14px] px-3 md:px-4 py-[2px] md:py-2 border border-[#D4D4D4] rounded-[20px] md:rounded-[15px] focus:outline-none focus:ring-2 focus:ring-[#24AA69]" />
                            <button class="absolute right-3 top-2 text-gray-500" @click="searchAI">
                                <img src="/assets/img/orion/icon/green-search.svg" alt="search icon"
                                    class="w-[10px] h-[15px] md:size-6" />
                            </button>
                        </div>
                    </div>
                    <div class="w-[100px] hidden md:block"></div>
                </div>

                <div v-if="loadingSearch" class="flex items-center justify-center h-32 mt-8">
                    <svg class="animate-spin h-5 w-5 mr-3 text-[#24AA69]" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                        </path>
                    </svg>
                </div>

                <div v-if="!loadingSearch && !displayedAssistants?.length" class="text-center text-gray-500 my-4">
                    <p>Không có kết quả nào phù hợp</p>
                </div>

                <div v-if="!loadingSearch && displayedAssistants?.length"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-3 md:gap-4 px-[20px] md:px-0">
                    <div v-for="(ai, index) in displayedAssistants" :key="index"
                        class="ai-items bg-white p-2 rounded-3xl relative cursor-pointer border-2 border-[#D4D4D4]"
                        @click="navigateTo(ai)">
                        <div class="flex items-center justify-between space-x-1 md:mb-2">
                            <div class="w-[60px] md:w-2/8 border border-[#D4D4D4] rounded-[13px] overflow-hidden">
                                <img :src="ai?.thumbnail_url || '/assets/img/orion/big-robot.png'" alt="AI Image"
                                    class="h-[60px] w-[60px] rounded-[13px]" />
                            </div>

                            <div class="flex flex-1 flex-col space-y-[2px]">
                                <h3 class="text-[12px] font-bold line-clamp-1">{{ ai?.name || 'AI mới' }}</h3>
                                <p class="text-gray-600 text-[12px] line-clamp-1">{{ ai?.description || "Đây là AI được Nhiều người yêu thích" }}</p>
                                <div class="flex items-center space-x-1 md:space-x-2">
                                    <div class="rounded-full p-1 md:p-2 bg-[#2C75E3]"></div>
                                    <p class="text-[10px] md:text-[12px] line-clamp-1">{{ ai?.occupation?.name || 'Giáo Dục' }}</p>
                                </div>
                            </div>

                            <div class="md:w-1/8 flex flex-col items-center justify-between space-y-5">
                                <div class="flex flex-row justify-between md:flex-col md:space-y-2 w-full">
                                    <button @click.stop="updateFavorites(ai)">
                                        <img v-if="!ai?.is_favorited_by_user" src="/assets/svgs/heart-icon.svg"
                                            alt="Heart Icon" class="size-[12px] md:size-4" />
                                        <img v-if="ai?.is_favorited_by_user" src="/assets/img/heart-active.png"
                                            alt="Heart Icon" class="size-[12px] md:size-4" />
                                    </button>
                                    <button>
                                        <img src="/assets/svgs/zoom-icon.svg" alt="Zoom Icon"
                                            class="size-[12px] md:size-4" />
                                    </button>
                                    <button>
                                        <img src="/assets/svgs/share-icon.svg" alt="Share Icon"
                                            class="size-[12px] md:size-4" />
                                    </button>
                                </div>
                                <!--                                    <div class="md:hidden w-full bg-[#DBDBDB] text-[8px] md:text-xs font-bold px-3 md:px-4 py-1 rounded-full">Miễn phí</div>-->
                            </div>
                        </div>
                        <!--                            <div class="hidden md:block w-full bg-[#DBDBDB] text-[12px] font-bold px-4 py-1 rounded-full">Miễn phí</div>-->
                    </div>
                </div>

            </div>
            <Pagination
            v-if="displayedAssistants?.length"
                v-model="currentPage"
                :page-count="totalPages"
                :click-handler="handlePageClick"
                :container-class="'pagination'"
                :prev-text="'<'"
                :next-text="'>'"
            />
        </div>
        <div ref="assistant" v-if="isShowAssistant">

            <AIText :ai_assistant="ai_assistant" :key="aiKey"/>
        </div>
    </div>

    <Popup 
        v-if="showDeleteChat"
        title="Cảnh báo !"
        message="Bạn có chắc chắn muốn xóa đoạn chat này?"
        cancelButtonText="Huỷ"
        acceptButtonText="Đồng ý"
        @cancel="closePopup"
        @accept="submitDeleteChat"
    />

</template>
<script setup>
import { computed, nextTick, onBeforeMount, onMounted, ref, watch } from "vue";
import Layout from "@/Layouts/Client/ClientLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import Footer from "../Home/Components/Footer.vue";
import Credit from '@/Components/Credit.vue';
import MainMenu from "@/Components/MainMenu.vue";
import Modal from '@/Components/Modal.vue';
import CreditBuyModal from '../../../Components/ModalBuyCredit/BuyCredit.vue';
import axios from "axios";
import { toast } from "vue3-toastify";
import PopupAff from '@/Components/PopupAff.vue';
import Popup from '@/Components/Popup/Index.vue'
import Pagination from '@/Components/Pagination.vue';
import AIText from "../AIText/AIText.vue";
import { marked } from "marked";

({ layout: Layout });

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
const showHistory = ref(false);
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
const popupMessage = ref('Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!')
const acceptUpgrade = ref(true)
const loadingMore = ref(false);
const isShowAssistant = ref(false);
const loadingSearch = ref(false);
const displayedAssistants = ref([]);
const totalPages = ref(1); 
const searchQuery = ref('');
const session_id = ref(null);
const offset = ref(15)
const chatHistory = ref([]);

const displayedHistoryChat = ref([]);
const totalHistoryChatPages = ref(1); 
const currentHistoryChatPage = ref(1);

const toggleHistory = () => {
    showHistory.value = !showHistory.value;
};

const loadChat = async (history) => {
    showHistory.value = false;
    chatId.value = history.id
    const response = await axios.get(
        route("ai-text.list-chat-item", {
            page: currentPage.value,
            chat_id:history.id
        })
    );
    if(response) {
        messages.value = response.data.data.data;
        messages.value.sort((a, b) => a.id - b.id);
    }
};
const showDeleteChat = ref(false)
const chatRemoveId = ref(0)
const removeChat = (history) => {
    showDeleteChat.value = true
    chatRemoveId.value = history.id
}
const closePopup = () => {
    showDeleteChat.value = false
}
const submitDeleteChat = async () => {
    const response = await axios.post(
        route("ai-text.delete-chat", {
            id:chatRemoveId.value
        })
    );
    if(response.data.success) {
        chatHistory.value =  chatHistory.value.filter(item => item.id !== chatRemoveId.value);
        toast.success(response.data.message);
    } else {
        toast.error(response.data.message);
    }
    showDeleteChat.value = false
}

const createNewChat = () => {
    chatId.value = ""
    messages.value = []
    showHistory.value = false
}
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
    window.location.href = '/pricing';
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
        formData.append('type', 'text');
        formData.append('message', messages);
        formData.append('model', model.value);
        // Gọi API để kiểm tra credit của user
        const response = await axios.post(route("credit.check_credit"), formData);
        if (response.data.success) {
            // Đủ credit để tiếp tục
            return true;
        } else if (response.data.is_vip_expired) {
            if (response.data.affCode == 'aff_trial') {
                popupMessage.value = " Gói của bạn đã hết hạn. Xin vui lòng gia hạn tài khoản để tiếp tục sử dụng tính năng này."
                acceptUpgrade.value = false
            }
            showAffKeyPopup.value = true
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

const formatText = (text) => {
    if(!text) {
        return "";
    }
    return text.replace(/\n/g, "<br>"); // Chuyển \n thành <br>
}
const aiId = ref(0)
const chatId = ref(0)
const isProcessing = ref(false);

const sendChat = async () => {
    if (isProcessing.value) {
        return;
    }
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
const animatedText = ref("")
const animateText = (fullText) => {
    animatedText.value = ""; // Xóa nội dung cũ
    let index = 0;
    const interval = setInterval(() => {
        if (index < fullText.length) {
            animatedText.value += fullText[index];
        index++;
        nextTick(() => {
            if (messagesContainer.value) {
                messagesContainer.value.scrollTop =
                    messagesContainer.value.scrollHeight;
            }
        });
        } else {
            clearInterval(interval);
        }
    }, 10); // Tốc độ gõ (50ms mỗi ký tự)
   
}


// Delete session/message preparation
const prepareDeleteSession = (sessionId) => {
    sessionIdToDelete.value = sessionId;
    showConfirmModal.value = true;
};

const prepareDeleteMessage = (id) => {
    messageIdToDelete.value = id;
    showConfirmModal.value = true;
};

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

// Load messages on mount
onBeforeMount(async () => {
    try {
        fetchChat();
    } catch (error) {
        console.error("Đã xảy ra lỗi khi lấy lịch session:", error.message);
    }
});

const handleHistoryChatPageClick = (page) => {
    currentHistoryChatPage.value = page;
    fetchChat(page); 
};

// Load more messages
const loadMoreMessage = async () => {
    if (hasNextPage.value) {
        currentPage.value++;
        isLoading.value = true;

        try {
            const response = await axios.get(
                route("ai-text.list-chat", {
                    chat_id: chatId.value,
                    page: currentPage.value,
                })
            );
            hasNextPage.value = response?.data?.data?.next_page_url || null;
            const newMessages = response.data.data.data || [];
            if (newMessages.length > 0) {
                messages.value = [...newMessages, ...messages.value];
            }
        } catch (error) {
            console.error(
                "Đã xảy ra lỗi khi lấy lịch sử tin nhắn:",
                error.message
            );
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
            messagesContainer.value.scrollTop =
                messagesContainer.value.scrollHeight;
        }
    });
});

const searchAI = async () => {
  try {
    loadingSearch.value = true;
    const response = await axios.post(route('popular.search'), { search: searchQuery.value });
    displayedAssistants.value = response.data;
    offset.value = response.data.from;

  } catch (error) {
    console.error('Lỗi khi tìm kiếm AI:', error);
  } finally {
    loadingSearch.value = false;
  }
  };

  const updateFavorites = async (ai) => {
    const response = await axios.post(route('favorites.update', { aiAssistantId: ai.id }));
    response.data.message === 'Added to favorites' ? ai.is_favorited_by_user = true : ai.is_favorited_by_user = false;
  }

  const getAI = async (page = 1) => {
    try {
        loadingSearch.value = true
        const response = await axios.post(route('popular.get-all'), { page });
        displayedAssistants.value = response.data.popularAssistants.data;
        totalPages.value = response.data.popularAssistants.last_page; 
        currentPage.value = response.data.popularAssistants.current_page; 
    } catch (error) {
        console.log(error)
    } finally {
        loadingSearch.value = false
    }
  }
const handlePageClick = (page) => {
    currentPage.value = page;
    getAI(page); 
};
const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text);
        alert("Nội dung đã được sao chép vào bộ nhớ đệm!");
    } catch (err) {
        console.error("Có lỗi xảy ra khi sao chép:", err);
        alert("Có lỗi xảy ra khi sao chép nội dung.");
    }
};

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

// Load chat history sessions on mount
onMounted(async () => {
    try {
        const response = await axios.get(
            "/ai-chat/chat/history?page=" + currentPageSessions.value
        );
        if (response?.data?.data?.next_page_url) {
            hasNextPageSessions.value = response.data.data.next_page_url;
        }
        history.value = response.data.data.data;
    } catch (error) {
        console.error(
            "Đã xảy ra lỗi khi lấy lịch sử các phiên trò chuyện:",
            error.message
        );
    }
    getAI();
});

// Load more chat sessions
const loadMoreSession = async () => {
    if (hasNextPageSessions.value) {
        currentPageSessions.value++;
        isLoading.value = true;

        try {
            const response = await axios.get(
                "/ai-chat/chat/history?page=" + currentPageSessions.value
            );
            hasNextPageSessions.value =
                response?.data?.data?.next_page_url || null;
            const newSessions = response.data.data.data || [];
            if (newSessions.length > 0) {
                history.value = [...history.value, ...newSessions];
            }
        } catch (error) {
            console.error(
                "Đã xảy ra lỗi khi lấy lịch sử tin nhắn:",
                error.message
            );
        } finally {
            isLoading.value = false;
        }
    } else {
        console.log("Không còn trang tiếp theo.");
    }
};

const ai_assistant = ref(null);
const assistant = ref(null);
const aiKey = ref(0);
const navigateTo = async (item) => {
  ai_assistant.value = item;
  isShowAssistant.value = false;
  await nextTick();

  isShowAssistant.value = true; 
  aiKey.value++;

  await nextTick(); 
  if (assistant.value) {
    assistant.value.scrollIntoView({ behavior: "smooth", block: "start" });
  }
};
</script>

<style>
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
.message {
  white-space: pre-line;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    overflow: hidden;
}

th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

th {
    background: #f7f7f7;
    font-weight: bold;
}

tr:hover {
    background: #f1f1f1;
}

/* Tiêu đề */
.chat-response h1 {
    font-size: 24px;
    font-weight: bold;
    color: #0078ff;
    border-bottom: 2px solid #0078ff;
    padding-bottom: 5px;
    margin-bottom: 15px;
}

.chat-response h2 {
    font-size: 20px;
    font-weight: bold;
    color: #0056b3;
    margin-top: 15px;
}

.chat-response h3 {
    font-size: 18px;
    font-weight: bold;
    color: #333;
}

/* Đoạn văn */
.chat-response p {
    font-size: 16px;
    color: #444;
    margin-bottom: 10px;
}

/* Danh sách */
.chat-response ul {
    padding-left: 20px;
}

.chat-response ul li {
    margin-bottom: 5px;
}

/* Code block */
.chat-response pre {
    background: #f4f4f4;
    padding: 10px;
    border-radius: 5px;
    overflow-x: auto;
    font-family: "Courier New", monospace;
}

.chat-response code {
    background: #eee;
    padding: 2px 5px;
    border-radius: 3px;
    font-family: "Courier New", monospace;
    color: #d63384;
}

/* Blockquote */
.chat-response blockquote {
    border-left: 4px solid #0078ff;
    padding-left: 10px;
    color: #555;
    font-style: italic;
    margin: 10px 0;
}

.chat-response ol, 
.chat-response ul {
    display: block;
    list-style-type: decimal;
    margin-block-start: 1em;
    margin-block-end: 1em;
    margin-inline-start: 0px;
    margin-inline-end: 0px;
    padding-inline-start: 40px;
    unicode-bidi: isolate;
}
.r-20 {
    right: 20px;
}

</style>
