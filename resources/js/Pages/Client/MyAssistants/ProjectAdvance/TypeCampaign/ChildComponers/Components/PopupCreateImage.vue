<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center" style="z-index: 9999999;" :class="props.is_show ? '':'hidden'">
        <div class="bg-white py-6 px-4 md:p-8 shadow-lg w-[320px] md:w-[700px] xl:w-[1000px] rounded-[40px] relative" >
            <div class="">
                <div class="flex flex-col lg:flex-row md:justify-end md:items-end lg:items-center mt-4 flex-wrap lg:flex-nowrap lg:justify-between">
                    <h2 class="text-black font-bold text-2xl mb-4">Tạo lại ảnh</h2>
                    <img src="/assets/img/close2.png" class="cursor-pointer absolute top-2 right-5" @click="closePopup" />
                </div>
                 <div class="flex w-full border-b-[3px] border-[#d6d6d6] mb-5 px-5"></div>
            </div>

            <div class="relative">
                <div class="overflow-y-auto h-[60vh] lg:h-[70vh] mb-2 relative">
                    <div class="flex lg:justify-between gap-8 flex-wrap md:flex-nowrap">
                        <section ref="resultBox"
                    class="bg-white w-full  lg:w-1/2  h-fit">
                        <div class="flex flex-col mb-2">
                            <p class="text-ai3goc-sec text-sm md:text-base font-bold">Thực hiện theo các bước sau:</p>
                        </div>
                            <form @submit.prevent="generateImage" class="">
                                <div class="relative">
                                    <div class="text-black">
                                        <Step :step="1" stepName="Mô tả nội dung ảnh" forName="image-description" />
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
                                    <div class="flex text-black items-center justify-between mt-4 w-full">
                                        <div class="">
                                            <span class="text-[12px] lg:text-[16px]">Hoặc chọn/thêm mẫu ảnh</span>
                                        </div>
                                        <div class="flex justify-start w-fit">
                                            <div class="rounded-lg justify-center flex items-center">
                                            <span class="flex items-center rounded-lg justify-center bg-orion-orange cursor-pointer text-xs md:text-[15px] w-[100px] h-[26px] text-white" @click="showModelImage()"> Kho ảnh mẫu </span>
                                                <label for="image-product-model" class="flex items-center rounded-lg justify-center bg-orion-orange cursor-pointer text-xs md:text-[15px] w-[26px] h-[26px] text-aiwow-sec ml-2">
                                                    <img src="/assets/svgs/upload_white.svg" alt="">
                                                    <input type="file" class="hidden" id="image-product-model" @change="uploadImageModel" />
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex text-black items-center justify-between mt-4 w-full" v-if="imageModel">
                                        <div class="w-full lg:w-7/12">
                                            <img style="width:100px" :src="imageModel">
                                         </div>
                                    </div>
                                    <div class="mt-4">
                                            <div class="flex flex-row md:flex-col xl:flex-row xl:items-center xl:gap-8 gap-1 justify-between  xl:justify-between">
                                                <Step :step="2" stepName="Chọn kích thước" forName="video-dimensions" />

                                                <select v-model="imageDimension"
                                                    :class="{ 'bg-gray-200 border-gray-200': !imageDimension, 'bg-white border-orion-sec': imageDimension }"
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
                                    <div class="flex text-black items-center justify-between mt-4 w-full">
                                        <div class="w-full lg:w-7/12">
                                                <Step :step="3" stepName="Bấm vào đây" forName="create-image" />
                                        </div>
                                        <div class="flex justify-start w-[30%] lg:w-5/12">
                                            <button id="create-image" :disabled="loadingCreateImage" type="submit" class="md:px-2 py-2 bg-ai3goc-sec text-white rounded-[10px] w-full text-[12px] xl:text-[14px] disabled:opacity-50 disabled:pointer-events-none hover:scale-105 font-bold" @click="combineImages">Gửi yêu cầu</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </section>

                        <section class="bg-white  w-full lg:w-1/2">
                            <p class="text-[#1E405A] font-thin mb-1">Kết quả</p>
                            <div class="flex flex-col justify-between items-center h-fit">
                                <div class="flex items-center justify-center">
                                    <div class="bg-[#E6E6E6] flex items-center justify-center rounded-xl w-full h-auto min-h-[200px] min-w-[300px]" >
                                        <img v-if="!loadingCreateImage" :src="imageS3Url ? imageS3Url: '/assets/svgs/aiwow/image.svg'" alt="loading" class="mx-auto w-24" :class="!imageS3Url ? '' : 'w-full h-auto'">
                                         <div v-if="loadingCreateImage" class="flex flex-col items-center">
                                            <img class="w-24 h-24" src="/assets/img/dashboard/loading.gif" alt="" />
                                            <h3 class="text-white">Hệ thống đang xử lý, xin đợi một chút</h3>
                                        </div>
                                    </div>
                               </div>
                            </div>
                             <div class="flex text-black items-center justify-around mt-4 w-full gap-4">
                                <div class="w-1/2 lg:w-1/3">
                                     <button @click="saveImage" type="button" class="md:px-2 py-2 bg-ai3goc-sec text-white rounded-[10px] w-full text-[12px] xl:text-[14px] disabled:opacity-50 disabled:pointer-events-none hover:scale-105 font-bold">Sử dụng ảnh này</button>
                                </div>
                                <div class="flex justify-start w-1/2 lg:w-1/3">
                                    <button @click="imageS3Url ? downloadImage(imageS3Url) : ''" type="submit" class="justify-between items-center px-2 py-2 bg-ai3goc-primary text-white rounded-[10px] w-full text-[12px] xl:text-[14px] disabled:opacity-50 disabled:pointer-events-none hover:scale-105 font-bold">
                                         <svg style="float: left;" width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M15.6 11.1313V14.3758C15.6 14.806 15.4291 15.2187 15.1249 15.5229C14.8206 15.8271 14.408 15.998 13.9778 15.998H2.62222C2.19198 15.998 1.77936 15.8271 1.47514 15.5229C1.17091 15.2187 1 14.806 1 14.3758V11.1313" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M11.9494 7.4833L8.29941 11.1333L4.64941 7.4833" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M8.2998 11.1333V1.39997" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span>Tải xuống</span>
                                    </button>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";
