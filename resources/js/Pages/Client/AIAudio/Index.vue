<template>

    <Head title="Giọng nói - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData" isBig>
        <div class="flex flex-col md:flex-row justify-center items-center gap-4 xl:hidden mb-2">
            <a
                class="bg-ai3goc-primary md:px-4 md:py-2 flex gap-2 items-center p-2 rounded-lg text-white text-nowrap cursor-default">
                <img src="/assets/img/aiwow/ai_audio/text_to_speech_active.png" class="w-[32px] h-[12px]" alt="">
                <span class="text-sm md:text-base font-bold">Văn bản thành giọng nói</span>
            </a>
            <a class="bg-white border-2 border-[#D4D4D4] md:px-4 md:py-2 flex gap-2 items-center p-2 rounded-lg text-[#9C9C9C] font-semibold text-xs md:text-base text-nowrap"
                :href="route('ai-audio.audioToText')">
                <img src="/assets/img/aiwow/ai_audio/speech_to_text_disable.png" class="w-[32px] h-[12px]" alt="">
                <span class="text-sm md:text-base font-bold">Giọng nói thành văn bản</span>
            </a>
        </div>
        <div
            class="bg-white rounded-2xl shadow-[rgba(50,50,93,0.25)_0px_6px_12px_-2px,_rgba(0,0,0,0.3)_0px_3px_7px_-3px] py-6 text-black font-lexend">
            <div class="flex flex-col md:flex-row justify-between md:items-center md:px-5 px-4 ">
                <div class="flex gap-1">
                    <div class="flex justify-start items-center gap-2">
                        <div class="w-[40px] h-full overflow-hidden rounded-[10px]">
                            <img class="w-full h-auto object-contain" src="/assets/img/ai3goc/logo/logo_img.svg" alt="Robot" />
                        </div>

                        <h2 class="text-black font-bold text-xl lg:text-xl 2xl:text-3xl leading-none">
                            Giọng nói A.I
                        </h2>
                    </div>
                </div>
                <div class="xl:flex gap-4 hidden md:px-5 px-4 ">
                    <a
                        class="bg-ai3goc-primary md:px-4 md:py-2 flex gap-2 items-center p-2 rounded-xl text-white text-nowrap">
                        <img src="/assets/img/aiwow/ai_audio/text_to_speech_active.png" alt="">
                        <span class="text-[15px] font-bold">Văn bản thành giọng nói</span>
                    </a>
                    <a class="bg-white border-2 border-[#D4D4D4] md:px-4 md:py-2 flex gap-2 items-center p-2 rounded-xl text-[#9C9C9C] font-semibold text-xs md:text-base text-nowrap"
                        :href="route('ai-audio.audioToText')">
                        <img src="/assets/img/aiwow/ai_audio/speech_to_text_disable.png" alt="">
                        <span class="text-[15px] font-bold">Giọng nói thành văn bản</span>
                    </a>
                </div>

            </div>
            <p class="text-sm md:text-base text-ai3goc-sec font-bold md:px-5 px-4 my-4">Thực hiện theo các bước sau:</p>

            <div class="md:px-5 px-4 ">
                <form @submit.prevent="submit" class="px-6">
                    <label for="image-description"
                        class="text-xs lg:text-sm flex gap-1 items-center mb-1 font-semibold -ml-6">
                            <Step :step="1" stepName="Nhập nội dung cần chuyển đổi thành giọng nói"/>
                    </label>
                    <div class="flex flex-col relative mt-2">
                        <SuggestionPrompt v-model="message" :type="'audio'" :screenId="12" />
                        
                        <textarea required v-model="message"
                            :placeholder="isRecordVoice ? textToRecord : 'Nhập nội dung...'"
                            class="rounded-xl px-8 py-4 border-[#D4D4D4] resize-none h-48" rows="4"></textarea>
                        <div class="object-mic relative ml-auto">
                            <div v-if="isRecording"
                                class="outline-mic right-3 bottom-3 flex items-center justify-center"></div>
                            <div v-if="isRecording"
                                class="outline-mic right-3 bottom-3 flex items-center justify-center" id="delayed">
                            </div>
                            <div v-if="isRecording"
                                class="button-mic right-3 bottom-3 flex items-center justify-center"></div>
                            <button
                                class="button-mic icon-mic absolute right-3 bottom-3 flex items-center justify-center"
                                @click="startRecording" :disabled="disabledRecord" type="button">
                                <img src="/assets/img/ai3goc/icon/micro.svg" alt="">
                            </button>
                        </div>
                    </div>
                    <div class="w-full">
                        <!-- VOCAL TO SPEECH -->
                        <div class="relative flex flex-col mt-6">
                            <div class="flex flex-col md:flex-row justify-between">
                                <div
                                    :class="['flex flex-col md:flex-row lg:items-center w-full md:w-8/12 mt-2 md:mt-0', isActiveCloneVoice !== 0 ? 'justify-end': 'justify-between']">
                                    <div v-if="isActiveCloneVoice === 0" class="flex gap-4 mb-2 items-center">
                                        <img src="/assets/img/aiwow/ai_audio/vip_feature.png" alt="icons-golden-crown"
                                            class="w-5 h-5">
                                        <div class="flex flex-col">
                                            <span class="text-[#B7B7B7] text-sm md:text-base font-semibold text-nowrap">Nhái
                                                giọng</span>
                                            <span class="text-xs md:text-sm text-[#B7B7B7] italic">(Vui lòng liên hệ admin để
                                                trải
                                                nghiệm tính năng này.) </span>
                                        </div>
                                        
                                    </div>

                                    <button type="button" @click="addVocalVoices"
                                        :class="['px-4 py-3 rounded-xl bg-[#B7B7B7] text-sm md:text-base font-bold text-nowrap w-full md:w-1/2', isActiveCloneVoice !== 0 ? 'text-white' : 'text-white bg-[#B7B7B7]']"
                                        :disabled="isActiveCloneVoice === 0">
                                        Thêm giọng của bạn
                                    </button>
                                </div>
                            </div>
                            <div v-if="isOpenAddVoice">
                                <!-- Audio Upload/Record Buttons -->
                                <div class="flex flex-col sm:flex-row items-center justify-between sm:gap-4 gap-2 mt-2">
                                    <input type="text" v-model="voice_name"
                                        class="rounded-xl px-4 py-2 text-[12px] md:text-14 border-orion-primary resize-none w-full"
                                        placeholder="Nhập tên giọng nói">
                                    <button type="button" @click="triggerAudioUpload"
                                        class="flex items-center w-full font-bold px-3 py-1.5 justify-center gap-2 bg-transparent text-orion-primary rounded-xl backdrop-blur-sm transition-colors hover:scale-105 border-2 border-orion-primary">
                                        <img src="/assets/svgs/aiwow/upload.svg" class="size-6 md:size-5 xl:size-6" />
                                        <span class="text-[12px] xl:text-[14px]">Tải lên giọng nói
                                            của bạn</span>
                                    </button>
                                </div>

                                <div class="mb-3 p-1 mt-4 bg-gray-100 rounded">
                                    <div class="bg-gray-100 rounded">
                                        <audio controls class="w-full" ref="audioPreview" :key="audioPreviewKey">
                                            <source :src="audioFile ? uploadedAudioPreview : DEFAULT_AUDIO_URL"
                                                :type="audioFile?.type || 'audio/wav'">
                                            Browser không hỗ trợ audio.
                                        </audio>
                                    </div>
                                </div>
                                <div class="flex flex-col sm:flex-row items-center gap-4 justify-between">
                                    <p class="text-xs lg:text-sm font-semibold ">Bấm vào đây để bắt đầu nhái giọng</p>
                                    <button type="button" @click="handleVoiceClone" :disabled="isLoading_clone"
                                        class="px-2 sm:w-4/12 text-xs lg:text-sm w-full flex items-center gap-2 text-white justify-center py-3 rounded-xl bg-ai3goc-primary">
                                        <!-- Nhái giọng -->
                                        {{ isLoading_clone ? 'Đang xử lý...' : 'Nhái giọng' }}
                                    </button>
                                </div>
                                <input type="file" ref="audioInput" accept="audio/*" @change="handleAudioUpload"
                                    class="hidden" />
                            </div>
                        </div>
                        <!-- SYSTEM VOICE -->
                        <div class="flex justify-between flex-col md:flex-row gap-3 md:gap-4 mt-4">
                            <label for="image-description"
                                class="md:w-4/12 -ml-6 text-xs lg:text-sm flex flex-nowrap gap-1 items-center md:mb-1 font-semibold">
                                <label for="image-description"
                                    class="font-bold text-sm lg:text-[14px] leading-none">
                                    <Step :step="2" stepName="Chọn giọng nói"/>
                                </label>
                            </label>
                            <div class="relative md:w-8/12">
                                <!-- Giọng đã chọn -->
                                <div @click="toggleDropdown"
                                    class="bg-white p-3 pl-3 rounded-lg cursor-pointer border border-orion-primary text-sm font-normal">
                                    <span v-if="selectedVoice">{{ getVoiceName(selectedVoice) }}</span>
                                    <span v-if="!selectedVoice">
                                        Bạn chưa tạo giọng nói
                                    </span>
                                </div>

                                <!-- Danh sách tùy chọn -->
                                <ul v-if="isDropdownOpen"
                                    class="absolute left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto z-10">
                                    <li v-for="(item, index) in voiceTypes" :key="index" @click="selectVoice(item)"
                                        class="flex justify-between items-center px-4 py-2 hover:bg-gray-100 cursor-pointer">
                                        <!-- Tên giọng -->
                                        <span class="inline-block w-[200px] text-sm font-normal">
                                            {{ item.name }}
                                        </span>
                                        <!-- Nút Play -->
                                        <div class="flex items-center">
                                            <div @click.stop="playSampleVoice(item.demo)"
                                                class="text-white px-3 py-1 rounded-lg focus:outline-none w-[50px]">
                                                <img src="/assets/img/ai3goc/icon/play.svg" alt="">
                                            </div>
                                            <div @click.stop="handleShowConfirmModal(item.id)"
                                                class="text-white px-3 py-1 rounded-lg focus:outline-none w-[50px]">
                                                <img src="/assets/img/ai3goc/icon/remove_audio.svg" alt="">
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <!-- <img class="absolute top-1/2 -translate-y-1/2 left-4"
                                    src="/assets/img/ai_text/voice.png" alt="" /> -->
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between flex-col md:flex-row gap-1 md:gap-4 mt-2">
                        <label for="image-description"
                            class="md:w-4/12 text-xs -ml-6 lg:text-sm flex flex-nowrap gap-1 items-center mb-1 font-semibold">
                            <Step :step="3" stepName=""/>
                        </label>
                        <button v-if="!isLoading" type="submit" :disabled="isLoading"
                            class=" px-2 md:w-8/12 flex items-center gap-2 text-white justify-center py-3 rounded-xl bg-ai3goc-sec">
                            <img src="/assets/img/aiwow/ai_audio/text_to_speech_active.png" class="w-[32px] h-[12px]"
                                alt="">
                            <span class="text-sm md:text-base font-bold text-nowrap">Chuyển đổi giọng nói</span>
                        </button>

                        <button v-else="isLoading" :disabled="isLoading"
                            class="px-12 md:w-8/12 text-white py-3 rounded-xl bg-ai3goc-primary ml">
                            <svg aria-hidden="true"
                                class="w-4 h-4 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                                viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                    fill="currentColor" />
                                <path
                                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                    fill="currentFill" />
                            </svg>
                            <span class="sr-only">Loading...</span>
                        </button>
                    </div>
                </form>
            </div>
            <div class="border-t-2 mt-10 border-[#D4D4D4]">
                <div v-if="result.data.length > 0" class="md:px-5 px-4 mt-4">
                    <h2 class="mb-4 flex items-center gap-2">
                        <span class="text-sm md:text-base font-semibold">Kết quả</span>
                    </h2>
                    <div @scroll="onScroll" class="max-h-[30vh] overflow-y-auto px-6">
                        <div v-for="(item, index) in result.data" :key="index"
                            class="flex justify-between items-center pl-8 pr-1 py-2 rounded-2xl border border-[#D4D4D4] mb-8 gap-2">
                            <div class="flex-1">
                                <p class="line-clamp-1 text-sm font-normal">{{ item.description }}</p>
                            </div>

                            <div class="flex items-center gap-2">
                                <div class="hidden sm:flex items-center max-w-[200px]">
                                    <img class="size-7 rounded-full" src="/assets/img/ai3goc/logo/logo_img.svg" alt="">
                                    <p
                                        class="ml-1 mr-4 flex-1 overflow-hidden text-ellipsis whitespace-nowrap text-[15px] font-normal">
                                        {{ getVoiceName(item.voice_type) }}</p>
                                </div>
                                <div>
                                    <div>
                                        <img v-if="!isPlaying[index]" @click="playAudio(item.s3_url, index)"
                                            class="cursor-pointer w-[30px]" src="/assets/img/ai3goc/icon/play.svg"
                                            alt="play" />
                                        <img v-if="isPlaying[index]" @click="pauseAudio(index)"
                                            class="cursor-pointer w-[30px]" src="/assets/img/ai3goc/icon/pause.svg"
                                            alt="pause" />
                                    </div>
                                </div>
                                <img @click="downloadAudio(item.s3_url)" class="cursor-pointer w-[30px]"
                                    src="/assets/img/ai3goc/icon/download.svg" alt="download" />
                                <img @click="prepareDeleteMessage(item.id)" src="/assets/img/ai3goc/icon/remove_audio.svg"
                                    class="cursor-pointer w-[30px]" alt="delete">
                                <div class="relative">
                                    <img @click="() => toggleConfigForItem(index)" class="cursor-pointer w-[30px]"
                                        src="/assets/img/ai3goc/icon/adjust.svg" alt="config" />
                                    <input v-if="toggleConfig[index]" class="absolute -bottom-8 right-0" type="range"
                                        id="volumeControl" min="0" max="1" step="0.1" v-model="volume[index]"
                                        @input="updateVolume(index)" />
                                </div>

                                <audio ref="audio" :src="item.s3_url" @ended="isPlaying[index] = false"></audio>
                            </div>
                        </div>
                        <div v-if="loading" class="text-center p-2 text-gray-500">
                            Đang tải thêm...
                        </div>
                        <div v-if="lastPage && !loading" class="text-center p-2 text-gray-500">
                            Không có nội dung nào.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>

    <Popup
        v-if="showConfirmModal"
        title="Cảnh báo !"
        message="Bạn có chắc chắn muốn xóa audio này không?"
        cancelButtonText="Huỷ"
        acceptButtonText="Xoá"
        @cancel="cancelDelete"
        @accept="confirmDelete"
    />

    <Popup
        v-if="showConfirmDeleteVoice"
        title="Cảnh báo !"
        message="Bạn có chắc chắn muốn xóa giọng nhái này không?"
        cancelButtonText="Huỷ"
        acceptButtonText="Xoá"
        @cancel="showConfirmDeleteVoice = false"
        @accept="deleteVoice()"
    />

    <div v-if="isSuggestionLoading"
        class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
        <div class="loader"></div>
    </div>
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true"
        :acceptUpgrade="acceptUpgrade" />
    <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleBuyCancel"
        :currentCredit="pageProps.auth.user.credit || 0" :additionalCredit="additionalCredit" />
