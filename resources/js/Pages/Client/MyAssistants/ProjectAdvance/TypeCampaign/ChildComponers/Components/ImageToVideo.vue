<template>
    <div class="flex flex-col lg:flex-row gap-4 mt-4">
        <div class="w-full">
            <!-- Upload Buttons -->
            <div class="flex justify-between items-center mb-5">
                <p class="text-ai3goc-bgclr text-[15px] font-bold">Hình ảnh thành video:</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 items-center justify-center w-full gap-2">
                <div class="flex flex-col justify-center items-center h-full w-full rounded-xl overflow-hidden min-h-[120px] relative" v-for="(video, index_video) in videos" :key="index_video">
                    <input v-if="video?.s3_url" type="checkbox"  :checked="video.is_checked" @change="selectVideoItem(index_video)" class="ml-0 top-2 left-2 absolute rounded-full checked:bg-orion-orange hover:checked:bg-orion-orange checked:text-orion-orange checked:ring-orion-orange z-9999" />
                    <div v-if="video?.s3_url" class="video-player cursor-pointer" @click="showFullVideo(video?.s3_url)">
                        <div class="video-inner" :class="video.ratio === '16:9' ? 'ratio-16-9':'ratio-9-16'">
                            <img :src="video.s3_thumbnail" alt="Video Thumbnail" class="video-thumbnail">
                        </div>

                        <div class="play-button">
                            <img src="/assets/img/icon/icon_play.png" alt="Video Thumbnail">
                        </div>
                        <div class="controls">
                            <span>▶ 0:00 / 0:{{video.duraion < 10 ? '0'+video.duraion : video.duraion}}</span>
                            <div class="progress-bar"></div>
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="8.36523" y="0.396484" width="5.01927" height="1.67309" fill="white"/>
                                    <rect x="11.7129" y="5.41797" width="5.01927" height="1.67309" transform="rotate(-90 11.7129 5.41797)" fill="white"/>
                                    <rect width="5.01927" height="1.67309" transform="matrix(1 0 0 -1 8.36523 13.7871)" fill="white"/>
                                    <rect width="5.01927" height="1.67309" transform="matrix(-4.37114e-08 1 1 4.37114e-08 11.7129 8.76562)" fill="white"/>
                                    <rect width="5.01927" height="1.67309" transform="matrix(-1 0 0 1 5.01758 0.396484)" fill="white"/>
                                    <rect width="5.01927" height="1.67309" transform="matrix(4.37114e-08 -1 -1 -4.37114e-08 1.67188 5.41797)" fill="white"/>
                                    <rect x="5.01758" y="13.7871" width="5.01927" height="1.67309" transform="rotate(180 5.01758 13.7871)" fill="white"/>
                                    <rect x="1.67188" y="8.76562" width="5.01927" height="1.67309" transform="rotate(90 1.67188 8.76562)" fill="white"/>
                                </svg>
                        </div>
                    </div>
                    <div v-if="!video.isLoading && !video.s3_url" class="bg-[#E6E6E6] flex aspect-video items-center justify-center w-full rounded-xl">
                        <img src="/assets/img/aiwow/homepage/play_button.png" class="w-16" alt="" />
                    </div>
                    <div v-if="video.isLoading && !video.s3_url" class="bg-[#E6E6E6] flex aspect-video items-center justify-center w-full rounded-xl">
                        <div class="flex flex-col justify-center items-center w-full min-h-[260px]">
                            <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                            <h3 class="text-white text-center">Hệ thống đang xử lý, xin đợi một chút</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <!-- Video Section -->
        <div class="flex flex-col">
            <div class="relative">
                <div class="flex justify-center gap-2 md:gap-4 mb-4 h-[40px] mt-10">
                    <button @click="confirmShowMergeVideo" class="relative h-[40px] md:h-[50px] min-w-[80%] lg:min-w-[360px] text-sm lg:text-base px-4 bg-ai3goc-bgclr text-white rounded-lg lg:rounded-2xl font-bold">
                        <svg class="absolute" width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.57 2H4.93C3.72602 2 2.75 2.97602 2.75 4.18V19.82C2.75 21.024 3.72602 22 4.93 22H20.57C21.774 22 22.75 21.024 22.75 19.82V4.18C22.75 2.97602 21.774 2 20.57 2Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7.75 2V22" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17.75 2V22" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2.75 12H22.75" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2.75 7H7.75" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2.75 17H7.75" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17.75 17H22.75" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17.75 7H22.75" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Ghép video
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center" style="z-index: 9999999;" v-if="isShowCreateVideo">
        <div class="bg-white py-6 px-4 md:p-8 shadow-lg w-[320px] md:w-[500px] xl:w-[500px] rounded-[40px] relative"  style="height: 100%;overflow: auto;">
            <div class="">
                <div class="flex flex-col lg:flex-row md:justify-end md:items-end lg:items-center mt-4 flex-wrap lg:flex-nowrap lg:justify-between">
                    <h2 class="text-black font-bold text-2xl mb-4">Ảnh thành video</h2>
                    <img src="/assets/img/orion/icon/close_yellow.svg" class="cursor-pointer absolute top-2 right-5" @click="closePopup" />
                </div>
                 <div class="flex w-full border-b-[3px] border-[#d6d6d6] mb-5 px-5"></div>
            </div>

            <div class="relative">
                <div class="overflow-y-auto  mb-2 relative text-center">
                    <div class="relative">
                        <div class="mt-4">
                            <div class="flex flex-rows gap-2 text-center self-center mb-2">
                                <div class="flex flex-col w-full relative pointer">
                                    <img v-if="imageUrl" :src="imageUrl"
                                        class="w-full md:max-w-[500px] h-[240px] mx-auto object-contain bg-black rounded-md" />
                                    <div v-else
                                        class="bg-[#E6E6E6] w-full h-[240px] rounded-2xl flex flex-col items-center justify-center" @click="handleChangeInputImageType(1)">
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-rows gap-2 text-center self-center mb-2">
                                <Step :step="1" stepName="Mô tả chuyển động của hình ảnh" forName="video_description" />
                            </div>
                            <div class="flex flex-rows gap-2 text-center self-center mb-2 relative">
                                <SuggestionPrompt v-model="video_description" :type="'image-to-video'" :screenId="2" />
                                <textarea v-model="video_description" placeholder="Nhập mô tả..."
                                    rows="2"
                                    class="mt-1 block w-full border-orion-sec rounded-md shadow-sm focus:outline-none focus:bg-white focus:border-gray-500 min-h-[160px] lg:min-h-0"></textarea>
                            </div>

                            <div class="flex flex-row md:flex-col xl:flex-row xl:items-center xl:gap-8 gap-1 justify-between  xl:justify-between">
                                <Step :step="2" stepName="Chọn thời lượng" forName="video-duration" />
                                <select id="video-duration" v-model="videoDuration"
                                    :class="{ 'bg-gray-200 border-gray-200': !videoDuration, 'bg-white border-orion-sec': videoDuration }"
                                    class="block w-1/3 sm:w-1/2 md:w-full xl:w-2/5 text-[14px] appearance-none col-span-2 text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                    <option v-for="duration in durations" :value="duration" :key="duration">
                                        {{ duration }} giây
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="flex flex-row md:flex-col xl:flex-row xl:items-center xl:gap-8 gap-1 justify-between  xl:justify-between">
                                <Step :step="3" stepName="Chọn kích thước" forName="video-dimensions" />

                                <select id="video-dimensions" v-model="videoDimensions"
                                    :class="{ 'bg-gray-200 border-gray-200': !videoDimensions, 'bg-white border-orion-sec': videoDimensions }"
                                    class="block text-[14px] w-1/3 sm:w-1/2 md:w-full xl:w-2/5 appearance-none col-span-2 text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                    <option v-for="dimension in validDimensions" :value="dimension"
                                        :key="dimension">
                                        {{ dimension }}
                                    </option>
                                </select>
                            </div>
                            <span v-if="errors.dimensions" class="text-red-500 text-sm">{{ errors.dimensions
                                }}</span>
                        </div>
                    </div>
                </div>
            </div>
             <div class="flex w-full border-b-[3px] border-[#d6d6d6] mt-5 px-5"></div>
            <div class="flex items-center gap-4 md:gap-8 justify-center mt-6">
                <button @click="confirmCreateVideo" class="h-[40px] md:h-[50px] min-w-[80%] lg:min-w-[360px] text-sm lg:text-base px-4 bg-ai3goc-bgclr text-white rounded-lg lg:rounded-2xl font-bold">
                   Xác nhận
                </button>
            </div>
        </div>
    </div>
    <div>
        <div v-if="isShowFullVideo" class="overlay" style="z-index: 9999999;" >
            <button class="close-btn" @click.stop="isShowFullVideo = false">✖</button>
            <video controls v-if="videoFullUrl" :src="videoFullUrl" alt="image generated by AI" class="max-h-[800px]" />
        </div>
    </div>
    <PopupMergeVideo @close="isShowMergeVideo=false" :is_show="isShowMergeVideo" @confirmMergeVideo="confirmMergeVideo" />
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";
import { eventBus } from "@/lib/eventBus.js";
import SuggestionPrompt from "@/Components/AIVirtual/SuggestionPrompt.vue";
import PopupMergeVideo from "./PopupMergeVideo.vue";
import { computed, nextTick, onMounted, ref, watch } from "vue";
import { toast } from "vue3-toastify";
import ButtonTaskBar from "@/Components/ButtonTaskBar.vue";
import Step from "@/Components/Step/Index.vue";
const emit = defineEmits(["saveGenerationResult","onSubmit","onSuccess","onFinish"]);
const isShowCreateVideo = ref(false)
const props = defineProps({
    message: String,
    imageModel:String,
    is_show:Boolean,
    isError:Boolean,
    type: String,
    facebookContentDetail: {
        type: Object,
        default: {
            content:""
        }
    },
});
const videoFullUrl = ref("")
const isShowFullVideo = ref(false)
const showFullVideo = (videoUrl) => {
    videoFullUrl.value = videoUrl
    isShowFullVideo.value = true
}
const imageDimension = ref('16:9')
const facebookContentDetail = ref(props.facebookContentDetail)
const videosDefault = [
    {
        isLoading:false,
        s3_url:"",
        duraion:0,
        ratio:''
    },
    {
        isLoading:false,
        s3_url:"",
        duraion:0,
        ratio:''
    },
    {
        isLoading:false,
        s3_url:"",
        duraion:0,
        ratio:''
    },
    {
        isLoading:false,
        s3_url:"",
        duraion:0,
        ratio:''
    },
]
const videos = ref(props.facebookContentDetail.videos ? props.facebookContentDetail.videos : videosDefault)
const closePopup = () => {
    isShowCreateVideo.value = false
};
const showModelImage = () => {
    emit('showImageList');
}
const confirmVideo = () => {
    emit('confirmVideo');
};

