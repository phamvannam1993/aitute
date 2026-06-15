<template>
   <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-4">
        <div class="relative flex flex-row lg:items-center gap-2 w-full">
            <div class="w-full">
                <SelectRadix :options="targets" selected="" :placeholder="targetValue ? targetValue : 'Mục tiêu'"  :selected="targetValue" :handle-change="(value) => selectOption(value, 'target')"/>
            </div>
            <div class="w-full">
                <SelectRadix :options="emotions" selected="" :placeholder="emotionValue ? emotionValue : 'Cảm xúc'" :handle-change="(value) => selectOption(value, 'emotion')"/>
            </div>
            <div class="w-full">
                <SelectRadix :options="styles" selected="" :placeholder="styleValue ? styleValue : 'Phong Cách'" :handle-change="(value) => selectOption(value, 'style')"/>
            </div>
            <div class="absolute -top-10 md:-top-[56px] right-0 lg:-right-20 flex items-center gap-2 z-index-9999">
                <DropdowRadix :open="isOpen" @update:open="isOpen = $event">
                    <template #trigger>
                        <button type="button" class="flex w-full items-center justify-center px-4 py-2 bg-orion-sec rounded-2xl shadow-sm text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orion-sec relative">
                            <span class="text-xs lg:text-sm text-white font-bold line-clamp-1">Định dạng</span>
                        </button>
                    </template>
                    <template #content>
                        <ul class="flex flex-col lg:mt-0 min-w-[200px] w-full bg-white border border-gray-200 rounded-lg shadow-xl z-50 p-2 px-3 z-index-9999">
                            <li v-for="(optionItem, idx) in postFormats" :key="idx" @click="selectOption(optionItem, 'postFormat')" class="px-4 py-2 cursor-pointer hover:bg-blue-50 hover:text-ai3goc-sec rounded-full" :class="{ 'bg-blue-100 text-ai3goc-sec': postFormat === optionItem }">
                                {{ optionItem }}
                            </li>
                        </ul>
                    </template>
                </DropdowRadix>
            </div>
        </div>
    </div>
    <div class="w-full">
        <div class="flex flex-col gap-4">
            <div class="flex justify-between items-center">
                <p class="text-[#1E405A] text-[15px] font-bold">Nội dung đã tạo:</p>

                <button @click="openGradeModal()" v-if="mode != 'basic'" class="bg-orion-sec text-white py-2.5 text-xs lg:text-sm rounded-lg lg:rounded-2xl px-6 hover:bg-cyan-600 flex items-center gap-2">
                    <img src="/assets/svgs/grade.svg" class="size-5" alt="" />
                    Chấm điểm
                </button>
            </div>
            <textarea rows="10" v-model="results.content" class="text-black whitespace-pre-line text-base border border-gray-400 rounded-xl p-4 max-h-96 overflow-y-auto" >{{convertLineBreaks(results.content)}} </textarea>
        </div>
    </div>

    <Modal :show="showGradeModal" @close="closeGradeModal" v-if="showGradeModal">
        <div class="p-4 md:p-12 max-h-[90vh] overflow-y-scroll">
            <div>
                <div class="flex gap-1">
                    <div class="flex justify-start items-center gap-2">
                        <div class="w-[52px] h-[75%] overflow-hidden rounded-[10px] border-[1px] border-[#d4d4d4]">
                            <img class="w-full h-auto object-top" src="/assets/img/orion/big-robot.png" alt="Robot" />
                        </div>

                        <h2 class="text-black font-bold text-xl lg:text-xl 2xl:text-3xl leading-none">Chấm điểm nội dung</h2>
                    </div>
                </div>
                <!-- <div class="flex justify-start gap-4">
                    <img src="/assets/svgs/robot.svg"/>
                    <h2 class="text-lg font-bold text-[#005F49]">Chấm điểm nội dung</h2>
                </div> -->
                <h2 class="text-sm md:text-base font-bold my-2">Nội dung đã tạo:</h2>
                <textarea v-model="gradeContentInput" class="w-full p-2 border-[1px] rounded-lg" rows="10"></textarea>
            </div>
            <!-- <div class="flex justify-center items-center">
                <button @click="gradeContent" class="bg-orion-sec text-white px-4 py-2 rounded-full my-4 flex justify-center items-center gap-2" :disabled="gradeLoading">
                    <img src="/assets/svgs/grade.svg" class="size-5" alt="">Chấm điểm
                </button>
            </div> -->
            <div class="border-t-2 mt-4">
                <div v-if="gradeLoading">
                    <LoadingCircle loading-title="Đang chấm điểm..." />
                </div>
                <div v-else>
                    <h2 class="text-sm md:text-base font-bold mb-2 mt-4">Kết quả chấm điểm nội dung đã tạo:</h2>
                    <div class="p-2 border-[1px] rounded-lg">
                        <p class="whitespace-pre-line">{{ gradeResult }}</p>
                    </div>
                </div>
            </div>
            <div class="flex justify-center items-center">
                <button @click="improveContent" class="bg-orion-sec text-white px-4 py-2 rounded-full my-4 flex justify-center items-center gap-2" :class="gradeLoading || improveLoading ? 'opacity-35' : ''" :disabled="gradeLoading || improveLoading"><img src="/assets/svgs/grade.svg" class="size-5" alt="" />Cải thiện</button>
            </div>
            <div class="border-t-2" v-if="showImproveResult">
                <div v-if="improveLoading">
                    <LoadingCircle loading-title="Đang cải thiện..." />
                </div>
                <div v-else>
                    <h2 class="text-sm md:text-base font-bold mb-2 mt-4">Kết quả cải thiện nội dung đã tạo:</h2>
                    <div class="p-2 border-[1px] rounded-lg">
                        <p class="whitespace-pre-line">{{ improveResult }}</p>
                    </div>
                    <div v-if="improveResult" class="flex justify-center">
                        <button @click="replaceWithImprovedContent" class="bg-green-600 text-white px-4 py-2 rounded-full my-4 flex justify-center items-center gap-2">Thay thế nội dung</button>
                    </div>
                </div>
            </div>
            <div class="flex justify-center items-center">
                <button @click="closeGradeModal" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-full mt-4">Đóng</button>
            </div>
        </div>
    </Modal>