</template>

<script setup>
import { ref, onBeforeMount, computed, nextTick, onMounted } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { toast } from "vue3-toastify";
import SuggestionPrompt from "@/Components/AIVirtual/SuggestionPrompt.vue";
import Credit from '@/Components/Credit.vue';
import CreditBuyModal from '../../../Components/ModalBuyCredit/BuyCredit.vue';
import PopupAff from '@/Components/PopupAff.vue';
import Layout from "@/Layouts/Client/Layout.vue";
import Popup from '@/Components/Popup/Index.vue'
import Step from "@/Components/Step/Index.vue";


const props = defineProps({
    listAllVoice: Array,
    isActiveCloneVoice: String,
})

const breadcrumbsData = [
    { text: "Giọng nói", link: "ai-audio.index" },
];

const { props: pageProps } = usePage();
const auth = computed(() => pageProps.value.auth);
const additionalCredit = ref(0);
const showAffKeyPopup = ref(false);
const credits = ref(0);
const isVocalToSpeech = ref(false);

const voiceTypes = ref(props.listAllVoice);

const selectedVoice = ref(null);

let messageIdToDelete = ref(null);
const message = ref("");
const isLoading = ref(false);
const isLoading_clone = ref(false);
const isSuggestionLoading = ref(false);
const isDeleteLoading = ref(false);
const result = ref({ data: [] });
const assistant_id = ref(null);

