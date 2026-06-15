<template>
    <form @submit.prevent="submit" class="relative  py-2 text-xs lg:text-sm text-black">
        <div>
            <div class="flex flex-col lg:flex-row gap-2 mb-4 ">
                <div class="flex w-full lg:w-2/5 font-bold">
                    1. Chọn nền tảng
                </div>
                <div class="w-full">
                    <div class="flex flex-wrap gap-1 justify-between w-full ">
                        <label v-for="(value, key) in SOCIAL_POSTABLE_TYPE" :key="key" class="flex items-center gap-1">
                            <input @change="handleChangeSocialPostableType" :disabled="!!form.id" type="radio"
                                v-model="form.social_postable_type" :value="value" :class="form.id ? 'bg-gray-200' : ''"
                                class="rounded-full text-orion-orange focus:ring-orion-orange" />
                            <span>{{ value.replace('Fanpage', '') }}</span>
                        </label>
                    </div>
                    <div v-if="$page.props.errors?.social_postable_type"
                        v-text="$page.props.errors?.social_postable_type" class="text-red-500 my-1">
                    </div>
                </div>
            </div>
            <div class="mt-4 mb-7">
                <div v-if="form.social_postable_type === SOCIAL_POSTABLE_TYPE.FacebookFanpage">
                    <div class="flex flex-wrap flex-1 items-center justify-center space-x-2">
                        <div v-for="(fanpage) in pageData.fanpages" :key="fanpage.id" class="flex items-center">
                            <div @click="activeSocialPostableId(fanpage)"
                                :class="form.social_postable_id == fanpage.id ? 'border-green-500' : ''"
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
                        <label v-if="!pageData.onlyAutoPostAi" class="flex items-center gap-1">
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


            <div class="mb-2 flex flex-col md:flex-row md:justify-between items-start md:items-center gap-2">
                <div class="flex w-full lg:w-2/5 font-bold">
                    3. Ngôn ngữ
                </div>
                <div class="w-full">
                    <select v-model="autoForm.lang" class="w-full border-[#B5B5BE] rounded-[10px]">
                        <option v-for="(item, index) in languages" :key="index">
                            {{ item }}
                        </option>
                    </select>
                    <div v-if="$page.props?.errors?.lang" v-text="$page.props?.errors?.lang" class="text-red-500 my-1">
                    </div>
                </div>
            </div>

            <div class="mb-2 flex flex-col md:flex-row md:justify-between items-start md:items-center gap-2">
                <div class="flex w-full lg:w-2/5 font-bold">
                    4. Ngày đăng
                </div>
                <div class="w-full ">
                    <VueDatePicker format="dd/MM/yyyy" :ui="{ input: 'rounded-[10px] border-[#B5B5BE]' }" locale="vi"
                        v-model="autoForm.date_range" range auto-apply :close-on-auto-apply="false"
                        :enable-time-picker="false" :min-date="minDate" :max-date="maxDate"
                        :day-names="['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN']" />

                    <div v-if="$page.props.errors && $page.props.errors['date_range.0']" v-text="$page.props.errors['date_range.0']"
                        class="text-red-500 my-1">
                    </div>
                </div>

            </div>
            <div class="mb-2 flex flex-col md:flex-row md:justify-between items-start md:items-center gap-2">
                <div class="flex w-full lg:w-2/5  font-bold">
                    5. Giờ đăng
                </div>
                <div class="w-full ">
                    <VueDatePicker select-text="Chọn" cancel-text="Đóng" v-model="autoForm.time" time-picker />

                    <div v-if="$page.props.errors && $page.props.errors['time.hours']" v-text="$page.props.errors['time.hours']"
                        class="text-red-500 my-1">
                    </div>
                    <div v-if="$page.props.errors && $page.props.errors['time.minutes']" v-text="$page.props.errors['time.minutes']"
                        class="text-red-500 my-1">
                    </div>
                    <div v-if="$page.props.errors && $page.props.errors['time.seconds']" v-text="$page.props.errors['time.seconds']"
                        class="text-red-500 my-1">
                    </div>
                </div>

            </div>

            <div class="errors">
                <div v-for="(message, field) in $page.props.errors" :key="field" class="text-red-500 my-1">
                    {{ message }}
                </div>
            </div>

            <div class="my-8 flex justify-center items-center gap-6">
                <div v-if="localData.is_loading" class="flex flex-col">
                    <LoadingCircle />
                    <div>
                        Hệ thống đang tự động tạo bài viết.
                        <br />
                        Bạn có thể quay lại xem kết quả tại <a class="text-blue-700" :href="route('calendar')">Quản lý
                            Social</a>
                    </div>
                </div>
                <button v-else type="submit"
                    class="text-center inline-flex gap-2 items-center justify-center w-44 px-2 py-2 lg:py-3 bg-orion-primary rounded-lg lg:rounded-2xl border border-[#C5C5C5] text-white font-bold">
                    <img class="h-5 w-6" src="/assets/img/aiwow/btn-post.png" />
                    Đăng bài
                </button>
            </div>
            <div class="mb-8 border-b-2 border-gray-300"></div>

            <div class="mb-10 ">
                <div class="flex w-full lg:w-2/5  font-bold">
                    6. Xem kết quả bài đăng và hiệu chỉnh
                </div>
            </div>
            <Calendar ref="calendarRef" :form="form" />
        </div>
    </form>

