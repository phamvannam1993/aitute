<template>

    <Head title="Video từ link sản phẩm - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData">
        <div class="flex flex-col items-center w-full flex-1 relative h-fit">
            <div ref="resultBox"
                class="w-full bg-white rounded-[10px] md:rounded-[25px] p-[12px] md:p-[25px] drop-shadow-xl w-11/12 md:w-3/4 mx-auto">
                <div class="flex justify-start items-center gap-4">
                    <img class="p-2 rounded-2xl" src="/assets/img/ai3goc/logo/circle.svg" />
                    <div>
                        <h2 class="text-black font-bold text-[20px] md:text-[25px] line-clamp-1">Video từ link sản phẩm</h2>
                    </div>
                </div>
                <p class="mt-[20px] text-sm lg:text-base text-[#1E405A] font-bold">Thực hiện theo các bước sau:</p>
                <div class=" bg-white rounded-lg  relative flex flex-col gap-2 mt-3 text-[10px] md:text-sm">
                    <Step :step="1" step-name="Nhập link hoặc nội dung tạo video" />
                    <div class='flex gap-1 items-center cursor-pointer' @click="onOffLink(true)" >
                        <input type="radio" @click="onOffLink(true)" :checked="enableLink ? true : false"
                            class="ml-0 rounded-full text-orion-orange focus:ring-orion-orange cursor-pointer" />
                       <span class="font-semibold text-sm md:text-base text-black">Nhập link</span>
                    </div>
                    <div class="flex flex-col gap-1 text-black">
                        <textarea rows="1" class="w-full rounded-lg resize-none border-[#D4D4D4]" type="url" v-model="urlValue" @input="validateUrl"
                            placeholder="Nhập link hợp lệ (bắt đầu bằng http:// hoặc https://)" />
                        <p v-if="enableLink && isUrlInvalid" class="text-red-500 text-sm">Định dạng link không hợp lệ</p>
                    </div>
                    <p v-if="props.message_alert_pictory" class="text-red-500 md:text-base text-sm">{{
                        props.message_alert_pictory }}</p>
                    <p class="text-[#1E405A] italic text-sm md:text-base">Chuyển đổi URL của bài viết hoặc blog của bạn thành video hấp dẫn.
                                                             Nhập hoặc dán URL hợp lệ và AITUTE sẽ thực hiện phần còn lại.</p>
                    <div class='flex gap-1 items-center cursor-pointer' @click="onOffLink(false)" >
                        <input type="radio" @click="onOffLink(false)" :checked="enableLink ? false : true"
                            class="ml-0 rounded-full text-orion-orange focus:ring-orion-orange cursor-pointer" />
                       <span class="font-semibold text-sm md:text-base text-black">Nhập nội dung video</span>
                    </div>
                    <div class="flex flex-col gap-1 text-black relative">
                        <SuggestionPrompt v-model="video_description" :type="'image'" :screenId="2" v-if="!enableLink"/>
                        <textarea id="image-description" v-model="video_description" type="text" placeholder="Nhập nội dung bạn muốn tạo video..." :disabled="loadingCreateImage" class="w-full p-3 mt-1 h-[200px] border text-black border-[#D4D4D4] rounded-[10px] shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder:font-light"></textarea>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center md:justify-between justify-center gap-2">
                        <Step :step="2" step-name="Bấm vào đây"/>
                        <button :disabled="isLoading || (enableLink && !urlValue) || (enableLink && urlValue && isUrlInvalid) || (!enableLink && !video_description)" @click="submitCreateVideoByUrl()"
                            class="sm:w-1/4 md:w-1/4 rounded-lg bg-[#1E405A] text-white py-2 text-sm md:text-base disabled:opacity-65">
                            <span v-if="!isLoading">Gửi yêu cầu</span>
                            <div v-else
                                class="inline-block animate-spin rounded-full h-4 w-4 border-t-2 border-b-2 border-blue-500">
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed, nextTick, onMounted, ref, watch } from "vue";
import { toast } from "vue3-toastify";
import Layout from "@/Layouts/Client/Layout.vue";
import Step from "@/Components/Step.vue";
import SuggestionPrompt from "@/Components/AIVirtual/SuggestionPrompt.vue";

const props = defineProps({
    ai_assistant: Object,
    message_alert_pictory: String,
    video_description: String,
});
console.log(props.message_alert_pictory)
const breadcrumbsData = [
    { text: "Tạo video bằng link", link: "ai-video.url-to-video.create" },
];

const { props: pageProps } = usePage();
const auth = computed(() => pageProps.value.auth);
const additionalCredit = ref(0);

onMounted(() => {
    if (props.video_description) {
        video_description.value = props.video_description
    }
})
watch(() => props.video_description, (value) => {
    video_description.value = value
})
const credits = ref(0);
const enableLink = ref(true)
const onOffLink = (value) => {
    if(!value) {
        urlValue.value = ""
    } else {
        video_description.value = ""
    }
    enableLink.value = value
}
const urlValue = ref("");
const isUrlInvalid = ref(false);
const isLoading = ref(false);
const video_description = ref("")
const validateUrl = () => {
    const url = urlValue.value;
    const regex = /^(https?:\/\/)?([a-z0-9]+\.)+[a-z]{2,6}(\/[^\s]*)?$/i;
    if (regex.test(url)) {
        isUrlInvalid.value = false;
    } else {
        isUrlInvalid.value = true;
    }
};

const submitCreateVideoByUrl = async () => {
    isLoading.value = true;
    const formData = new FormData();
    formData.append("article_url", urlValue.value);
    formData.append("video_description", video_description.value);
    const response = await axios.post(route("ai-video.url-to-video.create-video-pictory"), formData, {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    });
    if(response.data.id) {
         window.location.href = route("ai-video.url-to-video.index", {
            id: response.data.id,
        });
    }
};

</script>

<style scoped>
.loader {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #3498db;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 2s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}
</style>
