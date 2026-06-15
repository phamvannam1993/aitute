<template>
      <div class="flex-col lg:rounded-[35px] bg-white shadow-xl md:px-[80px] md:py-[20px] p-3 flex items-center justify-between mt-4">
        <div class="flex items-center self-start justify-between w-full gap-2 mb-2 text-xs lg:text-sm mt-2 text-black">
            <label class="text-black font-semibold text-[14px] cursor-pointer my-auto flex-shrink-0">
                <div class="flex items-center gap-1">
                    <div>
                        <Step
                            class="flex-shrink-0"
                            :step="3"
                            step-name="Xem kết quả và hiệu chỉnh"
                        />
                    </div>
                </div>
            </label>
        </div>
        <div class="w-full lg:w-4/5 mt-10 md:mt-5 text-black text-xs lg:text-[15px] relative" v-if="open">
            <ContentSection ref="contentSectionRef" :conversationId="props.conversationId" :results="results"  :projectId="props.projectId"  @updateResult="handleUpdateResult" :updateContent="handleUpdateContent" :selectedProject="selectedProject" @updateProject="updateProjectExpert"/>
        </div>
        <div class="flex justify-center gap-4 mt-4 mb-4" v-if="open">
            <button type="submit" class="h-[40px] md:h-[50px] min-w-[80%] lg:min-w-[360px] text-sm lg:text-base px-4 bg-aiwow-sec text-white rounded-lg lg:rounded-2xl font-bold" :disabled="loading">
                <div role="status" v-if="loading">
                    <svg aria-hidden="true" class="inline w-6 h-6 text-gray-200 animate-spin dark:text-gray-600 fill-gray-600 dark:fill-gray-300" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                    </svg>
                </div>
                <span v-else @click="createMusic"> Tạo nhạc </span>
            </button>
        </div>
    </div>
    <div class="w-full h-full mt-10" v-if="content">
        <CreateMusic @saveGenerationResult="(song) => handleSaveGenerationResultMusic(song)" :content="content" :titleMusic="selectedProject?.title" />
    </div>
</template>

<script setup>
    import { ref, watch, onMounted } from 'vue';
    import Step from '@/Components/Step.vue';
    import ContentSection from '../TypeCampaign/ChildResults/ContentSection.vue'
    import CreateMusic from "./CreateMusic.vue";
    import { toast } from "vue3-toastify";
    const emit = defineEmits(['toggle', 'submit']);
    const isShowVideo = ref(false)
    const content = ref("")
    const props =  defineProps({
        results: {
            type: Object,
            default: {
                "conent":"Tôi là ai"
            }
        },
        selectedProject: {
            type: Object,
            default: {
                "title":""
            }
        },
        open: {
            type: Boolean,
            default: false
        },
        projectId: {
            type: Number,
            default: 0
        },
        conversationId: {
            type: String,
            default: 0
        },
    });

    const results = ref(props.results)
    const contentSectionRef = ref("")
    const selectedProject = ref(props.selectedProject)
    const updateContent = (content, isUpdate = false) => {
        results.value.content = content
        if(contentSectionRef.value) {
            contentSectionRef.value.updateContent(content)
        }
    }
    const createMusic = async () => {
        const response = await axios.get(route('ai-business.get-project-by-id', {projectId:props.projectId}));
        const results = response.data.data.results

        content.value = response.data.data.results.content
        content.value = content.value.replace(/\r?\n/g, '');
    }
    const handleSaveGenerationResultMusic = async (song) => {
        const response = await axios.get(route('ai-business.get-project-by-id', {projectId:props.projectId}));
        const results = response.data.data.results
        selectedProject.value = response.data.data
        let postForm = {
            content: convertLineBreaks(results.content),
            published: 1,
            scheduled_publish_time: null,
            files: [],
            autoPostAi: {
            },
            onlyAutoPostAi: true
        };
        postForm.files.push({
            s3_url: song,
            type: "video",
        });
        console.log("DEBUG", postForm)
        emit('showPostForm', postForm)
    }

    const convertLineBreaks = (text) => {
      if (typeof(text) == "undefined") {
        return text
      }
      text = text.replace(/\n\n/g, '');
      return text.replace(/\n/g, '');
    }

    defineExpose({ updateContent});
</script>
