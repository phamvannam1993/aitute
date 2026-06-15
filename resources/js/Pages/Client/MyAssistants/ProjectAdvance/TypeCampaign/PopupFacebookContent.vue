<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white shadow-lg cursor-pointer relative h-[90%] rounded-lg w-10/12" :class="!isSuccess && !isLoading ? '' : 'hidden'">
            <svg @click="closePopup" class="icon-close absolute top-5 right-5" width="35" height="35" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="29" height="29" rx="14.5" fill="#F49A23"/>
            <path d="M19.7294 9.27002L9.26953 19.7299" stroke="white" stroke-width="3" stroke-linecap="square" stroke-linejoin="round"/>
            <path d="M9.26953 9.27002L19.7294 19.7299" stroke="white" stroke-width="3" stroke-linecap="square" stroke-linejoin="round"/>
            </svg>

<!--            <img src="/assets/img/aiwow/icon_close_yellow.jpg" class="md:w-[50px] w-[30px] icon-close absolute top-5 right-5" @click="closePopup" />-->
            <div class="w-full h-full overflow-auto py-6 px-4 md:p-8">
                <div class="flex-col lg:rounded-[35px] bg-white shadow-xl md:px-[80px] md:py-[20px] p-3 flex items-center justify-between mt-4">
                    <div class="flex items-center self-start justify-between w-full gap-2 mb-2 text-xs lg:text-sm mt-2 text-black">
                        <label class="text-black font-semibold text-[14px] cursor-pointer my-auto flex-shrink-0">
                            <div class="flex items-center gap-1">
                                <div>
                                    <div class="flex">
                                        <span class="font-bold text-sm lg:text-base">Xem kết quả và hiệu chỉnh</span
                                        ><!--v-if-->
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="w-full lg:w-4/5 mt-2 md:mt-5 text-black text-xs lg:text-[15px] relative">
                        <ContentSection :project-type="projectType" ref="contentSectionRef" :facebookContentDetail="facebookContentDetail" @updateFacebookContent="handleUpdateFacebookContent" :updateContent="handleUpdateContent" />
                        <ImageSection ref="childImageRef" :facebookContentDetail="facebookContentDetail" @updateFacebookContent="handleUpdateFacebookContent" @toggleGenerateImage="toggleGenerateImage"  :selectedProject="selectedProject"/>
                        <VideoSection :dataVideo="facebookContentDetail" @updateFacebookContent="handleUpdateFacebookContent" ref="childRef" @toggleGenerateImage="toggleGenerateImage" :facebookContentDetail="facebookContentDetail" />
                    </div>
                </div>
                <div class="w-full mt-10 md:mt-5 text-black text-xs lg:text-[15px] relative" v-if="isShowVideo" id="section_video">
                    <CreateForm :topic="facebookContentDetail.content" @saveGenerationResult="(value) => handleSaveGenerationResult(value, 'video')" />
                </div>
                <div ref="myAiRef" v-if="myAi" class="mt-4" id="section_myAi">
                    <MyAIComponent @saveGenerationResult="(value) => handleSaveGenerationResult(value, 'image')" :listCollection="listCollection" :collectionName="collectionSelected?.title" :collection-selected="collectionSelected" :my_ai_image_models="my_ai_image_models" />
                </div>

                <div ref="textToImageRef" v-if="textToImage" class="mt-4" id="section_textToImage">
                    <TextToImage @saveGenerationResult="(value) => handleSaveGenerationResult(value, 'image')" :listCollection="listCollection" :collectionName="collectionSelected?.title" :collection-selected="collectionSelected" :my_ai_image_models="my_ai_image_models" :image-description="facebookContentDetail.content" :isMemo="true" />
                </div>

                <div ref="backgroundRef" v-if="background" class="mt-4" id="section_background">
                    <AIBackgroundComponent @saveGenerationResult="(value) => handleSaveGenerationResult(value, 'image')" :listCollection="listCollection" :collectionName="collectionSelected?.title" :collection-selected="collectionSelected" :my_ai_image_models="my_ai_image_models" />
                </div>

                <div class="mt-4">
                    <PostFormSection :facebookContentDetail="facebookContentDetail" @updateFacebookContent="handleUpdateFacebookContent" @postFacebook="handlePostFacebook" @showPopupSuccess="showPopupSuccess" />
                </div>
            </div>
        </div>

        <div v-if="isSuccess && !isLoading" class="flex-col lg:rounded-[35px] bg-white shadow-xl md:px-[80px] md:py-[20px] p-3 flex items-center justify-between mt-4">
            <div class="text-center space-y-4">
                <div class="flex justify-center items-center gap-2">
                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" v-if="statusSuccess == 2">
                    <circle cx="7.43874" cy="7.4407" r="6.33779" fill="#24AA69"/>
                    <circle cx="7.43874" cy="7.4407" r="6.33779" stroke="#FFA411" stroke-width="2.2019"/>
                    <circle cx="7.43874" cy="7.4407" r="6.33779" stroke="#D4D4D4" stroke-width="2.2019"/>
                    <circle cx="7.43874" cy="7.43874" r="6.33779" fill="#24AA69"/>
                    <circle cx="7.43874" cy="7.43874" r="6.33779" stroke="#FFA411" stroke-width="2.2019"/>
                    <circle cx="7.43874" cy="7.43874" r="6.33779" stroke="#FFA411" stroke-width="2.2019"/>
                    <circle cx="7.43892" cy="7.44087" r="4.60494" fill="#FFA411"/>
                    <circle cx="7.43874" cy="7.43874" r="7.43874" fill="#24AA69"/>
                    <path d="M4.25 6.94265L7.03953 9.91815L10.6261 4.95898" stroke="white" stroke-width="3.30285" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" v-else>
                    <circle cx="7.43874" cy="7.43874" r="7.43874" fill="#FF6565"/>
                    <path d="M8.56533 4.579C9.05348 4.09086 9.84476 4.09085 10.3329 4.579C10.821 5.06715 10.821 5.85843 10.3329 6.34658L9.21669 7.46279L10.3329 8.579C10.821 9.06713 10.821 9.85842 10.3329 10.3466C9.84475 10.8347 9.05348 10.8347 8.56533 10.3466L7.44912 9.23037L6.33291 10.3466C5.84475 10.8347 5.05348 10.8347 4.56533 10.3466C4.07718 9.85843 4.07719 9.06716 4.56533 8.579L5.68154 7.46279L4.56533 6.34658C4.07728 5.85841 4.07721 5.06712 4.56533 4.579C5.05345 4.09096 5.84477 4.09098 6.33291 4.579L7.44912 5.69521L8.56533 4.579Z" fill="white"/>
                    </svg>
                    <span class="font-bold text-base text-black" :class="statusSuccess == 2 ? 'text-red-500' : 'text-black'">{{ textSuccess }}</span>
                </div>

                <div class="flex justify-center gap-4 pt-4">
                    <button @click="goBack" class="px-4 py-2 bg-[#9E9E9E] text-white rounded-2xl text-nowrap w-1/2 md:w-2/3">Quay lại</button>
                    <button @click="goNext" class="px-4 py-2 bg-[#207A91] text-white rounded-2xl text-nowrap w-1/2 md:w-2/3">Bài tiếp theo</button>
                </div>
            </div>
        </div>
        <div v-if="isLoading" class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
            <div class="loader"></div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, nextTick } from "vue";
