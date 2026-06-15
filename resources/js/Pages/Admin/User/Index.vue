<template>
  <Master>
    <div
      class="lg:w-5/6 w-full bg-[url('../../public/assets/img/aiwow/layout_background.png')]  bg-cover bg-center min-h-screen p-5 lg:p-10">
      <div class="mx-auto sm:px-6 lg:px-8">
        <div class="py-12 px-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div>
              <div class="max-w-7xl mx-auto">
                  <!-- Stats Cards -->
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                          <h3 class="text-gray-500 text-sm font-medium">Tổng tiền bán trên sàn và kích hoạt key</h3>
                          <p class="text-2xl font-semibold mt-2 text-black"> {{formatCurrency(totalKeyRevenue)}} VND</p>
                      </div>

                      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                          <h3 class="text-gray-500 text-sm font-medium">Tổng số tiền nạp credit</h3>
                          <p class="text-2xl font-semibold mt-2 text-black">{{formatCurrency(totalCreditAmount)}} VND</p>
                      </div>

                      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                          <h3 class="text-gray-500 text-sm font-medium">Tổng số</h3>
                          <p class="text-2xl font-semibold mt-2 text-black">{{formatCurrency(grandTotal)}} VND</p>
                      </div>
                  </div>

                  <!-- Search & Filters -->
                  <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                      <!-- Search Bar -->
                      <div class="flex gap-4 mb-6">
                          <div class="flex-1 relative">
                              <input
                                  v-model="form_search.search"
                                  type="text"
                                  placeholder="Tìm kiếm..."
                                  class="w-full h-12 px-4 pr-12 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-black"
                              />
                              <button
                                  v-if="form_search.search"
                                  @click="clearSearch"
                                  class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                              >
                                  ✕
                              </button>
                          </div>
                      </div>

                      <!-- Filters -->
                      <div class="space-y-4">
                          <div class="flex flex-col md:flex-row gap-4">
                              <!-- Date Range -->
                              <div class="flex-1">
                                  <label class="block text-sm font-medium text-gray-700 mb-2">Thống kê theo ngày</label>
                                  <div class="flex items-center gap-3">
                                      <div class="relative flex-1">
                                          <input
                                              v-model="form_search.from_date"
                                              type="date"
                                              placeholder="Từ ngày"
                                              class="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-black pr-10"
                                          />
                                          <button
                                              v-if="form_search.from_date"
                                              @click="form_search.from_date = null"
                                              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none"
                                              type="button"
                                          >
                                              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                              </svg>
                                          </button>
                                      </div>

                                      <span class="text-gray-500">→</span>

                                      <div class="relative flex-1">
                                          <input
                                              v-model="form_search.to_date"
                                              type="date"
                                              placeholder="Đến ngày"
                                              class="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-black pr-10"
                                          />
                                          <button
                                              v-if="form_search.to_date"
                                              @click="form_search.to_date = null"
                                              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none"
                                              type="button"
                                          >
                                              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                              </svg>
                                          </button>
                                      </div>
                                  </div>
                              </div>

                              <!-- VIP Filter -->
                              <div class="flex-1">
                                  <label class="block text-sm font-medium text-gray-700 mb-2">Thời gian hết hạn VIP</label>
                                  <select
                                      v-model="form_search.time_remaining_until_expiration"
                                      @change="submit"
                                      class="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-black"
                                  >
                                      <option value="">Tất cả</option>
                                      <option value="7">Còn 7 ngày</option>
                                      <option value="30">Còn 30 ngày</option>
                                      <option value="60">Còn 60 ngày</option>
                                      <option value="90">Còn 90 ngày</option>
                                  </select>
                              </div>

                              <div class="flex-1">
                                  <label class="block text-sm font-medium text-gray-700 mb-2">Gói</label>
                                  <select
                                      v-model="form_search.package_id"
                                      class="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-black"
                                  >
                                      <option value="">Tất cả gói</option>
                                      <option
                                          v-for="pac in packagesSup"
                                          :key="pac.id"
                                          :value="pac.id"
                                      >
                                          {{ pac.name }} ({{ formatCurrency(pac.price) }} VND)
                                      </option>
                                  </select>
                              </div>

                                <div class="flex-1">
                                  <label class="block text-sm font-medium text-gray-700 mb-2">Loại tài Khoản</label>
                                  <select
                                      v-model="form_search.is_gift"
                                      class="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-black"
                                  >
                                      <option value="">Tất cả</option>
                                      <option value="0">Tính phí</option>
                                      <option value="1">Tặng</option>
                                  </select>
                              </div>

                          </div>

                          <!-- Action Buttons -->
                          <div class="flex justify-end gap-3">
                              <button
                                  @click="submit"
                                  class="h-12 px-6 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                              >
                                  Tìm kiếm
                              </button>
                              <a
                                  v-if="can(['users.export'])"
                                  @click="exportUsers"
                                  class="inline-flex cursor-pointer h-12 items-center px-4 py-2 bg-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                              >
                                  Export CSV
                              </a>
                              <a
                                  v-if="can(['users.create'])"
                                  :href="route('admin.users.create')"
                                  class="inline-flex h-12 items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                              >
                                  Tạo mới
                              </a>
                          </div>
                      </div>
                  </div>
              </div>

            <div class="flex  text-black mt-6">
              Số người dùng: {{ paginateUsers.total }}
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
                          Email</th>
                            <th
                          class="px-6 py-3 font-medium leading-4 tracking-wider text-center text-gray-500 uppercase bg-gray-100 border-b border-gray-200">
                          Loại tài khoản</th>
                        <th
                          class="px-6 py-3 font-medium leading-4 tracking-wider text-center text-gray-500 uppercase bg-gray-100 border-b border-gray-200">
                          Phone</th>
                        <th
                          class="px-6 py-3 font-medium leading-4 tracking-wider text-center text-gray-500 uppercase bg-gray-100 border-b border-gray-200">
                          Gói</th>
                        <th
                          class="px-6 py-3 font-medium leading-4 tracking-wider text-center text-gray-500 uppercase bg-gray-100 border-b border-gray-200">
                          Credit</th>
                        <th
                          class="px-6 py-3 font-medium leading-4 tracking-wider text-center text-gray-500 uppercase bg-gray-100 border-b border-gray-200">
                          Ngày hết hạn VIP</th>
                        <th
                          class="px-6 py-3 font-medium leading-4 tracking-wider text-center text-gray-500 uppercase bg-gray-100 border-b border-gray-200">
                          Ngày kích hoạt</th>
                        <!-- <th
                          class="px-6 py-3 font-medium leading-4 tracking-wider text-center text-gray-500 uppercase bg-gray-100 border-b border-gray-200">
                          URL Bác Sĩ AI</th>
                        <th
                          class="px-6 py-3 font-medium leading-4 tracking-wider text-center text-gray-500 uppercase bg-gray-100 border-b border-gray-200">
                          QR Bác Sĩ AI</th> -->
                        <th
                          class="px-6 py-3 font-medium leading-4 tracking-wider text-center text-gray-500 uppercase bg-gray-100 border-b border-gray-200">
                          Thao tác</th>
                      </tr>
                    </thead>
                    <tbody class="bg-white">
                      <tr v-for="(item, k) in paginateUsers.data" :key="k">
                        <td class="px-6 py-4 border-b border-gray-200">
                          <div class="text-center">
                            <div class="text-center font-medium leading-5 text-gray-900">
                              {{ item.id }}
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 border-b border-gray-200 text-left leading-5 text-gray-500">
                          <p class="line-clamp-2 text-ellipsis overflow-hidden">
                            {{ item.name }}
                          </p>
                        </td>

                        <td class="px-6 py-4 border-b border-gray-200 text-left leading-5 text-gray-500">
                          <p class="line-clamp-2 text-ellipsis overflow-hidden">
                              {{ item.email }}
                          </p>
                        </td>
                            <td class="px-6 py-4 border-b border-gray-200 text-left leading-5 text-gray-500">
                          <p class="line-clamp-2 text-ellipsis overflow-hidden">
                            {{ item.is_gift === 0 ? "Tính phí" : "Tặng" }}
                          </p>
                        </td>
                        <td class="px-6 py-4 border-b border-gray-200 text-left leading-5 text-gray-500">
                          <p class="line-clamp-2 text-ellipsis overflow-hidden">
                            {{ item.phone }}
                          </p>
                        </td>
                        <td class="px-6 py-4 border-b border-gray-200 text-left leading-5 text-gray-500">
                          <p class="line-clamp-2 text-ellipsis overflow-hidden">
                            {{ item.config_aff_name }}
                          </p>
                        </td>

                        <td class="px-6 py-4 border-b border-gray-200 text-left leading-5 text-gray-500">
                          <p class="line-clamp-2 text-ellipsis overflow-hidden">
                            {{ item.credit?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") }}
                          </p>
                        </td>

                        <td class="px-6 py-4 border-b border-gray-200 text-left leading-5 text-gray-500">
                          <p class="line-clamp-2 text-ellipsis overflow-hidden">
                            {{ item.vip_expired_at }}
                          </p>
                        </td>

                        <td class="px-6 py-4 border-b border-gray-200 text-left leading-5 text-gray-500">
                          <p class="line-clamp-2 text-ellipsis overflow-hidden">
                            {{ item.created_at }}
                          </p>
                        </td>

                        <!-- <td class="px-6 py-4 border-b border-gray-200 text-left leading-5 text-gray-500">
                          <p class="line-clamp-2 text-ellipsis overflow-hidden">
                            <a :href="item.docter_ai_url" target="_blank" rel="noopener noreferrer" class="text-blue-700">Link</a>
                          </p>
                        </td>

                        <td class="px-6 py-4 border-b border-gray-200 text-left leading-5 text-gray-500 text-center">
                          <p class="line-clamp-2 text-ellipsis overflow-hidden">
                            <img :src="'data:image/png;base64,' + item.docter_ai_qr" alt="QR Code">
                          </p>
                          <small class="underline text-blue-700 cursor-pointer"><a @click="downloadQr(item.download_qr_url)">Tải xuống</a></small>
                        </td> -->
                        <td>
                          <div
                            class="text-center flex gap-1 md:gap-0 flex-row md:flex-col font-medium leading-5 border-b border-gray-200">
                            <a v-if="can(['users.edit'])" :href="route('admin.users.edit', { user: item.id })"
                              class=" text-indigo-600 hover:text-indigo-900">Sửa</a>
                            <a v-if="can(['users.destroy'])" :data-id="item.id"
                              class="text-red-600 cursor-pointer remove-user hover:text-red-900">Xoá</a>
                            <a v-if="can(['activity-logs.index'])" :href="route('admin.activity-logs.index', { subject_id: item.id })"
                              class=" text-indigo-600 hover:text-indigo-900">Lịch sử</a>
                          </div>

                        </td>
                      </tr>
                    </tbody>
                  </DataTable>
                  <PaginationLinks class="mt-6" :links="paginateUsers.links" />
                </div>
              </div>
            </div>
          </div>


          <Modal :show="modalConfirmDeleteUser.isShow" @close="closeModal">
            <div class="p-12 text-center">
              <p> Bạn có muốn xóa user này không?</p>
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

const props = defineProps({
    paginateUsers: Object,
    packagesSup: Array,
    totalKeyRevenue: {
        type: Number,
        default: 0
    },
    totalCreditAmount: {
        type: Number,
        default: 0
    },
    grandTotal: {
        type: Number,
        default: 0
    }
});

const { can } = useAuthorization();
const { paginateUsers, totalKeyRevenue, totalCreditAmount, grandTotal } = props;
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


const modalConfirmDeleteUser = reactive({
  isShow: false,
})

const form_search = reactive({
    search: urlParams.has('search') ? urlParams.get('search') : null,
    time_remaining_until_expiration: urlParams.has('time_remaining_until_expiration') ? urlParams.get('time_remaining_until_expiration') : '',
    from_date: urlParams.has('from_date') ? urlParams.get('from_date') : null,
    to_date: urlParams.has('to_date') ? urlParams.get('to_date') : null,
    package_id: urlParams.has('package_id') ? urlParams.get('package_id') : '',
    is_gift: urlParams.has('is_gift') ? urlParams.get('is_gift') : '0',
})

const form_delete = reactive({
  user_id: null,
  _method: 'DELETE',
})

const closeModal = () => {
  form_delete.user_id = null
  modalConfirmDeleteUser.isShow = false
}

const confirmDeleteUser = (user_id) => {
  page.props.errors = []
  form_delete.user_id = user_id
  modalConfirmDeleteUser.isShow = true
}

const submitDeleteLesson = () => {
  router.post(route('admin.users.destroy', { user: form_delete.user_id }), form_delete, {
    onSuccess: (page) => {
      modalConfirmDeleteUser.isShow = false
      forceUpdateDataTable.value++
    },
  })
}
const downloadQr = (link) => {
  // window.open('http://localhost/qr-download', '_blank'); // Gọi API để tải file
  // console.log(link);
  window.open(link, '_blank');
}

const clearSearch = () => {
  form_search.search = null
  submit()
}

const submit = () => {
    // Kiểm tra ngày tìm kiếm
    if (form_search.from_date && form_search.to_date) {
        if (new Date(form_search.from_date) > new Date(form_search.to_date)) {
            alert('Ngày bắt đầu không thể lớn hơn ngày kết thúc');
            return;
        }
    }

    router.get(route('admin.users.index'), form_search);
}

const onClickRemoveUser = () => {
  tableRef.value.addEventListener('click', (event) => {
    const target = event.target;
    if (target.classList.contains('remove-user')) {
      const userId = target.getAttribute('data-id');
      confirmDeleteUser(userId);
    }
  });
}

const exportUsers = () => {
  window.location.href = route('admin.users.export', form_search);
}

onMounted(() => {
    onClickRemoveUser()
})

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('vi-VN').format(amount);
}


</script>
<style>
@import 'datatables.net-dt';
@import 'datatables.net-responsive-dt';
</style>