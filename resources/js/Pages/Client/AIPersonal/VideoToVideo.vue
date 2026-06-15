<template>
    <section class="flex flex-col gap-5 p-4 w-full text-black bg-white drop-shadow-xl rounded-3xl">
        <div class="w-full flex flex-col lg:flex-row justify-between lg:items-center">
            <div class="flex justify-start items-center gap-4 mb-1">
                <img class="rounded-2xl" src="/assets/img/orion/mini-robot.svg" />
                <div>
                    <h2 class="text-black font-bold text-base lg:text-[30px] leading-[32px] line-clamp-1">Ghép video</h2>
                </div>
            </div>
        </div>

        <p class="text-sm text-orion-primary font-bold">Thực hiện theo các bước sau:</p>

        <div class="flex flex-col gap-7 w-full">
            <div class="flex max-md:flex-wrap justify-between items-center">
                <div class="mb-1">
                    <Step :step="1" stepName="Chọn video / ảnh AI của bạn" forName="image-description" />
                    <span class="italic font-thin text-xs text-orion-primary">Lưu ý: Chọn tối thiểu 2 video / ảnh. Hình ảnh mặc định xuất hiện đầu tiên trong video của bạn.</span>
                </div>
                

                <div class="flex gap-2 flex-col">
                    <!-- <div class="flex flex-col">
                        <div class="flex text-lg text-[#ADADAD] font-bold justify-evenly">
                            <p @click="statusActive = 'video'" :class="statusActive === 'video' && 'text-primary-color'" class="px-3 cursor-pointer">Video</p>
                            <p @click="statusActive = 'image'" :class="statusActive === 'image' && 'text-primary-color'" class="px-3 cursor-pointer">Ảnh</p>
                        </div>
                        <p class="w-full h-2 bg-[#D9D9D9] rounded-lg relative">
                            <span v-if="statusActive === 'video'" class="w-[50%] h-2 absolute left-0 bg-primary-color rounded-l-lg"></span>
                            <span v-else class="w-[50%] h-2 absolute right-0 bg-primary-color rounded-r-lg"></span>
                        </p>
                    </div> -->
                    <div class="relative inline-block min-w-20" ref="dropdownRef">
                        <button @click="isDropdownOpen = !isDropdownOpen" class="flex min-w-48 gap-2 items-center border-2 border-[#D4D4D4] px-3 h-10 rounded-[20px] outline-none w-full justify-center">
                            <p class="">{{ videoTypeActive }}</p>
                            <svg :class="isDropdownOpen ? 'rotate-0' : 'rotate-180'" width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1L5 5L9 1" stroke="#C5C5C5" />
                            </svg>
                        </button>

                        <div v-if="isDropdownOpen" class="absolute mt-2 bg-white border-2 border-[#D4D4D4] rounded-[20px] shadow-lg w-full z-10">
                            <div v-for="video in listVideoType" :key="video" class="px-4 py-2 hover:bg-gray-100 rounded-[20px] cursor-pointer text-black" @click="selectVideoActive(video)">
                                {{ video.value }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="h-40vh lg:h-[50vh] grid grid-cols-2 lg:grid-cols-4 gap-2 lg:gap-5 overflow-y-auto w-full">
                <button @click="handleSelectVideo(video)" v-for="video in imagesHistory" :key="video.id" class="relative w-full bg-[#D4D4D4] rounded-2xl" :class="videoTypeKey == video.video_type || videoTypeKey == 'all' ? '':'hidden'">
                    <img :src="video.thumbnail" class="w-full object-cover rounded-2xl max-h-175px" />
                    <button :class="videoSelected.findIndex((item) => item.id === video.id) !== -1 && 'border-orion-orange'" class="absolute border-2 top-2 lg:top-4 right-2 lg:right-4 cursor-pointer outline-none size-6 bg-white rounded-full flex items-center justify-center">
                        <span class="font-bold text-orion-orange " v-if="videoSelected.findIndex((item) => item.id === video.id) !== -1">
                            {{ videoSelected.findIndex((item) => item.id === video.id) + 1 }}
                        </span>
                    </button>
                </button>
            </div>

            <div class="flex flex-col gap-2 w-full overflow-x-auto">
                <div v-if="imageSelected || videoSelected" class="flex gap-2 font-semibold justify-start items-center">
                    <img src="/assets/img/orion/icon/label-circle.svg" class="h-7 w-6" alt="collection" />
                    <p class="text-sm">Video / Ảnh đã chọn:</p>
                </div>
                <!-- Scrolling Container -->
                <div class="flex gap-4 w-full overflow-x-auto">
                    <div class="flex gap-4 flex-nowrap">
                        <div v-for="video in videoSelected" :key="video.id" class="relative w-40 h-28 bg-[#00000033] rounded-2xl">
                            <img class="w-full h-full object-contain" :src="video.thumbnail" alt="video selected" />
                            <button @click="handleSelectVideo(video)" class="absolute top-0 -right-2 bg-orion-sec rounded-full size-6 text-white font-bold">-</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-10 justify-between mt-10">
            <section>
                <form @submit.prevent="handleSubmit" class="flex flex-col gap-5 items-start">
                    <div class="w-full flex flex-col gap-4">
                    <Step :step="2" stepName="Nhập nội dung thuyết minh" forName="image-description" />
                        <div class="relative">
                            <SuggestionPrompt v-model="imageDescription" :type="'image'" :screenId="2" />
                            <textarea id="explanation" v-model="imageDescription" type="text" placeholder="Nhập nội dung thuyết minh video..." :disabled="loadingCreateImage" class="w-full p-3 mt-1 h-[200px] border text-black border-[#2C75E3] rounded-[10px] shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder:font-light"></textarea>

                            <div class="object-mic relative ml-auto">
                                <div v-if="isRecording" class="outline-mic right-3 bottom-3 flex items-center justify-center"></div>
                                <div v-if="isRecording" class="outline-mic right-3 bottom-3 flex items-center justify-center" id="delayed"></div>
                                <div v-if="isRecording" class="button-mic right-3 bottom-3 flex items-center justify-center"></div>
                                <button class="button-mic icon-mic absolute right-3 bottom-3 flex items-center justify-center" @click="startRecording" :disabled="disabledRecord" type="button">
                                    <svg class="h-6 w-6" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000">
                                        <g>
                                            <path d="M500,683.8c84.6,0,153.1-68.6,153.1-153.1V163.1C653.1,78.6,584.6,10,500,10c-84.6,0-153.1,68.6-153.1,153.1v367.5C346.9,615.2,415.4,683.8,500,683.8z M714.4,438.8v91.9C714.4,649,618.4,745,500,745c-118.4,0-214.4-96-214.4-214.4v-91.9h-61.3v91.9c0,141.9,107.2,258.7,245,273.9v124.2H346.9V990h306.3v-61.3H530.6V804.5c137.8-15.2,245-132.1,245-273.9v-91.9H714.4z" />
                                        </g>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 w-full">
                            <input type="file" ref="fileInput" accept="audio/*,video/mp4,.mp4" @change="handleAudioFileChange" class="hidden" />
                            <label for="video-dimensions" class="text-xs lg:text-sm gap-1 items-center mb-1 font-semibold">
                                <Step :step="3" stepName="Tải nhạc nền" forName="image-description" />
                                <p class="text-[#A4A4A4] text-sm font-thin italic w-5/6 max-sm:hidden">{{audioName ? audioName : '(Vui lòng tải lên các tệp âm thanh có định dạng .mp3, .wav hoặc .mp4.)'}}</p>
                            </label>
                            <button type="button" @click="handleUploadClick" class="flex items-center font-bold px-3 py-1.5 justify-center gap-2 bg-transparent text-orion-sec rounded-xl backdrop-blur-sm hover:scale-105 border-2 border-orion-sec h-fit transform transition-transform">
                                <img src="/assets/img/orion/icon/upload.svg" class="size-6 md:size-5 xl:size-6" />
                                <span class="text-[12px] md:hidden xl:block xl:text-[14px]">Tải nhạc nền</span>
                            </button>
                        </div>
                        <p class="text-[#A4A4A4] text-sm font-thin italic w-5/6 md:hidden">{{audioName ? audioName : '(Vui lòng tải lên các tệp âm thanh có định dạng .mp3 hoặc .wav.)'}}</p>

                        <div class="grid grid-cols-2 w-full">
                            <Step :step="4" stepName="Phụ đề" forName="image-description" />

                            <div class="grid grid-cols-2">
                                <div class="flex gap-1 items-center cursor-pointer">
                                    <input type="radio" id="enableCaptionTrue" name="enableCaption" value="true" v-model="enableCaption" class="ml-0 rounded-full text-orion-sec focus:ring-orion-sec" />
                                    <label for="enableCaptionTrue">Bật</label>
                                </div>
                                <div class="flex gap-1 items-center cursor-pointer">
                                    <input type="radio" id="enableCaptionFalse" name="enableCaption" value="false" v-model="enableCaption" class="ml-0 rounded-full text-orion-sec focus:ring-orion-sec" />
                                    <label for="enableCaptionFalse">Tắt</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 w-full">
                        <Step :step="5" stepName="Kích thước" forName="image-description" />

                        <select
                            v-model="videoDimensions"
                            id="video-dimensions"
                            :class="{
                                'bg-gray-200 border-gray-200': !videoDimensions,
                                'bg-white border-orion-sec': videoDimensions,
                            }"
                            class="block mt-1 text-[14px] appearance-none text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-orion-sec w-full"
                        >
                            <option v-for="dimension in validDimensions" :value="dimension" :key="dimension">
                                {{ dimension }}
                            </option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 w-full">
                        <Step :step="5" stepName="Bấm vào đây" forName="image-description" />
                        <button @click="mergeData" class="w-full bg-orion-primary text-white font-bold rounded-xl py-2 hover:scale-105 transform transition-transform">Tạo video</button>
                    </div>
                </form>
            </section>

            <section class="flex flex-col gap-5">
                <p class="italic font-thin text-orion-primary text-sm">Hiển thị kết quả</p>
                <div class="flex justify-center items-center mb-4">
                    <video v-if="videoRef" :src="videoRef" alt="video-generate-by-AI" controls="" preload="auto" class="w-full h-auto md:max-w-[500px] md:max-h-[500px] object-cover" />
                    <div v-else class="bg-[#E6E6E6] w-full h-[50vh] rounded-2xl showLoaderVideo items-center flex justify-center">
                        <div v-if="isLoadingVideo">
                            <div class="loaderVideo"></div>
                            <p class="text-loading">Video đang được tạo. Vui lòng chờ !</p>
                        </div>
                        <img v-else src="\assets\img\aiwow\ai_image\placeholder.png" alt="" class="w-20" />
                    </div>
                </div>
                <TaskBar :isActive="resultValue" :selectedImage="resultValue" :shareLinkableType="'AIGeneratedMedia'" :features="['lipsync']" />
                <div class="w-full flex justify-end">
                    <a :href="route('ai-video.history',{type:'all'})" class="px-4 w-[180px] text-center py-2 bg-orion-primary text-white font-bold text-[15px] rounded-[10px] h-fit hover:scale-105 transform transition-transform">Lịch sử</a>
                </div>
            </section>
        </div>
    </section>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from "vue";
