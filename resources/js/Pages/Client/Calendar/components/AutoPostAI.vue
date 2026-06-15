<template>
    <form @submit.prevent="submit" class="relative p-6 mt-6 md:m-6">
        <h2 class="font-bold text-[24px] text-ai3goc-sec mb-2">Tạo bài đăng tự động</h2>
        <div v-if="$page.props.errors.message" v-text="$page.props.errors.message" class="text-red-500 my-1"></div>
        <div class="relative">
            <div class="mb-2 flex flex-col gap-2">
                <label for="description" class="text-black font-semibold text-[14px] cursor-pointer">
                    <div class="flex items-center gap-1">
                        <div class="flex items-center gap-1 py-1.5 px-2 rounded-full bg-ai3goc-primary min-w-[84px]">
                            
                            <span class="text-white text-[12px] lg:text-[14px] leading-none font-bold">Bước 1</span>
                        </div>
                        <span class="font-bold text-[12px] lg:text-[14px] leading-none">Mô tả</span>
                    </div>
                </label>

                <div class="w-full relative">
                    <SuggestionPrompt v-model="form.description"  />
                    <textarea id="description" v-model="form.description" type="text"
                        placeholder="Nhập mô tả nội dung bài viết bạn muốn tạo..."
                        class="p-3 w-full  h-[160px] border text-black border-[#B5B5BE] rounded-[10px] shadow-sm "></textarea>
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
                                                    :disabled="disabledRecord"
                                                    type="button"
                                                >
                                                    <img src="/assets/img/ai3goc/icon/mic.svg" alt="microphone" />
                                                </button>
                                            </div>
                    <div v-if="$page.props.errors.description"
                        class="text-red-500 my-1">
                        <span>Mô tả không được bỏ trống</span>
                    </div>
                </div>
            </div>

            <div class="mb-2 flex flex-col md:flex-row md:justify-between items-start md:items-center gap-2">
                <label for="description" class="text-black font-semibold text-[14px] cursor-pointer">
                    <div class="flex items-center gap-1">
                        <div class="flex items-center gap-1 py-1.5 px-2 rounded-full bg-ai3goc-primary min-w-[84px]">
                            
                            <span class="text-white text-[12px] lg:text-[14px] leading-none font-bold">Bước 2</span>
                        </div>
                        <span class="font-bold text-[12px] lg:text-[14px] leading-none">Độ dài bài viết</span>
                    </div>
                </label>
                <div class="w-full lg:w-2/3">
                    <select v-model="form.limit" class="w-full border-[#B5B5BE] rounded-[10px]">
                        <option v-for="(item, index) in limits" :key="index">
                            {{ item }}
                        </option>
                    </select>
                    <div v-if="$page.props.errors.limit" v-text="$page.props.errors.limit" class="text-red-500 my-1">
                    </div>
                </div>
            </div>

            <div class="mb-2 flex flex-col md:flex-row md:justify-between items-start md:items-center gap-2">
                <label for="description" class="text-black font-semibold text-[14px] cursor-pointer">
                    <div class="flex items-center gap-1">
                        <div class="flex items-center gap-1 py-1.5 px-2 rounded-full bg-ai3goc-primary min-w-[84px]">
                            
                            <span class="text-white text-[12px] lg:text-[14px] leading-none font-bold">Bước 3</span>
                        </div>
                        <span class="font-bold text-[12px] lg:text-[14px] leading-none">Ngôn ngữ</span>
                    </div>
                </label>
                <div class="w-full lg:w-2/3">
                    <select v-model="form.lang" class="w-full border-[#B5B5BE] rounded-[10px]">
                        <option v-for="(item, index) in languages" :key="index">
                            {{ item }}
                        </option>
                    </select>
                    <div v-if="$page.props.errors.lang" v-text="$page.props.errors.lang" class="text-red-500 my-1">
                    </div>
                </div>
            </div>

            <div class="mb-2 flex flex-col md:flex-row md:justify-between items-start md:items-center gap-2">
                <label for="description" class="text-black font-semibold text-[14px] cursor-pointer">
                    <div class="flex items-center gap-1">
                        <div class="flex items-center gap-1 py-1.5 px-2 rounded-full bg-ai3goc-primary min-w-[84px]">
                            
                            <span class="text-white text-[12px] lg:text-[14px] leading-none font-bold">Bước 4</span>
                        </div>
                        <span class="font-bold text-[12px] lg:text-[14px] leading-none">Ngày đăng</span>
                    </div>
                </label>
                <div class="w-full lg:w-2/3">
                    <VueDatePicker format="dd/MM/yyyy" :ui="{ input: 'rounded-[10px] border-[#B5B5BE]' }"
                        locale="vi" v-model="form.date_range" range auto-apply :close-on-auto-apply="false"
                        :enable-time-picker="false" :min-date="minDate" :max-date="maxDate"
                        :day-names="['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN']" />

                    <div v-if="$page.props.errors['date_range.0']" v-text="$page.props.errors['date_range.0']"
                        class="text-red-500 my-1">
                    </div>
                </div>

            </div>

            <div class="mb-2 flex flex-col md:flex-row md:justify-between items-start md:items-center gap-2">
                <label for="description" class="text-black font-semibold text-[14px] cursor-pointer">
                    <div class="flex items-center gap-1">
                        <div class="flex items-center gap-1 py-1.5 px-2 rounded-full bg-ai3goc-primary min-w-[84px]">
                            
                            <span class="text-white text-[12px] lg:text-[14px] leading-none font-bold">Bước 5</span>
                        </div>
                        <span class="font-bold text-[12px] lg:text-[14px] leading-none">Giờ đăng</span>
                    </div>
                </label>
                <div class="w-full lg:w-2/3">
                    <VueDatePicker select-text="Chọn" cancel-text="Đóng" v-model="form.time" time-picker />

                    <div v-if="$page.props.errors['time.hours']" v-text="$page.props.errors['time.hours']"
                        class="text-red-500 my-1">
                    </div>
                    <div v-if="$page.props.errors['time.minutes']" v-text="$page.props.errors['time.minutes']"
                        class="text-red-500 my-1">
                    </div>
                    <div v-if="$page.props.errors['time.seconds']" v-text="$page.props.errors['time.seconds']"
                        class="text-red-500 my-1">
                    </div>
                </div>

            </div>

            <div class="mt-5 flex justify-center">
                <button type="submit"
                    class="flex items-center justify-center px-4 py-2 bg-ai3goc-sec text-white rounded-[15px] text-[15px] font-semibold gap-2 w-[140px]">
                    <img src="/assets/img/orion/icon/create_post-white.svg" class="w-[26px] h-auto"/>
                    <span>Gửi</span>
                </button>
            </div>
        </div>



    </form>
    <div v-if="isLoading" class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
        <div class="loader"></div>
    </div>

