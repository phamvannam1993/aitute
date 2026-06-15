<template>
    <div class="flex justify-between gap-4 w-full flex-wrap md:flex-nowrap">
        <section ref="resultBox" 
        :class="showImage ? 'lg:w-2/5' : 'lg:w-full'"
        class="bg-white w-full rounded-[10px] md:rounded-[25px] p-[12px] md:p-[20px] h-fit drop-shadow-xl border-[3px] border-[#d4d4d4]">
            <div class="flex flex-col mb-2">
                <div class="flex gap-1">
                    <div class="flex justify-start items-center gap-2">
                        <div class="w-[52px] overflow-hidden rounded-full border-[1px] border-[#d4d4d4]">
                            <img class="w-full h-auto object-top" src="/assets/img/ai3goc/logo/circle.svg" alt="Robot" />
                        </div>

                        <h2 class="text-black font-bold text-xl lg:text-xl 2xl:text-[25px] leading-none">
                            Tạo ảnh từ câu lệnh (prompt)
                        </h2>
                    </div>
                </div>
                <p class="text-ai3goc-sec font-bold text-xs md:text-base my-1 md:my-4">Thực hiện theo các bước sau:</p>
            </div>         
                <form @submit.prevent="generateImage" class="">
                    <div class="relative">
                        <div class="text-black">
                            <Step :step="1" stepName="Mô tả hình ảnh" forName="image-description" />
                            <div class="relative">
                                <SuggestionPrompt v-model="imageDescription" :type="'image'" :screenId="2" />
                                <textarea id="image-description" v-model="imageDescription" type="text" placeholder="Nhập mô tả ảnh bạn muốn tạo..." :disabled="loadingCreateImage" class="w-full p-3 mt-1 h-[200px] border text-black border-orion-sec rounded-[10px] shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder:font-light"></textarea>

                                <div class="object-mic relative ml-auto">
                                    <div v-if="isRecording" class="outline-mic right-3 bottom-3 flex items-center justify-center"></div>
                                    <div v-if="isRecording" class="outline-mic right-3 bottom-3 flex items-center justify-center" id="delayed"></div>
                                    <div v-if="isRecording" class="button-mic right-3 bottom-3 flex items-center justify-center"></div>
                                    <button class="button-mic icon-mic absolute right-3 bottom-3 flex items-center justify-center" @click="startRecording" :disabled="disabledRecord" type="button">
                                        <img class="w-6 h-6" src="/assets/img/ai3goc/icon/mic.svg" alt="mic" />
                                    </button>
                                </div>
                            </div>

                            <span v-if="errors.description" class="text-red-500 text-sm">{{ errors.description }}</span>
                        </div>
                        <div class="mt-4 lg:flex grid grid-cols-2 items-center justify-between">
                            <div class="w-full lg:w-7/12">
                                <Step :step="2" stepName="Số lượng ảnh" forName="image-model" />
                            </div>
                            <select
                                v-model="numberImageSelect"
                                :disabled="loadingCreateImage"
                                :class="{
                                    'bg-gray-200 border-gray-200': !numberImageSelect,
                                    'bg-white border-orion-sec': numberImageSelect,
                                }"
                                class="block mt-1 text-[14px] appearance-none w-full lg:w-5/12 text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                id="image-model"
                            >
                                <option v-for="(model, index) in 4" :value="model" :key="index">
                                    {{ model }}
                                </option>
                            </select>
                            <span v-if="errors.number" class="text-red-500 text-sm">{{ errors.number }}</span>
                        </div>


                        <div class="mt-4 lg:flex grid grid-cols-2 justify-between items-center">
                            <div class="w-full lg:w-7/12">
                                <Step :step="3" stepName="Kích thước" :forName="`image-dimensions-${index}`" />
                            </div>
                            <select
                                v-model="imageDimensions"
                                :id="`image-dimensions-${index}`"
                                :disabled="loadingCreateImage"
                                :class="{
                                    'bg-gray-200 border-gray-200': !imageDimensions,
                                    'bg-white border-orion-sec': imageDimensions,
                                }"
                                class="block mt-1 text-[14px] appearance-none w-full lg:w-5/12 text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none bg-white border-orion-sec"
                            >
                                <option v-for="dimension in validDimensions" :value="dimension" :key="dimension.key">
                                    {{ dimension.key }}
                                </option>
                            </select>
                            <span v-if="errors.dimensions && errors.dimensions[index]" class="text-red-500 text-sm">
                                {{ errors.dimensions[index] }}
                            </span>
                        </div>
                        <div class="mt-4 lg:flex grid grid-cols-2 justify-between items-center">
                            <div class="w-full lg:w-7/12">
                                <Step :step="3 + numberImageSelect" stepName="Phong cách" forName="image-style" />
                            </div>
                            <select
                                v-model="imageStyle"
                                :disabled="loadingCreateImage"
                                :class="{
                                    'bg-gray-200 border-gray-200': !imageStyle,
                                    'bg-white border-orion-sec': imageStyle,
                                }"
                                :max-height="'300px'"
                                class="block mt-1 text-[14px] appearance-none w-full lg:w-5/12 text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                id="image-style"
                            >
                                <option v-for="(style, index) in styles" :value="style" :key="index">
                                    {{ style }}
                                </option>
                            </select>
                        </div>
                        <div v-if="imageModel !== 'Flux Schnell' && imageModel !== 'Aesthetic'">
                            <div class="mt-4">
                                <label for="image-style" class="text-black font-semibold text-[14px]"> Phong cách </label>
                                <select
                                    v-model="imageStyle"
                                    :class="{
                                        'bg-gray-200 border-gray-200': !imageStyle,
                                        'bg-white border-orion-sec': imageStyle,
                                    }"
                                    class="block mt-1 text-[14px] appearance-none w-full text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                    id="image-style"
                                >
                                    <option v-for="(style, index) in styles" :value="style" :key="index">
                                        {{ style }}
                                    </option>
                                </select>
                                <span v-if="errors.style" class="text-red-500 text-sm">{{ errors.style }}</span>
                            </div>
                            <div class="mt-4">
                                <label for="image-artist" class="text-black font-semibold text-[14px]">Nghệ sĩ</label>
                                <select
                                    v-model="imageArtist"
                                    :class="{
                                        'bg-gray-200 border-gray-200': !imageArtist,
                                        'bg-white border-orion-sec': imageArtist,
                                    }"
                                    class="block mt-1 text-[14px] appearance-none w-full text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                    id="image-artist"
                                >
                                    <option v-for="(artist, index) in artistes" :value="artist" :key="index">
                                        {{ artist }}
                                    </option>
                                </select>
                                <span v-if="errors.artist" class="text-red-500 text-sm">{{ errors.artist }}</span>
                            </div>
                        </div>
                    <div class="flex text-black items-center justify-between mt-4 w-full">
                        <div class="w-full lg:w-7/12">
                                <Step :step="4 + numberImageSelect" stepName="" forName="create-image" />
                        </div>
                        <div class="flex justify-start w-full lg:w-5/12">
                            <button id="create-image" :disabled="loadingCreateImage" type="submit" class="md:px-2 py-2 bg-ai3goc-sec text-white rounded-[10px] w-full text-[12px] xl:text-[14px] disabled:opacity-50 disabled:pointer-events-none hover:scale-105 font-bold">Gửi yêu cầu</button>
                        </div>
                    </div>
                    </div>
                </form>
            </section>

            <section v-if="showImage" class="bg-white rounded-[10px] md:rounded-[25px] w-full lg:w-3/5 p-[12px] md:p-[25px] drop-shadow-xl border-[3px] border-[#d4d4d4]">
                <p class="text-orion-sec italic font-thin mb-1 text-center">Hiển thị kết quả</p>
                <div class="flex flex-col justify-between items-center h-fit">
                    <div :class="`${numberImageSelect > 1 ? 'grid lg:grid-cols-2 grid-cols-1 gap-2' : 'grid grid-cols-1 text-sm'}`" class="gap-0.5 w-full columns-2">
                        <div v-for="index in numberImageSelect" :key="index">
                            <div v-if="imageRef[index - 1]" class="relative rounded-xl">
                                <div class="flex items-center justify-center"><img @click="openDetail(imageRef[index - 1])" :src="imageRef[index - 1].s3_url" alt="image generated by AI" class="w-auto object-contain cursor-pointer rounded-xl" :style="{ height: `${imageDimensions.value[1] / 2}px` }" /></div>
                            </div>
                            <div v-else :class="numberImageSelect > 1 ? 'justify-end' : 'justify-center'" class="flex items-center justify-end">
                                <div :class="index % 2 == 0 && 'mr-auto'" class="bg-[#E6E6E6] flex items-center justify-center rounded-xl" :style="{ height: `${imageDimensions.value[1] / (numberImageSelect > 1 ? 3 : 2)}px`,
                                                    width: `${imageDimensions.value[0] / (numberImageSelect > 1 ? 3 : 2)}px`}">
                                    <img v-if="!loadingImageState[index - 1]" src="/assets/svgs/aiwow/image.svg" alt="loading" class="mx-auto w-24" />
                                    <div v-else class="flex flex-col items-center">
                                        <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                                        <h3 class="text-white px-4 text-center">Hệ thống đang xử lý, xin đợi một chút</h3>
                                    </div>
                                </div>
                            </div>
                            <ButtonTaskBar :isActive="imageRef[index - 1]" :selectedImage="imageRef[index - 1]" type="image" :shareLinkableType="'AIGeneratedMedia'" :features="['background','video', 'swap_face']"/>
                        </div>
                    </div>
                    <div class="w-full justify-center items-center flex flex-col md:flex-row mt-2 md:w-full xl:w-full lg:pb-[4px]">
                        <div class="flex mt-4" ref="resultBox">
                            <a :href="route('ai-image.history')" class="px-4 w-[180px] text-center py-2 bg-ai3goc-sec text-white font-bold text-[15px] rounded-[10px] h-fit"> Lịch sử </a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
