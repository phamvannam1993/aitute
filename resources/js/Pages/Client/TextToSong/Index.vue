<template>

    <Head title="Âm Nhạc - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData">
            <div class="w-full overflow-y-auto text-black rounded-[25px] min-w-2/3 py-2 px-3">
                <form @submit.prevent="submit">

                    <div class="w-full pb-12">
                        <div class="flex flex-col justify-center items-start md:flex-row gap-4">
                            <div class="md:w-1/2 w-full max-w-xl bg-white shadow-[0_3px_10px_rgb(0,0,0,0.2)] rounded-xl">
                                <div class="flex flex-col justify-between min-h-[560px] p-4 rounded-lg ">
                                    <div>
                                        <div class="flex flex-col md:flex-row justify-between">
                                            <div class="flex justify-start items-center gap-2">
                                                <img class="bg-[#D4D4D4] p-2 rounded-2xl" src="/assets/img/aiwow/robot.png" />
                                                <h2 class="text-black font-bold text-[25px] lg:text-[30px]">Bài hát từ văn bản</h2>
                                            </div>
                                        </div>
                                        <p class="text-xs lg:text-sm text-[#092A99] font-bold mb-4">Thực hiện theo các bước sau:</p>

                                        <label for="music-description"
                                            class="text-xs lg:text-sm flex items-center gap-1 w-full lg:w-fit">
                                            <div
                                                class="flex items-center py-1 px-2 rounded-2xl text-white bg-primary-color h-fit">
                                                <img class="h-2 w-4" src="/assets/img/aiwow/right_arrow.png" />
                                                <p class="text-xs lg:text-sm font-semibold">Bước 1</p>
                                            </div>
                                            <div class="flex flex-col gap-1">
                                                <p class="text-xs lg:text-sm font-semibold">Mô tả nội dung bài hát</p>
                                            </div>
                                        </label>
                                        <div class="relative  mt-2">
                                            <DescriptionSuggestionMusic v-model="content" :screenId="10" />
                                            <textarea v-model="content" rows="15" placeholder="Mô tả ngắn gọn chủ đề âm nhạc"
                                                class="resize-none w-full border-[#2C75E3] rounded-lg "></textarea>
                                            <div class="object-mic relative ml-auto">
                                                            <div
                                                                v-if="isRecording"
                                                                class="outline-mic right-3 bottom-3 flex items-center justify-center"
                                                            ></div>
                                                            <div
                                                                v-if="isRecording"
                                                                class="outline-mic right-3 bottom-3 flex items-center justify-center"
                                                                id="delayed"
                                                            ></div>
                                                            <div
                                                                v-if="isRecording"
                                                                class="button-mic right-3 bottom-3 flex items-center justify-center"
                                                            ></div>
                                                            <button
                                                                class="button-mic icon-mic absolute right-3 bottom-3 flex items-center justify-center"
                                                                @click="startRecording"
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
                                        </div>
                                        <!-- Chọn File âm thanh - Hiển thị khi createFromExistingFile được chọn -->
                                        <div class="mb-2 mt-3" v-if="createFromExistingFile">
                                            <span class="ml-1 font-semibold my-2">Chọn File âm thanh</span>
                                            <div class="flex my-3 items-center">
                                                <div class="flex items-center space-x-4 rounded-lg">
                                                    <label
                                                        class="cursor-pointer inline-flex items-center p-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-blue-600 bg-white hover:bg-blue-50">
                                                        <img src="/assets/svgs/upload-icon.svg" class="pr-2" />
                                                        <template v-if="file">
                                                            {{ file.name || '' }}
                                                        </template>
                                                        <template v-else>
                                                            Chọn tài liệu
                                                        </template>
                                                        <input type="file" class="hidden" @change="handleFileUpload" />
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div :class="content.length>=100 ? 'text-red-600'  : 'text-gray-700'" class="text-end ">{{content.length}}/100</div>
                                    </div>
                                    <div class="w-full flex flex-col sm:flex-row justify-between sm:items-center icon px-0 gap-3 mt-[26px]">
                                        <label for="music-description" class="text-xs lg:text-sm flex items-center gap-1 w-2/3 lg:w-fit">
                                            <div class="flex items-center py-1 px-2 rounded-2xl text-white bg-primary-color h-fit">
                                                <img class="h-2 w-4" src="/assets/img/aiwow/right_arrow.png" />
                                                <p class="text-xs lg:text-sm font-semibold">Bước 2</p>
                                            </div>
                                            <p class="text-xs lg:text-sm text-[#092A99] font-bold">Bấm vào đây</p>

                                        </label>
                                        <button @click="sendData(step, index)" :disabled="isLoading"
                                        class="px-4 text-white py-[10px] rounded-xl text-center bg-[#2C75E3] w-full sm:w-1/2 flex justify-center items-center">
                                        <span v-if="!isLoading" class="text-xs lg:text-sm font-bold" >Tạo bài hát</span>
                                        <div v-else role="status" class="w-full">
                                            <svg aria-hidden="true"
                                                class="w-8 h-8 mx-auto text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
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
                            <div class="md:w-1/2 max-w-xl p-4 relative bg-white shadow-[0_3px_10px_rgb(0,0,0,0.2)] rounded-xl">
                                <p class="text-primary-color italic font-thin mb-4">Hiển thị kết quả</p>

                                <div class="bg-purple-100 p-6 rounded-xl min-h-[450px] shadow-lg max-w-xl mx-auto relative overflow-hidden">
                                    <!-- Layer background với ảnh mờ -->
                                    <div
                                        class="absolute inset-0 bg-center bg-cover opacity-60 filter blur-sm"
                                        :style="{ backgroundImage: `url(${resImg})` }">
                                    </div>

                                    <!-- Nội dung chính -->
                                    <div class="relative z-10">
                                        <img :src="resImg" alt="Album Art" class="w-32 h-32 rounded-lg mx-auto mb-4">

                                        <div class="h-56 bg-slate-100 bg-opacity-70 rounded-lg p-4 mb-4">
                                            <p class="text-center font-semibold text-lg">Lyrics</p>
                                            <p class="text-center text-sm text-gray-700 mb-4 h-40 overflow-y-auto" v-html="resLyric"></p>
                                        </div>

                                        <div class="mb-1">
                                            <span class="text-white">{{ formatTime(currentTime) }} / {{ formatTime(duration) }}</span>
                                        </div>

                                        <div class="w-full h-2 bg-gray-300 rounded cursor-pointer mb-2" @click="resAudio && seekAudio($event)">
                                            <div class="h-full bg-purple-500 rounded" :style="{ width: progress + '%' }"></div>
                                        </div>

                                        <div class="flex justify-center items-center">
                                            <button :disabled="!resAudio" @click="togglePlay"
                                                :class="{
                                                    'bg-purple-500 hover:bg-purple-600 cursor-pointer': resAudio,
                                                    'bg-gray-300 cursor-not-allowed': !resAudio
                                                }"
                                                class="p-3 rounded-full text-white focus:outline-none">
                                                <img class="w-8" v-if="isPlaying" src="/assets/img/ai_music/pause.svg" alt="pause">
                                                <img class="w-8" v-else src="/assets/img/ai_music/play.svg" alt="play">
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="resAudio" class="flex flex-wrap items-center justify-end w-full gap-4 mt-4">
                                    
                                    <div class="flex justify-end gap-4 my-2">
                                        <button @click="shareAIGeneratedMedia(musicId)" class="flex gap-2 items-center">
                                            <img src="/assets/svgs/aiwow/share.svg" class="w-6 h-6" />
                                            <span class="font-medium hover:scale-105 rounded-md">Chia sẻ</span>
                                        </button>
                                        <button class="flex gap-2 items-center" @click="downloadMusic()">
                                            <img src="/assets/svgs/aiwow/download.svg" alt="Download" class="w-6 h-6" />
                                            <span class="font-medium hover:scale-105 rounded-md">Tải xuống</span>
                                        </button>

                                    </div>
                                </div>
                                <div class="flex items-center justify-end mt-[15px]">
                                    <a :href="route('text-to-song.history')"
                                        class="px-4 md:px-11 py-3 bg-[#2C75E3] text-white rounded-[15px] text-[15px] font-bold flex justify-center items-center gap-1 w-1/2">Lịch sử</a>
                                </div>
                            
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div v-if="showConfirmModal" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-30">
                <div
                    class="bg-[url('../../public/assets/img/bg-modal.png')] bg-cover bg-center rounded-lg p-6 shadow-lg absolute z-[51] md:w-[500px] md:h-[346px] md:rounded-[41px] flex flex-col justify-start items-center">
                    <img class="w-40" src="/assets/img/model-delete-icon.png" />
                    <h3 class="text-lg font-semibold text-black">Cảnh báo !</h3>
                    <p class="text-black">
                        Bạn có chắc chắn muốn xóa nội dung này không?
                    </p>
                    <div class="flex justify-center mt-4 p-2">
                        <button @click="cancelDelete" :disabled="isDeleteLoading"
                            class="px-4 md:px-11 py-2 border-[1px] border-[#2C75E3] text-black font-semibold rounded-[15px] mr-2">
                            Huỷ
                        </button>
                        <button @click="confirmDelete" :disabled="isDeleteLoading"
                            class="px-4 md:px-11 py-2 bg-[#2C75E3] text-white font-semibold rounded-[15px]">
                            <span v-if="!isDeleteLoading">Xoá</span>
                            <div v-else role="status">
                                <svg aria-hidden="true"
                                    class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
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
                <div class="fixed inset-0 bg-black opacity-50"></div>
            </div>
        </Layout>

    <Modal :show="isShowShareLinkModal" maxWidth="4xl" @close="closeShareLink">
        <FormShareLink :shareLink="shareLink" />
    </Modal>
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="acceptUpgrade"/>
    <CreditModal :showCreditPopup="showCreditPopup" @buyCredit="handleBuyCredit" @cancel="handleCancel" />
    <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup"
                    @buyCredit="handleBuyCredit"
                    @cancel="handleBuyCancel"
                    :currentCredit = "pageProps.auth.user.credit || 0"
                    :additionalCredit = "additionalCredit"
    />
