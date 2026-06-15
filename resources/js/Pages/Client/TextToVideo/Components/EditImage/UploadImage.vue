<template>
    <input type="file" @input="handleChangeFile" accept=".jpg,.png,.jpeg" />
    <div v-if="fileUpload.url" :style="`background-image: url('${fileUpload.url}')`" class="bg-no-repeat rounded-md bg-center bg-cover w-11/12 mx-auto select-none aspect-video max-w-[400px] mt-[30px] shadow"></div>
    <div class="mt-[32px] text-center" v-if="fileUpload.file && showSubmit">
        <button @click="handleSumitImage" type="button" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Đồng ý</button>
    </div>
</template>
<script setup>
import { ref } from "vue";
import firebase from "../../../../../firebase/firebase";

const fileUpload = ref({ url: "", file: "" });
const showSubmit = ref(true);
const imageLink = defineModel();
// Emit handle event upload image
const emit = defineEmits(['imageUploaded']);
const handleChangeFile = (e) => {
    const reader = new FileReader();
    fileUpload.value.file = e.target.files[0];

    reader.onload = (e) => {
        fileUpload.value.url = e.target.result;
    };

    reader.readAsDataURL(fileUpload.value.file);
};

const handleSumitImage = () => {
    const formData = new FormData();
    formData.append("upload", fileUpload.value.file);
    formData.append("type", "icon");
    showSubmit.value = false;

    // axios
    //     .post(route("slide.uploadImage"), formData, {
    //         headers: {
    //             "content-type": "multipart/form-data",
    //         },
    //     })
    //     .then((res) => {
    //         showSubmit.value = true;
    //         imageLink.value = res.data.url;
    //     });

    firebase
        .uploadImage(fileUpload.value.file)
        .then((url) => {
            console.log("Ảnh đã được upload:", url);
            imageLink.value = url;
            showSubmit.value = true;
            // raise event when upload event successfully.
            emit('imageUploaded'); 
        })
        .catch((error) => {
            console.error("Lỗi upload ảnh lên Firebase:", error);
            showSubmit.value = true;
        });
};
</script>