const isPlaying = ref([]);
const toggleConfig = ref(Array(result.value.data.length).fill(false));
const audioPlayers = ref([]);
const volume = ref([]);

const isDropdownOpen = ref(false)
const isOpenAddVoice = ref(false);
const voiceIdDelete = ref(null);
const showConfirmDeleteVoice = ref(false);
const textToRecord = "Buổi sáng, ánh nắng nhẹ nhàng chiếu qua cửa sổ, mang theo sự ấm áp của ngày mới. Ngoài đường, tiếng xe cộ và tiếng nói cười của mọi người làm không gian trở nên sống động. Những hàng cây bên đường đung đưa theo gió, như đang hòa mình vào bản nhạc của thiên nhiên. Cuộc sống là những chuỗi ngày đầy sắc màu, và mỗi ngày mới là một cơ hội để ta trải nghiệm, học hỏi và tận hưởng những điều giản dị nhưng ý nghĩa."
// Load on scroll
const loading = ref(false);
const page = ref(1);
const lastPage = ref(false);

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value
}

const selectVoice = (voiceModel) => {
    if (voiceModel.model === "cloned") {
        isVocalToSpeech.value = true;
    } else {
        isVocalToSpeech.value = false;
    }
    selectedVoice.value = voiceModel.type;
    isDropdownOpen.value = false;
}
onMounted(() => {
    if (voiceTypes.value.length > 0) {
        selectedVoice.value = defaultVoiceValue();
    }
});


