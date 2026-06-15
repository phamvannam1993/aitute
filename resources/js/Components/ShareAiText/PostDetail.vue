<template>
    <Modal :show="pageData.is_show_post_detail_modal" maxWidth="xl-4xl" minWidth="w-full" @close="closePostDetail">
        <form @submit.prevent="submit" class="relative m-6">
            <div class="relative bg-white rounded-lg">
                <div>
                    <div class="p-3">
                        <div class="">
                            <div class="flex justify-between gap-md">
                                <h1 class="text-xs hidden xs:block xs:text-xl font-semibold">
                                    Bài viết
                                </h1>

                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-1 p-2 border-2 rounded-xl bg-green-500 cursor-pointer"
                                        @click="openMediaLibrary">
                                        <img src="/assets/svgs/media-icon.svg" alt="add" />
                                        <span class="text-white text-xs md:text-base">Kho đa phương tiện</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex text-black items-center justify-between py-2">
                            <div>
                                <VueDatePicker format="dd/MM/yyyy HH:mm:ss" v-if="pageData.is_schedule"
                                    :ui="{ input: 'rounded-[10px] border-[#B5B5BE]' }" locale="vi"
                                    v-model="form.scheduled_publish_time" auto-apply :close-on-auto-apply="false"
                                    :min-date="minDate" :max-date="maxDate"
                                    :day-names="['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN']" />

                            </div>
                        </div>


                        <div class="relative max-h-72 overflow-auto">


                            <div class="w-full">
                                <textarea v-model="form.content" placeholder="Nội dung..."
                                    class="h-44 w-full border rounded-md  focus:ring-0 resize-none">
                                </textarea>


                            </div>
                            <div class="items-center justify-between">
                                <div class="flex items-center gap-4 text-[#666]">
                                    <input ref="fileInput" hidden multiple type="file"
                                        accept="video/mp4,video/x-m4v,video/webm,video/ogg,video/*,.flv,.3gp,image/*"
                                        class="inputMedia" @change="handleFileChange">
                                    <button type="button"
                                        class=" p-1 rounded-lg hover:text-[#333] hover:bg-slate-100"
                                        @click="selectFile">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                                        </svg>
                                    </button>
                                </div>

                                <div v-if="filePreviewUrls.length" class="relative mt-4 rounded-lg group">
                                    <div class="mt-4">
                                        <div class="grid grid-cols-4 gap-2">
                                            <div v-for="(item, index) in filePreviewUrls" :key="index"
                                                class="relative">
                                                <img v-if="item.type.includes('image')" :src="item.url"
                                                    class="w-full h-auto rounded-md" />
                                                <video v-else :src="item.url" controls
                                                    class="w-full h-auto rounded-md"></video>
                                                <button  type="button"
                                                    @click="removeFilePreviewUrls(index)"
                                                    class="absolute top-2 right-2 bg-red-500 text-white w-8 h-8 rounded-full">
                                                    X
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <GenerateAI v-model:content="form.content" @selectMedia="selectMedia" />
                    <div
                        class="flex items-center justify-between border-t-[1px] border-[#ddd] bg-[#f8f9fb] p-3 rounded-bl-lg">
                        <div class="flex items-center space-x-2 md:space-x-4 relative">
                            <div v-if="!form.published || !form.id">
                                <span class="mr-1">Lên lịch bài viết</span>

                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" v-model="pageData.is_schedule">
                                    <div
                                        class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                    </div>

                                </label>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2 md:space-x-4 relative">
                            <button type="submit"
                                class="schedule-button flex gap-1 items-center px-4 py-2 bg-[#2C75E3] text-white rounded-[10px]   text-[15px] font-bold">
                                Xác nhận
                            </button>
                        </div>
                    </div>
                </div>
                <MediaLibrary v-if="pageData.is_show_media" :mediaLibrary="mediaLibrary"
                    @closeMediaLibrary="closeMediaLibrary" @selectMedia="selectMedia" :filesSelected="form.files" />
            </div>
        </form>
    </Modal>
</template>

<script setup>
import GenerateAI from '@/Components/GenerateAI.vue';
import MediaLibrary from '@/Components/MediaLibrary.vue';
import Modal from '@/Components/Modal.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import dayjs from 'dayjs';
import { defineEmits, inject, onMounted, reactive, ref, watch } from 'vue';
const emit = defineEmits(['updatePostDatas']);
const minDate = dayjs().startOf('day').format('YYYY/MM/DD HH:mm:ss');
const maxDate = dayjs().startOf('day').add(29, 'day').format('YYYY/MM/DD HH:mm:ss');
const helpers = inject('helpers')