</template>

<script setup>
import DescriptionSuggestionMusic from "@/Components/AIVirtual/DescriptionSuggestionMusic.vue";
import FormShareLink from '@/Components/FormShareLink.vue';
import Modal from '@/Components/Modal.vue';
import {Head, usePage} from "@inertiajs/vue3";
import axios from "axios";
import 'suneditor/dist/css/suneditor.min.css'; // Import CSS 
import { computed, onBeforeMount, onMounted, reactive, ref, watch } from "vue";
import { toast } from "vue3-toastify";
import Footer from "../Home/Components/Footer.vue";
import CreditModal from '../../../Components/ModalBuyCredit/Index.vue';
import CreditBuyModal from '../../../Components/ModalBuyCredit/BuyCredit.vue';
import "1llest-waveform-vue/lib/style.css"
import PopupAff from '@/Components/PopupAff.vue';
import Layout from "@/Layouts/Client/AiLayout.vue";

const props = defineProps({
    ai_assistant: Object,
    job: Object,
    operation: Object,
    credits: Number,
});
const breadcrumbsData = [
    { text: "Bài hát", link: "text-to-song.index" },
];
const { props: pageProps } = usePage();
const auth = computed(() => pageProps.value.auth);
const additionalCredit = ref(0);
const isShowShareLinkModal = ref(false);
const showAffKeyPopup = ref(false);
const shareLink = ref(null);
const credits = ref(props.credits);
const ai_assistant = ref(props.ai_assistant);
let messageIdToDelete = ref(null);
const isLoading = ref(false);
const isDeleteLoading = ref(false);
const history = ref([]);
const content = ref("");
const file = ref(null);
const createFromExistingFile = ref(false);
const currentLyricIndex = ref(0);
const resAudio = ref(null);
const resLyric = ref('Đây là một công cụ AI tạo nhạc mạnh mẽ. Bạn có thể tạo một bài hát bằng cách nhập một câu mô tả, chẳng hạn như "hãy tạo cho tôi một bài hát rock về tình yêu." Hoặc nhập lời bài hát cụ thể. Sau đó, bấm vào nút soạn để tạo bài hát chỉ với một cú nhấp chuột.');
const lyricsData = ref('');
const resImg = ref('/assets/img/aiwow/ai_music/default.png');
const audio = new Audio(resAudio.value);
const musicId = ref('')
const isPlaying = ref(false);
const currentTime = ref(0);
const duration = ref(0);
const progress = ref(0);

