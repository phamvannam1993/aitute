<template>
    <div :style="{ backgroundColor: data.theme.slideColor }" class="flex items-center rounded-2xl relative">
        <div class="flex items-center justify-center w-[40%] h-full relative">
            <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-gray-200 rounded-tl-2xl rounded-bl-2xl">
                <span class="text-gray-500">Loading...</span>
            </div>
            <EditImage :model-value="props.images[0]" @update:model-value="val => props.images[0] = val"
                        :active="active" :is-present="isPresent" :title="props.data.title" class="z-10">
                <img v-if="props.images[0]" :src="props.images[0]" alt="image"
                    class="w-[20rem] h-[20rem] object-cover rounded-tl-xl rounded-br-xl relative"
                    :class="randomAnimation()" @load="onImageLoad" @error="onImageError" />
            </EditImage>
            <div class="absolute inset-0 overflow-hidden rounded-tl-2xl rounded-bl-2xl">
                <img v-if="!loading" :src="props.images[0]" class="h-full z-[1] blur-[5rem]"  alt="">
            </div>
        </div>
        <div class="z-[50] flex absolute top-[40%] left-[50%] -translate-x-1/2 -translate-y-1/2 text-black">
            <component v-for="externalComponent in data.components" :is="components[externalComponent.name]" v-model="externalComponent.title" v-model:customStyle="externalComponent.title_style"
                       dragClass="absolute" wrapperClass="w-0 h-0 "
                       :resizeable="true"
            >
                {{ externalComponent.name }}
            </component>
        </div>
        <div class="w-3/5 mx-12">
            <h1 class="mb-4">
                <editable-content v-bind:isTyping="props.isTyping" v-model="data.title" v-model:customStyle="data.title_style" :is-heading="true" />
            </h1>
            <div v-for="(detail, index) in props.data.descriptions">
                <div class="flex items-center">
                    <div>
                        <p>
                            <editable-content v-bind:isTyping="props.isTyping" v-model="detail.sub_title" v-model:customStyle="data.subtitle_style" :is-heading="true" />
                        </p>
                        <!-- <p>
                            <editable-content v-bind:isTyping="props.isTyping" v-model="detail.description" v-model:customStyle="data.description_style" />
                        </p> -->
                    </div>
                </div>
            </div>
        </div>
        <div v-if="props?.virtual?.s3_url" class="w-1/2 absolute bottom-12 -right-52 mc-animate">
            <img :src="props.virtual.s3_url" :alt="props.virtual.s3_url" class="w-1/3"/>
        </div>
    </div>
</template>

<script setup>
import {ref} from "vue";
import EditableContent from "@/Pages/Client/TextToVideo/Components/EditableContent/Index.vue";
import { randomAnimation } from "@/Pages/Client/TextToVideo/Animation.js";
import EditImage from "@/Pages/Client/TextToVideo/Components/EditImage/Index.vue";
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
    virtual: Object,
});

const loading = ref(true);

const onImageLoad = () => {
    loading.value = false;
};

const onImageError = () => {
    console.error("Image failed to load");
    loading.value = false;
};

props.data.title_style = props.data.title_style
    ? props.data.title_style
    : {
          fontFamily: "Be Vietnam Pro",
          fontSize: 48,
          color: props.data.theme.headingColor,
      };
props.data.description_style = props.data.description_style
    ? props.data.description_style
    : {
          fontFamily: "Be Vietnam Pro",
          fontSize: 16,
          color: props.data.theme.textColor,
      };
props.data.subtitle_style = props.data.subtitle_style
    ? props.data.subtitle_style
    : {
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

<style scoped></style>
