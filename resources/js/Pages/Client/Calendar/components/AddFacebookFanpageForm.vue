<template>
    <form @submit.prevent="submit" class="relative mt-6 md:m-6">
        <h1 class="text-center mb-4">Thêm Page</h1>
        <div v-if="$page.props.errors.message" v-text="$page.props.errors.message" class="text-red-500 my-1">
        </div>
        <div class="max-h-96 overflow-auto">
            <div v-for="(fanpage, k) in fanpages" :key="fanpage.id" class="flex items-center mb-4">
                <input v-model="form.fb_page_ids" :id="'fanpage_' + k" type="checkbox" :value="fanpage.id"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <label :for="'fanpage_' + k" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{
                    fanpage.name }}</label>
            </div>
        </div>


        <div v-if="$page.props.errors.fanpages" v-text="$page.props.errors.fanpages" class="text-red-500 my-1">
        </div>

        <div v-if="pageData.is_loading">.....</div>
        <div v-if="formGetFanpages.paging_next" class="text-center mb-5">
            <a @click="loadMore()" class="cursor-pointer underline">Xem thêm</a>
        </div>

        <div class="text-center mb-4">
            <PrimaryButton>Lưu</PrimaryButton>
        </div>

    </form>
</template>
<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { inject, reactive, onMounted, ref } from 'vue';

const fanpages = ref([]);
const page = usePage();

const facebookIdActived = inject('facebookIdActived');

const pageData = reactive({
    is_loading: false,
});

const formGetFanpages = reactive({
    facebook_id: null,
    paging_next: false,
    paging_cursors_after: '',
});

const form = useForm({
    facebook_id: null,
    fanpages: [],
    fb_page_ids: [],
});

const emit = defineEmits();

onMounted(() => {
    getFanpages()
})

const loadMore = async () => {
    let isLoadMore = true
    getFanpages(isLoadMore)
}

const getFanpages = async (isLoadMore = false) => {
    try {
        pageData.is_loading = true;
        formGetFanpages.facebook_id = facebookIdActived.value
        const response = await axios.get(route('social.facebook.fanpages', formGetFanpages))

        if (isLoadMore) {
            fanpages.value = [...fanpages.value, ...response.data.data];
        } else {
            fanpages.value = response.data.data;
        }

        form.facebook_id = facebookIdActived.value
        formGetFanpages.paging_next = response.data?.paging_next
        formGetFanpages.paging_cursors_after = response.data?.paging_cursors_after
    } catch (error) {
        page.props.errors = error.response.data.errors
    } finally {
        pageData.is_loading = false;
    }
}

const submit = () => {
    fanpages.value.forEach(item => {
        if (form.fb_page_ids.includes(item.id)) {
            form.fanpages.push(item);
        }
    });

    form.post(route('social.facebook.facebook-fanpages.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
            emit('closeAddFacebookFanpageModal');
            emit('activeLastFanpage');
        },
        onError: () => { },
    });
};
</script>