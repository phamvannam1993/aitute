<template>

    <Head title="Âm Nhạc - Orion AI - Đơn giản thôi mà !" />
            <div class="w-full overflow-y-auto text-black rounded-[25px] min-w-2/3">
                <form @submit.prevent="submit">

                    <div class="w-full pb-12 ">
                        <div class="flex flex-col justify-center items-start w-full gap-4">
                            <div class=" w-full md:px-[80px] bg-white shadow-xl lg:rounded-[35px]">
                                <div class="flex flex-col justify-between min-h-[660px] p-4 border-b-[3px] border-[#d6d6d6] rounded-lg w-full">
                                    <div>
                                        <div class="flex flex-col md:flex-row justify-between">
                                            <div class="flex justify-start items-center gap-2 mb-2">
                                                <h2 class="text-black font-bold text-[25px] lg:text-[30px]">Tạo nhạc</h2>
                                            </div>
                                        </div>
                                        <p class="text-xs lg:text-sm text-orion-primary font-bold mb-4">Thực hiện theo các bước sau:</p>
                                        <Step :step="1" step-name="Tùy chọn phong cách âm nhạc" />
                                        <textarea v-model="musicStyle" rows="2" placeholder="Chọn phong cách âm nhạc ..."
                                                class="resize-none w-full border-aiwow-sec rounded-lg mt-3">
                                        </textarea>
                                            <div id="app" class="max-w-md mx-auto mt-5 flex justify-around relative">
                                                    <div>
                                                        <div>
                                                            <div class="flex flex-wrap items-center justify-between md:text-sm text-[11px] text-gray-600 font-medium mb-2 ">
                                                                <div class="flex gap-5">
                                                                    <span class="cursor-pointer"
                                                                    :class="{ 'text-aiwow-sec': activeTab === 'Instrument' && !showMusicOptions }"
                                                                    @click="changeTab('Instrument')">Nhạc cụ</span>
                                                                    <span class="cursor-pointer"
                                                                    :class="{ 'text-aiwow-sec': activeTab === 'Genre' && !showMusicOptions }"
                                                                    @click="changeTab('Genre')"> Thể loại  </span>
                                                                    <span class="cursor-pointer"
                                                                    :class="{ 'text-aiwow-sec': activeTab === 'Music Type' && !showMusicOptions }"
                                                                    @click="changeTab('Music Type')">  Loại nhạc</span>
                                                                </div>
                                                                <div class="flex items-center px-2 py-1 bg-gray-200 rounded-lg hover:bg-gray-300 text-gray-700 gap-1">
                                                                    <img src="/assets/img/icon/icons8-random-options.png" class="size-5" alt="">
                                                                    <button @click="randomStyle">Ngẫu nhiên</button>
                                                                </div>
                                                            </div>
                                                            <div v-if="activeTab === 'Instrument'" class="flex flex-wrap gap-2 mt-2 max-h-[240px] md:max-h-[340px] overflow-y-auto overscroll-contain p-3 border-[1px] rounded-lg border-aiwow-sec">
                                                                <button class="px-2 py-1 md:px-4 md:py-2 bg-gray-200 rounded-lg hover:bg-aiwow-sec hover:text-white md:text-sm text-xs"
                                                                v-for="instrument in instruments"
                                                                :key="instrument"
                                                                @click="selectOption(instrument)">
                                                                {{ instrument }}
                                                                </button>
                                                            </div>
                                                            <div v-if="activeTab === 'Genre'" class="flex flex-wrap gap-2 mt-2 max-h-[240px] md:max-h-[340px] overflow-y-auto overscroll-contain p-3 border-[1px] rounded-lg border-aiwow-sec">
                                                                <button class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-aiwow-sec hover:text-white md:text-sm text-xs"
                                                                v-for="genre in genres"
                                                                :key="genre"
                                                                @click="selectOption(genre)">
                                                                {{ genre }}
                                                                </button>
                                                            </div>
                                                            <div v-if="activeTab === 'Music Type'" class="flex flex-wrap gap-2 mt-2 max-h-[240px] md:max-h-[340px] overflow-y-auto overscroll-contain p-3 border-[1px] rounded-lg border-aiwow-sec">
                                                                <button  class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-aiwow-sec hover:text-white md:text-sm text-xs"
                                                                v-for="type in musicTypes"
                                                                :key="type"
                                                                @click="selectOption(type)">
                                                                {{ type }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                        <!-- Chọn File âm thanh - Hiển thị khi createFromExistingFile được chọn -->
                                        <div class="mb-2 mt-3" v-if="createFromExistingFile">
                                            <span class="ml-1 font-semibold my-2">Chọn File âm thanh</span>
                                            <div class="flex my-3 items-center">
                                                <div class="flex items-center space-x-4 rounded-lg">
                                                    <label
                                                        class="cursor-pointer inline-flex items-center p-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-blue-600 bg-white hover:bg-green-50">
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

                                    </div>
                                    <div class="w-full flex flex-col sm:flex-row justify-between sm:items-center icon px-0 gap-3 mt-[26px]">
                                        <Step :step="2" step-name="Bấm vào đây" />

                                        <button @click="sendData(step, index)" :disabled="isLoading"
                                        v-if="!showMusicOptions" class=" px-4 text-white py-[10px] rounded-xl text-center bg-aiwow-sec w-full sm:w-1/2 flex justify-center items-center gap-1">
                                        <span v-if="!isLoading" class="text-xs lg:text-sm font-bold" >Tạo bài hát</span>
                                        <div v-else role="status" class="">
                                            <svg aria-hidden="true"
                                                class="h-6 mx-auto text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
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
                                        <div v-if="isLoading"
                                        class="text-xs md:text-sm font-medium text-center p-0.5 leading-none rounded-full transition-all duration-300"
                                        >{{ loadingPercent }} %</div>
                                    </button>
                                    </div>
                                </div>
                                <div class="w-full p-4 relative bg-white rounded-xl">
                                    <p class="text-black text-center italic font-thin mb-4">Hiển thị kết quả</p>

                                    <div class="bg-purple-100 p-6 rounded-xl min-h-[450px] shadow-lg max-w-xl mx-auto relative overflow-hidden">
                                         <!-- Layer background với ảnh mờ -->
                                         <div
                                            class="absolute inset-0 bg-center bg-cover opacity-60 filter blur-sm"
                                            :style="{ backgroundImage: `url(${resImg})` }">
                                        </div>

                                        <!-- Nội dung chính -->
                                        <div class="relative z-10">
                                            <div v-if="songs.length !== 0" class="flex justify-center gap-4 items-center h-20">
                                                <div
                                                    v-for="(song, index) in songs"
                                                    :key="index"
                                                    class="cursor-pointer transition-all duration-300"
                                                    :class="{
                                                        'w-20 h-20 lg:w-24 lg:h-24 scale-110': selectedSong === song,
                                                        'w-16 h-16 lg:w-20 lg:h-20 opacity-50': selectedSong !== song,
                                                    }"
                                                    @click="changePlayer(song)"
                                                    >
                                                    <img :src="song.imageUrl" alt="Album Art" class="w-full h-full object-cover rounded-lg" />
                                                </div>
                                            </div>
                                            <img v-else :src="resImg" alt="Album Art" class="w-32 h-32 rounded-lg mx-auto mb-4">
                                            <div class="h-56 bg-slate-100 bg-opacity-70 rounded-lg p-4 my-6">
                                                <p :class="['text-center font-semibold text-lg', { 'pr-[18px]': songs.length > 0 }]"> Lyrics</p>
                                                <p class="text-center text-sm text-gray-700 mb-4 h-40 overflow-y-auto" v-html="resLyric"></p>
                                            </div>
                                            <div class="mb-1">
                                                <span class="text-white">{{ formatTime(currentTime) }} / {{ formatTime(duration) }}</span>
                                            </div>
                                            <div class="w-full h-2 bg-gray-300 rounded cursor-pointer mb-2" @click="resAudio && seekAudio($event)">
                                                <div class="h-full bg-orion-orange rounded" :style="{ width: progress + '%' }"></div>
                                            </div>
                                            <div class="flex justify-center items-center">
                                                <button :disabled="!resAudio" @click="togglePlay"
                                                    :class="{
                                                        'bg-aiwow-sec hover:bg-orion-button-sec cursor-pointer': resAudio,
                                                        'bg-gray-300 cursor-not-allowed': !resAudio
                                                    }"
                                                    class="p-3 rounded-full text-white focus:outline-none">
                                                    <img class="w-8" v-if="isPlaying" src="/assets/img/ai_music/pause.svg" alt="pause">
                                                    <img class="w-8" v-else src="/assets/img/ai_music/play.svg" alt="play">
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="selectedSong" class="w-full flex items-center justify-center p-4 mt-2">
                                        <button class="flex gap-2 items-center border-[3px] border-aiwow-sec p-2 rounded-xl hover:scale-105" @click="convertAudioToVideo(selectedSong.id)" :disabled="isLoadingVideo">
                                            <img src="/assets/img/orion/icon/taskbar/video.svg" alt="Video" class="w-6 h-6" />
                                            <span class="font-medium">Chuyển bài hát thành Video</span>
                                        </button>
                                    </div>
                                    <div v-if="isLoadingVideo" class="flex flex-col items-center justify-center mt-4">
                                        <div class="w-10 h-10 border-4 border-t-green-500 border-gray-300 rounded-full animate-spin"></div>
                                        <p class="mt-2 text-lg font-medium">{{ loadingPercent }}%</p>
                                    </div>

                                    <!-- Hiển thị video vừa tạo -->
                                    <div v-if="currentVideoUrl" class="w-full max-w-xl mx-auto mt-4">
                                        <video controls class="w-full rounded-lg" :src="currentVideoUrl"></video>
                                    </div>
                                    <div v-if="resAudio && (musicIds.length >= 2)" class="flex flex-wrap items-center justify-end w-full gap-4 mt-4">

                                        <div class="flex justify-center gap-4 my-2">
                                            <button @click="shareAIGeneratedMedia(musicId)" class="flex gap-2 items-center">
                                                <img src="/assets/img/orion/icon/taskbar/share.svg" class="w-6 h-6" />
                                                <span class="font-medium hover:scale-105 rounded-md">Chia sẻ</span>
                                            </button>
                                            <button class="flex gap-2 items-center" @click="downloadMusic()">
                                                <img src="/assets/img/orion/icon/taskbar/download.svg" alt="Download" class="w-6 h-6" />
                                                <span class="font-medium hover:scale-105 rounded-md">Tải xuống</span>
                                            </button>
                                            <button
                                                v-if="currentVideoUrl"
                                                class="flex gap-2 items-center"
                                                @click="downloadVideo">
                                                <img src="/assets/img/orion/icon/taskbar/download.svg" alt="Download" class="w-6 h-6" />
                                                <span class="font-medium hover:scale-105 rounded-md">Tải xuống video</span>
                                            </button>

                                        </div>
                                    </div>
                                    <div class="flex items-center justify-center mt-[15px]">
                                        <a :href="route('text-to-song.history')"
                                            class="px-4 md:px-11 py-3 bg-business-primary text-white rounded-[15px] text-[15px] font-bold flex justify-center items-center gap-1 w-1/2">Lịch sử</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <Popup
        v-if="showConfirmModal"
        title="Cảnh báo !"
        message="Bạn có chắc chắn muốn xóa nội dung này không?"
        cancelButtonText="Huỷ"
        acceptButtonText="Xoá"
        @cancel="cancelDelete"
        @accept="confirmDelete"
    />


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
import CreditModal from '@/Components/ModalBuyCredit/Index.vue';
import CreditBuyModal from '@/Components/ModalBuyCredit/BuyCredit.vue';
import "1llest-waveform-vue/lib/style.css"
import PopupAff from '@/Components/PopupAff.vue';
import Popup from '@/Components/Popup.vue'
import Step from "@/Components/Step.vue";

