<template>
    <img src="/assets/svgs/footer-head-robo.svg" alt="ai" class="absolute w-[32px] h-[32px] right-[6px] top-[36px]"
         @click.stop="handleOpenAudioContentAI" />
    <Modal :show="showAudioContentAI" :maxWidth=" modalStep === 1 ? 'xl' : '5xl'" custom-class="mx-[20px]" @close="showAudioContentAI = false">
        <div v-if="modalStep === 1" class="bg-white p-5 rounded-[10px] md:rounded-[25px] min-h-[400px] flex flex-col items-center justify-center gap-[20px]">
            <div class="w-full">
                <label class="text-black font-semibold">Nhập nội dung bạn muốn tạo cho âm thanh</label>
                <textarea class="w-full h-[100px] text-black mt-2"
                          rows="30"
                          placeholder="Miêu tả âm thanh"
                          v-model="userAudioPrompt"
                ></textarea>
            </div>
            <button class="text-center right-0 -top-4 py-2 bg-blue-500 text-white font-semibold rounded-[10px] w-[120px]"
                    @click="handleGetAudioSuggest"
            >Tạo</button>

            <div class="w-full">
                <label class="text-black font-semibold">Nội dung gợi ý bởi AI</label>
                <textarea class="w-full h-[200px] text-black mt-2"
                          rows="30"
                          placeholder="Nội dung gợi ý cho âm thanh"
                          v-model="suggestAudioContent"
                ></textarea>
            </div>
            <button
                :disabled="!suggestAudioContent || suggestAudioContent.length > 255"
                class="text-center right-0 -top-4 py-2 text-white font-semibold rounded-[10px] w-[120px]"
                :class="{
                        'bg-blue-500': suggestAudioContent && suggestAudioContent.length <= 255,
                        'bg-gray-500': !suggestAudioContent || suggestAudioContent.length > 255
                    }"
                @click="handleApplyAudioContent"
            >Áp dụng</button>
        </div>
    </Modal>
</template>

<script setup>
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";
import axios from "axios";
import { toast } from "vue3-toastify";

const audioDescription = defineModel({ default: '' });
const isSuggestionLoading = defineModel('isSuggestionLoading', { default: false });
const showAudioContentAI = ref(false);
const modalStep = ref(1);

const userAudioPrompt = ref("");
const suggestAudioContent = ref("");

const handleOpenAudioContentAI = () => {
    showAudioContentAI.value = true;
}

const handleGetAudioSuggest = async () => {
    try {
        isSuggestionLoading.value = true;
        const response = await axios.get(route('mc-virtual.suggest-content-audio', { content: userAudioPrompt.value }));
        suggestAudioContent.value = response.data.data;
    } catch (e) {
        toast.error("Có lỗi xảy ra khi lấy nội dung gợi ý từ AI.");
    } finally {
        isSuggestionLoading.value = false;
    }
}

const handleApplyAudioContent = () => {
    if (suggestAudioContent.value.length > 0) {
        audioDescription.value = suggestAudioContent.value;
    }
    showAudioContentAI.value = false;
}
</script>
