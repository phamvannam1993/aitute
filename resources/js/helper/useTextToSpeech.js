// useMobile.js
import axios from "axios";
import { ref, onMounted, onUnmounted } from "vue";

export function useTextToSpeech() {
    const listVoice = ref([]);
    const fetchAllVoice = async () => {
        const voices = await axios(route("ai-audio.list-voices"));
        return voices;
    };
    const handleTextToSpeech = async ({ text, voice, cloned = false,screen_id,feature_id }) => {
        if (cloned) {
            const result = await axios.post(route("ai-audio.text-to-speech"), {
                voice_id: voice,
                language: 'vi',
                text: text,
                isSaveDb: false,
                screen_id: screen_id,
                feature_id: feature_id,
            })
            if (result.data.success) {
                return result.data.data
            }
        }
        const response = await axios.post(route("ai-audio.send"), {
            text: text,
            voice: voice,
            screen_id: screen_id,
        });
        return response.data.data.s3_url
    }

    onMounted(async () => {
        const voices = await fetchAllVoice();
        console.log("onMounted ~ voices:", voices.data.data)
        if (voices.data.data) {
            listVoice.value = voices.data.data;
        }
    });

    return {
        listVoice,
        handleTextToSpeech
    };
}