</template>
<script setup>
import SuggestionPrompt from "@/Components/AIVirtual/SuggestionPrompt.vue";
import { usePage } from "@inertiajs/vue3";
import { computed, nextTick, onMounted, ref, watch } from "vue";
import { toast } from "vue3-toastify";
import axios from "axios";
import ButtonTaskBar from "@/Components/ButtonTaskBar.vue";
import Step from "@/Components/Step/Index.vue";

const emit = defineEmits(["saveGenerationResult","onSubmit","onSuccess","onFinish"]);

const props = defineProps({
    imageDescription: String,
    showImage: {
        type: Boolean,
        default: true
    }
});

const { props: pageProps } = usePage();
const auth = computed(() => pageProps.value.auth);
const additionalCredit = ref(0);

const disabledRecord = ref(false);
const credits = ref(0);
const imageRef = ref([]);
const currentImgSelect = ref(null);
onMounted(() => {
    if (props.imageDescription) {
        imageDescription.value = props.imageDescription
    }
})
watch(() => props.imageDescription, (value) => {
    imageDescription.value = value
})
watch(imageRef, (newValue) => {
    // Set currentImgSelect to the first element of imageRef if it exists
    currentImgSelect.value = newValue.length > 0 ? newValue[0] : null;
    if(numberImageSelect.value == newValue.length) {
        emit('onSuccess')
    }
});

