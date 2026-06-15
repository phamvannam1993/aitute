<template>
    <form @submit.prevent="submit(form)" >
        <div class="grid grid-cols-1 gap-6 mt-4 text-black">
            <div v-if="$page.props.errors.message" class="mt-5 text-red-500">
                {{ $page.props.errors.message }}
            </div>

            <div class="flex-col md:flex-row flex items-left gap-5">
                <label class="block text-gray-700 text-sm font-bold w-4/12" for="id">
                    Tiêu đề
                </label>
                <div class="w-full">
                    <input v-model="form.title"
                        class="w-full p-2 border border-gray-300 rounded-md focus:border-indigo-600 focus:ring focus:ring-opacity-40 focus:ring-indigo-500"
                        type="text" />
                    <div v-if="$page.props.errors.title" v-text="$page.props.errors.title" class="text-red-500 mt-1">
                    </div>
                </div>
            </div>

            <div class="flex-col md:flex-row flex items-left gap-5">
                <label class="block text-gray-700 text-sm font-bold w-4/12" for="id">
                    Ảnh
                </label>
                <div class="w-full">
                    <div
                        class="relative w-24 h-24 bg-blue-100 rounded-lg overflow-hidden flex items-center justify-center">
                        <img :src="pageData.imageUrl" v-if="pageData.imageUrl" class="object-cover w-full h-full" />
                        <span v-if="!pageData.imageUrl" class="text-gray-400">Ảnh</span>
                        <input type="file"  @change="onImageChange" class="absolute inset-0 opacity-0 cursor-pointer" />
                        <div class="absolute bottom-1 right-1 bg-white p-1 rounded-full">
                            <img src="/assets/img/admin/icon_upload.png" class="w-6 h-6">
                        </div>
                    </div>
                    <div v-if="$page.props.errors.image" v-text="$page.props.errors.image" class="text-red-500 mt-1">
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-end mt-4 gap-2">
            <button
                class="px-4 py-2 text-gray-200 bg-[#0B1C63] rounded-md hover:opacity-80 focus:outline-none focus:bg-[#0B1C63] font-bold">Lưu</button>
        </div>
    </form>
</template>

<script setup>
import { reactive } from "vue";

const { imageCollection } = defineProps({ imageCollection: Object, submit: Function });

const pageData = reactive({
    imageUrl: imageCollection.s3_url,
});

const form = reactive({
    id: imageCollection.id,
    title: imageCollection.title,
    image: null,
});

const onImageChange = (event) => {
    const file = event.target.files[0];

    const reader = new FileReader();
    reader.onload = (e) => {
        pageData.imageUrl = e.target.result;
        form.image = file;
    };
    reader.readAsDataURL(file);
};

</script>