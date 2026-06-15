<template>
      <div class="flex-col lg:rounded-[35px] bg-white shadow-xl md:px-[80px] md:py-[20px] p-3 flex items-center justify-between mt-4">
        <div class="flex items-center self-start justify-between w-full gap-2 mb-2 text-xs lg:text-sm mt-2 text-black">
            <label class="text-black font-semibold text-[14px] cursor-pointer my-auto flex-shrink-0">
                <div class="flex items-center gap-1">
                    <div>
                        <Step
                            class="flex-shrink-0"
                            :step="7"
                            step-name="Xem kết quả và hiệu chỉnh"
                        />
                    </div>
                </div>
            </label>
        </div>
        <div class="w-full lg:w-4/5 mt-10 md:mt-5 text-black text-xs lg:text-[15px] relative">
            <ContentSection ref="contentSectionRef" :results="results"  @updateResult="handleUpdateResult" :updateContent="handleUpdateContent" @updateProject="updateProjectExpert"/>
            <ImageSection :results="results" @updateResult="handleUpdateResult" @updateProject="updateProjectExpert"/>
            <VideoSection :dataVideo="results" @updateResult="handleUpdateResult" ref="childRef" @showFormVideo="handleShowFormVideo" :results="results" @updateProject="updateProjectExpert"/>
        </div>
        <div class="w-full lg:w-4/5 mt-10 md:mt-5 text-black text-xs lg:text-[15px] relative" v-if="isShowVideo" >
            <CreateForm :topic="results.content" @saveGenerationResult="handleSaveGenerationResult"/>
        </div>
        <div class="flex justify-center gap-4 mt-4 mb-4">
            <button type="submit" class="h-[40px] md:h-[50px] min-w-[80%] lg:min-w-[360px] text-sm lg:text-base px-4 bg-orion-primary text-white rounded-lg lg:rounded-2xl font-bold" :disabled="loading">
                <div role="status" v-if="loading">
                    <svg aria-hidden="true" class="inline w-6 h-6 text-gray-200 animate-spin dark:text-gray-600 fill-gray-600 dark:fill-gray-300" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                    </svg>
                </div>
                <span v-else @click="showPostForm"> Xác nhận </span>
            </button>
        </div>
    </div>
</template>

<script setup>
    import { ref, watch, onMounted } from 'vue';
    import Step from '@/Components/Step.vue';
    import ContentSection from './childResults/ContentSection.vue'
    import ImageSection from './childResults/ImageSection.vue'
    import VideoSection from './childResults/VideoSection.vue'
    import PostFormSection from './childResults/PostFormSection.vue'
    import { toast } from "vue3-toastify";
    import CreateForm from "@/Pages/Client/TextToVideo/CreateForm.vue";
    const emit = defineEmits(['toggle', 'submit']);
    const isShowVideo = ref(false)
    const props =  defineProps({
        results: {
            type: Object,
            default: {
                "conent":""
            }
        },
        projectId: {
            type: Number,
            default: 0
        },
    });

    const videoRef = ref("")
    const childRef = ref("")
    const contentSectionRef = ref("")
    const results = ref(props.results);
    const handleSaveGenerationResult = (s3_url) => {
        videoRef.value = s3_url
        isShowVideo.value = false
        if(childRef.value) {
            childRef.value.handleSaveGenerationResultVideo(s3_url)
        }
    }
    const updateProjectExpert = (formData) => {
        formData.append("id", props.projectId)
        emit("updateProject", formData)
    } 
    const handleUpdateContent = async (content) => { 
        results.value.content = content
    }

    const handleShowFormVideo = async (isShow = true) => { 
        isShowVideo.value = isShow
    }

    const handlePostFacebook = async (id) => {
        try {
            const formData = new FormData()
            formData.append("id", id)
            const response = await axios.post(
                route("social.facebook.post-facebook"),
                formData,
                {
                    headers: { "Content-Type": "multipart/form-data" }
                }
            );
            if(response.data.success) {
                toast.success("Đăng bài thành công")
            } else {
                toast.error(response.data.message)
            }
        } catch(error) {

        }
    }

    const showPostForm = async () => {
        const response = await axios.get(route('ai-business.project-expert-detail', {id:props.projectId}));
        const results = response.data.project.results
        let postForm = {
            content: results.content,
            published: 1,
            scheduled_publish_time: null,
            files: [],
            autoPostAi: {
            },
            onlyAutoPostAi: true
        };
        if(results.is_video) {
            postForm.files.push({
                s3_url: results.video_url,
                type: "video",
            });
        } else {
            results?.images?.forEach((image) => {
                if(image.imageRef && image.is_post) {
                    postForm.files.push({
                        s3_url: image.imageRef.s3_url,
                        type: "image",
                    });
                }
            });
        }
        emit('showPostForm', postForm)
    }

    const handleUpdateFacebookContent = async (formData, status = 0) => {
        try {
            const response = await axios.post(
                route("social.facebook.update-facebook-content"),
                formData,
                {
                    headers: { "Content-Type": "multipart/form-data" }
                }
            );
            if(status == 1) {
                toast.success("Lưu thành công");
            } else if(status == 2) {
                toast.success("Không duyệt thành công");
            } else if(status == 3){
                toast.success("Duyệt thành công");
            }
        } catch(error) { 
        }
    }
</script>

