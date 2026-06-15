    <template>
        <form @submit.prevent="submit" class="relative m-6">
            <div class="relative bg-white rounded-lg">
                <div>
                    <div class="p-3">
                        <div class="">
                            <div class="flex justify-between gap-md">
                                <h1 class="text-xs hidden xs:block xs:text-xl font-semibold">
                                    Bài viết
                                </h1>

                                <div class="flex items-center gap-2">
                                    <div v-if="!postFormData.published || !postFormData.id"
                                        class="flex items-center gap-1 p-2 border-2 rounded-xl bg-green-500 cursor-pointer"
                                        @click="openMediaLibrary">
                                        <img src="/assets/svgs/media-icon.svg" alt="add" />
                                        <span class="text-white text-xs md:text-base">Kho đa phương tiện</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex text-black items-center justify-between py-2">
                            <div>
                                <VueDatePicker format="dd/MM/yyyy HH:mm:ss" v-if="isSchedule"
                                    :ui="{ input: 'rounded-[10px] border-[#B5B5BE]' }" locale="vi"
                                    v-model="form.scheduled_publish_time" auto-apply :close-on-auto-apply="false"
                                    :min-date="minDate" :max-date="maxDate"
                                    :day-names="['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN']" />

                            </div>
                        </div>
                        <div class="flex text-black items-center justify-end">
                            <div v-if="$page.props.errors?.scheduled_publish_time"
                                v-text="$page.props.errors?.scheduled_publish_time" class="text-red-500 my-1"></div>
                        </div>

                        <div class="relative max-h-72 overflow-auto">
                            <div v-if="$page.props.errors.message" v-text="$page.props.errors.message"
                                class="text-red-500 my-1">
                            </div>
                            <div v-if="$page.props.errors.facebook_fanpage_id"
                                v-text="$page.props.errors.facebook_fanpage_id" class="text-red-500 my-1">
                            </div>
                            <div class="w-full">
                                <textarea v-model="form.content" placeholder="Nội dung..."
                                    class="h-44 w-full border rounded-md  focus:ring-0 resize-none">
                                    </textarea>
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
                                <div v-if="$page.props.errors.content" v-text="$page.props.errors.content"
                                    class="text-red-500 my-1"></div>
                            </div>
                            <div class="items-center justify-between">
                                <div class="flex items-center gap-4 text-[#666]">
                                    <input ref="fileInput" hidden multiple type="file"
                                        accept="video/mp4,video/x-m4v,video/webm,video/ogg,video/*,.flv,.3gp,image/*"
                                        class="inputMedia" @change="handleFileChange">
                                    <button type="button" class=" p-1 rounded-lg hover:text-[#333] hover:bg-slate-100"
                                        @click="selectFile">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                                        </svg>
                                    </button>
                                </div>

                                <div v-if="filePreviewUrls.length" class="relative mt-4 rounded-lg group">
                                    <div class="mt-4">
                                        <div class="grid grid-cols-4 gap-2">
                                            <div v-for="(item, index) in filePreviewUrls" :key="index" class="relative">
                                               
                                                <img v-if="item.type.includes('image')" :src="item.url"
                                                    class="w-full h-auto rounded-md" />
                                                <video v-else :src="item.url" controls
                                                    class="w-full h-auto rounded-md"></video>
                                                <button v-if="!postFormData.published || !postFormData.id" type="button" @click="removeFilePreviewUrls(index)"
                                                    class="absolute top-2 right-2 bg-red-500 text-white w-8 h-8 rounded-full">
                                                    X
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="$page.props.errors['files.0']" v-text="$page.props.errors['files.0']"
                                class="text-red-500 my-1"></div>
                        </div>
                    </div>
                    <GenerateAI v-model:content="form.content" @selectMedia="selectMedia" />
                    <div
                        class="flex items-center justify-between border-t-[1px] border-[#ddd] bg-[#f8f9fb] p-3 rounded-bl-lg">
                        <div class="flex items-center space-x-2 md:space-x-4 relative">
                            <div v-if="!postFormData.published || !postFormData.id">
                                <span class="mr-1">Lên lịch bài viết</span>

                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" v-model="isSchedule">
                                    <div
                                        class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                    </div>

                                </label>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2 md:space-x-4 relative">
                            <button type="submit"
                                class="schedule-button flex gap-1 items-center px-4 py-2 bg-[#2C75E3] text-white rounded-[10px]   text-[15px] font-bold"
                                :class="{ 'bg-[#0000000a] text-[#00000040] border-[#d9d9d9]': isLoading, 'bg-[#1677ff] text-white border-[#4096ff]': !isLoading }"
                                :disabled="isLoading">
                                <span :class="!isLoading ? 'border-[#4096ff]' : 'border-[#d9d9d9]'">
                                    {{ isSchedule ? 'Lên lịch' : 'Đăng bài' }}
                                </span>

                            </button>
                        </div>
                    </div>
                </div>
                <MediaLibrary v-if="isShowMedia" :mediaLibrary="mediaLibrary" @closeMediaLibrary="closeMediaLibrary"
                    @selectMedia="selectMedia" :filesSelected="form.files"  />
            </div>
        </form>
        <div v-if="isLoading" class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
            <div class="loader"></div>
        </div>
    </template>