import { toast } from "vue3-toastify";

import TaskBar from "@/Components/TaskBar.vue";
import {usePage} from "@inertiajs/vue3";
import Step from "@/Components/Step/Index.vue";

const page = usePage()
const imagesHistory = ref([]);

const imageSelected = ref(imagesHistory.value[0]);
const videoModel = ref("Runway/gen3-alphaturbo");
const videoDuration = ref(5);
const videoDimensions = ref("16:9");
const resultValue = ref(null);
const isLoading = ref(false);
const videoRef = ref("");
const statusActive = ref("video");
const isDropdownOpen = ref(false);
const dropdownRef = ref(null);
const videoTypeActive = ref("Kho video");
const videoTypeKey = ref("all")
const videoSelected = ref([]);
const enableCaption = ref(false);

const handleSelectVideo = (video) => {
    const index = videoSelected.value.findIndex((item) => item.id === video.id);
    if (index == -1) {
        videoSelected.value.push(video);
    } else {
        videoSelected.value.splice(index, 1);
    }
};

const durations = computed(() => {
    if (videoModel.value === "Kling") {
        videoDuration.value = 5;
        return [5, 10];
    }
    return [5, 10];
});

const validDimensions = computed(() => {
    if (videoModel.value === "Kling") {
        videoDimensions.value = "16:9";
        return ["16:9", "9:16", "1:1"];
    }
    if (videoModel.value === "Runway/gen3-alphaturbo") {
        videoDimensions.value = "16:9";
        return ["16:9", "9:16"];
    }
});

