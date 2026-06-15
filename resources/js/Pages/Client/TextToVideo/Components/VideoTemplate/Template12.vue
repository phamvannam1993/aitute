<template>
    <div :style="{ backgroundColor: data.theme.slideColor }" class="flex justify-end rounded-2xl relative">
            <!-- Background overlay để chứa background image với độ mờ -->
        <div :style="{
                backgroundImage: `url(${data.background})`,
                backgroundSize: 'cover',
                backgroundPosition: 'center',
                opacity: 0.5 // Đặt độ mờ cho lớp nền
            }"
            class="absolute inset-0 rounded-2xl pointer-events-none"
        ></div>
        <div class="z-[50] flex absolute top-[40%] left-[50%] -translate-x-1/2 -translate-y-1/2 text-black">
            <component v-for="externalComponent in data.components" :is="components[externalComponent.name]" v-model="externalComponent.title" v-model:customStyle="externalComponent.title_style"
                       dragClass="absolute" wrapperClass="w-0 h-0 "
                       :resizeable="true"
            >
                {{ externalComponent.name }}
            </component>
        </div>
        <div class="w-full flex-col flex ">
            <h1 class="mb-4 mt-4 lg:mb-4 lg:mt-16 mx-auto" :class="isPresent ? 'animat-3' : '', { paused: props.isPaused }">
                <editable-content v-bind:isTyping="props.isTyping" v-model="data.title"
                    v-model:customStyle="data.title_style" :is-heading="true" />
            </h1>
            <div class="w-2/6 ml-auto mr-24 items-end flex flex-col gap-4">
                <EditImage :model-value="imgRef[0]" @update:model-value="val => imgRef[0] = val" :title="props.data.title">
                    <img :src="imgRef[0]" alt="img" class="h-[250px] w-[250px] object-fit" :class="isPresent ? 'img-1' : '', { paused: props.isPaused }">
                </EditImage>
                <EditImage :model-value="imgRef[1]" @update:model-value="val => imgRef[1] = val" :title="props.data.title">
                    <img :src="imgRef[1]" alt="img" class="h-[250px] w-[250px] object-fit" :class="isPresent ? 'img-2' : '', { paused: props.isPaused }">
                </EditImage>
            </div>
        </div>
        <div v-if="props?.virtual?.s3_url" class="w-1/2 absolute bottom-12 left-40 mc-animate">
            <DragAndResize :x="mcStyle.x" :y="mcStyle.y" :width="mcStyle.width" :change="adjustMc" :preview="preview">
                <img :autoplay="isPresent" :src="props?.virtual?.s3_url" :style="{ width: `${mcStyle.width}px` }" class="mx-auto select-none pointer-events-none" :alt="props?.virtual?.s3_url" />
            </DragAndResize>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import EditableContent from "@/Pages/Client/TextToVideo/Components/EditableContent/Index.vue";
import EditImage from "@/Pages/Client/TextToVideo/Components/EditImage/Index.vue";
import DragAndResize from "@/Components/DragAndResize.vue";
import components from "@/Pages/Client/TextToVideo/Components/components.js";

const props = defineProps({
    title: String,
    description: String,
    images: Array,
    keyword: String,
    active: Boolean,
    data: Object,
    isPresent: Boolean,
    isTyping: Boolean,
    imagesCallback: Function,
    isPaused: Boolean,
    virtual: Object
});

const videoRef = ref(null);
const imgRef = ref(props.images)
const background = ref(props.data.background);

const mcStyle = ref({
    x: Number(props.virtual.x) || 0,
    y: Number(props.virtual.y) || 0,
    width: Number(props.virtual.width) || 300,
});

console.log("props.data.title_style ", props.data.title_style);

watch(() => props.images, async (newImages) => {
    // Nếu images rỗng, gọi callback để lấy ảnh mới
    if (props.images?.length === 0) {
        try {
            const response = await axios.post(route("text-to-video.find-images-for-video"), { keyword: props.keyword });
            imgRef.value = response.data.images
        } catch (error) {
            console.log("Error refImage ", error);
        }
}
}, { immediate: true });

const adjustMc = (newValue) => {
    mcStyle.value = { ...mcStyle.value, ...newValue };

    props.virtual.x = mcStyle.value.x;
    props.virtual.y = mcStyle.value.y;
    props.virtual.width = mcStyle.value.width;
};

const handleVideoLoad = () => {
    console.log("Video loaded successfully!");
}
const handleVideoError = (event) => {
    console.error("Error loading video:", event);
}
const loading = ref(true);

const onImageLoad = () => {
    loading.value = false;
};

const onImageError = () => {
    console.error("Image failed to load");
    loading.value = false;
};

props.data.title_style = props.data.title_style || {
    fontFamily: "Be Vietnam Pro",
    fontSize: 48,
    color: props.data.theme.headingColor,
};
props.data.description_style = props.data.description_style || {
    fontFamily: "Be Vietnam Pro",
    fontSize: 16,
    color: props.data.theme.textColor,
};
props.data.subtitle_style = props.data.subtitle_style || {
    fontFamily: "Be Vietnam Pro",
    fontSize: 24,
    color: props.data.theme.headingColor,
};

if (props.images?.length === 0) {
    if (props.imagesCallback) {
        props
            .imagesCallback()
            .then((res) => {
                props.images = res;
            })
            .catch((err) => {
                console.log(err);
            });
    }
}

watch(() => props.isPaused, (newValue) => {
    if (newValue) {
        videoRef.value?.pause(); // Dừng video
    } else {
        videoRef.value?.play(); // Phát video
    }
});
</script>

<style scoped>
@keyframes slideUp {
    0% {
        transform: translateY(100%);
        /* bắt đầu từ phía dưới */
        opacity: 0;
    }

    100% {
        transform: translateY(0);
        animation-delay: 5s;
        opacity: 1;
    }
}

@keyframes slideRight {
    0% {
        transform: translatex(35%);
        /* bắt đầu từ phía dưới */
        scale: .7;
    }

    100% {
        transform: translateY(0);
        /* animation-delay: 5s; */
        scale: 1;
    }
}

.mc-animate {
    animation: slideRight 2s ease;
    animation-fill-mode: both;
}

.text {
    animation: slideUp 2s ease-out;
    animation-delay: 3s;
    /* Thời gian trễ 3 giây */
    animation-fill-mode: both;
    /* Đảm bảo trạng thái ban đầu được giữ */
}

.img-1 {
    animation: slideUp 1.2s ease-out;
    animation-delay: 5s;
    /* Thời gian trễ 3 giây */
    animation-fill-mode: both;
    /* Đảm bảo trạng thái ban đầu được giữ */
}

.img-2 {
    animation: slideUp 1.2s ease-out;
    animation-delay: 8s;
    /* Thời gian trễ 3 giây */
    animation-fill-mode: both;
    /* Đảm bảo trạng thái ban đầu được giữ */
}

.paused {
    animation-play-state: paused;
}
</style>
