<template>

    <Head>
        <title>Chia sẻ âm nhạc</title>
        <meta head-key="og-title" property="og:title" content="AI 3 GỐC - Cộng đồng AI tử tế" />
        <meta head-key="og-image" property="og:image" :content="url" />
        <meta head-key="og-description" property="og:description" content="AI 3 GỐC - Cộng đồng AI tử tế" />
        <meta head-key="og-site_name" property="og:site_name" content="Orion AI" />
        <meta head-key="og-type" property="og:type" content="website" />
    </Head>

    <div
        class="relative w-full mx-auto bg-[url('../../public/assets/img/aiwow/layout_background.png')] bg-cover bg-center min-h-screen p-5 lg:p-10 text-xs lg:text-sm lg:px-[15%]">
        <div class="flex gap-3 text-[#2C75E3] mt-14 ml-2 mb-6 text-sm">
            <a :href="route('home.index')">
                <img src="/assets/img/admin/icon_left_home.png" alt="Trang chủ" class="w-auto h-[19px]" />
            </a>
            <span>/</span>
            <span>
                Share Link
            </span>
        </div>
        <div class="w-full overflow-y-auto text-center bg-white text-black rounded-[25px] min-w-2/3 py-2 px-3">
            <div class="w-full p-4 rounded-lg relative">
                <div class="absolute inset-0 bg-center bg-cover opacity-60 filter blur-sm"
                    :style="{ backgroundImage: `url(${params['image_url'] || resImg})` }">
                </div>
                <div class="relative z-10">
                    <div class="text-center text-2xl capitalize mb-4">
                        {{ params['prompt'] }}
                    </div>
                    <img :src="params['image_url'] || resImg" alt="Album Art" class="w-32 h-32 rounded-lg mx-auto mb-4">
                    <!-- Audio Player -->
                    <audio controls class="md:w-1/2 w-full mx-auto rounded-md shadow-sm" :key="params['url']" ref="audioPlayer"
                        @timeupdate="updatePlayhead" @play="handlePlay" @pause="handlePause" @ended="handleEnd">
                        <source :src="params['url']" type="audio/mpeg" />
                        Trình duyệt của bạn không hỗ trợ phần tử âm thanh.
                    </audio>

                    <!-- Lyrics -->
                    <div class="text-center h-60 overflow-y-auto mt-4 bg-slate-100 bg-opacity-70 md:w-1/2 mx-auto rounded-md">
                        <h2 class="font-bold">Lyrics</h2>
                        <p v-html="formatLyrics(params['lyrics'])"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>


</template>

<script setup>
import Layout from "@/Layouts/Client/ClientLayout.vue";
import { Head } from "@inertiajs/vue3";
import { IllestWaveform } from "1llest-waveform-vue"
import { reactive } from "vue";
import { ref } from "vue";
defineOptions({ layout: Layout });
defineProps({
    params: Object,
});
const waveOptions = reactive({
    url: '/assets/audio/default_music.wav',
    lineWidth: 1,
    lineColor: "#3b82f6",
    cursorWidth: 2,
    cursorColor: "#000",
    interact: false,
    fade: true,
})
const waveformRef = ref(null);
const playing = ref(false);
const currentTime = ref("0:00");
const durationTime = ref("0:00");
const progressBarWidth = ref(0); // Thanh tiến trình
const audioPlayer = ref(null);
const resImg = ref('/assets/img/ai_music/default.png');
const initHandler = (v) => { };
const fetchedHandler = (v) => { };
const readyHandler = (v) => {
    getDuration();
};
const finishHandler = (v) => { };
// Hàm xử lý nhấn vào waveform
const clickHandler = (event) => {
    const waveformElement = waveformRef.value;
    const clickX = event.offsetX;
    const percentage = (clickX / waveformElement.offsetWidth) * 100;
    playheadPosition.value = percentage;
    const audio = audioPlayer.value;
    const duration = audio.duration;
    audio.currentTime = (percentage / 100) * duration; // Tính thời gian dựa trên tỷ lệ
};
const playheadPosition = ref(0);  // Vị trí thanh dọc (playhead)
const updatePlayhead = () => {
    const audio = audioPlayer.value;  // Sử dụng audioPlayer.value thay vì $refs
    if (audio) {
        const currentTime = audio.currentTime;
        const duration = audio.duration;
        // Tính toán vị trí phần trăm của thanh dọc (playhead)
        playheadPosition.value = (currentTime / duration) * 100;
    }
};
const handlePlay = () => {
    playing.value = true;
};
const handlePause = () => {
    playing.value = false;
};
const handleEnd = () => {
    playing.value = false;
    playheadPosition.value = 0;  // Đặt lại vị trí thanh dọc về đầu
};
const getDuration = () => {
    durationTime.value = waveformRef.value?.getDuration() || "0:00";
};
const formatLyrics = (lyrics) => {
    return lyrics.replace(/\n/g, '<br>');
}
</script>