const listVideoType = [
    {
        key:"all",
        value:"Kho video"
    },
    {
        key:"video-merge",
        value:"Video đã ghép"
    },
    {
        key:"video",
        value:"Phim"
    },
    {
        key:"mc_virtual",
        value:"MC ảo"
    },
    {
        key:"image-video",
        value:"Ảnh thành Video"
    },
    {
        key:"lipsync",
        value:"Tôi là A.I"
    },
];

const selectVideoActive = (typeVideo) => {
    videoTypeActive.value = typeVideo.value;
    videoTypeKey.value = typeVideo.key;
};

const closeDropdown = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isDropdownOpen.value = false;
    }
};

const selectedModel = ref('*');

const fetchMediaList = async () => {
  try {
    var url = route("ai-video.getAllVideo", { type_query:'all' })
    const response = await axios.get(url);

    if (response.data.success) {
      imagesHistory.value = response.data.data;
    } else {
      console.error('Failed to fetch media list:', response.data.errors);
    }

  } catch (error) {
    console.error('Error fetching media list:', error);
  }
};

const fileInput = ref(null);
const handleUploadClick = () => {
    fileInput.value?.click();
};

const audioName = ref('');
const audioFile = ref();
const handleAudioFileChange = (event) => {
    var type = event.target.files[0].type;
    const allowedAudioTypes = ["audio/mpeg", "audio/wav", "audio/mp4", "video/mp4"];
    if (!allowedAudioTypes.includes(type)) {
        alert("Xin vui lòng tải lên các tệp âm thanh có định dạng .mp3, .wav hoặc .mp4.");
        audioInput.value.value = "";
        return;
    }
    audioFile.value = event.target.files[0];
    audioName.value = audioFile.value ? audioFile.value.name : 'Chưa có tệp nào được chọn';
};
const imageInput = ref(null)
const isLoadingVideo = ref(null)
const aiVideoId = ref(null)
const mergeData = async () => {
    videoRef.value = null
    videoRef.value
    if(videoSelected.value.length < 2) {
        return false;
    }
    const formData = new FormData();
    var ids = [];
    if (audioFile.value) {
        formData.append('audio_file', audioFile.value);
    }
    if (imageInput.value) {
        formData.append('image_file', imageInput.value);
    }
    formData.append('ratio', videoDimensions.value);
    await textToSpeechGoogle()
    formData.append('audioDescription', audioDescription.value);
    for(var i = 0; i < videoSelected.value.length; i++) {
        ids.push({
            id:videoSelected.value[i].id,
            type:videoSelected.value[i].table,
        })
    }
    var url = route("ai-video.mergeVideo", { ids: ids,type_query:'all'})
    const response = await axios.post(url, formData,
        {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        }
    );
    if(response.data.success) {
        isLoadingVideo.value = true;
        aiVideoId.value = response.data.id
    } else {
        alert(response.data.message)
    }
}

