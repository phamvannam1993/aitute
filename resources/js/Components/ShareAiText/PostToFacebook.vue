<template>
    <Modal :show="pageData.is_show_share_link_modal" maxWidth="4xl" @close="closePostToFacebook">
        <form @submit.prevent="submit" class="max-h-screen overflow-auto">
            <div class="p-5">
                <p class="mb-2 flex gap-2">
                    <span>Chọn bài viết để chia sẻ lên Facebook</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 1024 1024"
                        id="facebook">
                        <path fill="#1877f2"
                            d="M1024,512C1024,229.23016,794.76978,0,512,0S0,229.23016,0,512c0,255.554,187.231,467.37012,432,505.77777V660H302V512H432V399.2C432,270.87982,508.43854,200,625.38922,200,681.40765,200,740,210,740,210V336H675.43713C611.83508,336,592,375.46667,592,415.95728V512H734L711.3,660H592v357.77777C836.769,979.37012,1024,767.554,1024,512Z">
                        </path>
                        <path fill="#fff"
                            d="M711.3,660,734,512H592V415.95728C592,375.46667,611.83508,336,675.43713,336H740V210s-58.59235-10-114.61078-10C508.43854,200,432,270.87982,432,399.2V512H302V660H432v357.77777a517.39619,517.39619,0,0,0,160,0V660Z">
                        </path>
                    </svg>
                </p>

                <div class="px-4 py-3 mb-1">
                    <div class="flex flex-1 items-center justify-center space-x-2">
                        <div v-for="(fanpage) in fanpages" :key="fanpage.id" class="flex items-center">
                            <div @click="activeFanpage(fanpage)"
                                :class="form.facebook_fanpage_id == fanpage.id ? 'border-blue-500' : ''"
                                class="cursor-pointer relative flex flex-col items-center border-b-4 p-2">
                                <img :src="fanpage.page_image ? fanpage.page_image : '/assets/img/login_icon/success.png'"
                                    class="w-10 h-10" alt="Avatar" />
                                <span class="text-xs font-medium text-gray-700">{{ fanpage.page_name }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-3 mb-8">
                    <div v-for="(post_step_data, index) in pageData.post_step_datas" :key="index"
                        class="mb-2 px-9 py-4 relative border-2">
                        <input type="checkbox" :value="index" :id="'checkbox_' + index"
                            v-model="pageData.step_ai_selected"
                            class="absolute left-[-7px] top-[40%] w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">

                        <h2 class="mb-3 text-blue-500 text-xl font-medium">{{ post_step_data.name }}</h2>
                        <div class="sm:flex">
                            <label :for="'checkbox_' + index" class="w-4/5">
                                <div class="mb-3 line-clamp-2"
                                    v-html="$helpers.convertHtmlToText(post_step_data.conversations)"></div>
                            </label>
                            <div @click="openPostDetail(post_step_data, index)"
                                class="mb-3 sm:w-1/5 flex sm:flex-col justify-between sm:items-end gap-3">
                                <img src="/assets/img/gallery.png"
                                    class="w-6 h-6 flex items-end justify-end  cursor-pointer" alt="" />
                                <span class=" flex items-end justify-end underline cursor-pointer"><a>Xem chi
                                        tiết</a></span>
                            </div>
                        </div>
                        <div class="flex">
                            Ngày đăng: <span @click="openPostDetail(post_step_data, index)"
                                class="px-1 cursor-pointer underline"> {{
                                    post_step_data.published !== false ? 'Đăng ngay' : post_step_data.scheduled_publish_time
                                }} </span>
                        </div>

                        <div class="show-errors">
                            <div v-if="$page.props.errors['post_datas.' + index + '.scheduled_publish_time']"
                                v-text="$page.props.errors['post_datas.' + index + '.scheduled_publish_time']"
                                class="text-red-500 my-1"></div>
                            <div v-if="$page.props.errors['post_datas.' + index + '.content']"
                                v-text="$page.props.errors['post_datas.' + index + '.content']" class="text-red-500 my-1">
                            </div>
                            <div v-if="$page.props.errors['post_datas.' + index + '.files']"
                                v-text="$page.props.errors['post_datas.' + index + '.files']" class="text-red-500 my-1">
                            </div>
                        </div>
                    </div>

                    <div class="show-errors">
                        <div v-if="$page.props.errors?.post_datas" v-text="$page.props.errors?.post_datas"
                            class="text-red-500 my-1"></div>
                    </div>
                </div>

                <div class="flex justify-between">
                    <div class="flex flex-col justify-start xl:flex-row items-start xl:items-center gap-1">
                        <span class="xl:w-[116px] text-[8px] text-left md:text-[10px] lg:text-[12px] font-medium">Chọn
                            nền
                            tảng khác: </span>
                        <div
                            class="grid grid-cols-4 md:flex md:items-center lg:grid lg:grid-cols-4 xl:flex justify-start flex-1 gap-2">
                            <img src="/assets/img/ai_text/share_social.png"
                                class="h-[16px] md:h-[18px] lg:h-[20px] w-auto" alt="Share" />

                            <img src="/assets/img/ai_text/share_tiktok.png"
                                class="h-[16px] md:h-[18px] lg:h-[20px] w-auto" alt="Share" />
                            <img src="/assets/img/ai_text/share_youtube.png"
                                class="h-[16px] md:h-[18px] lg:h-[20px] w-auto" alt="Share" />
                        </div>
                    </div>
                    <button type="submit" class="font-semibold bg-[#2C75E3] px-4 py-2 text-white rounded-lg">
                        Đăng bài
                    </button>
                    <button @click="toggleCheckAll" class="font-semibold bg-[#2C75E3] px-4 py-2 text-white rounded-lg"
                        type="button">

                        {{
                            (pageData.post_step_datas.length === pageData.step_ai_selected.length) ? 'Bỏ chọn' : 'Chọn tất cả'
                        }}

                    </button>
                </div>

            </div>
            <div v-if="pageData.is_loading"
                class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
                <div class="loader"></div>
            </div>
        </form>
    </Modal>
    <PostDetail ref="postDetailRef" @updatePostDatas="updatePostDatas" />
</template>

<script setup>
import Modal from '@/Components/Modal.vue';
import { usePage } from '@inertiajs/vue3';
import { inject, reactive, ref, watch } from "vue";
import { toast } from "vue3-toastify";
import PostDetail from './PostDetail.vue';
const page = usePage();

const { step_ais, fanpages } = defineProps({
    step_ais: Object,
    fanpages: Array
});

const helpers = inject('helpers')
const postDetailRef = ref(null);

const pageData = reactive({
    is_show_share_link_modal: false,
    is_loading: false,
    post_step_datas: step_ais,
    step_ai_selected: [],
});

const form = reactive({
    facebook_fanpage_id: null,
    post_datas: [],
});

watch(
    () => [pageData.step_ai_selected],
    ([], []) => {
        let post_datas = [];
        for (let index = 0; index < pageData.post_step_datas.length; index++) {
            const element = pageData.post_step_datas[index];
            if (pageData.step_ai_selected.includes(index)) {
                post_datas.push(convertData(element, index))
            }
        }

        form.post_datas = post_datas;
    }
);

function openPostDetail(post_step_data, index) {
    if (postDetailRef.value) {
        let postForm = convertData(post_step_data, index);
        postDetailRef.value.openPostDetail(postForm);
    }
}

function convertData(post_step_data, index) {
    let postForm = {
        name: post_step_data.name,
        index: index,
        content: post_step_data.conversations ? helpers.convertHtmlToText(post_step_data.conversations) : '',
        published: post_step_data.hasOwnProperty('published') ? post_step_data.published : true,
        scheduled_publish_time: post_step_data.scheduled_publish_time ? helpers.formatTimestampToYMD(post_step_data.scheduled_publish_time) : null,
        files: post_step_data.files ? post_step_data.files : [],
    }
    return postForm;
}


function updatePostDatas(updatePostData) {
    let postData = {
        ...updatePostData,
        conversations: updatePostData.content
    }
    pageData.post_step_datas[updatePostData.index] = { ...postData };
    form.post_datas = pageData.post_step_datas.filter((step, index) => pageData.step_ai_selected.includes(index));
}

const activeFirstFanpage = () => {
    if (fanpages.length) {
        activeFanpage(fanpages[0])
    }
};

const activeFanpage = (fanpage) => {
    form.facebook_fanpage_id = fanpage.id
}

const openPostToFacebook = () => {
    page.props.errors = []
    pageData.is_show_share_link_modal = true;
    pageData.post_step_datas = step_ais
    activeFirstFanpage()
};


const toggleCheckAll = () => {
    if (pageData.post_step_datas.length === pageData.step_ai_selected.length) {
        pageData.step_ai_selected = []
    } else {
        let step_ai_selected = [];
        for (let index = 0; index < pageData.post_step_datas.length; index++) {
            step_ai_selected.push(index)
        }
        pageData.step_ai_selected = step_ai_selected
    }
};

const closePostToFacebook = () => {
    pageData.is_show_share_link_modal = false;
};

const submit = async () => {
    try {
        pageData.is_loading = true
        const response = await axios.post(route('social.multi-post-to-facebook'), form)
        toast.success("Đăng bài thành công");
        closePostToFacebook()
    } catch (error) {
        let post_datas_errors = convertMessageKeyErrors(error);
        page.props.errors = post_datas_errors

    } finally {
        pageData.is_loading = false;
    }
}

const convertMessageKeyErrors = (error) => {
    let post_datas_errors = [];
    for (const messageKey in error.response.data.errors) {
        let arrayKey = messageKey.split('.');
        let indexError = form.post_datas[arrayKey[1]].index;
        post_datas_errors[arrayKey[0] + '.' + indexError + '.' + arrayKey[2]] = error.response.data.errors[messageKey];
    }

    return post_datas_errors;
};



defineExpose({
    openPostToFacebook,
});
</script>

<style scoped>
.loader {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #3498db;
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}
</style>