let currentAudio = null;
const playSampleVoice = (demoUrl) => {
    if (currentAudio) {
        currentAudio.pause();
        currentAudio.currentTime = 0;
    }

    currentAudio = new Audio(demoUrl);
    currentAudio.play();
};
const handleShowConfirmModal = (id) => {
    voiceIdDelete.value = id;
    showConfirmDeleteVoice.value = true;
};
const deleteVoice = async () => {
    try {
        const response = await axios.delete(route('ai-audio.delete-voice', { id: voiceIdDelete.value }));
        if (response.data.success) {
            // Remove the deleted voice from voiceTypes array
            const index = voiceTypes.value.findIndex(voice => voice.id === voiceIdDelete.value);
            if (index !== -1) {
                voiceTypes.value.splice(index, 1);
            }
            toast.success('Xóa giọng nói thành công');
            showConfirmDeleteVoice.value = false;
        }
    } catch (error) {
        console.error('Error deleting voice:', error);
        toast.error('Không thể xóa giọng nói. Vui lòng thử lại.');
    }
};

const popupMessage = ref('Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!')
const acceptUpgrade = ref(true)

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
const checkCredit = async () => {
    try {
        const formData = new FormData();
        formData.append('type', 'ai-audio');

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

const defaultVoiceValue = () => {
    if ((voiceTypes.value)[0].model === "cloned") {
        isVocalToSpeech.value = true;
    }
    return (voiceTypes.value)[0].type
}

const submit = async () => {
    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) {
        isLoading.value = false;
        return; // Dừng nếu không đủ credit
    }
    isLoading.value = true;
    try {
        if (isVocalToSpeech.value) {
            console.log("With voice clone");
            const response = await axios.post(route("ai-audio.text-to-speech"), {
                voice_id: selectedVoice.value,
                language: language.value,
                text: message.value,
                isSaveDb: true,
            });
            if (response.data.success) {
                isLoading.value = false;
                result.value.current_page = 0;
                result.value.data = [];
                message.value = "";
                loadMore();
            }
        } else {
            console.log("With voice normal");
            const response = await axios.post(route("ai-audio.send"), {
                voice: selectedVoice.value,
                text: message.value,
            });
            if (response.data.success) {
                result.value.data.unshift({
                    id: response.data.data.id,
                    description: response.data.data.description,
                    voice_type: response.data.data.voice_type,
                    s3_url: response.data.data.s3_url
                });
                credits.value = response.data.data.credits;
                toast.success("Chuyển đổi thành công.");
                console.log(result.value);
                isLoading.value = false;
            } else {
                console.error("Phản hồi không thành công:", response.data);
            }
            message.value = "";
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi khi gửi tin nhắn:", error.message);
    } finally {
        isLoading.value = false;
    }
};

function getVoiceName(type) {
    for (const key in voiceTypes.value) {
        if (voiceTypes.value[key].type === type) {
            return voiceTypes.value[key].name;
        }
    }
    return 'Bạn chưa tạo giọng nói'
}

// kiểm tra xem đã tồn tại voice_clone chưa
const checkClonedVoice = () => {
    return voiceTypes.value.some((user) => user.model === "cloned")
}

// Add more vocal voice
const addVocalVoices = () => {
    isOpenAddVoice.value = !isOpenAddVoice.value;
}
const voice_name = ref(null)
const language = ref('vi');
const audioFile = ref(null);
const audioInput = ref(null);
const audioPreviewKey = ref(0);
const audioPreview = ref(null);
const uploadedAudioPreview = ref(null);

const triggerAudioUpload = () => {
    audioInput.value.click();
};
// api clone-voices api
const handleVoiceClone = async () => {
    if (checkClonedVoice()) {
        toast.warning("Bạn đã tạo giọng nói. Vui lòng xóa giọng nói cũ trước khi tạo giọng mới.");
        return;
    }
    // const hasEnoughCredit = await checkCredit();
    // if (!hasEnoughCredit) {
    //     isLoading.value = false;
    //     return; // Dừng nếu không đủ credit
    // }

    isLoading_clone.value = true;
    try {
        const formData = new FormData();
        if (!voice_name.value) {
            toast.warning("Vui lòng điền tên voice của bạn")
            return;
        } else if (!audioFile.value) {
            toast.warning("Vui lòng tải audio hoặc ghi âm giọng của bạn")
            return;
        }
        formData.append("name", voice_name.value)
        formData.append("description", "Giọng nói thuyết trình mạnh mẽ")
        formData.append("files", audioFile.value);
        const response = await axios.post(route("ai-audio.clone-voice"), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            }
        }
        );

        if (response.status) {
            toast.success("Clone giọng thành công.");
            voiceTypes.value.unshift({
                ...response.data.data,
            })
            selectedVoice.value = response.data.data.type;
            credits.value = response.data.data.credits;
            isOpenAddVoice.value = false;
            audioFile.value = ""
        } else {
            console.error("Phản hồi không thành công:", response.data);
        }
        message.value = "";
    } catch (error) {
        console.error("Đã xảy ra lỗi khi gửi tin nhắn:", error.message);
    } finally {
        isLoading_clone.value = false;
    }
}