</template>
<script setup>
import LoadingCircle from "@/Components/LoadingCircle.vue";
import VueDatePicker from "@vuepic/vue-datepicker";
import { toast } from "vue3-toastify";
import { SOCIAL_POSTABLE_TYPE } from "@/constants/social_postable";
import dayjs from 'dayjs';
import { useForm, usePage } from '@inertiajs/vue3';
import Calendar from "@/Components/ShareAiText/components/Calendar/Calendar.vue";
import { ref, reactive } from 'vue';
const page = usePage();


const { pageData, form, closePostDetail } = defineProps({
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
    closePostDetail: {
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

const languages = [
    "Tiếng Việt",
    "Tiếng Anh",
    "Tiếng Trung",
    "Tiếng Nhật",
    "Tiếng Hàn",
    "Tiếng Pháp",
    "Tiếng Đức",
];

const startDate = dayjs().startOf('day').format('YYYY/MM/DD HH:mm:ss');
const endDate = dayjs().startOf('day').add(7, 'day').format('YYYY/MM/DD HH:mm:ss');
const minDate = dayjs().startOf('day').format('YYYY/MM/DD HH:mm:ss');
const maxDate = dayjs().startOf('day').add(29, 'day').format('YYYY/MM/DD HH:mm:ss');

const calendarRef = ref(null);


const localData = reactive({
    is_loading: false,
    time_loading: 0,
});

const autoForm = useForm({
    social_postable_id: form.social_postable_id,
    social_postable_type: form.social_postable_type,
    description: pageData?.autoPostAi?.description,
    category: pageData?.autoPostAi?.category,
    project_name: pageData?.autoPostAi?.project_name,
    target: pageData?.autoPostAi?.target,
    options_rewrite: pageData?.autoPostAi?.options_rewrite,
    limit: 1000,
    lang: 'Tiếng Việt',
    date_range: [startDate, endDate],
    time: {
        hours: 0,
        minutes: 0,
        seconds: 0,
    },
});

const submit = async () => {
    try {
        page.props.errors = []

        if(autoForm.date_range){
            autoForm.date_range[0] = dayjs(autoForm.date_range[0]).format('YYYY/MM/DD HH:mm:ss')
            autoForm.date_range[1] = dayjs(autoForm.date_range[1]).format('YYYY/MM/DD HH:mm:ss')
        }
        pageData.is_loading = true;
        localData.is_loading = true;

        await axios.post(route('social.ajax-job-create-content-ai'), autoForm)

        toast.success("Bài viết đang được tạo. Chờ trong giây lát để bài viết được cập nhật", { autoClose: 10000 });
        calendarRef.value.setLoadding(true);
        setFalseLoadding(autoForm)

        closePostDetail();
    } catch (error) {
        console.error(error)
        page.props.errors = error.response.data.errors
        toast.error('Đăng bài thất bại!');
        localData.is_loading = false;
    } finally {
        pageData.is_loading = false;
    }
}

const setFalseLoadding = (autoForm = null) => {
    const startDate = dayjs(autoForm.date_range[0]);
    const endDate = dayjs(autoForm.date_range[1]);
    const diffInDays = endDate.diff(startDate, 'day') + 1;
    const randomNumber = Math.floor(Math.random() * 5) + 1;
    const seconds = diffInDays * 45 + randomNumber;
    calendarRef.value.setTimeLoading(seconds);
    localData.time_loading = seconds

    let count = 0;
    const interval = setInterval(() => {
        calendarRef.value.getCalendar();
        if (count < diffInDays) {
            count++;
        } else {
            clearInterval(interval);
            calendarRef.value.setLoadding(false);
            calendarRef.value.setTimeLoading(0);
            localData.is_loading = false;
        }
    }, 45000);

    const intervalTimeLoading = setInterval(() => {
        if (localData.time_loading > 0) {
            --localData.time_loading
        } else {
            clearInterval(intervalTimeLoading);
        }
    }, 1000);


}


</script>