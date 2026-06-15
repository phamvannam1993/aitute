<template>
  <Master>
    <div
      class="lg:w-5/6 w-full bg-[url('../../public/assets/img/aiwow/layout_background.png')]  bg-cover bg-center min-h-screen p-5 lg:p-10">
      <div class="mx-auto sm:px-6 lg:px-8">
        <div class="py-12 px-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div>
            <div>
              <form @submit.prevent="submit" class="flex justify-center gap-2 w-full">
                <div class="w-1/2 relative">
                  <input v-model="form_search.search" placeholder="Tìm kiếm..."
                    class="w-full border p-2 border-gray-500 text-black rounded-md focus:border-indigo-600 focus:ring focus:ring-opacity-40 focus:ring-indigo-500"
                    type="text" />
                  <button v-if="form_search.search" @click="clearSearch" type="button"
                    class="absolute right-0 px-4 py-2 text-gray-800 bg-white-800 rounded-md focus:outline-none">X</button>
                </div>
                <button type="submit"
                  class="px-4 py-2 text-gray-200 bg-gray-800 rounded-md hover:bg-gray-700 focus:outline-none focus:bg-gray-700">Tìm
                  kiếm</button>
              </form>
            </div>

            <div class="flex justify-end text-black mt-6">
              <div class="flex gap-2">
                <a v-if="can(['my-ai-image-collections.create'])"
                  class="inline-flex h-12 items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                  :href="route('admin.my-ai-image-collections.create')">Tạo mới</a>
              </div>
            </div>

            <div class="flex  text-black mt-6">
              Số lượng: {{ paginateCollections.total }}
            </div>

            <div class="flex flex-col text-black">
              <div class="py-2 -my-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                <div ref="tableRef"
                  class="w-full overflow-hidden align-middle border-b border-gray-200 shadow sm:rounded-lg">
                  <DataTable :key="forceUpdateDataTable" :options="optionsDataTable" class="display">
                    <thead>
                      <tr>
                        <th
                          class="px-6 py-3 font-medium leading-4 tracking-wider text-center text-gray-500 uppercase bg-gray-100 border-b border-gray-200">
                          Id</th>
                        <th
                          class="px-6 py-3 font-medium leading-4 tracking-wider text-center text-gray-500 uppercase bg-gray-100 border-b border-gray-200">
                          Tên</th>
                        <th
                          class="px-6 py-3 font-medium leading-4 tracking-wider text-center text-gray-500 uppercase bg-gray-100 border-b border-gray-200">
                          Thao tác</th>
                      </tr>
                    </thead>
                    <tbody class="bg-white">
                      <tr v-for="(item, k) in paginateCollections.data" :key="k">
                        <td class="px-6 py-4 border-b border-gray-200">
                          <div class="text-center">
                            <div class="text-center font-medium leading-5 text-gray-900">
                              {{ item.id }}
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 border-b border-gray-200 text-left leading-5 text-gray-500">
                          <p class="line-clamp-2 text-ellipsis overflow-hidden">
                            {{ item.title }}
                          </p>
                        </td>
                        <td>
                          <div class="text-center flex gap-2 flex-row font-medium leading-5 border-b border-gray-200">
                            <a v-if="can(['my-ai-image-collections.edit'])"
                              :href="route('admin.my-ai-image-collections.show', { id: item.id })"
                              class=" text-green-600 hover:text-indigo-900">Xem</a>
                            <a v-if="can(['my-ai-image-collections.edit'])"
                              :href="route('admin.my-ai-image-collections.edit', { id: item.id })"
                              class=" text-indigo-600 hover:text-indigo-900">Sửa</a>
                            <a v-if="can(['my-ai-image-collections.destroy'])" :data-id="item.id"
                              class="text-red-600 cursor-pointer remove-collection hover:text-red-900">Xoá</a>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </DataTable>
                  <PaginationLinks class="mt-6" :links="paginateCollections.links" />
                </div>
              </div>
            </div>
          </div>


          <Modal :show="modalConfirmDeleteCollection.isShow" @close="closeModal">
            <div class="p-12 text-center">
              <p> Bạn có muốn xóa bộ sưu tập này không?</p>
              <div v-if="$page.props.errors.message" class="mt-5 text-red-500">
                {{ $page.props.errors.message }}
              </div>
              <div class="mt-10 m-auto flex justify-between w-1/2">
                <button class="cursor-pointer text-red-600 hover:text-red-900" @click="submitDeleteLesson">
                  Xoá
                </button>
                <button class="cursor-pointer" @click="closeModal"> Huỷ </button>
              </div>
            </div>
          </Modal>

        </div>
      </div>
    </div>
  </Master>
</template>

<script setup>
import Modal from '@/Components/Modal.vue';
import PaginationLinks from '@/Components/PaginationLinks.vue';
import { router, usePage } from '@inertiajs/vue3';
import { reactive, ref, onMounted } from 'vue';
import Master from "../../../Layouts/Admin/Master.vue";
import { useAuthorization } from './../../../lib/useAuthorization';
import DataTable from 'datatables.net-vue3'
import DataTablesLib from 'datatables.net';
import 'datatables.net-responsive'

DataTable.use(DataTablesLib)

const { can } = useAuthorization();
const { paginateCollections } = defineProps({ paginateCollections: Object })
const urlParams = new URLSearchParams(window.location.search)
const page = usePage();
const tableRef = ref(null);
const forceUpdateDataTable = ref(0)
const optionsDataTable = {
  responsive: true,
  searching: false,
  paging: false,
  lengthChange: false,
  info: false,
  ordering: false,
}

const modalConfirmDeleteCollection = reactive({
  isShow: false,
})

const form_search = reactive({
  search: urlParams.has('search') ? urlParams.get('search') : null,
})

const form_delete = reactive({
  collection_id: null,
  _method: 'DELETE',
})

const closeModal = () => {
  form_delete.collection_id = null
  modalConfirmDeleteCollection.isShow = false
}

const confirmDeleteCollection = (collection_id) => {
  page.props.errors = []
  form_delete.collection_id = collection_id
  modalConfirmDeleteCollection.isShow = true
}

const submitDeleteLesson = () => {
  router.post(route('admin.my-ai-image-collections.destroy', { id: form_delete.collection_id }), form_delete, {
    onSuccess: (page) => {
      modalConfirmDeleteCollection.isShow = false
      forceUpdateDataTable.value++
    },
  })
}

const clearSearch = () => {
  form_search.search = null
  submit()
}

const submit = () => {
  router.get(route('admin.my-ai-image-collections.index'), form_search)
}

const onClickRemoveCollection = () => {
  tableRef.value.addEventListener('click', (event) => {
    const target = event.target;
    if (target.classList.contains('remove-collection')) {
      const collectionId = target.getAttribute('data-id');
      confirmDeleteCollection(collectionId);
    }
  });
}

onMounted(() => {
  onClickRemoveCollection()
})

</script>
<style>
@import 'datatables.net-dt';
@import 'datatables.net-responsive-dt';
</style>