const popupMessage = ref("Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!");
const acceptUpgrade = ref(true);

const styles = ["3D", "Art Deco", "Bút chì", "Cảnh quan", "Cổ điển", "Cubism", "Digital Art", "Hoạt hình", "Impressionism", "Lãng mạn", "Low poly", "Màu nước", "Neon", "Thiên nhiên", "Nhật Bản (Anime)", "Trung Quốc (Ink painting)", "Pop art", "Retro", "Siêu thực", "Sơn dầu", "Tối giản", "Trừu tượng", "Vintage", "Surrealism", "Tương lai", "Thực tế"];

const artistes = ["Leonardo da Vinci", "Vincent van Gogh", "Pablo Picasso", "Claude Monet", "Rembrandt van Rijn", "Salvador Dalí", "Michelangelo Buonarroti", "Johannes Vermeer", "Henri Matisse", "Frida Kahlo"];

const validDimensions = computed(() => {
    if (imageModel.value === "Flux Schnell" || imageModel.value === "Aesthetic") {
        return [
            { key: "1:1", value: [1024, 1024] },
            { key: "16:9", value: [1360, 768] },
            { key: "9:16", value: [768, 1360] },
        ];
    }

    return [
        { key: "256x256", value: [256, 256] },
        { key: "256x512", value: [256, 512] },
        { key: "512x256", value: [512, 256] },
        { key: "512x512", value: [512, 512] },
        { key: "512x768", value: [512, 768] },
        { key: "768x512", value: [768, 512] },
        { key: "768x768", value: [768, 768] },
        { key: "768x1024", value: [768, 1024] },
        { key: "1024x768", value: [1024, 768] },
        { key: "1024x1024", value: [1024, 1024] },
    ];
});