const emit = defineEmits(["saveGenerationResult"]);
const props = defineProps({
    credits: Number,
    content: String,
    titleMusic: String,
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
const musicStyle = ref("")
const file = ref(null);
const createFromExistingFile = ref(false);
const currentLyricIndex = ref(0);
const resAudio = ref(null);
const resLyric = ref('Đây là một công cụ AI tạo nhạc mạnh mẽ. Bạn có thể tạo một bài hát bằng cách nhập một câu mô tả, chẳng hạn như "hãy tạo cho tôi một bài hát rock về tình yêu." Hoặc nhập lời bài hát cụ thể. Sau đó, bấm vào nút soạn để tạo bài hát chỉ với một cú nhấp chuột.');
const lyricsData = ref('');
const resImg = ref('/assets/svgs/aiwow_business/aiwow_bus.png');
const audio = new Audio(resAudio.value);
const share_audio1 = ref('');
const share_audio2 = ref('');
const musicIds = ref([]);
const musicId = ref('');
const isPlaying = ref(false);
const currentTime = ref(0);
const duration = ref(0);
const progress = ref(0);
const showMusicOptions = ref(false)
const instruments = ref([
  'Piano', 'Guitar', 'Electric guitar', 'Violin', 'Flute', 'Drum', 'Acoustic guitar', 'Synth', 'Drums', 'Bass', 'Saxophone', 'Trumpet', 'Cello', 'Synthesizer', 'Ukulele', 'Harp', 'Accordion', 'Harmonica', 'Tuba', 'Trombone', 'French Horn', 'Recorder', 'Xylophone', 'Marimba', 'Glockenspiel', 'Vibraphone', 'Steel Drums', 'Conga', 'Bongo', 'Triangle', 'Tambourine', 'Maracas', 'Cymbals', 'Timpani', 'Cajon', 'Djembe', 'Sitar', 'Guzheng', 'Pipa', 'Erhu', 'Dizi', 'Sheng', 'Hulusi', 'Xun', 'Banjo', 'Mandolin', 'Clarinet', 'Oboe', 'Viola', 'Organ', 'Electric Bass', 'Electric Piano', 'MIDI Keyboard', 'Drum Machine', 'Sequencer', 'Mixer', 'Audio Interface'
]);
const genres = ref(['Pop', 'Rock', 'Rap', 'Metal', 'Electronic', 'Hip Hop', 'Trap', 'Country', 'Jazz', 'Blues', 'Classical', 'Folk', 'Soul', 'Funk', 'Reggae', 'Punk', 'R&B', 'Disco', 'Indie', 'Alternative', 'World Music', 'Latin', 'New Age', 'Experimental', 'Ambient', 'Post-Rock', 'EDM', 'Hardcore', 'Industrial', 'Gothic', 'Rap Rock', 'Fusion Jazz', 'Bluegrass', 'Folk Rock', 'Heavy Metal', 'Death Metal', 'Black Metal', 'Progressive Metal', 'Alternative Rock', 'Garage Rock', 'Punk Rock', 'Grunge', 'Blues Rock', 'Hard Rock', 'Psychedelic Rock', 'Progressive Rock', 'Glam Rock', 'Electronic Rock', 'Alternative Metal', 'Nu Metal', 'Industrial Metal', 'Rap Metal', 'Gangsta Rap', 'Alternative Hip Hop', 'Jazz Rap', 'House', 'Techno', 'Drum and Bass', 'Dubstep', 'Ambient House', 'Dub', 'Acid House', 'Cool Jazz', 'Bebop', 'Free Jazz', 'Swing Jazz', 'Blues Jazz', 'Country Blues', 'Chicago Blues', 'Baroque', 'Romantic', 'Modern Classical', 'Minimalism', 'Neoclassical', 'Opera', 'Alternative Country', 'Contemporary Folk', 'Traditional Folk', 'Celtic', 'African', 'Latin Jazz', 'Salsa', 'Samba', 'Bossa Nova', 'Reggae Rock', 'Ska', 'Funk Rock', 'New Wave', 'Post-Punk', 'Gothic Rock', 'Industrial Rock', 'Noise Rock', 'Dream Pop', 'Shoegaze', 'Post-Metal', 'Post-Hardcore', 'Emo', 'Math Rock', 'Atmospheric Black Metal', 'Symphonic Metal', 'Folk Metal', 'Viking Metal', 'Electronicore', 'Trap Metal', 'Post-Britpop', 'New Psychedelia', 'Space Rock', 'Art Rock', 'New Romanticism', 'Synthpop', 'Future Bass', 'Vaporwave', 'Retrowave', 'Electropop', 'Tropical House', 'Deep House', 'Tech House', 'Minimal Techno', 'Hard Techno', 'Industrial Techno', 'Liquid Drum and Bass', 'Neurofunk', 'Breakbeat', 'Big Beat', 'Trap (EDM)', 'Future House', 'Post-Dubstep', 'Ambient Dubstep', 'Experimental Electronic', 'Hyperpop', '8-bit Music', 'Synthwave', 'Ballad', 'Dance', 'J-pop', 'Orchestral', 'Psychedelic', 'Progressive', 'K-pop', 'Pop Rock', 'Cantonese', 'Gospel', 'Phonk'
]);
const musicTypes = ref(['Upbeat', 'Melodic', 'Dark', 'Epic', 'Bass', 'Emotional', 'Acoustic', 'Cheerful', 'Sad', 'Passionate', 'Calm', 'Excited', 'Melancholic', 'Mysterious', 'Tense', 'Relaxed', 'Anxious', 'Angry', 'Gentle', 'Intense', 'Dreamy', 'Joyful', 'Depressed', 'Hopeful', 'Fearful', 'Humorous', 'Solemn', 'Energetic', 'Gloomy', 'Warm', 'Cold', 'Profound', 'Sorrowful', 'Comforting', 'Lonely', 'Nostalgic', 'Uplifting', 'Contemplative', 'Thrilling', 'Peaceful', 'Frenzied', 'Elegant', 'Rugged', 'Sweet', 'Moody', 'Exuberant', 'Worried', 'Content', 'Lost', 'Confident', 'Sensitive', 'Strong', 'Vulnerable', 'Enthusiastic', 'Indifferent', 'Sympathetic', 'Doubtful', 'Determined', 'Confused', 'Serene', 'Restless', 'Delightful', 'Heavy', 'Light', 'Stirring', 'Comfortable', 'Uneasy', 'Sacred', 'Secular', 'Transcendent', 'Simple', 'Elaborate', 'Sunny', 'Bright', 'Hazy', 'Clear', 'Bewildered', 'Cozy', 'Distant', 'Intimate', 'Majestic', 'Subtle', 'Overwhelming', 'Ethereal', 'Grounded', 'Radical', 'Conservative', 'Avant-garde', 'Modern', 'Futuristic'
]);

const activeTab = ref('Instrument');
const activeTabOptions = ref('Instrument');


const popupMessage = ref('Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!')
const acceptUpgrade = ref(true)

const sunoTaskId = ref(null);

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
onMounted(() => {
    if (props.content) {
        content.value = props.content
    }
})
watch(props.content, (newContent) => {
        content.value = newContent;
});


const changeTab = (tab) => {
    if (!showMusicOptions.value){
        activeTab.value = tab;
    } else {
        activeTabOptions.value = tab;
    }
};

const selectOption = (option) => {
      musicStyle.value += option + ",";
};

const randomStyle = () => {
    // random each options
  const randomInstrument = instruments.value[Math.floor(Math.random() * instruments.value.length)];
  const randomGenre = genres.value[Math.floor(Math.random() * genres.value.length)];
  const randomMusicType = musicTypes.value[Math.floor(Math.random() * musicTypes.value.length)];

  musicStyle.value = `${randomInstrument} , ${randomGenre} , ${randomMusicType} `;
};

const togglePlay = () => {
  if (isPlaying.value) {
    audio.pause();
  } else {
    audio.play();
  }
};

const videoUrls = ref({});
const selectedSong = ref(null);

const currentVideoUrl = computed(() => {
  return selectedSong.value && videoUrls.value[selectedSong.value.id]
    ? videoUrls.value[selectedSong.value.id]
    : null;
});

const changePlayer = (song) => {
    if(musicId.value === share_audio1.value){
        musicId.value = share_audio2.value
    }else if(musicId.value === share_audio2.value){
        musicId.value = share_audio1.value
    }
    selectedSong.value = song;
    resImg.value = song.imageUrl;
    resLyric.value = formattedText(song.prompt);
    resAudio.value = song.audioUrl;

    audio.src = resAudio.value;
    audio.load();

    isPlaying.value = false;

    audio.onplay = null;
    audio.onpause = null;
    progress.value = 0;
    audio.addEventListener('loadedmetadata', () => {
        duration.value = audio.duration;
    });
};

const videoUrl = ref(null);
const isLoadingVideo = ref(false)
const convertAudioToVideo = async (audioId) => {
    isLoadingVideo.value = true;
    try {
        const hasEnoughCredit = await checkCredit();
        if (!hasEnoughCredit) {
            isLoadingVideo.value = false;
            return;
        }

        if (!sunoTaskId.value || !audioId) {
            toast.warn('Thiếu thông tin audioId hoặc taskId của audio');
            isLoadingVideo.value = false;
            return;
        }

        loadingPercent.value = 0;
        const callBackUrl = route("text-to-song.convert-audio-to-video");

        const simulateLoading = () => {
            if (loadingPercent.value < 99) {
                const increment = Math.floor(Math.random() * 4) + 2;
                loadingPercent.value = Math.min(loadingPercent.value + increment, 99);
                setTimeout(simulateLoading, 8000);
            }
        };
        simulateLoading();

        const dataRequest = {
            audioId: audioId,
            taskId: sunoTaskId.value,
            callBackUrl: callBackUrl,
        };

        const response = await fetch(route("text-to-song.convert-audio-to-video"), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(dataRequest),
        });

        if (!response.ok) {
            const dataResponse = await response.json();
            if(response.status === 500) {
                toast.warning("Hệ thống đang quá tải, vui lòng thử lại sau.");
                console.error("Chi tiết lỗi => ", dataResponse.message);
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
            videoUrls.value[selectedSong.value.id] = data.data.videoUrl;
            emit("saveGenerationResult", data.data.videoUrl)
            toast.success("Chuyển đổi video thành công!");
        } else {
            toast.warning("Hệ thống đang quá tải, vui lòng thử lại sau.");
        }
    } catch (error) {
        if (error.message.includes('504')) {
            toast.warning("Hệ thống đang bảo trì, vui lòng thử lại sau.");
        } else {
            console.error("Lỗi xảy ra:", error);
        }
    } finally {
        isLoadingVideo.value = false;
    }
};

const downloadVideo = () => {
  const url = currentVideoUrl.value;
  if (!url) return;

  // Tạo thẻ <a> ẩn để tải
  const link = document.createElement("a");
  link.href = url;
  link.setAttribute("download", `video-${selectedSong.value?.title || 'untitled'}.mp4`);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};


const formattedText = (originalText) => {
    return originalText
        .replace(/\*\*/g, '') // Thay thế tất cả dấu ** thành rỗng
        .replace(/(Verse \d+|Chorus|Outro):/g, '<br><strong>$1:</strong><br>') // Xuống dòng trước và sau các tiêu đề như Verse 1, Chorus, Outro
        .replace(/: /g, ':<br>') // Thêm xuống dòng sau dấu `: `
        .replace(/, /g, ',<br>') // Thêm xuống dòng sau dấu phẩy
        .replace(/\n/g, '<br>'); // Thay thế xuống dòng thành <br>
};

const getGeneratedPrompt = async (data) => {
    const dataRequest = {
            promt : data,
            musicTypes : musicStyle.value,
        }
    const response = await fetch(route("text-to-song.summary"), {
            method: 'POST',
            headers: {
                    'Content-Type': 'application/json',
                },
            body : JSON.stringify(dataRequest)
    })

    if(response.ok){
            const data = await response.json();
            if(data.success){
                return data.data;
            }
    }else {
            toast.error("Hệ thống đang quá tải vui lòng thử lại sau");
            return false;
        }
}

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

onMounted(() => {
  audio.addEventListener('loadedmetadata', () => {
    duration.value = audio.duration;
  });
  audio.addEventListener('timeupdate', () => {
    currentTime.value = audio.currentTime;
    progress.value = (audio.currentTime / audio.duration) * 100;

//     lyricsData.value.forEach((line, index) => {
//     if (audio.currentTime >= line.time) {
//       currentLyricIndex.value = index;
//     }
//   });
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
const downloadMusic = async () => {
    if (resAudio.value) {
        try {
            // dow file from s3
            const response = await fetch(resAudio.value);
            if (!response.ok) throw new Error('Không thể tải file từ S3.');

            // Blob
            const blob = await response.blob();
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.href = url;
            link.download = 'audio-file.mp3';
            link.click();

            // Hủy object URL sau khi tải xuống
            URL.revokeObjectURL(url);
        } catch (error) {
            console.error('Lỗi tải xuống:', error);
            toast.warning('Không thể tải tệp âm thanh xuống!');
        }
    } else {
        toast.warning('Không có tệp âm thanh để tải xuống!');
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
const songs = ref([]);
const loadingPercent = ref(0);

const sendData = async () => {
    isLoading.value = true;
    try {
        isUpdatingFromAPI = true;
        const hasEnoughCredit = await checkCredit();
        if (!hasEnoughCredit) {
            isLoading.value = false;
            return;
        }

        if (!content.value) {
            toast.warn('Vui lòng mô tả nội dung bài hát');
            isLoading.value = false;
            return;
        }
        loadingPercent.value = 0;

        const callBackUrl = route("text-to-song.generate-song");
        const simulateLoading = () => {
            if (loadingPercent.value < 99) {
                const increment = Math.floor(Math.random() * 4) + 2;
                loadingPercent.value = Math.min(loadingPercent.value + increment, 99);
                setTimeout(simulateLoading, 8000);
            }
        };
        simulateLoading();
        resAudio.value = null;

        const dataRequest = {
            prompt: content.value,            // Mô tả nội dung bài hát hoặc lời bài hát
            style: musicStyle.value,        // Ví dụ: "Classical"
            title: props.titleMusic,        // Ví dụ: "Peaceful Piano Meditation"
            customMode: true,
            instrumental: false,            // false nếu muốn có lời; true nếu chỉ cần nhạc nền
            model: 'V4',
            callBackUrl: callBackUrl,
        };

        const response = await fetch(route("text-to-song.generate-song"), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(dataRequest),
        });

        if (!response.ok) {
            const dataResponse = await response.json();
            if (response.status == 500) {
                toast.warning("Hệ thống đang quá tải, vui lòng thử lại sau.")
                console.log("Chi tiết lỗi => ", dataResponse.message)
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.success){
            sunoTaskId.value = data.data.taskId;
            songs.value = data.data.sunoData;
            changePlayer(songs.value[0]);
            setTimeout(async () => {
                saveData(data.data.sunoData);
            }, 1000);
        } else {
            toast.warning("Hệ thống đang quá tải, vui lòng thử lại sau.")
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

// const sendData = async () => {
//     isLoading.value = true;
//     try {
//         isUpdatingFromAPI = true;
//         const hasEnoughCredit = await checkCredit();
//         if (!hasEnoughCredit) {
//             isLoading.value = false;
//             return;
//         }

//         if (!content.value) {
//             toast.warn('Vui lòng mô tả nội dung bài hát');
//             isLoading.value = false;
//             return;
//         }
//         loadingPercent.value = 0;

//         const simulateLoading = () => {
//             if (loadingPercent.value < 99) {
//                 const increment = Math.floor(Math.random() * 4) + 2;
//                 loadingPercent.value = Math.min(loadingPercent.value + increment, 99);
//                 setTimeout(simulateLoading, 8000);
//             }
//         };
//         simulateLoading();
//         resAudio.value = null;
//         const lyricsData = content.value;
//         const summaryPromt = await getGeneratedPrompt(props.titleMusic)
//         const dataRequest = {
//             content : summaryPromt,
//             lyrics : lyricsData,
//             musicTitle : props.titleMusic,
//         }

//         const response = await fetch(route("text-to-song.song"), {
//                 method: 'POST',
//                 headers: {
//                     'Content-Type': 'application/json',
//                 },
//                 body: JSON.stringify(dataRequest),
//         });

//         if (!response.ok) {
//             const dataResponse = await response.json();
//                 if(response.status == 500) {
//                     toast.warning("Hệ thống đang quá tải, vui lòng thử lại sau.")
//                     console.log("Chi tiết lỗi => ", dataResponse.message)
//                 }
//                 throw new Error(`HTTP error! status: ${response.status}`);
//         }

//         const data = await response.json();

//         if (data.success){
//             songs.value = data.data;
//             changePlayer(songs.value[0]);
//             setTimeout(async() =>{
//                 saveData(data.data);
//             }, 1000);
//         } else {
//             toast.warning("Hệ thống đang quá tải, vui lòng thử lại sau.")
//         }
//     } catch (error) {
//         if (error.message.includes('504')) {
//             toast.warning("Hệ thống đang bảo trì, vui lòng thử lại sau.")
//         } else {
//             console.error("Lỗi xảy ra:", error);
//         }
//     } finally {
//         isLoading.value = false;
//         isUpdatingFromAPI = false;
//     }
// };

const saveData = async (data) => {
    try {
        console.log(data)
        const songTitle = data[0].title;
        const response = await axios.post(route("text-to-song.store"), {
            prompt: songTitle,
            data : data
        });
        history.value.push(response.data.response);
        if (response.data.success) {
            toast.success("Lưu dữ liệu thành công");
            musicIds.value = response.data.response;
            share_audio1.value = response.data.response[0].id;
            share_audio2.value = response.data.response[1].id;
            musicId.value = response.data.response[0].id;
            // return response.data.response.data;
        }else{
            toast.success("Hệ thống đang quá tải, vui lòng thử lại sao.");
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

.fade-scale-enter-active, .fade-scale-leave-active {
    transition: opacity 0.5s ease, transform 0.5s ease;
}

.fade-scale-enter-from, .fade-scale-leave-to {
    opacity: 0;
    transform: scale(0.95);
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
