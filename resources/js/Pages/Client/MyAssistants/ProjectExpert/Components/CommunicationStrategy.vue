<template>
    <div id="section_com" class="bg-white shadow-xl md:px-[80px] md:py-[20px] p-3 flex flex-col mt-4 lg:rounded-[35px]">
        <!-- Header -->
        <div :class="open ? 'max-w-full' : 'max-w-[300px]'"
             class="md:max-w-full line-clamp-1 flex items-center gap-2 mb-2 text-xs lg:text-sm mt-2 text-black">
            <Step class="flex-shrink-0" :step="4" stepName="Chiến lược truyền thông" />
        </div>

        <!-- Form Content -->
        <div v-if="open && mainSectionOpen" class="w-full lg:w-4/5 self-center">
            <form @submit.prevent="handleSubmit" class="flex flex-col gap-6">
                <div class="bg-[#F8F8F8] p-4 rounded-lg">
                    <div id="content" class="w-full p-4 border border-gray-200 rounded-lg text-sm text-gray-600 resize-none focus:outline-none focus:border-[#2A5F4C] sun-editor-editable show-content" v-html="marked(formData.content)"></div>
                </div>

                <div class="flex justify-center">
                    <button
                        type="submit"
                        :disabled="isLoading"
                        class="px-12 py-2 bg-[#2A5F4C] text-white rounded-md hover:bg-[#224939] disabled:opacity-50"
                    >
                        Xác nhận
                    </button>
                </div>
            </form>
        </div>

        <div v-if="open" class="self-end text-ai3goc-sec mt-2 font-medium flex items-center cursor-pointer text-xs md:text-sm" @click="toggleMainSection">
                  <span class="hidden md:block">{{ mainSectionOpen ? "Thu gọn" : "Hiển thị" }}</span>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': mainSectionOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>

    </div>
</template>

<script setup>
    import { ref, watch } from 'vue';
    import Step from '@/Components/Step.vue'
    import { marked } from "marked";
    const props = defineProps({
        open: {
            type: Boolean,
            required: true
        },
        loading: {
            type: Boolean,
            default: false
        },
        contents: {
            type: Object,
            default: () => ({
                content: ''
            })
        }
    });
    const isLoading = ref(false)
     const mainSectionOpen = ref(true)
     const toggleMainSection = () => {
        mainSectionOpen.value = !mainSectionOpen.value;
      };
    const emit = defineEmits(['submit', 'toggle']);

    const formData = ref({
        content: ''
    });

    const validateForm = () => {
        if (!formData.value.content?.trim()) {
            // Có thể thêm toast.error ở đây
            return false;
        }
        return true;
    };

    const handleSubmit = () => {
        if (validateForm()) {
            console.log("formData CommunicationStrategy", formData.value);
            const formDataAPI = new FormData()
            formDataAPI.append("id", props.projectId)
            formDataAPI.append("communication_strategy", formData.value.content)
            emit("showSection", 4)
            mainSectionOpen.value = false
        }
    };

    const scrollIntoView = () => {
        const targetSection = document.getElementById('section_com');
        targetSection.scrollIntoView({ behavior: "smooth" });
    }

    const updateDataMedia = (content, isUpdate = false) => {
        isLoading.value = !isUpdate
        formData.value.content = content
        scrollToIndex()
    }

    const scrollToIndex = () => {
        const el = document.getElementById('content')
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'start' })
            el.scrollTop = el.scrollHeight
        }
    }

    defineExpose({ updateDataMedia, scrollIntoView, toggleMainSection });
</script>