</template>

<script setup>
    import { ref, watch, onMounted, inject } from 'vue';
    import Step from '@/Components/Step.vue';
    import SelectRadix from "@/Components/SelectRadix.vue";
    import DropdowRadix from "@/Components/DropdowRadix.vue";
    import { eventBus } from "@/lib/eventBus.js";
    const helpers = inject("helpers");

    const convertLineBreaks = (text) => {
      if (typeof(myVariable) == "undefined") {
        return ""
      }
      text = text.replace(/\n\n/g, '<br><br>');
      return text.replace(/\n/g, '<br>');
    }

    const props = defineProps({
        results: {
            type: Object,
            default: ""
        },
        projectId: {
            type: Number,
            default: 0
        },
    });
    const results = ref(props.results)
    const targetValue = ref("")
    const emotionValue = ref("")
    const styleValue = ref("")
    const postFormatValue = ref("")
    const isOpen = ref(false)
    const postFormats = ref([
        "Tiều đề hấp dẫn hơn",
        "Làm lại 1 bài ngắn hơn",
        "Làm lại 1 bài dài hơn"
    ])
    const targets = ref([
        "Tăng Nhận Diện Thương Hiệu",
        "Tăng Nhận Diện Thương Hiệu",
        "Thông Tin Sản Phẩm/Dịch Vụ",
        "Thông Tin Sản Phẩm/Dịch Vụ",
        "Tạo trao đổi về Sản Phẩm / Dịch vụ",
        "Kích Thích Sự Tương Tác (Engagement)",
        "Quảng bá về Tiện ích và Thái độ phục vụ",
        "Tạo Độ Tin Cậy và Chuyên Môn",
        "Phá bỏ Rào cản và tạo động lực mua",
        "Kích thích hành động mua ngay",
        "Kết Nối và Phản Hồi Khách Hàng",
        "Xây Dựng Cộng Đồng",
        "Phân Tích Rào cản Khách Hàng",
        "Tạo Dựng Mối Quan Hệ Với Khách Hàng",
        "Xu Hướng Và Tương Lai",
        "Tăng Cường Hình Ảnh Thương Hiệu"
    ]);

    const styles = ref([
        "Trang Trọng",
        "Hấp Dẫn và Sáng Tạo",
        "Thư Giãn và Thân Thiện",
        "Chuyên Nghiệp",
        "Hài hước và vui vẻ",
        "Thể Thao và Năng Động",
        "Hướng Dẫn và Giảng Dạy",
        "Chất Lượng và Tinh Tế",
        "Ngắn Gọn và Súc Tích",
        "Chia Sẻ Kinh Nghiệm Cá Nhân",
        "Truyện Cười và Giải Trí",
        "Đánh Giá và So Sánh",
        "Cảm Xúc và Sâu Sắc",
        "Tone Nghiêm túc",
        "Tone Lạc quan",
        "Tone Hấp Dẫn và Thú vị",
        "Tone Thư Thái, nhẹ nhàng"
    ]);

    const emotions = ref([
        "Tin tưởng",
        "Hứng khởi",
        "Khẩn cấp",
        "Hạnh phúc",
        "Tự hào",
        "Thấu hiểu",
        "Khao khát",
        "An tâm",
        "Truyền cảm hứng",
        "Tò mò",
        "Yêu thương",
        "Vui vẻ Hài hước",
        "Động lực",
        "Hoài niệm"
    ]);
    const selectOption = async (value, key) => {
        if(key == "target") {
            targetValue.value = value
        }
        if(key == "style") {
            styleValue.value = value
        }
        if(key == "emotion") {
            emotionValue.value = value
        }
        if(key == "postFormat") {
            postFormatValue.value = value
        }
        isOpen.value = false
        await handleDataStream()
    }
    const emit = defineEmits(['submit', 'toggle']);

    const handleSubmit = async () => {
        if (validateForm()) {
            console.log(formData.value.selectedAction)
            var totalPost = 0
            if(formData.value.selectedAction.value == "fanpage_content") {
                totalPost = await getTotalPost()
            }
            formData.value.selectedAction.totalPost = totalPost
            emit('submit', formData.value.selectedAction);
        }
    };

    const gradeResult = ref("");
    const gradeLoading = ref(false);
    const improveResult = ref("");
    const improveLoading = ref(false);
    const gradeContentInput = ref("");
    const showGradeModal = ref(false);
    const showImproveResult = ref(false);
    let currentItem = null;

    const openGradeModal = () => {
        gradeContentInput.value = helpers.convertHtmlToText(results.value.content);
        showGradeModal.value = true;
        gradeContent();
    };

    const gradeContent = async () => {
        try {
            gradeLoading.value = true;
            const response = await axios.post(route("ai-business.grade-content"), { content: gradeContentInput.value });
            gradeResult.value = response.data;
        } catch (e) {
            toast.error("Error grading content");
        } finally {
            gradeLoading.value = false;
        }
    };

    const improveContent = async () => {
        try {
            showImproveResult.value = true;
            improveLoading.value = true;
            const response = await axios.post(route("ai-business.improve-content"), { content: gradeContentInput.value });
            improveResult.value = response.data;
        } catch (e) {
            toast.error("Error improving content");
        } finally {
            improveLoading.value = false;
        }
    };

    const replaceWithImprovedContent = () => {
        if (currentItem) {
            currentItem.content = improveResult.value;
        }
        resetAndCloseModal();
    };

    const resetAndCloseModal = () => {
        gradeResult.value = "";
        improveResult.value = "";
        gradeContentInput.value = "";
        showImproveResult.value = false;
        closeGradeModal();
    };

    const closeGradeModal = () => {
        showGradeModal.value = false;
    };

    const callApiChatStream = async (payload) => {
        // Try primary endpoint
        let response = await fetch(route("ai-business.sendChatStreamingExpert"), {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
            body: JSON.stringify(payload),
        });
        return response
    }

    const handleDataStream = async () => {
        const formData = new FormData();
        const query = {
            "currentStep": "buoc5",
            "mode": "expert",
            "action": "Tạo 1 Status Fanpage quảng cáo Sản phẩm",
            "currentSubStep": "buoc5_2",
            "goal": targetValue.value,
            "emotion": emotionValue.value,
            "style": styleValue.value,
            "post": results.value.content,
            "articleFormat":postFormatValue.value
        }
        const payload = {
            query: JSON.stringify(query),
        };
        const response = await callApiChatStream(payload);
        // Process stream
        const reader = response.body.getReader();
        const decoder = new TextDecoder();
        let buffer = "";
        let messages = "";

        while (true) {
            const { value, done } = await reader.read();
            if (done) break;

            buffer += decoder.decode(value, { stream: true });
            const lines = buffer.split("\n");
            buffer = lines.pop() || "";

            for (const line of lines) {
                if (line.trim().startsWith("data: ")) {
                    try {
                        const jsonStr = line.trim().slice(5);
                        const data = JSON.parse(jsonStr);

                        if (data.event === "message" && data.answer) {
                            messages += data.answer;
                            results.value.content = messages
                        }

                        if (data.event === "workflow_finished") {
                            await new Promise(resolve => setTimeout(resolve, 100));
                        }
                    } catch (e) {
                        console.error("JSON parse error:", e);
                        continue;
                    }
                }
            }
        }

        // Process remaining buffer
        if (buffer.trim()) {
            try {
                const data = JSON.parse(buffer.trim().slice(5));
                if (data.event === "message" && data.answer) {
                    results.value.content = messages
                }
            } catch (e) {
                console.warn("Final buffer parse error:", e);
            }
        }
        eventBus.emit('updateResult', results.value);
        const formDataAPI = new FormData()
        formDataAPI.append("res_content", JSON.stringify(results.value.content))
        emit('updateProject', formDataAPI);
        emit('updateContent', results.value);
    }

</script>

<style>
    .modal {
        max-width: 1200px;
        overflow: auto;
        max-height: 800px;
    }
    [data-radix-popper-content-wrapper] {
        z-index: 9999 !important;
    }
    .box-title {
        background: #fff;
        border-radius: 20px;
        padding: 10px 10px;
        border: 1px solid #e6e6e6;
    }
</style>
