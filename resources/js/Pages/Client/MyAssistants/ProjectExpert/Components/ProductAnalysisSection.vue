<template>
    <div id="section_pro" :class="open ? 'flex-col lg:rounded-[35px] items-start' : 'flex-row lg:rounded-full items-center'" class="bg-white shadow-xl md:px-[80px] md:py-[20px] p-3 flex justify-between mt-4">
        <!-- Header -->

        <div @click="toggleMainSection" class="cursor-pointer flex justify-between items-center md:max-w-full line-clamp-1 gap-2 mb-2 text-xs lg:text-sm mt-2 text-black">
            <Step class="flex-shrink-0" :step="3" stepName="Phân tích sản phẩm" />
        </div>

        <!-- Form Content -->
        <div class="w-full lg:w-4/5 self-center" v-if="open && mainSectionOpen">
            <div class="flex flex-col gap-6">
                <div class="space-y-4">
                    <div v-for="(section, index) in analysisSections" :key="index"
                         class="bg-[#F8F8F8] p-4 rounded-lg flex items-center justify-between">
                        <div class="flex items-start gap-3 cursor-pointer" @click="handleSectionClick(section, index)">
                            <div class="flex-shrink-0">
                                <img src="/assets/img/orion-1.png" alt="icon" class="w-5 h-5"/>
                            </div>
                            <div class="flex-grow">
                                <h3 class="text-sm font-medium text-gray-900 flex items-center mb-0">
                                    {{ index + 1 }}. {{ section.title }}
                                </h3>
                                <div v-if="section.open">
                                  <div
                                      v-if="section.content" :id="'content-' + index"
                                      class="text-sm text-gray-600 whitespace-pre-line bg-white p-4 rounded-lg mt-2 sun-editor-editable show-content"
                                  >
                                      <div v-html="marked(section.content)"></div>
                                  </div>
                                  <div
                                      v-else-if="results && results[section.key]"
                                      class="text-sm text-gray-600 whitespace-pre-line bg-white p-4 rounded-lg mt-2"
                                  >
                                      {{ results[section.key] }}
                                  </div>
                                  <div
                                      v-else
                                      class="mt-2 text-sm text-gray-400 italic bg-white p-4 rounded-lg"
                                  >
                                      Đang phân tích...
                                  </div>
                                </div>
                                <div class="text-ai3goc-sec self-end font-medium flex items-center cursor-pointer text-xs md:text-sm" v-if="section.content && section.open && !section.isLoading">
                                    <span class="hidden md:block" @click="toggleShowSection(index)">Thu gọn</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': section.open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                                <div v-if="section.isLoading || section.loadingPercent > 0 && section.loadingPercent < 99"  class="text-ai3goc-sec  font-medium flex items-center cursor-pointer text-xs md:text-sm justify-center mt-2">
                                    <button class="text-sm md:text-base relative flex gap-1 items-center justify-center h-[40px] md:h-[50px] place-items-center w-full max-w-[180px] px-4 bg-ai3goc-primary text-white rounded-2xl font-bold">
                                        <LoadingCircle  loading-title="" />
                                        <div class="text-xs md:text-sm font-medium text-center p-0.5 leading-none rounded-full transition-all duration-300">{{ section.loadingPercent }} %</div>
                                    </button>
                                </div>

                            </div>

                        </div>
                        <div class="text-ai3goc-sec font-medium flex items-center cursor-pointer text-xs md:text-sm" v-if="section.content && !section.open">
                          <span class="hidden md:block" @click="toggleShowSection(index)">{{ section.open ? "Thu gọn" : "Hiển thị" }}</span>
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': section.open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                          </svg>
                        </div>

                    </div>

                    <div class="flex justify-center mt-6">
                        <button
                            @click="handleSubmit"
                            :disabled="index != -1"
                            class="px-12 py-2 bg-[#2A5F4C] text-white rounded-md hover:bg-[#224939] disabled:opacity-50"
                        >
                            <span v-if="!loading && (loadingPercent > 98 || loadingPercent == 0)">Xác nhận</span>
                            <div v-else class="text-xs md:text-sm font-medium text-center p-0.5 leading-none rounded-full transition-all duration-300">{{ loadingPercent }} %</div>
                        </button>
                    </div>
                </div>
            </div>
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
    import { ref } from 'vue';
    import Step from '@/Components/Step.vue'
    import { marked } from "marked";
    import LoadingCircle from "@/Components/LoadingCircle.vue";
    const props = defineProps({
        open: {
            type: Boolean,
            default: false
        },
        loading: {
            type: Boolean,
            default: false
        },
        projectId: {
            type: Number,
            default: 0
        },
        results: {
            type: Object,
            default: () => ({})
        }
    });
    const loading = ref(false)
    const loadingPercent = ref(0)
    const loadingPercentTab = ref(0)
    const emit = defineEmits(['submit']);

    const scrollIntoView = () => {
        const targetSection = document.getElementById('section_pro');
        targetSection.scrollIntoView({ behavior: "smooth" });
    }

    const analysisSections = ref([
        {
            id:1,
            title: 'Chân dung khách hàng (Customer Persona)',
            key: 'customerPersona',
            type: 'customer_persona',
            content: '',
            open:true
        },
        {
            id:2,
            title: 'Phân tích thị trường, đối thủ cạnh tranh',
            key: 'marketAnalysis',
            type: 'market_analysis',
            content: ''
        },
        {
            id:3,
            title: 'Phân tích SWOT',
            key: 'swotAnalysis',
            type: 'swot_analysis',
            content: ''
        },
        {
            id:4,
            title: 'Tâm lý khách hàng',
            key: 'customerPsychology',
            type: 'customer_psychology',
            content: ''
        },
        {
            id:5,
            title: 'Xu hướng thị trường (Trend Analysis)',
            key: 'trendAnalysis',
            type: 'trend_analysis',
            content: ''
        },
        {
            id:6,
            title: 'Giá trị cảm xúc (Emotional Selling Proposition - ESP)',
            key: 'emotionalValue',
            type: 'emotional_value',
            content: ''
        }
    ]);
    var index = -1
    const updateDataSection = async(section, content, isUpdate = false) => {
        for(var i = 0; i < analysisSections.value.length; i++) {
            if(analysisSections.value[i].id == section.id) {
                if(!isUpdate) {
                    analysisSections.value[i].content = content
                    analysisSections.value[i].isLoading = true
                    scrollToIndex(i)
                    index = i
                }
            }
        }
        if(isUpdate) {
            const formData = new FormData()
            analysisSections.value[index].isLoading = false
            formData.append("id", props.projectId)
            formData.append("analysis_sections", JSON.stringify(analysisSections.value))
            emit("updateProject", formData)
            loading.value = false
            index = -1
        }
    }

    const updateAnalysisSections = (data) => {
        analysisSections.value = data
    }

    const toggleShowSection = (index) => {
      analysisSections.value[index].open = !analysisSections.value[index].open
    }

    setInterval(async () => {
        if(loading.value) {
            if(loadingPercent.value < 99) {
                loadingPercent.value =  loadingPercent.value + 1
            }
        } else if(loadingPercent.value < 99) {
            for(var idx = 0; idx < 10; idx ++) {
                loadingPercent.value =  loadingPercent.value + 1
            }
        }
    }, 500)

    setInterval(async () => {
         if(index > 0) {
            if(analysisSections.value[index].loadingPercent < 99) {
                analysisSections.value[index].loadingPercent = analysisSections.value[index].loadingPercent + 1
            }
        } else {
           for(var i = 0; i < analysisSections.value.length; i++) {
                if(analysisSections.value[i].loadingPercent > 0) {
                    for(var idx = 0; idx < 10; idx ++) {
                        analysisSections.value[i].loadingPercent = analysisSections.value[i].loadingPercent + 1
                    }
                }
           }
        }
    }, 200)

    const updateSuccess = () => {
        loading.value = false
    };
    const mainSectionOpen = ref(true)
    const toggleMainSection = () => {
      mainSectionOpen.value = !mainSectionOpen.value;
    };

    const handleSectionClick = (section, idx) => {
        if(!section.content && index == -1) {
            analysisSections.value[idx].open = true
            analysisSections.value[idx].loadingPercent = 0
            emit("submit", section)
        } else {

        }
    };

    const scrollToIndex = (index) => {
        const el = document.getElementById('content-'+index)
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'start' })
            el.scrollTop = el.scrollHeight
        }
    }

    // Nút xác nhận để submit tất cả (nếu cần)
    const handleSubmit = () => {
        if(loading.value) {
            return
        }
        emit("showSection", 3)
        emit('submitAnalysisSection');
        loading.value = true
        loadingPercent.value = 0
    };
    defineExpose({ updateDataSection, updateSuccess, updateAnalysisSections, scrollIntoView, toggleMainSection });
</script>

<style scoped>
    .whitespace-pre-line {
        white-space: pre-line;
    }
</style>
