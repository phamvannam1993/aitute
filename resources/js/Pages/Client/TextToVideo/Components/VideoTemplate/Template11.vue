<template>
    <div
        :style="{
            backgroundColor: data.theme.slideColor,
        }"
        class="flex flex-col p-4 relative rounded-2xl select-none"
    >
        <div
            :style="{
                backgroundImage: `url(${data.background})`,
                backgroundSize: 'cover',
                backgroundPosition: 'center',
                opacity: 0.5, // Đặt độ mờ cho lớp nền
            }"
            class="absolute inset-0 rounded-2xl pointer-events-none"
        ></div>

        <div>
            <h1 class="mb-4 mt-4 lg:mb-4 lg:mt-12 mx-auto" :class="(isPresent ? 'animat-1' : '', { paused: props.isPaused })">
                <EditableContent v-bind:isTyping="props.isTyping" v-model="data.title" v-model:customStyle="data.title_style" :is-heading="true" :resizeable="true" />
            </h1>
            <div class="absolute top-[40%] left-[50%] text-black">
                <div class="absolute" v-for="externalComponent in data.components">
                    <EditableContent v-if="externalComponent.name === 'EditableContent'" v-model="externalComponent.title" v-model:customStyle="externalComponent.title_style" dragClass="absolute" :resizeable="true">
                        {{ externalComponent.name }}
                    </EditableContent>
                    <EditImage :width="250" :height="250" v-else-if="externalComponent.name === 'EditImage' && externalComponent.src" v-model="externalComponent.src" v-model:customStyle="externalComponent.style" :title="props.data.title" :preview="preview" :draggable="true" :resizable="true">
                        <div
                            :class="`w-full ${externalComponent.style.sharp || 'sharp-square'}`"
                            :style="{
                                backgroundImage: `url(${externalComponent.src})`,
                                backgroundSize: 'cover',
                                backgroundPosition: 'center',
                            }"
                        ></div>
                    </EditImage>
                    <template v-else-if="externalComponent.name === 'EditVideo'">
                        <DragAndResize v-model="externalComponent.style" :preview="preview">
                            <EditPopup v-model="externalComponent.style" type="video" :preview="preview">
                                <div :class="`bg-white overflow-hidden bg-opacity-90 border-2 border-white flex items-center justify-center ${externalComponent.style.animation} ${externalComponent.style.sharp || 'aspect-square'}`" :style="{ width: `${externalComponent.style.width}px` }">
                                    <video class="h-full" style="max-width: max-content" controls muted :autoplay="preview" v-if="externalComponent.style.src">
                                        <source :src="externalComponent.style.src" type="video/mp4" />
                                        Your browser does not support the video tag.
                                    </video>
                                    <div v-else class="pointer-events-none">
                                        <img src="/assets/img/upload_video.png" alt="add" class="w-full" />
                                    </div>
                                </div>
                            </EditPopup>
                        </DragAndResize>
                    </template>
                </div>
            </div>
            <div class="flex justify-between rounded-2xl">
                <template v-if="data?.virtual?.s3_url">
                    <DragAndResize v-model="data.virtual" :preview="preview" customClass="z-50">
                        <EditPopup v-model="data.virtual" type="mc" :preview="preview">
                            <img :autoplay="isPresent" :src="data.virtual.s3_url" :style="{ width: `${data.virtual.width}px` }" :class="`${data.virtual.animation} mx-auto select-none pointer-events-none`" :alt="data.virtual.s3_url" />
                        </EditPopup>
                    </DragAndResize>
                </template>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from "vue";
import components from "../components";
import EditableContent from "@/Pages/Client/TextToVideo/Components/EditableContent/Index.vue";
import EditImage from "@/Pages/Client/TextToVideo/Components/EditImage/Index.vue";
import EditPopup from "@/Pages/Client/TextToVideo/Components/EditPopup/Index.vue";
import DragAndResize from "@/Components/DragAndResize.vue";

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
    virtual: Object,
    preview: Boolean,
});

const background = ref(props.data.background || "");

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
    fontSize: 36,
    color: props.data.theme.headingColor,
    dragStyle: {
        top: 0,
        left: 0,
        width: 500,
        height: 84,
    },
};
props.data.description_style = props.data.description_style || {
    fontFamily: "Be Vietnam Pro",
    fontSize: 16,
    color: props.data.theme.textColor,
    dragStyle: {
        top: 0,
        left: 0,
        width: 500,
        height: 84,
    },
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

@keyframes slideMid {
    0% {
        transform: translateX(75%);
        /* bắt đầu từ phía dưới */
    }

    100% {
        transform: translateX(50%);
        /* animation-delay: 5s; */
    }
}

.mc-animate {
    animation: slideMid 2s ease;
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

.sharp-circle {
    aspect-ratio: 1;
    border-radius: 50%;
}

.sharp-square {
    aspect-ratio: 1;
}

.sharp-video {
    aspect-ratio: 16/9;
}

.ani-left-to-right {
    animation: ani-left-to-right 1s ease-in-out;
}

.ani-right-to-left {
    animation: ani-right-to-left 1s ease-in-out;
}

.ani-top-to-bottom {
    animation: ani-top-to-bottom 1s ease-in-out;
}

.ani-bottom-to-top {
    animation: ani-bottom-to-top 1s ease-in-out;
}

@keyframes ani-left-to-right {
    0% {
        transform: translateX(-300px);
    }
    100% {
        transform: none;
    }
}

@keyframes ani-right-to-left {
    0% {
        transform: translateX(300px);
    }
    100% {
        transform: none;
    }
}

@keyframes ani-top-to-bottom {
    0% {
        transform: translateY(-300px);
    }
    100% {
        transform: none;
    }
}

@keyframes ani-bottom-to-top {
    0% {
        transform: translateY(300px);
    }
    100% {
        transform: none;
    }
}
</style>