// Handle-Audio-Upload
const handleAudioUpload = (event) => {
    const file = event.target.files[0];

    if (file) {
        const allowedAudioTypes = ["audio/mpeg", "audio/wav"];
        if (!allowedAudioTypes.includes(file.type)) {
            alert("Xin vui lòng tải lên các tệp âm thanh có định dạng .mp3 hoặc .wav.");
            audioInput.value.value = "";
            return;
        }

        const audio = new Audio();
        const objectUrl = URL.createObjectURL(file);
        audio.src = objectUrl;
        // kiểm tra thời lượng file âm thanh
        audio.addEventListener('loadedmetadata', () => {
            const duration = audio.duration; // Thời lượng file (giây)
            if (duration < 30) {
                toast.warning('Thời lượng file âm thanh phải lớn hơn hoặc bằng 30 giây!');
            } else {
                toast.success('File âm thanh hợp lệ!');
                // Revoke old URL if exists
                if (uploadedAudioPreview.value) {
                    URL.revokeObjectURL(uploadedAudioPreview.value);
                }
                // Update with new file
                audioFile.value = file;
                uploadedAudioPreview.value = URL.createObjectURL(file);
                // Force audio preview to reload
                audioPreviewKey.value += 1;
                // Ensure audio is loaded with new source
                nextTick(() => {
                    if (audioPreview.value) {
                        audioPreview.value.load();
                    }
                });
            }
            // Dọn dẹp URL đối tượng
            URL.revokeObjectURL(objectUrl);
        });
    }
};

