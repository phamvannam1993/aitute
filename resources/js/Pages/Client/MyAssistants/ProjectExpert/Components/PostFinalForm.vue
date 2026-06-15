<template>
    <div ref="resultTarget">
        <div :class="mainSectionOpen ? 'flex-col lg:rounded-[35px] items-start' : 'flex-row lg:rounded-full items-center'" class="bg-white shadow-xl md:px-[80px] md:py-[20px] p-3 flex justify-between mt-4">

            <div @click="toggleMainSection" class="cursor-pointer flex justify-between items-center md:max-w-full line-clamp-1 gap-2 mb-2 text-xs lg:text-sm mt-2 text-black">
                <Step
                    class="flex-shrink-0"
                    :step="8"
                    step-name="Đăng bài"
                />
        </div>

            <div v-if="mainSectionOpen" class="w-full lg:w-4/5 self-center">
                <PostForm
                    ref="postFormRef"
                    :template-post-form="isGenAIStatus ? 'AutoPostAIForm' : 'BasicForm'"
                    @before-submit="beforeSubmitPostForm"
                    @on-success="onSuccessPostForm"
                />
            </div>
            <div :class="mainSectionOpen ? 'self-end' : ''" class="text-ai3goc-sec mt-2 font-medium flex items-center cursor-pointer text-xs md:text-sm" @click="toggleMainSection">
                  <span class="hidden md:block">{{ mainSectionOpen ? "Thu gọn" : "Hiển thị" }}</span>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': mainSectionOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
        </div>
    </div>
</template>

<script setup>
    import { ref } from 'vue';
    import Step from '@/Components/Step.vue';
    import PostForm from "@/Components/ShareAiText/PostForm.vue";

    const props = defineProps({
        isGenAIStatus: {
            type: Boolean,
            default: false
        }
    });

    const postFormRef = ref(null);

    const beforeSubmitPostForm = () => {
        // Handle before submit
        console.log('Before submit post form');
    };

    const onSuccessPostForm = () => {
        // Handle success
        console.log('Post form submitted successfully');
    };

    const openPostDetail = (postForm) => {
        if(postFormRef.value) {
            postFormRef.value.openPostDetail(postForm)
        }
    }

    const mainSectionOpen = ref(true);

    const toggleMainSection = () => {
        mainSectionOpen.value = !mainSectionOpen.value;
    };
    defineExpose({ openPostDetail });
</script>
