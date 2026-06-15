<template>
  <Modal :show="pageData.is_show_facebook_form_modal" alignItems="items-start" maxWidth="xl-6xl" minWidth="w-full"
    @close="closeFacebookFormModal">
    <FacebookForm  @onSuccess="onSuccess" @closeFacebookFormModal="closeFacebookFormModal"/>
  </Modal>

  <Loading v-if="pageData.is_loading" />

</template>
<script setup>
import Loading from "@/Components/Loading.vue";
import Modal from "@/Components/Modal.vue";
import FacebookForm from "@/Pages/Client/Facebook/components/FacebookForm.vue";
import { reactive } from "vue";


defineProps({
  onSuccess: {
        type: Function,
    },
});

const pageData = reactive({
  is_show_facebook_form_modal: false,
  is_loading: false,
});


const openFacebookFormModal = () => {
  pageData.is_show_facebook_form_modal = true;
};


const closeFacebookFormModal = () => {
  pageData.is_show_facebook_form_modal = false;
};

defineExpose({
  openFacebookFormModal,
});
</script>