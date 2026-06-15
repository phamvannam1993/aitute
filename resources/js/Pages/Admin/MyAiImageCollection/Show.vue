<template>
  <Master>
    <div
      class="lg:w-5/6 text-gray-700 w-full bg-[url('../../public/assets/img/aiwow/layout_background.png')]  bg-cover bg-center min-h-screen p-5 lg:p-10">

      <div class="mx-auto sm:px-6 lg:px-8">
        <div class="py-12 px-6 mb-5 bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <h1 class="text-[#092A99] font-semibold text-[25px] mb-8">
            Bộ sưu tập
          </h1>
          <div class="grid grid-cols-1 gap-6 mt-4 text-black">

            <div class="flex-col md:flex-row flex items-left gap-5">
              <label class="block text-gray-700 text-sm font-bold w-4/12" for="id">
                Tiêu đề
              </label>
              <div class="w-full">
                {{ imageCollection.title }}
              </div>
            </div>

            <div class="flex-col md:flex-row flex items-left gap-5">
              <label class="block text-gray-700 text-sm font-bold w-4/12" for="id">
                Ảnh
              </label>
              <div class="w-full">
                <img :src="imageCollection.s3_url" class="w-1/4" />
              </div>
            </div>
          </div>
        </div>


      </div>
    </div>
  </Master>
</template>

<script setup>
import Master from "../../../Layouts/Admin/Master.vue";
defineProps({ imageCollection: Object })

</script>
