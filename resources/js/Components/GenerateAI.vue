<template>
    <div class="mb-3">
        <div class="text-[#0000B4] flex flex-col md:flex-row justify-between py-2">
            <div @click="showCreateContentAI"
                class="cursor-pointer flex px-3 py-2 justify-start items-center border-[#0000B4] rounded-[8px] gap-2">
                <img src="/assets/img/magic.png" class="w-6 h-6" alt="" />
                <p>Tạo nội dung bằng AI chuẩn SEO</p>
            </div>
            <div @click="showCreateVideoAi"
                class="flex px-3 py-2 justify-start cursor-pointer items-center border-[#0000B4] rounded-[8px] gap-2">
                <img src="/assets/img/online-video.png" class="w-6 h-6" alt="" />
                <p>Tạo video bằng AI</p>
            </div>
            <div @click="showCreateImageAi"
                class="cursor-pointer flex px-3 py-2 justify-start items-center border-[#0000B4] rounded-[8px] gap-2">
                <img src="/assets/img/gallery.png" class="w-6 h-6" alt="" />
                <p>Tạo hình ảnh bằng AI</p>
            </div>
        </div>
        <div class="text-[#0000B4] items-center justify-between py-2">
            <Modal :show="modal.showCreateContentAI" maxWidth="4xl" @close="hiddenCreateContentAI">
                <form class="p-10" @submit.prevent="submitCreateContentAI">
                    <h2 class="pb-9 text-xl text-center">Tạo nội dung bằng AI chuẩn SEO </h2>
                    <div
                        class="flex px-2 py-1 justify-between items-center rounded-2xl">
                        <img src="/assets/img/magic.png" class="w-6 h-6" alt="" />
                        <div class="w-[60%]">
                            <input type="text" v-model="formCreateContentAI.prompt"
                                class="w-full border-2 px-4 py-2 border-[#0000B4] text-sm md:text-base p-1 rounded-lg"
                                placeholder="Nhập prompt để tạo nội dung..." />
                                <div class="object-mic relative ml-auto">
                                    <div
                                        v-if="isContentRecording"
                                            class="outline-mic right-3 bottom-1.5 flex items-center justify-center"></div>
                                        <div v-if="isContentRecording"
                                            class="outline-mic right-3 bottom-1.5 flex items-center justify-center"
                                            id="delayed"></div>
                                        <div v-if="isContentRecording"
                                            class="button-mic right-3 bottom-1.5 flex items-center justify-center"></div>
                                        <button class="button-mic icon-mic absolute right-3 bottom-1.5 flex items-center justify-center"
                                            @click="startContentRecording"
                                            :disabled="disabledRecord"
                                            type="button">
                                            <svg class="size-4" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000">
                                                <g>
                                                    <path
                                                                d="M500,683.8c84.6,0,153.1-68.6,153.1-153.1V163.1C653.1,78.6,584.6,10,500,10c-84.6,0-153.1,68.6-153.1,153.1v367.5C346.9,615.2,415.4,683.8,500,683.8z M714.4,438.8v91.9C714.4,649,618.4,745,500,745c-118.4,0-214.4-96-214.4-214.4v-91.9h-61.3v91.9c0,141.9,107.2,258.7,245,273.9v124.2H346.9V990h306.3v-61.3H530.6V804.5c137.8-15.2,245-132.1,245-273.9v-91.9H714.4z"
                                                            />
                                                </g>
                                            </svg>
                                        </button>
                                </div>
                            <div v-if="$page.props.errors.prompt"
                                class="text-red-500 my-1">
                                <span>Prompt không được bỏ trống</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <div>
                                <button @click="hiddenCreateContentAI"
                                    class="px-4 py-2 rounded-xl text-white border-2 bg-[#ff2b2b] border-[#ff2b2b] hidden lg:block">
                                    Hủy
                                </button>
                                <button class="rounded-2xl p-1 text-white bg-[#ff2b2b] lg:hidden">
                                    <img src="/assets/svgs/close2.svg" alt="" />
                                </button>
                            </div>
                            <div>
                                <button
                                    class="px-4 py-2 rounded-xl text-white border-2 bg-[#403ECC] border-[#403ECC] hidden lg:block">
                                    Tạo nội dung
                                </button>
                                <button class="rounded-2xl p-1 text-white bg-[#403ECC] lg:hidden">
                                    <img src="/assets/svgs/check.svg" alt="" />
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <div v-if="isLoading"
                    class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
                    <div class="loader"></div>
                </div>
            </Modal>

            <Modal :show="modal.showCreateVideoAi" maxWidth="4xl" @close="hiddenCreateVideoAi">
                <form class="p-10" @submit.prevent="submitCreateVideoAi">
                    <h2 class="pb-9 text-xl text-center">Tạo video bằng AI </h2>
                    <div class="relative">
                        <div class="">
                            <label for="video-description" class="text-black">Mô tả video</label>
                            <textarea id="video-description" v-model="formCreateVideoAi.video_description" type="text"
                                placeholder="Nhập mô tả video bạn muốn tạo..."
                                class="w-full p-3 border text-black border-gray-300 rounded-[13px] shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                <div class="object-mic relative ml-auto">
                                                <div
                                                    v-if="isVideoRecording"
                                                    class="outline-mic right-3 bottom-3 flex items-center justify-center"
                                                ></div>
                                                <div
                                                    v-if="isVideoRecording"
                                                    class="outline-mic right-3 bottom-3 flex items-center justify-center"
                                                    id="delayed"
                                                ></div>
                                                <div
                                                    v-if="isVideoRecording"
                                                    class="button-mic right-3 bottom-3 flex items-center justify-center"
                                                ></div>
                                                <button
                                                    class="button-mic icon-mic absolute right-3 bottom-3 flex items-center justify-center"
                                                    @click="startVideoRecording"
                                                    :disabled="disabledRecord"
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
                            <div v-if="$page.props.errors.video_description"
                                v-text="$page.props.errors.video_description" class="text-red-500 my-1">
                            </div>
                        </div>
                        <div class="">
                            <label for="video-description" class="text-black">Mô tả âm thanh</label>
                            <textarea id="video-description" v-model="formCreateVideoAi.audio_description" type="text"
                                placeholder="Nhập mô tả âm thanh video bạn muốn tạo..."
                                class="w-full p-3 border text-black border-gray-300 rounded-[13px] shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
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
                                                    <svg class="h-6 w-6" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000">
                                                        <g>
                                                            <path
                                                                d="M500,683.8c84.6,0,153.1-68.6,153.1-153.1V163.1C653.1,78.6,584.6,10,500,10c-84.6,0-153.1,68.6-153.1,153.1v367.5C346.9,615.2,415.4,683.8,500,683.8z M714.4,438.8v91.9C714.4,649,618.4,745,500,745c-118.4,0-214.4-96-214.4-214.4v-91.9h-61.3v91.9c0,141.9,107.2,258.7,245,273.9v124.2H346.9V990h306.3v-61.3H530.6V804.5c137.8-15.2,245-132.1,245-273.9v-91.9H714.4z"
                                                            />
                                                        </g>
                                                    </svg>
                                                </button>
                                            </div>
                            <div v-if="$page.props.errors.audio_description"
                                v-text="$page.props.errors.audio_description" class="text-red-500 my-1">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="video-style" class="text-black">Mô hình</label>
                            <select v-model="formCreateVideoAi.model"
                                class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded-[15px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                id="video-style">
                                <option v-for="(model, index) in models" :value="model" :key="index">
                                    {{ model }}
                                </option>
                            </select>
                            <div v-if="$page.props.errors.model" v-text="$page.props.errors.model"
                                class="text-red-500 my-1">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="video-artist" class="text-black">Thời lượng</label>
                            <select v-model="formCreateVideoAi.duration"
                                class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded-[15px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                id="video-artist">
                                <option v-for="duration in durations" :value="duration" :key="duration">
                                    {{ duration }}
                                </option>
                            </select>
                            <div v-if="$page.props.errors.duration" v-text="$page.props.errors.duration"
                                class="text-red-500 my-1">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="video-resolutions" class="text-black">Kích thước</label>
                            <select v-model="formCreateVideoAi.resolution" id="video-resolutions"
                                class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded-[15px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                <option v-for="resolution in resolutions" :value="resolution" :key="resolution">
                                    {{ resolution }}
                                </option>
                            </select>
                            <div v-if="$page.props.errors.resolution" v-text="$page.props.errors.resolution"
                                class="text-red-500 my-1">
                            </div>
                        </div>
                        <div class="mt-4 flex justify-center">
                            <button type="submit"
                                class="px-4 md:px-11 py-2 bg-[#2C75E3] text-white font-semibold rounded-[15px] min-w-36">
                                Gửi
                            </button>
                        </div>
                    </div>
                </form>
                <div v-if="isLoading"
                    class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
                    <div class="loader"></div>
                </div>
            </Modal>


            <Modal :show="modal.showCreateImageAi" maxWidth="4xl" @close="hiddenCreateImageAi">
                <form class="p-10" @submit.prevent="submitCreateImageAi">
                    <h2 class="pb-9 text-xl text-center">Tạo hình ảnh bằng AI </h2>
                    <div
                        class="flex px-2 py-1 justify-between items-center rounded-2xl">
                        <img src="/assets/img/gallery.png" class="w-6 h-6" alt="" />
                        <div class="w-[60%]">
                            <input type="text" v-model="formCreateImageAI.description"
                                class="w-full border-2 px-4 py-2 border-[#0000B4] text-sm md:text-base p-1 rounded-lg"
                                placeholder="Nhập nội dung để tạo hình ảnh..." />
                                <div class="object-mic relative ml-auto">
                                    <div
                                        v-if="isImageRecording"
                                            class="outline-mic right-3 bottom-1.5 flex items-center justify-center"></div>
                                        <div v-if="isImageRecording"
                                            class="outline-mic right-3 bottom-1.5 flex items-center justify-center"
                                            id="delayed"></div>
                                        <div v-if="isImageRecording"
                                            class="button-mic right-3 bottom-1.5 flex items-center justify-center"></div>
                                        <button class="button-mic icon-mic absolute right-3 bottom-1.5 flex items-center justify-center"
                                            @click="startImageRecording"
                                            :disabled="disabledRecord"
                                            type="button">
                                            <svg class="size-4" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000">
                                                <g>
                                                    <path
                                                                d="M500,683.8c84.6,0,153.1-68.6,153.1-153.1V163.1C653.1,78.6,584.6,10,500,10c-84.6,0-153.1,68.6-153.1,153.1v367.5C346.9,615.2,415.4,683.8,500,683.8z M714.4,438.8v91.9C714.4,649,618.4,745,500,745c-118.4,0-214.4-96-214.4-214.4v-91.9h-61.3v91.9c0,141.9,107.2,258.7,245,273.9v124.2H346.9V990h306.3v-61.3H530.6V804.5c137.8-15.2,245-132.1,245-273.9v-91.9H714.4z"
                                                            />
                                                </g>
                                            </svg>
                                        </button>
                                </div>
                            <div v-if="$page.props.errors.description"
                                class="text-red-500 my-1">
                                <span>Nội dung không được bỏ trống</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <div>
                                <button @click="hiddenCreateImageAi"
                                    class="px-4 py-2 rounded-xl text-white border-2 bg-[#ff2b2b] border-[#ff2b2b] hidden lg:block">
                                    Hủy
                                </button>
                                <button class="rounded-2xl p-1 text-white bg-[#ff2b2b] lg:hidden">
                                    <img src="/assets/svgs/close2.svg" alt="" />
                                </button>
                            </div>
                            <div>
                                <button
                                    class="px-4 py-2 rounded-xl text-white border-2 bg-[#403ECC] border-[#403ECC] hidden lg:block">
                                    Tạo nội dung
                                </button>
                                <button class="rounded-2xl p-1 text-white bg-[#403ECC] lg:hidden">
                                    <img src="/assets/svgs/check.svg" alt="" />
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <div v-if="isLoading"
                    class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
                    <div class="loader"></div>
                </div>
            </Modal>
        </div>
    </div>

</template>

<script setup>
import Modal from '@/Components/Modal.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';
defineProps({ content: String });
const emit = defineEmits();
const isLoading = ref(false);
const page = usePage();

const modal = reactive({
    showCreateContentAI: false,
    showCreateVideoAi: false,
    showCreateImageAi: false,
});

const showCreateContentAI = () => {
    page.props.errors = []
    modal.showCreateContentAI = true;
};

const showCreateVideoAi = () => {
    page.props.errors = []
    modal.showCreateVideoAi = true;
};

const showCreateImageAi = () => {
    page.props.errors = []
    modal.showCreateImageAi = true;
};

const hiddenCreateContentAI = () => {
    modal.showCreateContentAI = false;
};

const hiddenCreateVideoAi = () => {
    modal.showCreateVideoAi = false;
};

const hiddenCreateImageAi = () => {
    modal.showCreateImageAi = false;
};

const formCreateContentAI = reactive({
    prompt: null,
});

const formCreateVideoAi = useForm({
    video_description: null,
    audio_description: null,
    model: 'Kling',
    duration: '5',
    resolution: '16:9',
});

const formCreateImageAI = reactive({
    description: null,
});

const models = [
    "Kling",
    "Lucataco/animate-diff",
];

const durations = computed(() => {
    formCreateVideoAi.duration = '5';
    if (formCreateVideoAi.model === "Kling") {
        return ['5', '10'];
    }
    return ['2', '5', '10'];
});

const resolutions = computed(() => {
    if (formCreateVideoAi.model === "Kling") {
        formCreateVideoAi.resolution = '16:9';
        return ["16:9", "9:16", "1:1"];
    }

    formCreateVideoAi.resolution = '1024x1024';
    return [
        "256x256",
        "256x512",
        "512x256",
        "512x512",
        "512x768",
        "768x512",
        "768x768",
        "768x1024",
        "1024x768",
        "1024x1024",
    ];
});

const submitCreateContentAI = async () => {
    try {
        isLoading.value = true;
        const response = await axios.post(route('generate-content-with-ai'), formCreateContentAI)
        emit('update:content', response.data.content)
        modal.showCreateContentAI = false;
    } catch (error) {
        page.props.errors = error.response.data.errors
    } finally {
        isLoading.value = false;
    }
}

const submitCreateVideoAi = async () => {
    try {
        isLoading.value = true;
        await axios.post(route('ai-video.generate-video-audio'), formCreateVideoAi)
        modal.showCreateVideoAi = false;
        alert("Video sẽ được tạo và hiển thị trong Kho đa phương tiện. Xin vui lòng chờ trong vài phút");
    } catch (error) {
        page.props.errors = error.response.data.errors
    } finally {
        isLoading.value = false;
    }
};

const submitCreateImageAi = async () => {
    try {
        isLoading.value = true;
        const response = await axios.post(route('ai-image.generate-image-for-post'), formCreateImageAI)
        modal.showCreateImageAi = false;
        selectMedia(response.data.generate_file)
    } catch (error) {
        page.props.errors = error.response.data.errors
    } finally {
        isLoading.value = false;
    }
}

const selectMedia = (item) => {
    item.isCheck = true
    emit('selectMedia', item);
};


const isVideoRecording = ref(false);
const startVideoRecording = async () => {
    if (!isVideoRecording.value) {
        // Nếu chưa ghi âm
        try {
            isVideoRecording.value = true; // Bắt đầu ghi âm
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
                    formCreateVideoAi.video_description.value = response?.data?.text || 'Vui lòng thử lại';
                } catch (error) {
                    console.error("Error in Speech-to-Text:", error);
                } finally {
                    // isTranscribing.value = false;
                }

                isVideoRecording.value = false;
            });

            // Bắt đầu ghi âm
            mediaRecorder.onstart = () => {
                audioChunks.value = []; // Xóa các đoạn âm thanh trước đó
            };

            mediaRecorder.start(); // Bắt đầu ghi
        } catch (error) {
            console.error("Lỗi khi bắt đầu ghi âm:", error);
            isVideoRecording.value = false; // Trở lại trạng thái chưa ghi âm nếu có lỗi
        }
    } else {
        // Nếu đang ghi âm
        isVideoRecording.value = false; // Dừng ghi âm
        mediaRecorder.stop(); // Kết thúc ghi âm
        device.getTracks().forEach((track) => track.stop()); // Dừng tất cả các tracks
    }
};

