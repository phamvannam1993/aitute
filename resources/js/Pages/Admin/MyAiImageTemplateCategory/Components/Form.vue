<template>
    <form @submit.prevent="submit(form)" enctype="multipart/form-data">
        <div class="grid grid-cols-1 gap-6 mt-4 text-black">
            <div v-if="$page.props.errors.message" class="mt-5 text-red-500">
                {{ $page.props.errors.message }}
            </div>


            <div class="flex-col md:flex-row flex items-left gap-5">
                <label class="block text-gray-700 text-sm font-bold w-4/12" for="id">
                    Bộ sưu tập
                </label>
                <div class="w-full">
                    <select v-model="form.my_ai_image_collection_id"
                        class="block appearance-none w-full  text-black py-3 px-4 pr-8 border border-gray-300 rounded-md leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        <option :value=null>-- Chọn bộ sưu tập --</option>
                        <option v-for="(imageCollection, index) in imageCollections" :value="imageCollection.id"
                            :key="index">
                            {{ imageCollection.title }}
                        </option>
                    </select>
                    <div v-if="$page.props.errors.my_ai_image_collection_id"
                        v-text="$page.props.errors.my_ai_image_collection_id" class="text-red-500 mt-1"></div>
                </div>
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
                    <div class="mb-2">
                        <div
                            class="relative w-10 h-10 bg-blue-100 rounded-lg cursor-pointer overflow-hidden flex items-center justify-center">
                            <input multiple type="file" @change="onImageChange"
                                class="absolute inset-0 z-10 opacity-0 cursor-pointer" />
                            <div class="absolute bottom-1 cursor-pointer right-1 bg-white p-1 rounded-full">
                                <img src="/assets/img/admin/icon_upload.png" class="cursor-pointer w-6 h-6">
                            </div>
                        </div>
                        <div v-if="$page.props.errors.images" v-text="$page.props.errors.images"
                            class="text-red-500 mt-1">
                        </div>
                    </div>
                    <div class="flex-col md:flex-row flex items-left gap-5">
                        <div class="w-full flex flex-wrap gap-4">
                            <template v-if="pageData.previewImages.length">
                                <div v-for="(image, index) in pageData.previewImages" :key="index">
                                    <div class="relative w-24 h-24 bg-blue-100 rounded-lg overflow-hidden">
                                        <img :src="image" class="object-cover w-full h-full" />
                                        <button @click="removeImage(index)"
                                            class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center">
                                            x
                                        </button>
                                    </div>
                                    <div v-if="$page.props.errors['images.' + index]"
                                        v-text="$page.props.errors['images.' + index]" class="text-red-500 mt-1">
                                    </div>
                                </div>
                            </template>
                            <template v-if="pageData.imageTemplates.length">
                                <div v-for="(image, index) in pageData.imageTemplates" :key="index">
                                    <div class="relative w-24 h-24 bg-blue-100 rounded-lg overflow-hidden">
                                        <img :src="image.s3_url" class="object-cover w-full h-full" />
                                        <button type="button" @click="removeImageS3(image.id, index)"
                                            class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center">
                                            x
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
            </div>


            <div class="flex-col md:flex-row flex items-left gap-5">
                <label class="block text-gray-700 text-sm font-bold w-4/12" for="id">
                    Vị trí
                </label>
                <div class="w-full">
                    <input v-model="form.order"
                        class="w-full p-2 border border-gray-300 rounded-md focus:border-indigo-600 focus:ring focus:ring-opacity-40 focus:ring-indigo-500"
                        type="number" />
                    <div v-if="$page.props.errors.order" v-text="$page.props.errors.order" class="text-red-500 mt-1">
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-end mt-4 gap-2">
            <button
                class="px-4 py-2 text-gray-200 bg-[#0B1C63] rounded-md hover:opacity-80 focus:outline-none focus:bg-[#0B1C63] font-bold">Lưu</button>
        </div>
    </form>
    <Loading v-if="pageData.isLoading" />
</template>

<script setup>
import { reactive } from "vue";
import Loading from '@/Components/Loading.vue';
import { usePage } from "@inertiajs/vue3";

const { imageTemplateCategory } = defineProps({ imageTemplateCategory: Object, imageCollections: Object, submit: Function });
const page = usePage();

const pageData = reactive({
    previewImages: [],
    imageTemplates: imageTemplateCategory.templates ?? [],
    isLoading: false,
});

const form = reactive({
    my_ai_image_collection_id: imageTemplateCategory.my_ai_image_collection_id,
    id: imageTemplateCategory.id,
    title: imageTemplateCategory.title,
    order: imageTemplateCategory.order,
    images: [],
});

const onImageChange = (event) => {
    const files = event.target.files;
    form.images = files;

    Array.from(files).forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            pageData.previewImages.push(e.target.result);
        };
        reader.readAsDataURL(file);
    });
};

const removeImage = (index) => {
    let images = [...form.images];
    images.splice(index, 1);
    form.images = [...images];

    pageData.previewImages.splice(index, 1);
};

const form_delete = reactive({
    image_template_id: null,
    _method: 'DELETE',
})

const removeImageS3 = async (id, index) => {
    try {
        pageData.isLoading = true;
        form_delete.image_template_id = id;
        await axios.post(route('admin.my-ai-image-templates.destroy', { id: form_delete.image_template_id }), form_delete)

        let imageTemplates = [...pageData.imageTemplates];
        imageTemplates.splice(index, 1);
        pageData.imageTemplates = [...imageTemplates];
    } catch (error) {
        page.props.errors = error.response.data.errors
    } finally {
        pageData.isLoading = false;
    }
};

</script>