const toggleTranscription = async (data) => {
    try {
        const response = await axios.post(route("ai-video.create-video-with-transcription"), {
            ai_media_id: data.id,
            duration: data.duration,
            screen_id: 3,
        });
        const transcription = response?.data?.video;
        videoRef.value = transcription;
        resultValue.value = data;
        isLoadingVideo.value = false;
        isTranscription.value = false;
    } catch (error) {
        console.error("Error creating transcription:", error);
        toast.error("Có lỗi xảy ra khi tạo video. Vui lòng thử lại.");
    } finally {
    }
};
const isTranscription = ref(false)
var intervalid1 = setInterval(async () => {
    if(aiVideoId.value && isLoadingVideo.value && !videoRef.value && !isTranscription.value) {
        var url =  route("ai-video.detail", { id: aiVideoId.value })
        try {
            const response = await axios.get(url)
            if(response.data.error_msg) {
                isLoadingVideo.value = false;
                alert(response.data.error_msg);
            } else if(response.data.s3_url) {
                if (enableCaption.value) {
                    isTranscription.value = true
                    await toggleTranscription(response?.data.data);
                } else {
                    videoRef.value = response.data.s3_url
                    resultValue.value = data;
                    isLoadingVideo.value = false;
                    resultValue.value = response?.data.data
                }
            }
        } catch (error) {

        }
    }
}, 10000);

const audioDescription = ref(null)
const imageDescription = ref(null)
const textToSpeechGoogle = async () => {
    if(imageDescription.value) {
        const result = await  axios.post(route("ai-audio.text-to-speech-google"), {
            text: imageDescription.value,
        })
        if (result.data.success) {
            const urlAudio = result.data.data;
            audioDescription.value = urlAudio;
        }
    }
    return true
}

onMounted(() => {
    if (page.props.auth.user) {
        fetchMediaList();
    }
    document.addEventListener("click", closeDropdown);
});

onBeforeUnmount(() => {
    document.removeEventListener("click", closeDropdown);
});
</script>

<style>
    .max-h-175px{
        max-height:175px;
    }
    .loaderVideo {
        border: 10px solid #f3f3f3;
        border-top: 10px solid #24AA69;
        border-radius: 50%;
        width: 90px;
        height: 90px;
        margin-left: 35%;
        animation: spin 2s linear infinite;
        margin-bottom: 10px;
    }
</style>