const playAudio = (url, index) => {
    stopAllAudio();

    if (audioPlayers.value[index]) {
        audioPlayers.value[index].play();
        const currentVolume = isFinite(volume.value[index])
            ? volume.value[index]
            : 0.5;
        audioPlayers.value[index].volume = currentVolume;
        isPlaying.value[index] = true;
    } else {
        const audio = new Audio(url);
        const currentVolume = isFinite(volume.value[index])
            ? volume.value[index]
            : 0.5;
        audio.volume = currentVolume;
        audio.play();
        isPlaying.value[index] = true;
        audioPlayers.value[index] = audio;

        audio.onended = () => {
            isPlaying.value[index] = false;
            audioPlayers.value[index] = null;
        };
    }
};

const pauseAudio = (index) => {
    if (audioPlayers.value[index]) {
        audioPlayers.value[index].pause();
        isPlaying.value[index] = false;
    }
};
const stopAllAudio = () => {
    isPlaying.value = isPlaying.value.map(() => false);
    audioPlayers.value.forEach((audio) => {
        if (audio) {
            audio.pause();
        }
    });
};

const updateVolume = (index) => {
    const currentVolume = isFinite(volume.value[index])
        ? volume.value[index]
        : 0.5;
    if (audioPlayers.value[index]) {
        audioPlayers.value[index].volume = currentVolume;
    }
};

