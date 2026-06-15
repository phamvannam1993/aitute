<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center" style="z-index: 9999999;" :class="props.is_show ? '':'hidden'">
        <div class="bg-white py-6 px-4 md:p-8 shadow-lg w-[320px] md:w-[500px] xl:w-[800px] rounded-[40px] relative" >
            <div class="">
                <div class="flex flex-col lg:flex-row md:justify-end md:items-end lg:items-center mt-4 flex-wrap lg:flex-nowrap lg:justify-between">
                    <h2 class="text-black font-bold text-2xl mb-4">Thiết lập video</h2>
                    <img src="/assets/img/close2.png" class="cursor-pointer absolute top-2 right-5" @click="closePopup" />
                </div>
                 <div class="flex w-full border-b-[3px] border-[#d6d6d6] mb-5 px-5"></div>
            </div>

            <div class="relative">
                <div class="overflow-y-auto  mb-2 relative text-center">
                    <div class="relative">
                        <div class="grid grid-cols-2 w-full mb-2">
                            <input type="file" ref="fileInput1" accept="audio/mpeg,audio/wav" @change="handleAudioFileChange1" class="hidden" />
                            <div class="flex flex-col gap-1">
                                <Step :step="1" stepName="Tải nhạc nền" forName="video-dimensions" />
                                <p class="text-[#A4A4A4] text-sm font-thin italic w-5/6 max-sm:hidden">
                                    (Vui lòng tải lên các tệp âm thanh có định dạng .mp3 hoặc .wav.)
                                </p>
                            </div>
                            <div class="flex flex-col gap-1">
                                <button type="button" @click="handleUploadClick1"
                                    class="flex items-center border-[2px] border-ai3goc-primary justify-center gap-2 p-1 bg-white hover:bg-black/10 text-ai3goc-primary rounded-lg backdrop-blur-sm transition-colors w-full h-fit">
                                    <img src="/assets/img/ai3goc/icon/upload.svg" class="size-6 md:size-5 xl:size-6" />
                                    <span class="text-[12px] xl:text-[16px] font-semibold">Tải nhạc nền</span>
                                </button>
                                <p class="text-[#A4A4A4] text-sm font-thin italic w-5/6 max-sm:hidden">{{ audioName1 }}
                                </p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 w-full mb-2">
                            <input type="file" ref="fileInput2" accept="audio/mpeg,audio/wav" @change="handleAudioFileChange2" class="hidden" />
                            <div class="flex flex-col gap-1">
                                <Step :step="2" stepName="Thuyết minh" forName="video-dimensions" />
                                <p class="text-[#A4A4A4] text-sm font-thin italic w-5/6 max-sm:hidden">
                                    (Vui lòng tải lên các tệp âm thanh có định dạng .mp3 hoặc .wav.)
                                </p>
                            </div>
                            <div class="flex flex-col gap-1">
                                <button type="button" @click="handleUploadClick2"
                                    class="flex items-center border-[2px] border-ai3goc-primary justify-center gap-2 p-1 bg-white hover:bg-black/10 text-ai3goc-primary rounded-lg backdrop-blur-sm transition-colors w-full h-fit">
                                    <img src="/assets/img/ai3goc/icon/upload.svg" class="size-6 md:size-5 xl:size-6" />
                                    <span class="text-[12px] xl:text-[16px] font-semibold">Tải file</span>
                                </button>
                                <p class="text-[#A4A4A4] text-sm font-thin italic w-5/6 max-sm:hidden">{{ audioName2 }}</p>
                            </div>
                        </div>
                        <div class="flex items-center mb-2 justify-between">
                            <Step :step="3" stepName="Tạo phụ đề" forName="video-dimensions" />
                            <div class="grid grid-cols-2 w-1/2 gap-2">
                                <div class='flex gap-1 items-center cursor-pointer'>
                                    <input type="radio" :checked="enableCaption ? true : false" @click="chaneCap(true)" class="rounded-full text-orion-orange focus:ring-orion-orange" />
                                    <label for="enableCaptionTrue" class="text-[12px] lg:text-[14px]">Bật</label>
                                </div>
                                <div class='flex gap-1 items-center cursor-pointer'>
                                    <input type="radio" :checked="enableCaption ? false : true" @click="chaneCap(false)" class="rounded-full text-orion-orange  focus:ring-orion-orange" />
                                    <label for="enableCaptionFalse" class="text-[12px] lg:text-[14px]">Tắt</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             <div class="flex w-full border-b-[3px] border-[#d6d6d6] mt-5 px-5"></div>
            <div class="flex items-center gap-4 md:gap-8 justify-center mt-6">
                <button @click="confirmMergeVideo" class="relative h-[40px] md:h-[50px] min-w-[80%] lg:min-w-[360px] text-sm lg:text-base px-4 bg-ai3goc-bgclr text-white rounded-lg lg:rounded-2xl font-bold">
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
</template>

<script setup>
import { ref, watch, computed, onMounted, defineEmits } from 'vue';
import Step from "@/Components/Step/Index.vue";
import axios from "axios";
import { toast } from "vue3-toastify";

const isLoading = ref(false);
const errors = ref({});
const props = defineProps({
    message: String,
    isError:Boolean,
    is_show:Boolean,
    type: String
});

const updateImageUrl = async (s3_url) => {
    imageUrl.value = s3_url;
}

const emit  = defineEmits();

const closePopup = () => {
    emit('close');
};
const fileInput1 = ref(null);
const audio1 = ref(null)
const audio2 = ref(null)
const enableCaption = ref(false)
const fileInput2 = ref(null);
const audioName1 = ref(null)
const audioName2 = ref(null)
const chaneCap = (val) => {
    enableCaption.value = val
}
const handleAudioFileChange1 = (event) => {
    var type = event.target.files[0].type;
    const allowedAudioTypes = ["audio/mpeg", "audio/wav"];
    if (!allowedAudioTypes.includes(type)) {
        toast.error( "Xin vui lòng tải lên các tệp âm thanh có định dạng .mp3 hoặc .wav.")
        return;
    }
    audio1.value = event.target.files[0]
    audioName1.value = audio1.value.name
    fileInput1.value.value = ""
};

const handleAudioFileChange2 = (event) => {
    var type = event.target.files[0].type;
    const allowedAudioTypes = ["audio/mpeg", "audio/wav"];
    if (!allowedAudioTypes.includes(type)) {
        toast.error( "Xin vui lòng tải lên các tệp âm thanh có định dạng .mp3 hoặc .wav.")
        return;
    }
    audio2.value = event.target.files[0]
    audioName2.value = audio2.value.name
    fileInput2.value.value = ""
};

const handleUploadClick1 = (index) => {
    fileInput1.value?.click();
};

const handleUploadClick2 = (index) => {
    fileInput2.value?.click();
};

const confirmMergeVideo = async () => {
    const data = {
        audio1:audio1.value,
        audio2:audio2.value,
        enableCaption:enableCaption.value,
    }
    emit('confirmMergeVideo', data)
}

defineExpose({ updateImageUrl });
</script>
