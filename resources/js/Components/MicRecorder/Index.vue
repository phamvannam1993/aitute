<template>
    <div class="object-mic relative ml-auto">
        <div v-if="isRecording" class="outline-mic right-3 bottom-3 flex items-center justify-center"></div>
        <button
            class="button-mic icon-mic absolute right-3 bottom-3 flex items-center justify-center"
            @click="toggleRecording"
            type="button"
        >
            <img src="/assets/img/ai3goc/icon/mic.svg" alt="mic" class="w-[18px] h-auto" />
        </button>
    </div>
</template>

<script setup>
import { ref, defineEmits } from "vue";
import axios from "axios";

const emit = defineEmits(["update:text", "clear:text"]);

const isRecording = ref(false);
const audioBlob = ref(null);
const audioUrl = ref(null);
const audioChunks = [];
let mediaRecorder = null;
let device = null;

const toggleRecording = async () => {
    if (!isRecording.value) {
        emit("clear:text");
        // Start recording
        try {
            isRecording.value = true;
            device = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(device);

            mediaRecorder.addEventListener("dataavailable", (event) => {
                audioChunks.push(event.data);
            });

            mediaRecorder.addEventListener("stop", async () => {
                // Create audio file and process speech-to-text
                audioBlob.value = new Blob(audioChunks, { type: "audio/mp3" });
                audioUrl.value = URL.createObjectURL(audioBlob.value);
                const formData = new FormData();
                formData.append("audio", new File([audioBlob.value], "audio.mp3", { type: "audio/mp3" }));

                try {
                    const response = await axios.post("/speech-to-text", formData, {
                        headers: { "Content-Type": "multipart/form-data" },
                    });
                    const recognizedText = response?.data?.text || "Không thể nhận diện giọng nói.";
                    emit("update:text", recognizedText);
                } catch (error) {
                    console.error("Error in Speech-to-Text:", error);
                }
                isRecording.value = false;
            });

            mediaRecorder.start();
        } catch (error) {
            console.error("Error starting recording:", error);
            isRecording.value = false;
        }
    } else {
        // Stop recording
        isRecording.value = false;
        mediaRecorder?.stop();
        device?.getTracks().forEach((track) => track.stop());
    }
};
</script>

<style scoped>
.object-mic {
    display: flex;
    flex: 0 1 100%;
    justify-content: center;
    align-items: center;
    align-content: stretch;
}
.outline-mic {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: 8px solid #679cff;
    animation: pulseMic 3s ease-out infinite;
    position: absolute;
}
.button-mic {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    box-shadow: 0px 0px 80px #0084f9;
    position: absolute;
    outline: none;
}
@keyframes pulseMic {
    0% {
        transform: scale(0.5);
        opacity: 1;
    }
    50% {
        transform: scale(1.5);
        opacity: 0.4;
    }
    100% {
        transform: scale(2);
        opacity: 0;
    }
}
</style>