<template>
    <header class="rounded-[25px] border-b-[1px] border-[#ddd] shadow-lg bg-white sticky top-0 z-10 px-3">
        <div class="flex items-center justify-between gap-2 h-[140px] overflow-auto">
                <div class="flex gap-2">
                    <div v-for="(fanpage) in fanpages" :key="fanpage.id" class="flex items-center box-item-fb">
                        <div @click="activeFanpage(fanpage)"
                            :class="facebookFanpageIdActived == fanpage.id ? 'border-[#FFA411]' : ''"
                            class="cursor-pointer relative flex flex-col items-center border-b-4 p-2">
                            <img src="/assets/img/icon/delete.png" class="icon-delete" @click="deletePage(fanpage.id)"/>
                            <img :src="fanpage.page_image ? fanpage.page_image : '/assets/img/login_icon/success.png'"
                                class="w-10 h-10" alt="Avatar" />
                            <span class="text-xs font-medium text-gray-700 page-name">{{ fanpage.page_name }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center">
                    <div @click="checkUserFacebook"
                        class="cursor-pointer flex flex-col items-center justify-center border-b-4 border-transparent gap-2 bg-orion-orange-hover rounded-[18px] w-[100px] h-[100px]">
                        <img src="/assets/img/orion/icon/add-page.svg" class="w-[32px] lg:w-[48px] h-auto"/>
                        <span class="text-[12px] lg:text-[16px] font-medium">Thêm trang</span>
                    </div>
                </div>
        </div>
    </header>
    <div class="flex items-center justify-center gap-4 md:gap-8 my-4">
            <button
                class="w-[200px] flex gap-1 items-center justify-center px-4 py-2 bg-ai3goc-primary text-white rounded-[10px] text-[12px] lg:text-[15px] font-semibold"
                @click="openPostAI">
                <img src="/assets/img/orion/icon/create_post-white.svg" class="w-[16px] lg:w-[26px] h-auto"/>
                <span class="">Tạo bài tự động</span>
            </button>

            <button
                class="w-[200px] flex gap-1 items-center justify-center px-4 py-2 bg-ai3goc-sec text-white rounded-[10px] text-[12px] lg:text-[15px] font-semibold"
                @click="openPostForm">
                <img src="/assets/img/orion/icon/create_post-white.svg" class="w-[16px] lg:w-[26px] h-auto"/>
                <span class="">Tạo bài viết</span>
            </button>
    </div>
    <Modal :show="isShowAddFacebookFanpageModal" @close="closeAddFacebookFanpageModal">
        <AddFacebookFanpageForm @closeAddFacebookFanpageModal="closeAddFacebookFanpageModal"
            @activeLastFanpage="activeLastFanpage" />
    </Modal>

    <PopupConfirmDelete @confirmDelete="confirmDelete" :message="message" @close="showDelete=false" v-if="showDelete">
    </PopupConfirmDelete>
</template>
<script setup>
import Modal from '@/Components/Modal.vue';
import PopupConfirmDelete from '@/Components/PopupConfirmDelete.vue';
import { usePage } from "@inertiajs/vue3";
import { toast } from "vue3-toastify";
import { defineEmits, inject, onMounted, reactive, ref } from 'vue';
import AddFacebookFanpageForm from './AddFacebookFanpageForm.vue';
const { facebooks, fanpages } = defineProps({ fanpages: Array, facebooks: Array });
const page = usePage();
const isShowAddFacebookFanpageModal = ref(false);
const emit = defineEmits(['openPostForm', 'openPostAI']);
const facebookFanpageIdActived = inject('facebookFanpageIdActived');
const facebookIdActived = inject('facebookIdActived');
const scopedData = reactive({
    facebookActived: null,
});

onMounted(() => {
    if (facebooks.length) {
        activeFacebook(facebooks[0])
        if (page.props.flash?.data?.show_add_facebook_fanpage_modal) {
            showAddFacebookFanpageModal()
        }
    }

    if (fanpages.length) {
        activeFanpage(fanpages[0])
        fanpages.value = fanpages
    }
})

const openPostForm = () => {
    emit('openPostForm');
};

const openPostAI = () => {
    emit('openPostAI');
};

const activeFacebook = (facebook) => {
    facebookIdActived.value = facebook.id
    scopedData.facebookActived = facebook
}
const pageDeleteId = ref(0)
const showDelete = ref(false)
const message = ref("Bạn có chắc chắc xóa page này?")
const deletePage = (pageId) => {
    showDelete.value = true
    pageDeleteId.value = pageId
}

const confirmDelete = async() => {
    const response = await axios.post(
        route("social.facebook.deletePage"),
        {
            pageId: pageDeleteId.value
        }
    );
    if(response.data.success) {
        toast.success(response.data.message)
        window.location.href = ""
    } else {
        toast.error(response.data.message)
    }
    showDelete.value = false
}

const activeLastFanpage = () => {
    if (fanpages.length) {
        activeFanpage(fanpages[fanpages.length - 1])
    }
};

const activeFanpage = (fanpage) => {
    facebookFanpageIdActived.value = fanpage.id
}

const checkUserFacebook = () => {
    if (scopedData.facebookActived?.user_access_token) {
        showAddFacebookFanpageModal()
    } else {
        window.location.href = route('social.facebook.request-access-token', { 'redirect-to': '/calendar', 'show-add-facebook-fanpage-modal': 1 });
    }
}

const showAddFacebookFanpageModal = () => {
    page.props.errors = []
    isShowAddFacebookFanpageModal.value = true;
};

const closeAddFacebookFanpageModal = () => {
    isShowAddFacebookFanpageModal.value = false;
};

</script>
<style>
    .box-item-fb {
        width: 100px;
        background: #F0F0F0;
        justify-content: center;
        border-radius: 10px;
    }
    .box-item-fb .icon-delete {
        width: 15px;
        margin-left: 70px;
    }
    .page-name {
        width: 90px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        text-align: center;
    }
</style>