const popupMessage = ref('Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!')
const acceptUpgrade = ref(true)

const formatLyrics = (lyrics) => {
    return lyrics
        .replace(/\[([A-Za-z]+)\]/g, '<p><strong>[$1]</strong></p>')
        .replace(/\n/g, '<br>');
}
const parseLyrics = (lyricsString) => {
  const lines = lyricsString.split('\n').filter(line => line.trim() !== '');

  let time;
  if (duration.value < 170) {
    time = 0;
  } else if (duration.value >= 170 && duration.value <= 190) {
    time = 5;
  } else if(duration.value > 190) {
    time = 10;
  } else {
    time = 0;
  }
  console.log(time)
  console.log('duration.value: ', duration.value)
  return lines.map((line) => {
    const isLyric = !line.match(/^\[.*\]$/);

    const parsedLine = {
      time: time,
      text: line.trim(),
    };

    if (isLyric) {
      time += 5;
    }

    return parsedLine;
  });
};


const togglePlay = () => {
  if (isPlaying.value) {
    audio.pause();
  } else {
    audio.play();
  }
};

const seekAudio = (event) => {
  const progressBar = event.currentTarget;
  const clickX = event.offsetX;
  const progressBarWidth = progressBar.clientWidth;
  const seekTime = (clickX / progressBarWidth) * audio.duration;
  audio.currentTime = seekTime;
};
watch(resAudio, (newAudioSrc) => {
  audio.src = newAudioSrc;
  audio.load();
});
watch(content, (newContent) => {
    if (newContent.length > 100) {
        content.value = newContent.slice(0, 100);
    }
});
onMounted(() => {
  audio.addEventListener('loadedmetadata', () => {
    duration.value = audio.duration;
  });
  audio.addEventListener('timeupdate', () => {
    currentTime.value = audio.currentTime;
    progress.value = (audio.currentTime / audio.duration) * 100;

    lyricsData.value.forEach((line, index) => {
    if (audio.currentTime >= line.time) {
      currentLyricIndex.value = index;
    }
  });
  });
  audio.addEventListener('play', () => {
    isPlaying.value = true;
  });
  audio.addEventListener('pause', () => {
    isPlaying.value = false;
  });
});
const shareAIGeneratedMedia = async (id) => {
    try {
        const response = await axios.post(route("share-link.store"), {
            share_linkable_id: id,
            share_linkable_type: 'Music',
        });
        shareLink.value = route("share-link.show", { 'token': response.data.data.link_token })
        openShareLink();
    } catch (error) {
        toast.error("Chia sẻ ảnh thất bại");
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
const formatTime = (time) => {
  const minutes = Math.floor(time / 60);
  const seconds = Math.floor(time % 60).toString().padStart(2, '0');
  return `${minutes}:${seconds}`;
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
        formData.append('type', 'music_to_text');

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
const sendData = async () => {
    isLoading.value = true;
    try {
        isUpdatingFromAPI = true;
        const hasEnoughCredit = await checkCredit();
        if (!hasEnoughCredit) {
            isLoading.value = false;
            return; // Dừng nếu không đủ credit
        }
        // Kiểm tra nếu chưa có nội dung
        if (!content.value) {
            toast.warn('Vui lòng mô tả nội dung bài hát');
            isLoading.value = false;
            return;
        }

        resAudio.value = null;

        const formData = new FormData();
        formData.append('content', content.value);
        // formData.append('continue', createFromExistingFile.value);

        // Nếu người dùng tick "Tạo mới dựa trên file có sẵn" và có file
        if (file.value) {
            formData.append('audioFile', file.value);
        }

        const response = await fetch(route("text-to-song.song"), {
                method: 'POST',
                body: formData,

        });

        if (!response.ok) {
                if(response.status == 500) {
                alert("Chức năng này đang bảo trì xin vui lòng thử lại sau")
                return false
                }
                throw new Error(`HTTP error! status: ${response.status}`);
            }

        const data = await response.json();
        if (data.success){
            resAudio.value = data.data.resultAudio['url'];
            resImg.value = data.data.resultImage['url'];
            resLyric.value = data.data.responeLyric;
            audio.src = resAudio.value;
            audio.load(); 
            console.log(musicId.value)
            audio.addEventListener('loadedmetadata', () => {
                duration.value = audio.duration;
                lyricsData.value = parseLyrics(data.data.responeLyric);
            });
            saveData(data.data); 
        }
    } catch (error) {
        if (error.message.includes('504')) {
            toast.warning("Hệ thống đang bảo trì, vui lòng thử lại sau.")
        } else {
            console.error("Lỗi xảy ra:", error);
        }
    } finally {
        isLoading.value = false;
        isUpdatingFromAPI = false;
    }
};

const saveData = async (data,allowToast = true) => {
    try {

        const response = await axios.post(route("text-to-song.store"), {
            prompt: content.value,
            result_audio: data.resultAudio['path'],
            image_url: data.resultImage['path'],
            lyrics:  data.responeLyric,
        });

        history.value.push(response.data.response.data);
        if (response) {
            if (allowToast) {
                toast.success("Lưu dữ liệu thành công");
            }
            musicId.value = response.data.response.id;
            return response.data.response.data;
        }
        return {};
    } catch (error) {
        console.log(error)
        return {};
    }
};

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