<script setup>

import GenerateAI from '@/Components/GenerateAI.vue';
import MediaLibrary from '@/Components/MediaLibrary.vue';
import { router } from '@inertiajs/vue3';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import dayjs from 'dayjs';
import { defineEmits, inject, onMounted, reactive, ref, watch } from 'vue';
const emit = defineEmits(['closePostForm']);
const facebookFanpageIdActived = inject('facebookFanpageIdActived');
const { postFormData } = defineProps({ postFormData: Object });
const minDate = dayjs().startOf('day').format('YYYY/MM/DD HH:mm:ss');
const maxDate = dayjs().startOf('day').add(29, 'day').format('YYYY/MM/DD HH:mm:ss');

const isLoading = ref(false);
const isShowMedia = ref(false);
const isSchedule = ref(!postFormData.published);

const mediaLibrary = reactive({
    video: {
        currentPage: 0,
        nextPage: 1,
        data: []
    },
    image: {
        currentPage: 0,
        nextPage: 1,
        data: []
    },
});

const form = reactive(postFormData);

const filePreviewUrls = ref([]);

onMounted(() => {
    uploadFilePreviewUrls();
});

const removeFilePreviewUrls = (index) => {
    form.files.splice(index, 1);
};

watch(isSchedule, () => {
    if (isSchedule.value) {
        form.scheduled_publish_time = dayjs().add(1, 'hour').format('YYYY/MM/DD HH:mm:ss')
    } else {
        form.scheduled_publish_time = null
    }
});

watch(form.files, () => {
    uploadFilePreviewUrls();
});

watch(
    () => [form.files],
    ([], []) => {
        uploadFilePreviewUrls();
    }
);


const uploadFilePreviewUrls = async () => {
    let previewUrls = [];
    const promises = Array.from(form.files).map(file => {
        return new Promise((resolve) => {
            if (file.s3_url) {
                previewUrls.push({
                    'url': file.s3_url,
                    'type': file.type,
                });
                resolve();
            } else {
                const reader = new FileReader();
                reader.onload = () => {
                    let objectURL = URL.createObjectURL(file);
                    previewUrls.push({
                        'url': objectURL,
                        'type': file.type,
                    });
                    resolve();
                };
                reader.readAsDataURL(file);
            }
        });
    });

    await Promise.all(promises);

    filePreviewUrls.value = previewUrls;
};

const openMediaLibrary = () => {
    isShowMedia.value = true;
};

const closeMediaLibrary = () => {
    isShowMedia.value = false;
};

const selectFile = () => {
    const fileInput = document.querySelector('.inputMedia');
    fileInput.click();
};

const selectMedia = (item) => {
    removeFileVideos()
    removeFileImages(item)
    if (item.type == 'video' && item.isCheck) {
        form.files = [item];
    }
    if (item.type == 'image' && item.isCheck) {
        form.files = [...form.files, item];
    }
};

const handleFileChange = (event) => {
    const files = event.target.files;

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        removeFileVideos()
        if (file.type.includes('video')) {
            form.files = [file];
        } else {
            form.files = [...form.files, file];
        }
    }
};

const removeFileVideos = () => {
    for (let i = 0; i < form.files.length; i++) {
        const file = form.files[i];
        if (file.type.includes('video')) {
            form.files.splice(i, 1);
        }
    }
};

const removeFileImages = (item) => {
    let files = [...form.files];
    if (!item.isCheck) {
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            if (file.id === item.id) {
                files.splice(i, 1);
            }
        }
    }

    form.files = [...files];
};


const submit = () => {
    form.facebook_fanpage_id = facebookFanpageIdActived.value
    form.published = !isSchedule.value
    form.scheduled_publish_time = form.scheduled_publish_time ? dayjs(form.scheduled_publish_time).format('YYYY/MM/DD HH:mm:ss') : form.scheduled_publish_time;
    isLoading.value = true;

    let url = route('social.post-to-facebook');
    if (form.id) {
        url = route('social.update-to-facebook')
    }

    router.post(url, form, {
        onSuccess: (page) => {
            emit('closePostForm');
            alert('Đăng bài thành công');
        },
        onFinish: (page) => {
            isLoading.value = false;
        },
    })
}
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

const stopRecording = () => {
    if (mediaRecorder) {
        mediaRecorder.stop();
        isRecording.value = false;
    }
};

</script>

<style scoped>
.modal-fade--animation {
    transition: opacity 0.3s ease;
}

.schedule-button:disabled {
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