const onScroll = (event) => {
    const container = event.target;
    if (
        container.scrollTop + container.clientHeight >=
        container.scrollHeight
    ) {
        loadMore();
    }
};

const loadMore = async () => {
    if (loading.value || lastPage.value) return;
    loading.value = true;
    try {
        const response = await axios.get(
            route("ai-audio.load", { page: result.value.current_page + 1, type: 'audio' })
        );
        if (response.data.data && response.data.data.data.length) {
            result.value.data = [
                ...result.value.data,
                ...response.data.data.data,
            ];
            result.value.current_page += 1;
            page.value++;
            if (page.value > response.data.last_page) {
                // Kiểm tra nếu đã tới trang cuối cùng
                lastPage.value = true;
            }
        } else {
            lastPage.value = true;
        }
        console.log(response.data);
    } catch (error) {
        console.log(error.message);
    } finally {
        loading.value = false;
    }
};

const toggleConfigForItem = (index) => {
    toggleConfig.value[index] = !toggleConfig.value[index]; // Chuyển đổi trạng thái
};
const prepareDeleteMessage = (id) => {
    messageIdToDelete.value = id;
    showConfirmModal.value = true;
};

const showConfirmModal = ref(false);
const confirmDelete = async () => {
    let response;
    isDeleteLoading.value = true;

    response = await axios.post(
        route("ai-audio.delete", { id: messageIdToDelete.value }),
        { id: messageIdToDelete.value }
    );
    if (response.status === 200) {
        const index = result.value.data.findIndex(
            (item) => item.id === messageIdToDelete.value
        );
        if (index !== -1) {
            result.value.data.splice(index, 1);
        }
        toast.success("Xóa thành công");
    }
    isDeleteLoading.value = false;
    showConfirmModal.value = false;
};