import Step from "@/Components/Step.vue";
import Modal from "@/Components/Modal.vue";
import ContentSection from "./ChildComponers/ContentSection.vue";
import ImageSection from "./ChildComponers/ImageSection.vue";
import VideoSection from "./ChildComponers/VideoSection.vue";
import PostFormSection from "./ChildComponers/PostFormSection.vue";
import { toast } from "vue3-toastify";
import { eventBus } from "@/lib/eventBus.js";
import AIBackgroundComponent from "@/Pages/Client/AIBackground/Component.vue";
import TextToImage from "@/Pages/Client/AIImage/CreateImage.vue";
import MyAIComponent from "@/Pages/Client/AIImage/MyAIComponent.vue";
import CreateForm from "@/Pages/Client/TextToVideo/CreateForm.vue";
const emit = defineEmits(["toggle", "submit"]);
const isShowVideo = ref(false);
const textSuccess = ref("");
const statusSuccess = ref(0);
const props = defineProps({
    facebookContentDetail: {
        type: Object,
        default: {
            content: "",
        },
    },
    selectedProject:{
        type: Object,
        default: {
            title:"",
            url:""
        }
    },
    projectType: {
        type: String,
        default: ''
    }
});
const selectedProject = ref(props.selectedProject)
watch(() => props.selectedProject, newValue => {
    selectedProject.value = newValue
}, {deep: true})
const facebookContentDetail = ref(props.facebookContentDetail)