const videoDuration = ref(5);
const videoDimensions = ref('16:9');
const video_description = ref("")
const validDimensions = computed(() => ['16:9', '9:16']);
const imageUrl = ref("https://firebasestorage.googleapis.com/v0/b/game-gotech.appspot.com/o/images%2Fchup-anh-chan-dung-troi-nang-6.jpeg?alt=media&token=9047e699-48ab-4134-a40c-fc4c73cdc94a")
const isLoading = ref(false);
const errors = ref({});

const videoRef = ref('');
const durations = computed(() => {
    videoDuration.value = 5;
    return [5, 10];
});

const selectVideoItem = (index) => {
    videos.value[index].is_checked = !videos.value[index].is_checked
    emit("updateVideos", videos.value)
}

const updateVideos = (videos) => {

}

const confirmCreateVideo = async () => {
    const data = {
        duration:videoDuration.value,
        s3_url:imageUrl.value,
        video_description:video_description.value,
        resolution:videoDimensions.value,
        number_result:videoDuration.value
    }
    createShortVideo(data)
}

  const createShortVideo = async (data) => {
        const formData = new FormData();
        formData.append("duration", data.duration);
        formData.append("number_result", data.number_result);
        formData.append("resolution", data.resolution);
        formData.append("s3_url", data.s3_url);
        formData.append("video_description", data.video_description);
        formData.append("video_key", videoKey.value);
        videos.value[videoKey.value].isLoading = true
        videos.value[videoKey.value].s3_url = ""
        isShowCreateVideo.value = false
        try {
            const response = await axios.post(route("short-video.create-video-ai-v3"), formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });
            if (response.data.success) {
                videos.value[response.data.video_key].isLoading = true
                videos.value[response.data.video_key].s3_thumbnail = response.data.s3_thumbnail
                videos.value[response.data.video_key].s3_url = response.data.s3_url
                videos.value[response.data.video_key].isLoading = false
                videos.value[response.data.video_key].ratio = data.resolution
                videos.value[response.data.video_key].duraion = data.duration
                emit("updateVideos", videos.value)
            } else {
                videos.value[response.data.video_key].isLoading = false
                toast.error('Có lỗi xảy ra trong quá trình tạo video')
            }
        } catch (err) {
            videos.value[videoKey.value].isLoading = false
            toast.error('Có lỗi xảy ra trong quá trình tạo video')
        }
    }
   const videoKey = ref(0)
   const isShowMergeVideo = ref(false)
   const showPopupCreateVideo = (index, s3_url) => {
        for(var i = 0 ; i < videos.value.length; i++) {
            if(videos.value[i].s3_url && videos.value[i].is_checked) {
                if(videos.value[i].isLoading) {
                    return false
                }
            }
        }
        video_description.value = ""
        videoDimensions.value = "16:9"
        imageUrl.value = s3_url;
        isShowCreateVideo.value = true
        videoKey.value = index
    }

    const confirmShowMergeVideo = () => {
        var videoUrls = [];
        for(var i = 0 ; i < videos.value.length; i++) {
            if(videos.value[i].s3_url && videos.value[i].is_checked) {
                videoUrls.push(videos.value[i].s3_url)
            }
        }
        if(videoUrls.length < 2) {
            toast.error("Số lượng video cần từ 2 video trở lên")
            return
        }
        isShowMergeVideo.value = true
    }

    const confirmMergeVideo = async (data) => {
        data.videos = videos.value
        isShowMergeVideo.value = false
        eventBus.emit('confirmMergeVideo', data);
    }

  defineExpose({ showPopupCreateVideo, updateVideos });
</script>
