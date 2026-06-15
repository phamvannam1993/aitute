<template>
    <div v-if="props.isShow" class="fixed inset-0 bg-black/30 flex items-center justify-center z-[999] text-black">
        <div class="w-11/12 md:w-8/12 lg:w-6/12 2xl:w-4/12 bg-white rounded-lg p-8 relative flex flex-col gap-8">
            <h2 class="font-bold text-lg">Link thành video</h2>
            <div class="flex flex-col gap-1">
                <input class="w-full rounded-lg" type="url" v-model="urlValue" @input="validateUrl" placeholder="Nhập link hợp lệ (bắt đầu bằng http:// hoặc https://)" required />
                <p v-if="isUrlInvalid" class="text-red-500 text-sm">Định dạng link không hợp lệ</p>
            </div>
            <p v-if="props.message_alert_pictory" class="text-red-500 text-base">{{ props.message_alert_pictory }}</p>
            <p class="text-gray-500">Chuyển đổi URL của bài viết hoặc blog của bạn thành video hấp dẫn. Nhập hoặc dán URL hợp lệ và Orion AI sẽ thực hiện phần còn lại.</p>
            <button :disabled="urlValue && isUrlInvalid" @click="submitCreateVideoByUrl()" class="w-full rounded-lg bg-[#0747C8] text-white py-3.5 disabled:opacity-65">Tạo video</button>
            <button @click="props.handleIsShowFunc" class="absolute top-0 right-0 rounded-full w-12 h-12 font-bold">x</button>
            <div class="flex justify-end">
            <a :href="route('ai-video.url-to-video.history')" class="font-semibold bg-[#0747C8] px-6 py-1.5 text-white rounded-lg">Lịch sử</a>
        </div>
        </div>
    </div>
    <div v-if="isLoading" class="fixed z-[9999] inset-0 flex items-center justify-center bg-black/80">
        <div class="text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
            <p class="mt-2">Đang xử lý dữ liệu, vui lòng đợi...</p>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";

const props = defineProps({
    isShow: {
        type: Boolean,
        required: true,
    },
    handleIsShowFunc: {
        type: Function,
        required: true,
    },
    message_alert_pictory: {
        type: String
    }
});

const urlValue = ref("");
const isUrlInvalid = ref(false);
const isLoading = ref(false);

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
    window.location.href = route("ai-video.url-to-video.index", {
        url: urlValue.value,
    });
    // await axios.get(
    //     route("ai-video.url-to-video.index", {
    //         url: urlValue.value,
    //     })
    // );
};
</script>
