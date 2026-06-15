<template>
    <div class=" relative rounded-md mb-4 flex transition-all duration-500 z-[0]">
        <div class="flex-1 transition-transform duration-500 p-4 bg-white border rounded-md " :class="isShowVideo && !props.isSubmit ? 'lg:-translate-x-[15%] xl:-translate-x-[25%]' : ''">
            <div>
                <div class="w-full flex justify-between items-center">
                    <h3 class="text-lg font-bold mb-2">Cảnh {{ index + 1 }}</h3>

                    <h3 class="text-base font-bold mb-2 cursor-pointer p-[6px] bg-blue-300 rounded-lg hover:bg-opacity-80" style="pointer-events: auto" @click="toggleShowVideo">
                        <div v-if="isLoadingVideo">
                            <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                            </svg>
                            <span class="sr-only">Loading...</span>
                        </div>
                        <span v-else>Xem trước</span>
                    </h3>
                </div>
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700">Cảnh:</label>
                    <input v-model="slide.title" id="title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                </div>
                <div class="mb-4">
                    <label :for="'avatar_select_' + index" class="block text-sm font-medium text-gray-700">Chọn Avatar cho Slide {{ index + 1 }}:</label>
                    <div class="avatar-list grid grid-cols-4 gap-2 max-h-[35vh] overflow-y-auto">
                        <div v-for="(avatar, avatarIndex) in avatars" :key="avatarIndex" class="avatar-item relative border border-gray-200 rounded-md" @click="selectSlideAvatar(slide, avatar)">
                            <div class="max-h-[200px] overflow-hidden rounded-md">
                                <img :src="avatar.s3_url" :alt="avatar.s3_url" />
                            </div>
                            <div v-if="slide.virtual?.id == avatar.id" class="absolute top-0 right-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label :for="voice_type" class="block text-sm font-medium text-gray-700">Giọng:</label>
                    <select v-model="slide.voice_type" :id="voice_type" class="w-full p-3 border mt-1 text-black border-gray-300 rounded-[13px] shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="young_female">Giọng nữ trẻ</option>
                        <option value="young_male">Giọng nam trẻ</option>
                        <option value="old_female">Giọng nữ trung niên</option>
                        <option value="old_male">Giọng nam trung niên</option>
                    </select>
                </div>
                <div v-for="(desc, descIndex) in slide.descriptions" :key="descIndex" class="mb-4">
                    <div>
                        <label :for="'description_' + descIndex" class="block text-sm font-medium text-gray-700">Typo (Nội dung âm thanh):</label>
                        <textarea v-model="desc.description" :id="'description_' + descIndex" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 min-h-[160px] lg:min-h-0"></textarea>
                    </div>
                </div>
                <!-- <div>
                    <label :for="keyword" class="block text-sm font-medium text-gray-700">Hình ảnh:</label>
                    <textarea v-model="slide.keyword" :id="keyword" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div> -->
                <div class="flex mt-3 py-2 gap-2 justify-center flex-col max-w-[250px]">
                    <!-- <label for="file" class="block text-sm font-medium text-gray-700">Chọn ảnh nền:</label>
                    <div class="flex gap-2 items-center">
                        <input ref="fileInput" type="file" @change="handleChangeBackground" class="border rounded px-2 py-1 max-w-[260px] md:max-w-max">
                            <button @click="resetBackground" class="text-black rounded-full w-8 h-4 flex items-center justify-center text-sm">
                                X
                            </button>
                    </div> -->
                    <button @click="deleteSlide(index)" class="mt-4 px-4 py-2 bg-red-500 text-white rounded-md w-[112px]">Xóa cảnh</button>
                </div>
            </div>
        </div>

        <!-- template view video -->
        <div v-show="isShowVideo && !props.isSubmit"  class="flex absolute items-center justify-center lg:static layout top-0 right-0 text-black w-full lg:w-[30%] h-full transition-all duration-500 rounded-lg z-[100] max-h-[300px] lg:max-h-[800px] md:top-0 lg:top-[100px]" :class="isShowVideo && !props.isSubmit ? 'lg:translate-x-[65%] xl:translate-x-[100%] opacity-100' : 'opacity-0'">
            <div class="flex relative transform-o  w-full md:w-[960px] md:h-[500px] rounded-lg md:top-[0] xl:top-[75px]">
                <!-- <div class="relative w-[373px] h-[265px] md:w-[720px] md:h-[400px] lg:w-full lg:h-full"> -->
                <img @click="toggleShowVideo" src="/assets/img/icon/close-btn.png" class="absolute z-[100] top-0 right-0 p-1 lg:p-5 font-semibold size-10 lg:size-16 cursor-pointer" alt="" />
                <!-- <component :is="activeSlide.component" v-bind="activeSlide.props" v-model:data="activeSlide.props" :active="true" :is-present="true" class="slide-content slide-presenting transform-o-3 flex w-[910px] h-[630px] md:w-[960px] md:h-[720px]" /> -->
                <component :is="activeSlide.component" v-bind="activeSlide.props" v-model:data="activeSlide.props" :active="true" :is-present="true" class="slide-content slide-presenting w-full h-[420px] md:w-full md:h-[520px] lg:w-[700px] lg:h-[420px] xl:w-[900px] xl:h-[650px] rounded-3xl" />
                <!-- <component :is="activeSlide.component" v-bind="activeSlide.props" v-model:data="activeSlide.props" :active="true" :is-present="true" class="slide-content slide-presenting transform-o-3 flex w-[960px] h-[520px]" /> -->
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, onBeforeMount } from "vue";
import Template10 from "../Components/VideoTemplate/Template10.vue";
import Template11 from "../Components/VideoTemplate/Template11.vue";
import Template12 from "../Components/VideoTemplate/Template12.vue";
import Template13 from "../Components/VideoTemplate/Template13.vue";
import axios from "axios";