const isLoading = ref(false);
const videoRef = ref("");
const childRef = ref("");
const childImageRef = ref("");
const contentSectionRef = ref(null);
const isSuccess = ref(false);
const closePopup = () => {
    isShowVideo.value = false;
    emit("showSection", 6, false);
};
const goBack = () => {
    isSuccess.value = false;
};

const goNext = async () => {
    var id = facebookContentDetail.value.id;
    var data = await getNextID(id);
    var index = data.is_next ? facebookContentDetail.value.index + 1 : 1;
    isSuccess.value = false;
    isShowVideo.value = false;
    emit("showSection", 6, false);
    eventBus.emit("showDetail", { id: data.id, index: index });
};

const getNextID = async (id) => {
    const response = await axios.get(route("social.facebook.facebook-content-detail", { id: id, is_next: true }));
    return response.data.data;
};

const handleSaveGenerationResult = (data, type = "video") => {
    if (type == "video") {
        videoRef.value = data;
        isShowVideo.value = false;
        if (childRef.value) {
            childRef.value.handleSaveGenerationResultVideo(data);
        }
    } else {
        if (childImageRef.value) {
            if (data.data) {
                childImageRef.value.saveGenerationResult(data.data);
            }
        }
    }
};

const handleUpdateContent = async (content) => {
    facebookContentDetail.value.content = content;
};

const handleShowFormVideo = async (isShow = true) => {
    isShowVideo.value = isShow;
};
const myAi = ref(false);
const textToImage = ref(false);
const background = ref(false);
const isGenerateImage = ref(false);
const toggleGenerateImage = async (type) => {
    if (type === "myAi") {
        myAi.value = true;
        background.value = false;
        textToImage.value = false;
        isGenerateImage.value = false;
        isShowVideo.value = false;
        await nextTick();
    } else if (type === "background") {
        myAi.value = false;
        background.value = true;
        textToImage.value = false;
        isGenerateImage.value = false;
        isShowVideo.value = false;
        await nextTick();
    } else if (type === "textToImage") {
        myAi.value = false;
        background.value = false;
        textToImage.value = true;
        isGenerateImage.value = false;
        isShowVideo.value = false;
        await nextTick();
    } else if (type == "video") {
        myAi.value = false;
        background.value = false;
        textToImage.value = true;
        isGenerateImage.value = false;
        isShowVideo.value = true;
        await nextTick();
    }
    const targetSection = document.getElementById("section_" + type);
    targetSection.scrollIntoView({ behavior: "smooth" });
};

const handlePostFacebook = async (id) => {
    isLoading.value = true;
    try {
        const formData = new FormData();
        formData.append("id", id);
        const response = await axios.post(route("social.facebook.post-facebook"), formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });
        isLoading.value = false;
        if (response.data.success) {
            statusSuccess.value = 3;
            isSuccess.value = true;
            textSuccess.value = "Đăng thành công";
            toast.success(response.data.message);
        } else {
            toast.error(response.data.message);
        }
    } catch (error) {
        isLoading.value = false;
    }
};

const handleUpdateFacebookContent = async (formData, status = 0, published = false, id = 0) => {
    try {
        const response = await axios.post(route("social.facebook.update-facebook-content"), formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });
        statusSuccess.value = status;
        if (status == 1) {
            toast.success("Lưu thành công");
        } else if (status == 2) {
            isSuccess.value = true;
            textSuccess.value = "Bài viết không được duyệt";
            toast.success("Không duyệt thành công");
        } else if (status == 3) {
            isSuccess.value = true;
            textSuccess.value = "Duyệt bài thành công";
            toast.success("Duyệt thành công");
            if(published) {
                handlePostFacebook(id);
            }
        }
        const targetSection = document.getElementById('section_'+type);
        targetSection.scrollIntoView({ behavior: "smooth" });
    }  catch(error) {
        isLoading.value = false
    }
}

onMounted(() => {
    document.body.className += ' no-scroll';
});
</script>
<style>
.modal {
    width: 90%;
}
.icon-close {
    position: absolute;
    top: 4%;
    z-index: 99999;
    right: 8%;
}
</style>