const pageData = reactive({
is_show_post_detail_modal: false,
is_show_media: false,
is_schedule: false,
});


const mediaLibrary = reactive({
video: {
    currentPage: 0,
    nextPage: 1,
    data: []
},
image: {
    currentPage: 0,
    nextPage: 1,
    data: []
},
});

const form = reactive({
index: null,
content: null,
name: null,
facebook_fanpage_id: null,
scheduled_publish_time: null,
published: true,
files: []
});

const filePreviewUrls = ref([]);

onMounted(() => {
uploadFilePreviewUrls();
});

const removeFilePreviewUrls = (index) => {
form.files = form.files.filter((_, i) => i !== index);
};

watch(pageData.is_schedule, () => {
if (pageData.is_schedule) {
    form.scheduled_publish_time = dayjs().add(1, 'hour').format('YYYY/MM/DD HH:mm:ss')
} else {
    form.scheduled_publish_time = null
}
});

watch(form.files, () => {
uploadFilePreviewUrls();
});

watch(
() => [form.files],
([], []) => {
    uploadFilePreviewUrls();
}
);

const openPostDetail = (postForm = {}) => {
Object.assign(form, {
    index: postForm.hasOwnProperty('index') ? postForm.index : null,
    facebook_fanpage_id: postForm.facebook_fanpage_id,
    name: postForm.name,
    content: postForm.content ? postForm.content : '',
    published: postForm.hasOwnProperty('published') ? postForm.published : true,
    scheduled_publish_time: postForm.scheduled_publish_time ? helpers.formatTimestampToYMD(postForm.scheduled_publish_time) : null,
    files: postForm.files ? postForm.files : [],
});

pageData.is_schedule = !form.published
pageData.is_show_post_detail_modal = true;
};

const closePostDetail = () => {
pageData.is_show_post_detail_modal = false;
};

const uploadFilePreviewUrls = async () => {
let previewUrls = [];
const promises = Array.from(form.files).map(file => {
    return new Promise((resolve) => {
        if (file.s3_url) {
            previewUrls.push({
                'url': file.s3_url,
                'type': file.type,
            });
            resolve();
        } else {
            const reader = new FileReader();
            reader.onload = () => {
                let objectURL = URL.createObjectURL(file);
                previewUrls.push({
                    'url': objectURL,
                    'type': file.type,
                });
                resolve();
            };
            reader.readAsDataURL(file);
        }
    });
});

await Promise.all(promises);

filePreviewUrls.value = previewUrls;
};

const openMediaLibrary = () => {
pageData.is_show_media = true;
};

const closeMediaLibrary = () => {
pageData.is_show_media = false;
};

const selectFile = () => {
const fileInput = document.querySelector('.inputMedia');
fileInput.click();
};

const selectMedia = (item) => {
removeFileVideos()
removeFileImages(item)
if (item.type == 'video' && item.isCheck) {
    form.files = [item];
}
if (item.type == 'image' && item.isCheck) {
    form.files = [...form.files, item];
}
};

const handleFileChange = (event) => {
const files = event.target.files;

for (let i = 0; i < files.length; i++) {
    const file = files[i];
    removeFileVideos()
    if (file.type.includes('video')) {
        form.files = [file];
    } else {
        form.files.push(file)
    }
}
};

const removeFileVideos = () => {
let files = [...form.files];
for (let i = 0; i < files.length; i++) {
    const file = files[i];
    if (file.type.includes('video')) {
        files.splice(i, 1);
    }
}

form.files = [...files];
};

const removeFileImages = (item) => {
let files = [...form.files];
if (!item.isCheck) {
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (file.id === item.id) {
            files.splice(i, 1);
        }
    }
}

form.files = [...files];
};

const submit = () => {
form.published = !pageData.is_schedule
form.scheduled_publish_time = form.scheduled_publish_time ? dayjs(form.scheduled_publish_time).format('YYYY/MM/DD HH:mm:ss') : form.scheduled_publish_time;

emit('updatePostDatas', form);
closePostDetail()
}

defineExpose({
openPostDetail,
});

</script>