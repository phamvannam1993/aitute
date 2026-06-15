<template>
    <div class="relative">
        <slot></slot>
        <span class="absolute top-5 left-5 cursor-pointer" @click="() => (isShow = true)" v-if="!preview">
            <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="palette" width="20px" height="20px" class="w-[20px] h-[20px]" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path class="scale-50" fill="currentColor" d="m199.04 672.64 193.984 112 224-387.968-193.92-112-224 388.032zm-23.872 60.16 32.896 148.288 144.896-45.696zM455.04 229.248l193.92 112 56.704-98.112-193.984-112-56.64 98.112zM104.32 708.8l384-665.024 304.768 175.936L409.152 884.8h.064l-248.448 78.336zm384 254.272v-64h448v64h-448z"></path>
            </svg>
        </span>
    </div>
    <div v-if="isShow" class="fixed top-0 left-0 right-0 bottom-0 bg-opacity-55 bg-black z-[99]">
        <div class="w-fit mx-auto my-20 bg-white relative rounded-md flex justify-center px-4 border-[#666666]">
            <span class="absolute top-4 right-4" @click="() => (isShow = false)">
                <svg class="cursor-pointer" @click.prevent="handleClosePopup" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="18" height="18" viewBox="0 0 24 24">
                    <path d="M11 0.7H13V23.3H11z" transform="rotate(-45.001 12 12)"></path>
                    <path d="M0.7 11H23.3V13H0.7z" transform="rotate(-45.001 12 12)"></path>
                </svg>
            </span>
            <div class="min-h-52 px-4 p-8">
                <template v-if="type == 'video'">
                    <div class="flex gap-2 items-center">
                        <label for="file-upload" class="px-3 py-2 flex gap-1 items-center rounded-md cursor-pointer bg-[#D9D9D9]">
                            <img src="/assets/svgs/choose_file.svg" alt="JOB" class="h-6 w-6" />
                            <p>Chọn tệp</p>
                        </label>
                        <p class="text-sm">{{ fileUploadName }}</p>
                    </div>
                    <input id="file-upload" class="mb-4" type="file" @input="handleChangeFile" accept="video/*" />
                </template>
                <div class="flex gap-2 items-center mb-1 mt-6">
                    <img src="/assets/svgs/animation.svg" alt="JOB" class="h-6 w-6" />
                    <p class="">Hiệu ứng</p>
                </div>
                <select class="rounded-md w-[450px] px-4 py-2 bg-[#D9D9D9] border-none" v-model="style.animation">
                    <option value="">Chọn hiệu ứng</option>
                    <option value="ani-left-to-right">trái qua phải</option>
                    <option value="ani-right-to-left">phải qua trái</option>
                    <option value="ani-top-to-bottom">trên xuống dưới</option>
                    <option value="ani-bottom-to-top">dưới lên trên</option>
                </select>
                <div class="flex gap-2 items-center mb-1 mt-6">
                    <img src="/assets/svgs/animation.svg" alt="JOB" class="h-6 w-6" />
                    <p>Chọn hình dạng</p>
                </div>
                <select class="rounded-md w-[450px] px-4 py-2 bg-[#D9D9D9] border-none" v-model="style.sharp">
                    <option value="">Chọn hình dạng</option>
                    <option value="sharp-circle">tròn</option>
                    <option value="sharp-square">vuông</option>
                    <option value="sharp-video">16:9</option>
                </select>
                <div class="flex gap-2 items-center mb-1 mt-6">
                    <img src="/assets/svgs/delay.svg" alt="JOB" class="h-6 w-6" />
                    <p class="">Thiết lập thời gian xuất hiện</p>
                </div>
                <div class="flex items-center gap-4">
                    <input type="text" v-model="style.delayTime" class="px-4 py-2 w-56 rounded-md bg-[#D9D9D9] border-none" />
                    <p class="text-xs mb-1">1000 tương ứng 1 giây</p>
                </div>
                <div class="mt-10 flex justify-center gap-4">
                    <span class="px-4 py-2 rounded-md cursor-pointer text-white bg-red-500" @click="handleDelete">Xóa {{ type }}</span>
                    <span class="px-4 py-2 rounded-md cursor-pointer text-white bg-cyan-500" @click="() => (isShow = false)">Xong</span>
                </div>
            </div>
        </div>
    </div>
    <div v-if="isUploading" class="fixed flex items-center justify-center top-0 left-0 right-0 bottom-0 bg-opacity-55 bg-black z-[100]">
        <span class="text-white">Đang tải tập tin...</span>
    </div>
</template>
<script setup>
import { ref } from "vue";

const props = defineProps({
    preview: {
        type: Boolean,
        default: false,
    },
    type: {
        type: String,
        default: "",
    },
});

const style = defineModel();
const isShow = ref(false);
const isUploading = ref(false);
const fileUploadName = ref("Không có tệp nào được chọn");

const handleChangeFile = (e) => {
    const formData = new FormData();
    const file = e.target.files[0];
    formData.append("upload", file);
    fileUploadName.value = file ? file.name : "Không có tệp nào được chọn";

    formData.append("type", "icon");
    isUploading.value = true;

    axios
        .post(route("slide.uploadImage"), formData, {
            headers: {
                "content-type": "multipart/form-data",
            },
        })
        .then((res) => {
            style.value.src = res.data.url;
            isUploading.value = false;
        });
};

const handleDelete = () => {
    // btodo send api delete video from storage
    style.value = null;
};
</script>

<style scoped>
input[type="file"] {
    display: none;
}

.custom-file-upload {
    border: 1px solid #ccc;
    display: inline-block;
    padding: 6px 12px;
    cursor: pointer;
}
</style>
