<template>
    <div id="section_action" class="bg-white shadow-xl md:px-[80px] md:py-[20px] p-3 flex flex-col mt-4 lg:rounded-[35px]">
        <!-- Header -->
        <div :class="open ? 'max-w-full' : 'max-w-[300px]'"
             class="md:max-w-full line-clamp-1 flex items-center gap-2 mb-2 text-xs lg:text-sm mt-2 text-black">
            <Step class="flex-shrink-0" :step="5" stepName="Tạo hành động" />
        </div>

        <!-- Form Content -->
        <div class="w-full lg:w-4/5 self-center" v-if="open">
            <form @submit.prevent="handleSubmit" class="flex flex-col gap-6">
                <div class="space-y-2" v-if="mainSectionOpen">
                    <div v-for="(option, index) in actionOptions"
                         :key="index"
                         @click="selectAction(option)"
                         class="relative">
                        <label :for="option.value" v-if="option.currentSubStep"
                               class="flex items-center w-full cursor-pointer">
                            <input
                                type="radio"
                                :id="option.value"
                                :value="option"
                                v-model="formData.selectedAction"
                                :checked="formData.selectedAction.value === option.value ? true : false"
                                class="form-radio rounded-full border-gray-300 text-[#FF9500] focus:ring-[#FF9500] w-5 h-5 cursor-pointer"
                            />
                            <span class="w-full py-3 px-4 ml-3 rounded-lg text-sm"
                                  :class="formData.selectedAction === option.value ? 'bg-[#FFF9E5] text-black' : 'text-gray-600 hover:bg-gray-50'">
                                {{ option.label }}
                            </span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-center mt-4" v-if="mainSectionOpen">
                    <button
                        type="submit"
                        :disabled="loading"
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
    import Step from '@/Components/Step.vue';

    const props = defineProps({
        open: {
            type: Boolean,
            required: true
        },
        loading: {
            type: Boolean,
            default: false
        },
        projectId : {
            type:Number,
            default: 0
        },
    });

    const emit = defineEmits(['submit', 'toggle']);
    const formData = ref({
        selectedAction: props.action || ''
    });
    const mainSectionOpen = ref(true)
    const toggleMainSection = () => {
        mainSectionOpen.value = !mainSectionOpen.value
    }

    const updateAction = (action) => {
        for(var i = 0; i < actionOptions.length;i++) {
            if(action == actionOptions[i].value) {
                formData.value.selectedAction = actionOptions[i]
            }
        }
    }

    const actionOptions = [
        { value: 'fanpage_content', label: 'Tạo 100 nội dung Fanpage tự động (AI Agent)', currentSubStep:'buoc5_1' },
        { value: 'fanpage_ads', label: 'Tạo 1 status Fanpage quảng cáo sản phẩm', currentSubStep:''},
        { value: 'seo_content', label: 'Tạo 1 bài viết quảng cáo chuẩn SEO', currentSubStep:'' },
        { value: 'product_ads', label: 'Tạo nhạc quảng cáo sản phẩm', currentSubStep:'' },
        { value: 'tiktok_strategy', label: 'Tạo chiến lược nội dung Video TikTok', currentSubStep:'' },
        { value: 'market_analysis', label: 'Phân tích thị trường', currentSubStep:'' },
        { value: 'communication_strategy', label: 'Chiến dịch Truyền thông', currentSubStep:'' },
        { value: 'marketing_360', label: 'Tạo Chiến dịch Marketing 360', currentSubStep:'' },
        { value: 'social_media', label: 'Tạo Chiến dịch Truyền thông Mạng xã hội', currentSubStep:'' },
        { value: 'product_review', label: 'Tạo Kịch bản Review Sản phẩm', currentSubStep:'' }
    ];

    const selectAction = (value) => {
        formData.value.selectedAction = value;
    };

    const validateForm = () => {
        if (!formData.value.selectedAction) {
            return false;
        }
        return true;
    };

    const handleSubmit = async () => {
        if (validateForm()) {
            var totalPost = 0
            if(formData.value.selectedAction.value == "fanpage_content") {
                totalPost = await getTotalPost()
                if(totalPost < 10) {
                    emit('showFBContent', false)
                }
            }
            formData.value.selectedAction.totalPost = totalPost

            const formDataProject = new FormData()
            formDataProject.append("id", props.projectId)
            formDataProject.append("action", formData.value.selectedAction.value)
            emit("updateProject", formDataProject)
            if(formData.value.selectedAction.value == "fanpage_content") {
                emit('submit', formData.value.selectedAction);
                emit("showSection", 5)
            }
            if(formData.value.selectedAction.value == "fanpage_ads") {
                emit("showSection", 5, false)
                emit("showSection", 7)
            }
        }
    };

    const scrollIntoView = () => {
        const targetSection = document.getElementById('section_action');
        targetSection.scrollIntoView({ behavior: "smooth" });
    }

    const getTotalPost = async () => {
        const response = await axios.get(route("social.facebook.total-content", {project_id: props.projectId}));
        return response.data.total
    }
  defineExpose({ scrollIntoView, toggleMainSection, updateAction });
</script>

<style scoped>
    .form-radio {
        @apply rounded-full;
        @apply border-gray-300;
        @apply text-[#FF9500];
        @apply focus:ring-[#FF9500];
    }

    .form-radio:checked {
        @apply bg-[#FF9500];
        @apply border-[#FF9500];
    }
</style>