import SuggestionPrompt from "@/Components/AIVirtual/SuggestionPrompt.vue";
import { computed, nextTick, onMounted, ref, watch } from "vue";
import { toast } from "vue3-toastify";
import ButtonTaskBar from "@/Components/ButtonTaskBar.vue";
import Step from "@/Components/Step/Index.vue";
const emit = defineEmits(["saveGenerationResult","onSubmit","onSuccess","onFinish"]);

const props = defineProps({
    message: String,
    imageModel:String,
    is_show:Boolean,
    isError:Boolean,
    type: String
});
const imageDimension = ref('16:9')
const closePopup = () => {
    emit('close');
};
const showModelImage = () => {
    emit('showImageList');
}
const confirmVideo = () => {
    emit('confirmVideo');
};

// setDefault Value For Model = Flux Schnell
const imageDescription = ref("");
const productImage = ref("")
const imageModel = ref("")
const isLoading = ref(false);
const isShowDes = ref(true);
const resultBox = ref(null);
const imageResponse = ref([]);
const errors = ref({});
const imageS3Url = ref("")
const loadingCreateImage = ref(false);
const validDimensions = computed(() => ['16:9', '9:16']);

const updateImageUrl = async (s3_url, productImg = '') => {
    if(productImg) {
        productImage.value = productImg
    }
    if(s3_url) {
        imageModel.value = s3_url
    }
    imageS3Url.value = ""
}

const uploadImageModel = async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    // Validate file type
    const validImageTypes = ["image/jpeg", "image/png", "image/jpg", "image/gif"];
    if (!validImageTypes.includes(file.type)) {
        toast.error("File phải là hình ảnh có định dạng: jpeg, png, jpg, gif");
        e.target.value = "";
        return;
    }

    // Validate file size (max 10MB)
    const maxSize = 10 * 1024 * 1024; // 10MB in bytes
    if (file.size > maxSize) {
        toast.error("Kích thước hình ảnh tối đa là 10MB");
        e.target.value = "";
        return;
    }

    const imageUrl = await getS3URL(file);
    if(imageUrl) {
        imageModel.value = imageUrl;
    } else {
        toast.error('Có lỗi xảy ra trong quá trình upload')
    }
};

const getS3URL = async (file) => {
    const formData = new FormData();
    formData.append("image_file", file);
    var url = route("short-video.uploadImageToS3")
    try {
        const response = await axios.post(url, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
        if (response.data.success) {
            return response.data.s3_url
        } else {
            return ""
        }
    } catch (err) {
        return ""
    }
}

const saveImage = () => {
    if(!imageS3Url.value) {
        return false
    }
    emit('updateImage', imageS3Url.value);
}

const combineImages = async () => {
    let formData = new FormData();
    if(loadingCreateImage.value) {
        return false
    }
    if(!imageModel.value || !productImage.value) {
        toast.error('Vui lòng thêm ảnh mẫu')
        return false
    }
    loadingCreateImage.value = true
    formData.append("image_model", imageModel.value);
    formData.append("image_product", productImage.value);
    formData.append("ratio", imageDimension.value);
    formData.append("prompt_kh", imageDescription.value);

    try {
        const response = await axios.post(route('ai-business.create-image'), formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });

        if (response.data.success) {
            imageS3Url.value = response.data.s3_url;
        } else {
            toast.error(response.data.message);
        }
        loadingCreateImage.value = false
    } catch (error) {
        loadingCreateImage.value = false
        toast.error("Lỗi khi kết hợp ảnh!");
    }
};

const downloadImage = (image) => {
    var url = route(("ai-video.downloadFile"), {url:image, name:"image.png"})
    window.open(url, '_blank');
};
  defineExpose({ updateImageUrl });
</script>