const models = ["Flux Schnell", "Aesthetic", "Latent Consistency"];
// setDefault Value For Model = Flux Schnell
const imageModel = ref("Flux Schnell");
const videoDimensions = ref("16:9");
const imageDimensions = ref(validDimensions.value[0]);
const imageDescription = ref("");
const imageStyle = ref("Thực tế");
const imageArtist = ref("");
const imageHeight = ref(1024);
const imageWidth = ref(1024);
const isLoading = ref(false);
const isShowDes = ref(true);
const resultBox = ref(null);
const imageResponse = ref([]);
const errors = ref({});
const numberImageSelect = ref(1);
const loadingCreateImage = ref(false);
const loadingImageState = ref([]);
const selectedImage = ref(null);
const openDetail = (item) => {
    selectedImage.value = item;
    currentImgSelect.value = item;
};

// watch(imageModel, (newModel) => {
//     videoDimensions.value = newModel === "Flux Schnell" ? "16:9" : "1024x1024";
//     videoDimensions.value = newModel === "Aesthetic" ? "16:9" : "1024x1024";
//     // Change all the size based on new model
//     imageDimensions.value = Array(numberImageSelect.value).fill(validDimensions.value[0]);
// });

// watch(numberImageSelect, (newVal) => {
//     imageDimensions.value = Array(newVal).fill(validDimensions.value[0]);
// });

const validateForm = () => {
    errors.value = {}; // Reset lỗi
    if (imageDescription.value?.trim().length < 2) {
        errors.value.description = "Mô tả hình ảnh tối thiểu 2 ký tự";
    } else if (imageDescription.value.length > 2000) {
        errors.value.description = "Mô tả hình ảnh không được vượt quá 2000 ký tự.";
    }
    if (!imageModel.value) {
        errors.value.model = "Mô hình là bắt buộc.";
    }
    if (imageModel.value != "Flux Schnell" && imageModel.value != "Aesthetic") {
        if (!imageStyle.value) {
            errors.value.style = "Phong cách là bắt buộc.";
        }
        if (!imageArtist.value) {
            errors.value.artist = "Nghệ sĩ là bắt buộc.";
        }
        if (!imageWidth.value) {
            errors.value.width = "Chiều rộng là bắt buộc.";
        }
        if (!imageHeight.value) {
            errors.value.height = "Chiều cao là bắt buộc.";
        }
    }
    return Object.keys(errors.value).length === 0;
};


const showAffKeyPopup = ref(false);
let showBuyCreditPopup = ref(false);