const isImageRecording = ref(false);
const startImageRecording = async () => {
    if (!isImageRecording.value) {
        // Nếu chưa ghi âm
        try {
            isImageRecording.value = true; // Bắt đầu ghi âm
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
                    formCreateImageAI.description.value = response?.data?.text || 'Vui lòng thử lại';
                } catch (error) {
                    console.error("Error in Speech-to-Text:", error);
                } finally {
                    // isTranscribing.value = false;
                }

                isImageRecording.value = false;
            });

            // Bắt đầu ghi âm
            mediaRecorder.onstart = () => {
                audioChunks.value = []; // Xóa các đoạn âm thanh trước đó
            };

            mediaRecorder.start(); // Bắt đầu ghi
        } catch (error) {
            console.error("Lỗi khi bắt đầu ghi âm:", error);
            isImageRecording.value = false; // Trở lại trạng thái chưa ghi âm nếu có lỗi
        }
    } else {
        // Nếu đang ghi âm
        isImageRecording.value = false; // Dừng ghi âm
        mediaRecorder.stop(); // Kết thúc ghi âm
        device.getTracks().forEach((track) => track.stop()); // Dừng tất cả các tracks
    }
};
const isContentRecording = ref(false);
const startContentRecording = async () => {
    if (!isContentRecording.value) {
        // Nếu chưa ghi âm
        try {
            isContentRecording.value = true; // Bắt đầu ghi âm
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
                    formCreateContentAI.prompt.value = response?.data?.text || 'Vui lòng thử lại';
                } catch (error) {
                    console.error("Error in Speech-to-Text:", error);
                } finally {
                    // isTranscribing.value = false;
                }

                isContentRecording.value = false;
            });

            // Bắt đầu ghi âm
            mediaRecorder.onstart = () => {
                audioChunks.value = []; // Xóa các đoạn âm thanh trước đó
            };

            mediaRecorder.start(); // Bắt đầu ghi
        } catch (error) {
            console.error("Lỗi khi bắt đầu ghi âm:", error);
            isContentRecording.value = false; // Trở lại trạng thái chưa ghi âm nếu có lỗi
        }
    } else {
        // Nếu đang ghi âm
        isContentRecording.value = false; // Dừng ghi âm
        mediaRecorder.stop(); // Kết thúc ghi âm
        device.getTracks().forEach((track) => track.stop()); // Dừng tất cả các tracks
    }
};

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
                    formCreateVideoAi.audio_description.value = response?.data?.text || 'Vui lòng thử lại';
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
