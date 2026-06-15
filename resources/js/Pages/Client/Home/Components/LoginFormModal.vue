    <template>
        <Modal :show="pageData.is_show_post_detail_modal" customClass='md:w-[524px] w-full' maxWidth="xl-4xl"
            minWidth="w-full" @close="closeModal">
            <LoginForm @openModalActionActivate = "openActivateAccount" />
        </Modal>
        <ActivateAccount
            :isVisible="showActivatePopup"
            @close="closeActivateAccount"
            @activate="handleActivate"
            :isLoading="isLoading"
        />
    </template>

<script setup>
import Modal from '@/Components/Modal.vue';
import LoginForm from '@/Pages/Auth/LoginForm.vue';
import ActivateAccount from "./ActivateAccount.vue";
import { toast } from "vue3-toastify";
import {ref, reactive} from 'vue'
const pageData = reactive({
    is_show_post_detail_modal: false,
});

const openModal = () => {
    pageData.is_show_post_detail_modal = true;
};

const closeModal = () => {
    pageData.is_show_post_detail_modal = false;
};

const showActivatePopup = ref(false);
const openActivateAccount = () => {
    closeModal();
    showActivatePopup.value = true;
};

const closeActivateAccount = () => {
    showActivatePopup.value = false;
};
const isLoading = ref(false);
const handleActivate = async (payload) => {
    try {
        isLoading.value = true;
        const response = await axios.post('/activate-account', payload);
        console.log(response.data.message);
        window.location.href = response.data.redirect;
        showActivatePopup.value = false;
    } catch (error) {
        console.error("Lỗi kích hoạt tài khoản:", error);
        if (error.response && error.response.data.errors) {
            const errors = error.response.data.errors;
            for (const field in errors) {
                errors[field].forEach((message) => {
                    toast.error(message);
                });
            }
        } else {
            toast.error(error.response.data.message);
            console.error("Đã xảy ra lỗi không xác định. Vui lòng thử lại.");
        }
    } finally {
        isLoading.value = false;
    }
};

defineExpose({
    openModal,
});

</script>
