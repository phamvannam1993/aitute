<template>
  <Master>
    <div
      class="lg:w-5/6 text-gray-700 w-full bg-[url('../../public/assets/img/aiwow/layout_background.png')]  bg-cover bg-center min-h-screen p-5 lg:p-10">
      <div class="mx-auto sm:px-6 lg:px-8">
        <List :paginateImageTemplateCategories="paginateImageTemplateCategories" />
      </div>
    </div>
  </Master>
</template>

<script setup>

import Master from "../../../Layouts/Admin/Master.vue";
import List from "./Components/List.vue";
defineProps({ paginateImageTemplateCategories: Object })


</script>