const props = defineProps({
    slide: Object,
    index: Number,
    deleteSlide: Function,
    isLoadingVideo: Boolean,
    isSubmit: Boolean,
    presenters: Array
});

console.log(props.slide)

const activeSlide = ref();
const isShowVideo = ref(false);
const backgroundImage = ref('');
const fileInput = ref(null);
const toggleShowVideo = () => {
    isShowVideo.value = !isShowVideo.value;
};
const avatars = ref([])
const defaultAvatar = [
    {
        id: "v2_public_alex@qcvo4gupoy",
        name: "alex",
        s3_url: "/assets/img/gifs/alex_360.gif",
    },
    {
        id: "v2_public_amy@seiu0o2gby",
        name: "amy",
        s3_url: "/assets/img/gifs/amy_360.gif",
    }
];

const selectSlideAvatar = (slide, avatar) => {
    if(slide.virtual?.name === avatar.name){
        slide.virtual = null
    }else{
        slide.virtual = avatar; // Cập nhật avatar cho slide cụ thể
    }
};

watch(
    () => props.slide,
    async (newVal) => {
        try {
            activeSlide.value = props.slide;
            activeSlide.value.imagesCallback = async () => {
                if (activeSlide.value.images?.length == 0) {
                    try {
                        const response = await axios.post(route("text-to-video.find-images-for-video"), { keyword: activeSlide.value.keyword });
                        activeSlide.value = {
                            ...activeSlide.value,
                            images: response.data.images,
                        };
                    } catch (error) {
                        console.error("Error finding images", error);
                        return [];
                    }
                }
            };

            let resultComponent = null;
            // Kiểm tra và trả về template phù hợp với từng  activeSlide.value
            if (activeSlide.value.key === "Template1") resultComponent = { component: Template11, props: activeSlide.value };
            if (activeSlide.value.key === "Template2") resultComponent = { component: Template11, props: activeSlide.value };
            if (activeSlide.value.key === "Template3") resultComponent = { component: Template11, props: activeSlide.value };
            if (activeSlide.value.key === "Template4") resultComponent = { component: Template11, props: activeSlide.value };
            if (activeSlide.value.key === "Template5") resultComponent = { component: Template11, props: activeSlide.value };
            if (activeSlide.value.key === "Template6") resultComponent = { component: Template11, props: activeSlide.value };
            if (activeSlide.value.key === "Template7") resultComponent = { component: Template11, props: activeSlide.value };
            if (activeSlide.value.key === "Template8") resultComponent = { component: Template11, props: activeSlide.value };
            if (activeSlide.value.key === "Template9") resultComponent = { component: Template11, props: activeSlide.value };
            if (activeSlide.value.key === "Template10") resultComponent = { component: Template10, props: activeSlide.value };

            activeSlide.value.title_style = {
                fontFamily: "Be Vietnam Pro",
                fontSize: 24,
                color: "#394D2E",
            };
            activeSlide.value.description_style = {
                fontFamily: "Be Vietnam Pro",
                fontSize: 10,
                color: "#878175",
            };
            activeSlide.value.subtitle_style = {
                fontFamily: "Be Vietnam Pro",
                fontSize: 12,
                color: "#394D2E",
            };

            // thêm key component và props vào activeSlide
            activeSlide.value = { ...activeSlide.value, ...resultComponent };
        } catch (error) {
            console.error("Error parsing slide suggestions:", error);
        }
    },
    { immediate: true }
);

