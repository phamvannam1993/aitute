<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center" style="z-index: 9999999;" :class="props.is_show ? '':'hidden'">
        <div class="bg-white py-6 px-4 md:p-8 shadow-lg w-[320px] md:w-[500px] xl:w-[500px] rounded-[40px] relative">
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
                            <span v-if="errors.duration" class="text-red-500 text-sm">{{ errors.duration
                                }}</span>
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
</template>

<script setup>
import { ref, watch, computed, onMounted, defineEmits } from 'vue';
import Step from "@/Components/Step/Index.vue";
import axios from "axios";
import { toast } from "vue3-toastify";

const videoDuration = ref(5);
const videoDimensions = ref('16:9');
const video_description = ref("")
const validDimensions = computed(() => ['16:9', '9:16']);
const imageUrl = ref("https://firebasestorage.googleapis.com/v0/b/game-gotech.appspot.com/o/images%2Fchup-anh-chan-dung-troi-nang-6.jpeg?alt=media&token=9047e699-48ab-4134-a40c-fc4c73cdc94a")
const isLoading = ref(false);
const errors = ref({});
const props = defineProps({
    message: String,
    isError:Boolean,
    is_show:Boolean,
    type: String
});

const updateImageUrl = async (s3_url) => {
    video_description.value = ""
    videoDimensions.value = "16:9"
    imageUrl.value = s3_url;
}

const emit  = defineEmits();

const closePopup = () => {
    emit('close');
};

const videoRef = ref('');
const durations = computed(() => {
    videoDuration.value = 5;
    return [5, 10];
});

const confirmCreateVideo = async () => {
    const data = {
        duration:videoDuration.value,
        s3_url:imageUrl.value,
        video_description:video_description.value,
        resolution:videoDimensions.value,
        number_result:videoDuration.value
    }
    emit('createShortVideo', data)
}

defineExpose({ updateImageUrl });
</script>
