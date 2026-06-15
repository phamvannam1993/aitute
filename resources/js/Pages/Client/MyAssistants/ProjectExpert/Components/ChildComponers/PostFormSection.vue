<template>
    <form  @submit.prevent="submit" class="relative  py-2 text-xs lg:text-sm text-black">
        <div>
            <div class="flex flex-col lg:flex-row gap-2 mb-4 ">
                <div class="flex w-full lg:w-2/5 font-bold">
                    1. Chọn nền tảng
                </div>
                <div class="w-full">
                    <div class="flex flex-wrap gap-1 justify-between w-full ">
                        <label v-for="(value, key) in SOCIAL_POSTABLE_TYPE" :key="key" class="flex items-center gap-1">
                            <input @change="handleChangeSocialPostableType" type="radio"
                                v-model="social_postable_type" :value="value"
                                class="rounded-full text-orion-orange focus:ring-orion-orange"/>
                            <span>{{ value.replace('Fanpage', '') }}</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="mt-4 mb-7">
                <div v-if="social_postable_type === SOCIAL_POSTABLE_TYPE.FacebookFanpage">
                    <div class="flex flex-wrap flex-1 items-center justify-center space-x-2">
                        <div v-for="(fanpage) in fanpages" :key="fanpage.id" class="flex items-center">
                            <div @click="activeSocialPostableId(fanpage)"
                                :class="social_postable_id == fanpage.id ? 'border-green-500' : ''"
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
            <div class="flex flex-col  gap-2 mb-4 ">
                <div class="flex w-full lg:w-2/5 font-bold">
                    2. Chọn ngày giờ đăng
                </div>
                <div class="w-full flex flex-col gap-3">
                    <div class="flex flex-col sm:flex-row justify-start sm:justify-between gap-5 w-full items-center">
                        <div class="flex gap-1 lg:gap-5  items-center justify-center">
                            <label class="flex items-center gap-1 lg:gap-2 h-full">
                                <input type="radio" v-model="published" :value="0"
                                    class="rounded-full text-orion-orange focus:ring-orion-orange" />
                                <div class="date-picker  w-[160px] sm:w-[200px]">
                                    <VueDatePicker format="dd/MM/yyyy"
                                        :ui="{ input: 'rounded-[10px] border-[#B5B5BE]' }" locale="vi"
                                        :enable-time-picker="false" v-model="select_date" auto-apply
                                        :close-on-auto-apply="false" :min-date="minDate" :max-date="maxDate"
                                        :disabled="published ? true : false"
                                        :day-names="['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN']" />
                                </div>
                                <div class="time-picker  w-[110px] sm:w-[130px]">
                                    <VueDatePicker format="HH:mm" :ui="{ input: 'rounded-[10px] border-[#B5B5BE]' }"
                                        time-picker :disabled="published ? true : false"
                                        v-model="select_time" auto-apply>
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
                                <input type="radio" v-model="published" :value="1" class="rounded-full text-orion-orange focus:ring-orion-orange" />
                                <span :class="published ? '' : 'bg-gray-100'"
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
                <button type="button" @click="handlePostFacebook(1)"
                    class="text-center inline-flex gap-2 items-center justify-center w-44 px-2 py-2 lg:py-3 btn-gray rounded-lg lg:rounded-2xl border border-[#C5C5C5] text-white font-bold">
                    Lưu nháp
                </button>
                <button type="button" @click="handlePostFacebook(2)"
                    class="text-center inline-flex gap-2 items-center justify-center w-44 px-2 py-2 lg:py-3 btn-red rounded-lg lg:rounded-2xl border border-[#C5C5C5] text-white font-bold">
                    Không duyệt
                </button>
                <button type="button" @click="handlePostFacebook(3)"
                    class="min-w-[100px] flex items-center justify-center gap-2 md:min-w-[150px] px-2 md:px-4 py-2 lg:py-3 btn-green text-white rounded-lg lg:rounded-2xl font-bold text-sm">
                    Duyệt bài
                </button>
            </div>
        </div>
    </form>
</template>
<script setup>
import Modal from "@/Components/Modal.vue";
import dayjs from "dayjs";
import VueDatePicker from "@vuepic/vue-datepicker";
import { reactive, ref } from "vue";
import { toast } from "vue3-toastify";
import { SOCIAL_POSTABLE_TYPE } from "@/constants/social_postable";
import FormShareLink from "@/Components/FormShareLink.vue";
const emit = defineEmits([]);
const props = defineProps({
    select_date: {
        type: Date,
        default: new Date()
    },
    select_time: {
        type: Object,
        default: {
            hours: dayjs().add(1, "hour").hour(),
            minutes: dayjs().minute(),
            seconds: dayjs().second(),
        }
    },
    facebookContentDetail: {
        type: Object,
        default: {
            content:""
        }
    }
})
    
const facebookContentDetail = ref(props.facebookContentDetail)
const social_postable_type = ref("FacebookFanpage")
const social_postable_id = ref("")
const published = ref(0)
const select_date = ref(props.facebookContentDetail.select_date)
const select_time = ref(props.facebookContentDetail.select_time)
const template_post_form = ref("")
const fanpages = ref([])
const localData = reactive({
    share_link: null,
    is_show_share_link_modal: false,
});

const getSocialPostableType = async () => {
    const response = await axios.get(route('get-social-postable-type', { social_postable_type: social_postable_type.value }))
    if (social_postable_type.value === SOCIAL_POSTABLE_TYPE.FacebookFanpage) {
        fanpages.value = response.data.data
        if(fanpages.value.length > 0) {
            social_postable_id.value = fanpages.value[0].id
            updateData()
        }
        return true;
    }
};
const handleChangeSocialPostableType = (event) => {
  getSocialPostableType();
}
const activeSocialPostableId = (fanpage) => {
    social_postable_id.value = fanpage.id
}
const handlePostFacebook = (status) => {
    updateData(status)
}
getSocialPostableType();
const updateData = (status = "") => {
    const formData = new FormData()
    formData.append("id", facebookContentDetail.value.id)
    if(social_postable_id.value) {
        formData.append("social_postable_id", social_postable_id.value)
    }
    if(select_date.value) {
        formData.append("post_date", select_date.value)
    }
    if(select_time.value) {
        formData.append("post_time", JSON.stringify(select_time.value))
    }
    if(published.value) {
        formData.append("published", published.value)
    }
    if(status) {
        formData.append("status", status)
    }
    emit('updateFacebookContent', formData, status);
    if(status == 3 && published.value) {
        emit('postFacebook', facebookContentDetail.value.id); 
    }
}
</script>
<style>
    .btn-red {
        background:#FF2F2F;
    }
    .btn-green {
        background:#34C759;
    }
    .btn-gray {
        background:#999999;
    }
</style>