<template>
    <div class="flex justify-center gap-4 w-full flex-wrap md:flex-nowrap">
        <div ref="resultBox"
            class="bg-white w-full rounded-[10px] md:rounded-[25px] lg:w-4/5 p-[12px] md:p-[25px] h-fit">
            <div class="flex justify-start items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 1024 1024" id="facebook">
                    <path fill="#1877f2"
                        d="M1024,512C1024,229.23016,794.76978,0,512,0S0,229.23016,0,512c0,255.554,187.231,467.37012,432,505.77777V660H302V512H432V399.2C432,270.87982,508.43854,200,625.38922,200,681.40765,200,740,210,740,210V336H675.43713C611.83508,336,592,375.46667,592,415.95728V512H734L711.3,660H592v357.77777C836.769,979.37012,1024,767.554,1024,512Z">
                    </path>
                    <path fill="#fff"
                        d="M711.3,660,734,512H592V415.95728C592,375.46667,611.83508,336,675.43713,336H740V210s-58.59235-10-114.61078-10C508.43854,200,432,270.87982,432,399.2V512H302V660H432v357.77777a517.39619,517.39619,0,0,0,160,0V660Z">
                    </path>
                </svg>
                <div>
                    <h2 class="text-black font-bold text-[30px] leading-[32px] line-clamp-1">
                        Facebook
                    </h2>

                </div>
            </div>

            <form @submit.prevent="submit" class="mt-6">
                <div v-if="$page.props.errors?.message" v-text="$page.props.errors?.message" class="text-red-500 my-1">
                </div>

                <div class="relative mb-5">
                    <label for="image-description" class="text-black font-semibold text-[14px]">
                        App ID
                    </label>
                    <div class="relative">
                        <input id="image-description" v-model="form.app_id" type="text" placeholder="App ID"
                            class="w-full p-3 mt-1 border text-black border-[#2C75E3] rounded-[10px] shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

                    </div>
                    <div v-if="$page.props.errors?.app_id" v-text="$page.props.errors?.app_id"
                        class="text-red-500 my-1">
                    </div>
                </div>
                <div class="relative mb-5">
                    <label for="image-description" class="text-black font-semibold text-[14px]">
                        App Secret
                    </label>
                    <div class="relative">
                        <input id="image-description" v-model="form.app_secret" type="text" placeholder="App Secret"
                            class="w-full p-3 mt-1 border text-black border-[#2C75E3] rounded-[10px] shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

                    </div>
                    <div v-if="$page.props.errors?.app_secret" v-text="$page.props.errors?.app_secret"
                        class="text-red-500 my-1">
                    </div>
                </div>

                <div class="relative mb-5">
                    <p class="text-center  font-semibold text-xl text-blue-600">
                        <a target="_blank" :href="route('social.facebook.document')"> Bấm vào đây để xem hướng dẫn tạo
                            App ID và App Secret</a>
                    </p>

                </div>

                <div class=" flex justify-center">
                    <button type="submit"
                        class="px-4 py-2 bg-orion-primary text-white rounded-[10px] min-w-36 md:w-[180px] text-[15px] font-bold disabled:opacity-50 disabled:pointer-events-none">
                        Lưu
                    </button>
                </div>
            </form>
        </div>
        <Loading v-if="pageData.is_loading" />
    </div>

</template>
<script setup>
import Loading from "@/Components/Loading.vue";
import { usePage } from "@inertiajs/vue3";
import { reactive } from "vue";
import { toast } from "vue3-toastify";

const { facebook } = defineProps({
    facebook: {
        type: Object,
    },
});

const emit = defineEmits(['onSuccess', 'closeFacebookFormModal']);

const pageData = reactive({
    is_loading: false,
});

const form = reactive({
    id: facebook?.id ?? null,
    app_id: facebook?.app_id ?? null,
    app_secret: facebook?.app_secret ?? null,
});
const page = usePage();

const submit = async () => {
    try {
        emit('beforeSubmit');
        pageData.is_loading = true;
        await axios.post(route('social.facebook.store'), form)
        window.open(route('social.facebook.request-access-token', { 'redirect-to': '/calendar', 'show-add-facebook-fanpage-modal': 1 }), '_blank');
        toast.success("Lưu thành công");
        emit('onSuccess');
        emit('closeFacebookFormModal');
    } catch (error) {
        console.error(error)
        page.props.errors = error.response.data?.errors
        toast.error('Lưu thất bại!');
    } finally {
        pageData.is_loading = false;
    }
}


</script>