</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import dayjs from 'dayjs';
import { defineEmits, inject, ref } from 'vue';
import SuggestionPrompt from "@/Components/AIVirtual/SuggestionPrompt.vue";
import { toast } from "vue3-toastify";

const emit = defineEmits(['closeAutoPostAI']);
const facebookFanpageIdActived = inject('facebookFanpageIdActived');
const languages = [
    "Tiếng Việt",
    "Tiếng Anh",
    "Tiếng Trung",
    "Tiếng Nhật",
    "Tiếng Hàn",
    "Tiếng Pháp",
    "Tiếng Đức",
];
const limits = [100, 200, 500, 1000, 2000, 3000];

const startDate = dayjs().startOf('day').format('YYYY/MM/DD HH:mm:ss');
const endDate = dayjs().startOf('day').add(7, 'day').format('YYYY/MM/DD HH:mm:ss');
const minDate = dayjs().startOf('day').format('YYYY/MM/DD HH:mm:ss');
const maxDate = dayjs().startOf('day').add(29, 'day').format('YYYY/MM/DD HH:mm:ss');
const isLoading = ref(false);
const form = useForm({
    social_postable_id: null,
    social_postable_type: 'FacebookFanpage',
    description: null,
    limit: 100,
    lang: 'Tiếng Việt',
    date_range: [startDate, endDate],
    time: {
        hours: 0,
        minutes: 0,
        seconds: 0,
    },
});

const submit = () => {
    form.social_postable_id = facebookFanpageIdActived.value
    form.date_range[0] = dayjs(form.date_range[0]).format('YYYY/MM/DD HH:mm:ss')
    form.date_range[1] = dayjs(form.date_range[1]).format('YYYY/MM/DD HH:mm:ss')

    form.post(route('social.job-create-content-ai'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
            emit('closeAutoPostAI');
            toast.success("Bài viết đang được tạo. Chờ trong giây lát để bài viết được cập nhật", { autoClose: 10000 });
        },
        onError: () => { },
    });
};

const isRecording = ref(false);
const audioBlob = ref(null);
const audioUrl = ref(null);
let mediaRecorder = null;
let audioChunks = [];
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


</script>

<style scoped>
.modal-fade--animation {
    transition: opacity 0.3s ease;
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