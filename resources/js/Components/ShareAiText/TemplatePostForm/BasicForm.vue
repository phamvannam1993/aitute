<template>
    <form v-if="pageData.is_show_post_detail_modal" @submit.prevent="submit" class="relative  py-2 text-xs lg:text-sm text-black">
        <div>
            <div class="flex flex-col lg:flex-row gap-2 mb-4 ">
                <div class="flex w-full lg:w-2/5 font-bold">
                    1. Chọn fanpage đăng bài
                </div>
<!--                <div class="w-full">-->
<!--                    <div class="flex flex-wrap gap-1 justify-between w-full ">-->
<!--                        <label v-for="(value, key) in SOCIAL_POSTABLE_TYPE" :key="key" class="flex items-center gap-1">-->
<!--                            <input @change="handleChangeSocialPostableType" :disabled="!!form.id" type="radio"-->
<!--                                v-model="form.social_postable_type" :value="value" :class="form.id ? 'bg-gray-200' : ''"-->
<!--                                class="rounded-full text-orion-orange focus:ring-orion-orange"/>-->
<!--                            <span>{{ value.replace('Fanpage', '') }}</span>-->
<!--                        </label>-->
<!--                    </div>-->
<!--                    <div v-if="$page.props.errors?.social_postable_type"-->
<!--                        v-text="$page.props.errors?.social_postable_type" class="text-red-500 my-1">-->
<!--                    </div>-->
<!--                </div>-->
            </div>
            <div class="mt-4 mb-7">
                <div v-if="form.social_postable_type === SOCIAL_POSTABLE_TYPE.FacebookFanpage">
                    <div class="flex flex-wrap flex-1 items-center justify-center space-x-2">
                        <div v-for="(fanpage) in pageData.fanpages" :key="fanpage.id" class="flex items-center">
                            <div @click="activeSocialPostableId(fanpage)"
                                :class="form.social_postable_id == fanpage.id ? 'border-[#FFA811]' : ''"
                                class="cursor-pointer relative  border-b-4 p-2">
                                <a class="flex flex-col items-center gap-1" :title="fanpage.page_name">
                                    <img :src="fanpage.page_image ? fanpage.page_image : '/assets/img/login_icon/success.png'"
                                        class="w-8 lg:w-10 aspect-square" alt="Avatar" />
                                    <span class="text-xs lg:text-sm font-medium text-gray-700">{{
                                        $helpers.truncateToTwoWords(fanpage.page_name) }}</span>
                                </a>
                            </div>

                        </div>
                    </div>
                    <div v-if="$page.props.errors?.social_postable_id" v-text="$page.props.errors?.social_postable_id"
                        class="text-red-500 my-1">
                    </div>
                </div>
            </div>
            <div class="flex flex-col lg:flex-row gap-2 mb-4 ">
                <div class="flex w-full lg:w-2/5 font-bold">
                    2. Chọn chế độ đăng bài
                </div>
                <div class="w-full">
                    <div class="flex flex-wrap gap-1 justify-between w-full ">
                        <label class="flex items-center gap-1">
                            <input type="radio" v-model="pageData.template_post_form" value="BasicForm"
                                class="rounded-full text-orion-orange focus:ring-orion-orange" />
                            <span>Chế độ đăng bài theo ngày</span>
                        </label>
                        <label class="flex items-center gap-1">
                            <input type="radio" v-model="pageData.template_post_form" value="AutoPostAIForm"
                                class="rounded-full text-orion-orange focus:ring-orion-orange" />
                            <span>Chế độ đăng bài tự động</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="flex flex-col  gap-2 mb-4 ">
                <div class="flex w-full lg:w-2/5 font-bold">
                    3. Chọn ngày giờ đăng
                </div>
                <div class="w-full flex flex-col gap-3">
                    <div class="flex flex-col sm:flex-row justify-start sm:justify-between gap-5 w-full items-center">
                        <div class="flex gap-1 lg:gap-5  items-center justify-center">
                            <label class="flex items-center gap-1 lg:gap-2 h-full">
                                <input type="radio" v-model="form.published" :value="0"
                                    class="rounded-full text-orion-orange focus:ring-orion-orange" />
                                <div class="date-picker  w-[160px] sm:w-[200px]">
                                    <VueDatePicker format="dd/MM/yyyy"
                                        :ui="{ input: 'rounded-[10px] border-[#B5B5BE]' }" locale="vi"
                                        :enable-time-picker="false" v-model="pageData.select_date" auto-apply
                                        :close-on-auto-apply="false" :min-date="minDate" :max-date="maxDate"
                                        :disabled="form.published ? true : false"
                                        :day-names="['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN']" />
                                </div>
                                <div class="time-picker  w-[110px] sm:w-[130px]">
                                    <VueDatePicker format="HH:mm" :ui="{ input: 'rounded-[10px] border-[#B5B5BE]' }"
                                        time-picker :disabled="form.published ? true : false"
                                        v-model="pageData.select_time" auto-apply>
                                        <template #input-icon>
                                            <img class="w-5 ml-3" src="/assets/svgs/icon-clock.svg" />
                                        </template>
                                    </VueDatePicker>
                                </div>
                            </label>
                        </div>
                        <div class="flex  justify-start sm:justify-between gap-2 items-center ">
                            <span class="text-gray-500 italic">hoặc</span>
                            <label class="flex items-center gap-2">
                                <input type="radio" v-model="form.published" :value="1" class="rounded-full text-orion-orange focus:ring-orion-orange" />
                                <span :class="form.published ? '' : 'bg-gray-100'"
                                    class="py-1 px-4 border border-gray-500 rounded-full">Đăng
                                    bài
                                    ngay</span>
                            </label>
                        </div>
                    </div>
                    <div v-if="$page.props.errors?.scheduled_publish_time"
                        v-text="$page.props.errors?.scheduled_publish_time" class="text-red-500 my-1">
                    </div>
                </div>
            </div>

            <div class="mb-8 border-b-2 border-gray-300"></div>
            <div class="errors">
                <div v-for="(message, field) in $page.props.errors" :key="field" class="text-red-500 my-1">
                {{ message }}
                </div>
            </div>

            <div  class="my-8 flex justify-center items-center gap-6">
                <button type="submit"
                    class="text-center inline-flex gap-2 items-center justify-center w-44 px-2 py-2 lg:py-3 bg-ai3goc-bgclr rounded-lg lg:rounded-2xl border border-[#C5C5C5] text-white font-bold">
                    <img class="h-5 w-6" src="/assets/img/aiwow/btn-post.png" />
                    Đăng bài
                </button>
                <button type="button" @click="shareAIContent()"
                    class="min-w-[100px] flex items-center justify-center gap-2 md:min-w-[150px] px-2 md:px-4 py-2 lg:py-3 bg-ai3goc-primary text-white rounded-lg lg:rounded-2xl font-bold text-sm">
                    <img src="/assets/img/my_assistant/share.png" class="w-5 xs:block hidden" alt="">
                    Chia sẻ
                </button>
            </div>
        </div>
    </form>

    <Modal :show="localData.is_show_share_link_modal" maxWidth="4xl" @close="closeShareLink" align-items="items-center">
        <FormShareLink :shareLink="localData.share_link" />
    </Modal>

