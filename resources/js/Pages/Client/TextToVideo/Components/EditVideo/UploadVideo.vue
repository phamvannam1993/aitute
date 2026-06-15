<template>
    <input type="file" @input="handleChangeFile" accept="video/*">
    <div v-if="fileUpload.url" class="mt-[30px] w-11/12 mx-auto max-w-[400px]">
        <video :src="fileUpload.url" controls class="w-full rounded-md shadow"></video>
    </div>
    <div class="mt-[32px] text-center" v-if="fileUpload.file && showSubmit">
        <button @click="handleSubmitVideo" type="button" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Đồng ý</button>
    </div>
</template>

<script setup>
import { ref } from "vue";

const fileUpload = ref({ url: "", file: "" });
const showSubmit = ref(true);
const videoLink = defineModel()

const handleChangeFile = (e) => {
    const reader = new FileReader();
    fileUpload.value.file = e.target.files[0];

    reader.onload = (e) => {
        fileUpload.value.url = e.target.result;
    };

    reader.readAsDataURL(fileUpload.value.file);
};

const handleSubmitVideo = () => {
    const formData = new FormData();
    formData.append("upload", fileUpload.value.file);
    formData.append("type", "video");
    showSubmit.value = false;

    axios
        .post(route("slide.uploadVideo"), formData, {
            headers: {
                "content-type": "multipart/form-data",
            },
        })
        .then((res) => {
            showSubmit.value = true;
            videoLink.value = res.data.path
        });
};
</script>