const downloadAudio = (url) => {
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", "audio.mp3");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
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

onBeforeMount(async () => {
    const urlPath = window.location.pathname;
    const segments = urlPath.split("/");
    assistant_id.value = segments[segments.length - 1];
    try {
        const url = route("ai-audio.load", { page: 1, type: 'audio' });
        const response = await axios.get(url);
        result.value = response.data.data;
        console.log(result.value.data);
    } catch (error) {
        console.error("Đã xảy ra lỗi khi lấy lịch sử tin nhắn:", error.message);
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
                    audioDescription.value = response?.data?.text || 'Vui lòng thử lại';
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
const isRecordVoice = ref(false);
const mediaRecordVoice = ref(null)
const audioChunksVoice = ref([]);
const recordingTimer = ref(null);
const recordingTime = ref(0);
const startRecordingCloneVoice = async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecordVoice.value = new MediaRecorder(stream);
        audioChunksVoice.value = [];

        mediaRecordVoice.value.ondataavailable = (event) => {
            audioChunksVoice.value.push(event.data);
        };

        mediaRecordVoice.value.onstop = async () => {
            try {
                // Revoke old URL if exists
                if (uploadedAudioPreview.value) {
                    URL.revokeObjectURL(uploadedAudioPreview.value);
                }

                // Tạo blob từ chunks âm thanh đã ghi
                const audioBlob = new Blob(audioChunksVoice.value, { type: 'audio/wav' });

                // Tạo WAV file với metadata
                const wavBlob = await createWavFile(audioBlob);

                // Tạo file với metadata
                const file = new File([wavBlob], 'recording.wav', {
                    type: 'audio/wav',
                    lastModified: new Date().getTime()
                });

                const audio = new Audio();
                const objectUrl = URL.createObjectURL(file);
                audio.src = objectUrl;

                audio.addEventListener('loadedmetadata', () => {
                    const duration = audio.duration; // Thời lượng file (giây)
                    if (duration < 30) {
                        toast.warning('Thời lượng file âm thanh phải lớn hơn hoặc bằng 30 giây!');
                    } else {
                        toast.success('File âm thanh hợp lệ!');
                        // Revoke old URL if exists
                        if (uploadedAudioPreview.value) {
                            URL.revokeObjectURL(uploadedAudioPreview.value);
                        }
                        // Update with new file
                        audioFile.value = file;
                        uploadedAudioPreview.value = URL.createObjectURL(file);
                        // Force audio preview to reload
                        audioPreviewKey.value += 1;
                        // Ensure audio is loaded with new source
                        nextTick(() => {
                            if (audioPreview.value) {
                                audioPreview.value.load();
                            }
                        });
                    }
                    // Dọn dẹp URL đối tượng
                    URL.revokeObjectURL(objectUrl);
                });
            } catch (error) {
                console.error('Error processing audio:', error);
                alert('Có lỗi xảy ra khi xử lý file âm thanh');
            }
        };

        mediaRecordVoice.value.start();
        isRecordVoice.value = true;
        recordingTime.value = 0;
        recordingTimer.value = setInterval(() => {
            recordingTime.value++;
        }, 1000);
    } catch (error) {
        console.error('Error accessing microphone:', error);
        alert('Không thể truy cập microphone. Vui lòng kiểm tra quyền truy cập.');
    }
};

const formatTime = (seconds) => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
};
const createWavFile = (audioBlob) => {
    return new Promise((resolve) => {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const fileReader = new FileReader();

        fileReader.onload = async (event) => {
            const audioData = event.target.result;
            const audioBuffer = await audioContext.decodeAudioData(audioData);

            // Tạo WAV file với metadata
            const numberOfChannels = audioBuffer.numberOfChannels;
            const length = audioBuffer.length;
            const sampleRate = audioBuffer.sampleRate;
            const wavBuffer = audioBuffer.getChannelData(0);

            // Tạo WAV header
            const buffer = new ArrayBuffer(44 + length * 2);
            const view = new DataView(buffer);

            // WAV header
            writeUTFBytes(view, 0, 'RIFF');
            view.setUint32(4, 44 + length * 2, true);
            writeUTFBytes(view, 8, 'WAVE');
            writeUTFBytes(view, 12, 'fmt ');
            view.setUint32(16, 16, true);
            view.setUint16(20, 1, true);
            view.setUint16(22, numberOfChannels, true);
            view.setUint32(24, sampleRate, true);
            view.setUint32(28, sampleRate * 4, true);
            view.setUint16(32, numberOfChannels * 2, true);
            view.setUint16(34, 16, true);
            writeUTFBytes(view, 36, 'data');
            view.setUint32(40, length * 2, true);

            // Write audio data
            const index = 44;
            const volume = 1;
            for (let i = 0; i < length; i++) {
                view.setInt16(index + i * 2, wavBuffer[i] * (0x7FFF * volume), true);
            }

            const wavBlob = new Blob([buffer], { type: 'audio/wav' });
            resolve(wavBlob);
        };

        fileReader.readAsArrayBuffer(audioBlob);
    });
};
const writeUTFBytes = (view, offset, string) => {
    const length = string.length;
    for (let i = 0; i < length; i++) {
        view.setUint8(offset + i, string.charCodeAt(i));
    }
};

const stopRecording = () => {
    if (mediaRecorder) {
        mediaRecorder.stop();
        isRecording.value = false;
    }
};

const stopRecorCloneVoice = async () => {
    if (mediaRecordVoice.value && isRecordVoice.value) {
        mediaRecordVoice.value.stop();
        mediaRecordVoice.value.stream.getTracks().forEach(track => track.stop());
        isRecordVoice.value = false;
        clearInterval(recordingTimer.value);
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
