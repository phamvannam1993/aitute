<template>
  <Master>
    <div
      class="lg:w-5/6 w-full bg-[url('../../public/assets/img/admin/operation.png')]  bg-cover bg-center min-h-screen p-5 lg:p-10">
      <div class="mx-auto sm:px-6 lg:px-8">

        <div class="py-12 px-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div>
            <h1 class="text-[#092A99] font-semibold text-[25px]">
              Lịch sử hoạt động
            </h1>
            <div class="flex flex-col mt-6">
              <div class="py-2 -my-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                <div class="w-full overflow-auto align-middle border-b border-gray-200 shadow sm:rounded-lg">
                  <table class="w-full">
                    <thead>
                      <tr>
                        <th
                          class="px-6 py-3 font-medium leading-4 tracking-wider text-center text-gray-500 uppercase bg-gray-100 border-b border-gray-200">
                          Id</th>
                        <th
                          class="px-6 py-3 font-medium leading-4 tracking-wider text-center text-gray-500 uppercase bg-gray-100 border-b border-gray-200">
                          Event</th>
                        <th
                          class="px-6 py-3 font-medium leading-4 tracking-wider text-center text-gray-500 uppercase bg-gray-100 border-b border-gray-200">
                          Email người dùng</th>
                        <th
                          class="w-1/5 px-6 py-3 font-medium leading-4 tracking-wider text-center text-gray-500 uppercase bg-gray-100 border-b border-gray-200">
                          Dữ liệu cũ</th>
                        <th
                          class="px-6 py-3 font-medium leading-4 tracking-wider text-center text-gray-500 uppercase bg-gray-100 border-b border-gray-200">
                          Dữ liệu mới</th>
                        <th
                          class="px-6 py-3 font-medium leading-4 tracking-wider text-center text-gray-500 uppercase bg-gray-100 border-b border-gray-200">
                          Admin thực hiện</th>
                        <th
                          class="px-6 py-3 font-medium leading-4 tracking-wider text-center text-gray-500 uppercase bg-gray-100 border-b border-gray-200">
                          Ngày thực hiện</th>

                      </tr>
                    </thead>
                    <tbody class="bg-white">
                      <tr v-for="(item, k) in paginateActivityLogs.data" :key="k">
                        <td class="px-6 py-4 border-b border-gray-200">
                          <div class="text-center">
                            <div class="text-center font-medium leading-5 text-gray-900">
                              {{ item.id }}
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 border-b border-gray-200 text-left leading-5 text-gray-500">
                          <p class="line-clamp-2 text-ellipsis overflow-hidden">
                            {{ item.event }}
                          </p>
                        </td>

                        <td class="px-6 py-4 border-b border-gray-200 text-left leading-5 text-gray-500">
                          <p class="line-clamp-2 text-ellipsis overflow-hidden">
                            {{ item.subject_email }}
                          </p>
                        </td>

                        <td class="w-1/5 px-6 py-4 border-b border-gray-200 text-left leading-5 text-gray-500">
                          <p class="mb-3" v-for="(old_value, old_key) in item.properties?.old" :key="old_key">
                            <strong>{{ old_key }}:</strong> {{ old_value }}
                          </p>
                        </td>
                        <td class="px-6 py-4 border-b border-gray-200 text-left leading-5 text-gray-500">
                          <p class="mb-3" v-for="(new_value, new_key) in item.properties?.attributes" :key="new_key">
                            <strong>{{ new_key }}:</strong> {{ new_value }}
                          </p>
                        </td>
                        <td class="px-6 py-4 text-center border-b border-gray-200  leading-5 text-gray-500">
                          <p class="line-clamp-2 text-ellipsis overflow-hidden">
                            {{ item.causer_email }}
                          </p>
                        </td>
                        <td class="px-6 py-4 text-center border-b border-gray-200  leading-5 text-gray-500">
                          <p class="line-clamp-2 text-ellipsis overflow-hidden">
                            {{ $helpers.formatTimestampToYMD(item.created_at) }}
                          </p>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <PaginationLinks class="mt-6" :links="paginateActivityLogs.links" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Master>
</template>

<script setup>
import PaginationLinks from '@/Components/PaginationLinks.vue';
import { router, usePage } from '@inertiajs/vue3';
import { reactive } from 'vue';
import Master from "../../../Layouts/Admin/Master.vue";

const { paginateActivityLogs } = defineProps({ paginateActivityLogs: Object })
const urlParams = new URLSearchParams(window.location.search)
const page = usePage();

const form_search = reactive({
  search: urlParams.has('search') ? urlParams.get('search') : null,
})

const clearSearch = () => {
  form_search.search = null
  submit()
}
const submit = () => {
  router.get(route('admin.users.index'), form_search)
}
</script>