const showBuyCreditModal = () => {
    showBuyCreditPopup.value = true;
};
const checkCredit = async () => {
    try {
        const formData = new FormData();
        formData.append("model", imageModel.value);
        formData.append("type", "image");
        // Gọi API để kiểm tra credit của user
        const response = await axios.post(route("credit.check_credit"), formData);
        if (response.data.success) {
            // Đủ credit để tiếp tục
            return true;
        } else if (response.data.is_vip_expired) {
            showAffKeyPopup.value = true;
            if (response.data.affCode == "aff_trial") {
                popupMessage.value = " Gói của bạn đã hết hạn. Xin vui lòng gia hạn tài khoản để tiếp tục sử dụng tính năng này.";
                acceptUpgrade.value = false;
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
            Object.values(errors).forEach((errorMessages) => {
                if (Array.isArray(errorMessages)) {
                    errorMessages.forEach((error) => {
                        toast.error(error);
                    });
                } else {
                    toast.error(errorMessages);
                }
            });
        } else {
            if (error.response) {
                toast.error(error.response.data.message || "Đã xảy ra lỗi.");
            } else if (error.request) {
                toast.error("Không thể kết nối đến server.");
            } else {
                toast.error("Lỗi: " + error.message);
            }
        }
        return false;
    }
};
const generateImage = async () => {
    if (!validateForm()) {
        return;
    }
    emit('onSubmit')
  
    loadingCreateImage.value = true;
    imageRef.value = [];
    loadingImageState.value = [];
    let currentDescription = imageDescription.value;

    try {
        for (let index = 0; index < numberImageSelect.value; index++) {
            const hasEnoughCredit = await checkCredit();
            if (!hasEnoughCredit) {
                isLoading.value = false;
                return; // Dừng nếu không đủ credit
            }
            loadingImageState.value[index] = true;

            // Only add additional descriptions if the model is not Aesthetic
            let finalDescription = currentDescription;
            if (imageStyle.value) {
                finalDescription += `, ảnh mang phong cách ${imageStyle.value}`;
            }

            try {
                const response = await callGenerateImage(finalDescription, index, models);

                if (response.status === 200 && response.data.url) {
                    isLoading.value = false;
                    imageResponse.value = [...imageResponse.value, response.data];
                    imageRef.value = [...imageRef.value, response.data.generate_file];
                    credits.value = response.data.credits;
                }
            } catch (error) {
                if (error.response && error.response.status === 403) {
                    console.log("error.response ", error.response);
                    showCreditModal();
                } else if (error.response && error.response.status === 500) {
                    toast.error("Đã có lỗi xảy ra khi tạo ảnh. Xin vui lòng tạo lại!");
                    loadingImageState.value[index] = false;
                    console.error("Có lỗi xảy ra: 500 Internal Server Error.");
                    continue;
                } else {
                    console.error("Có lỗi xảy ra:", error);
                    toast.error(error.response?.data?.message || "Đã có lỗi xảy ra khi tạo ảnh. Xin vui lòng tạo lại!");
                }
            }

            loadingImageState.value[index] = false;

            await new Promise((resolve) => setTimeout(resolve, 2000));
        }
        loadingCreateImage.value = false;
        emit('onFinish')
        if (!isShowDes.value && resultBox.value) {
            await nextTick(() => {
                resultBox.value?.scrollIntoView({ behavior: "smooth" });
            });
        }
    } catch (error) {
        console.error("Có lỗi xảy ra:", error);
        toast.error(error.response?.data?.message || "Unknown error occurred");
    } finally {
        isLoading.value = false;
    }
};

const callGenerateImage = async (finalDescription, index, models) => {
    if (models.length === 0) {
        throw new Error("Tất cả các model đều không thành công.");
    }

    try {
        const currentModel = models[0];
        const response = await axios.post(route("ai-image.generate"), {
            description: finalDescription,
            style: imageStyle.value,
            artist: imageArtist.value || "Leonardo da Vinci",
            height: imageDimensions.value.value[0],
            width: imageDimensions.value.value[1],
            model: currentModel,
            aspect_ratio: imageDimensions.value.key,
        });
        emit('saveGenerationResult', response)
        return response;
    } catch (error) {
        if (error.response && error.response.status === 403) {
            showCreditModal();
            throw error;
        } else {
            console.error(`Model ${models[0]} thất bại, thử model tiếp theo...`);
            // Call back
            return callGenerateImage(finalDescription, index, models.slice(1));
        }
    }
};

let showCreditPopup = ref(false);

const showCreditModal = () => {
    showCreditPopup.value = true;
};


const handleCheckSieze = () => {
    if (window.innerWidth >= 1024) {
        isShowDes.value = true;
    } else {
        isShowDes.value = false;
    }
};

onMounted(() => {
    handleCheckSieze();
    window.addEventListener("resize", handleCheckSieze);
});

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
                    const response = await axios.post("/speech-to-text", formData, {
                        headers: { "Content-Type": "multipart/form-data" },
                    });
                    // Xử lý văn bản trả về từ API
                    console.log("Transcription Text:", response);
                    imageDescription.value = response?.data?.text || "Vui lòng thử lại";
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
.loader {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #24AA69;
    border-radius: 50%;
    width: 60px;
    height: 60px;
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