</template>
<script setup>
import Modal from "@/Components/Modal.vue";

import VueDatePicker from "@vuepic/vue-datepicker";
import { reactive } from "vue";
import { toast } from "vue3-toastify";
import { SOCIAL_POSTABLE_TYPE } from "@/constants/social_postable";
import FormShareLink from "@/Components/FormShareLink.vue";

const { pageData, form } = defineProps({
    pageData: {
        type: Object,
        required: true,

    },
    form: {
        type: Object,
        required: true,
    },
    minDate: {
        type: String,
        required: true,
    },
    maxDate: {
        type: String,
        required: true,
    },
    MAX_IMAGE_FILES: {
        type: Number,
        required: true,
    },
    handleChangeSocialPostableType: {
        type: Function,
        required: true,
    },
    handleImageUpload: {
        type: Function,
        required: true,
    },
    openTextToImage: {
        type: Function,
        required: true,
    },
    handleVideoUpload: {
        type: Function,
        required: true,
    },
    checkImage: {
        type: Function,
        required: true,
    },
    removeImage: {
        type: Function,
        required: true,
    },
    selectImage: {
        type: Function,
        required: true,
    },
    checkVideo: {
        type: Function,
        required: true,
    },
    removeVideo: {
        type: Function,
        required: true,
    },
    selectVideo: {
        type: Function,
        required: true,
    },
    saveGenerationResult: {
        type: Function,
        required: true,
    },
    closeTextToImage: {
        type: Function,
        required: true,
    },
    onSubmitTextToImage: {
        type: Function,
        required: true,
    },
    onFinishTextToImage: {
        type: Function,
        required: true,
    },
    activeSocialPostableId: {
        type: Function,
        required: true,
    },
    handleChangeTemplatePostForm: {
        type: Function,
        required: true,
    },
    submit: {
        type: Function,
        required: true,
    },
});

const localData = reactive({
    share_link: null,
    is_show_share_link_modal: false,
});

const shareAIContent = async () => {
    try {
        pageData.is_loading = true;
        let formData = getFormData();
        const social = await axios.post(route("social.store"), formData);
        const response = await axios.post(route("share-link.store"), {
            share_linkable_id: social.data?.data?.id,
            share_linkable_type: "SocialPost",
        });

        localData.share_link = route("share-link.show", { token: response.data.data.link_token });
        openShareLink();

    } catch (error) {
        console.log(error)
        toast.error("Chia sẻ tin thất bại");
    } finally {
        pageData.is_loading = false;
    }
};


const getFormData = () => {
  const formData = new FormData();
  const files = [
    ...pageData.image_select_files.map((item) => item.file),
    ...pageData.video_select_files.map((item) => item.file),
  ];

  const appendToFormData = (key, value) => formData.append(key, value);

  appendToFormData('content', form.content);

  files.forEach((file, index) => {
    if ('s3_url' in file) {
      appendToFormData(`files[${index}][s3_url]`, file.s3_url);
      appendToFormData(`files[${index}][type]`, file.type);
    } else {
      appendToFormData(`files[${index}]`, file);
    }
  });

  return formData;
};


const openShareLink = () => {
    localData.is_show_share_link_modal = true;
};

const closeShareLink = () => {
    localData.is_show_share_link_modal = false;
};
</script>