const addDescription = () => {
    props.slide.descriptions.push({
        sub_title: "",
        description: "",
    });
};

const removeDescription = (index) => {
    props.slide.descriptions.splice(index, 1);
};


// handle user change background
const handleChangeBackground = async (event) => {
    const file = event.target.files[0];
    if (!file) {
        return;
    }
    const formData = new FormData();
    formData.append("upload", file);
    formData.append("type", "background");
    const response = await axios
        .post(route("slide.uploadImage"), formData, {
            headers: {
                "content-type": "multipart/form-data",
            },
        });

    backgroundImage.value = response.data.path
}

const resetBackground = () => {
    backgroundImage.value = '';
    if(fileInput.value) {
        fileInput.value.value = '' // Đặt lại giá trị input file
    }
}

watch(()=> backgroundImage.value, (newBackground) => {
    // update props background
    activeSlide.value.props.background = newBackground;
    // props.slide.background = newBackground;
})

onBeforeMount(() => {
    avatars.value = [...defaultAvatar, ...props.presenters];
    if (!props.slide.virtual) {
        props.slide.virtual = avatars[0];
    }
});

// chọn component
</script>
<style scoped>
/* Thêm style nếu cần */

/* Media query cho màn hình nhỏ (ví dụ: di động) */
@media (max-width: 600px) {
    .transform-o {
        transform-origin: 8px 4px;
        transform: scale(1, 0.95); /* Tỉ lệ cho màn hình nhỏ hơn */
    }
    .transform-o-3 {
        transform-origin: 8px 4px;
        transform: scale(0.4, 0.6);
    }
    .screen-o-4{
        width: 372px;
        height: 274px;
    }
    .transform-o-4 {
        transform-origin: 8px 4px;
        transform: scale(0.377, 0.37);
    }
}

/* Media query cho màn hình vừa (ví dụ: máy tính bảng) */
@media (min-width: 601px) and (max-width: 900px) {
    .transform-o {
        transform: scale(0.8, 0.8); /* Tỉ lệ cho màn hình vừa */
    }
}

/* Media query cho màn hình lớn (ví dụ: máy tính để bàn) */
@media (min-width: 1024px) {
    .transform-o-3 {
        transform-origin: 8px 4px;
        transform: scale(0.75, 0.75);
    }
    .screen-o-4{
        width:768px;
        height: 510px;
    }
    .transform-o-4 {
        transform-origin: 8px 4px;
        transform: scale(0.797, 0.797);
    }

    .transform-o {
        transform-origin: 8px 4px;
        transform: scale(0.57, 0.7); /* Tỉ lệ cho màn hình lớn */
    }
}

/* Media query cho màn hình lớn (ví dụ: máy tính để bàn) */
@media (min-width: 1280px) {
    .transform-o {
        transform-origin: 8px 4px;
        transform: scale(0.575, 0.7); /* Tỉ lệ cho màn hình lớn */
    }
}
</style>
