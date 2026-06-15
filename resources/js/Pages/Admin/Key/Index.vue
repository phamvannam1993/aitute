<template>
    <Master>
        <div
            class="lg:w-5/6 w-full bg-[url('../../public/assets/img/admin/operation.png')]  bg-cover bg-center min-h-screen p-5 lg:p-10">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="py-12 px-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div>
                        <div class="p-4 rounded-lg shadow-lg w-full mx-auto mb-6 bg-gray-100">
                            <!-- Nút Toggle -->
                            <button
                                @click="toggleSearch"
                                class="flex items-center justify-between w-full px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 focus:outline-none"
                            >
                                <span>Tìm kiếm</span>
                                <svg
                                    :class="showSearch ? 'rotate-180' : 'rotate-0'"
                                    class="w-5 h-5 transform transition-transform"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Form tìm kiếm -->
                            <div v-show="showSearch" class="mt-4 transition-all duration-300">
                                <form @submit.prevent="submit" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                                    <!-- Tìm kiếm Key -->
                                    <div>
                                        <label class="text-gray-600 text-sm mb-1 block">Tìm kiếm Key</label>
                                        <input
                                            v-model="form_search.key"
                                            placeholder="Nhập key..."
                                            class="w-full border p-2 border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-indigo-500 text-black"
                                            type="text"
                                        />
                                    </div>

                                    <!-- Tìm kiếm Email -->
                                    <div>
                                        <label class="text-gray-600 text-sm mb-1 block">Tìm kiếm Email</label>
                                        <input
                                            v-model="form_search.email"
                                            placeholder="Nhập email..."
                                            class="w-full border p-2 border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-indigo-500 text-black"
                                            type="email"
                                        />
                                    </div>

                                    <!-- Chọn gói -->
                                    <div>
                                        <label class="text-gray-600 text-sm mb-1 block">Chọn gói</label>
                                        <select
                                            v-model="form_search.package"
                                            class="w-full border p-2 border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-indigo-500 text-black cursor-pointer"
                                        >
                                            <option value="">Tất cả gói</option>
                                            <option v-for="option in configAffs" :key="option.id" :value="option.id">{{ option.name }}</option>
                                        </select>
                                    </div>

                                    <!-- Trạng thái kích hoạt -->
                                    <div>
                                        <label class="text-gray-600 text-sm mb-1 block">Trạng thái kích hoạt</label>
                                        <select
                                            v-model="form_search.status"
                                            class="w-full border p-2 border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-indigo-500 text-black cursor-pointer"
                                        >
                                            <option value="">Tất cả trạng thái</option>
                                            <option value="1">Đã kích hoạt</option>
                                            <option value="0">Chưa kích hoạt</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="text-gray-600 text-sm mb-1 block">Ngày bắt đầu tạo</label>
                                        <input
                                            v-model="form_search.startDate"
                                            type="date"
                                            class="w-full border p-2 border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-indigo-500 text-black"
                                        />

                                    </div>
                                    <div>
                                        <label class="text-gray-600 text-sm mb-1 block">Ngày kết thúc tạo</label>
                                        <input
                                            v-model="form_search.endDate"
                                            type="date"
                                            class="w-full border p-2 border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-indigo-500 text-black"
                                        />

                                    </div>

                                    <!-- Đại lý -->
                                    <div>
                                        <label class="text-gray-600 text-sm mb-1 block">Đại lý </label>
<!--                                        <select-->
<!--                                            v-model="form_search.agent"-->
<!--                                            class="w-full border p-2 border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-indigo-500 text-black cursor-pointer"-->
<!--                                        >-->
<!--                                            <option value="">Tất cả</option>-->
<!--                                            <option v-for="option in agents" :key="option.id" :value="option.id">{{ option.name }}</option>-->
<!--                                        </select>-->
                                        <SearchableSelect
                                            v-model="selectedId"
                                            :options="agents"
                                            placeholder="Chọn đại lý"
                                            searchPlaceholder="Tìm kiếm đại lý..."
                                            @change="handleAgentChange"

                                        />
                                    </div>

                                    <!-- Nút tìm kiếm -->
                                    <div class="col-span-1 sm:col-span-2 md:col-span-3 flex justify-center">
                                        <button
                                            type="submit"
                                            class="w-full sm:w-auto px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none"
                                        >
                                            Tìm kiếm
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>





                        <a class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                           :href="route('admin.keys.create')">Tạo mới</a>

                        <div class="flex flex-col mt-6">
                            <div class="mb-4 px-4 py-2 bg-gray-50 rounded-lg shadow-sm border border-gray-200 flex justify-between items-center">
                                <!-- Tổng số -->
                                <div class="flex items-center space-x-2 text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <span class="font-medium">Tổng số:</span>
                                    <span class="text-blue-600 font-semibold">{{ paginateKeys.total }}</span>
                                    <span>đại lý</span>
                                </div>

                                <!-- Nút tải về CSV -->
                                <button
                                    @click="downloadCsv"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none"
                                >
                                    Tải về CSV
                                </button>
                            </div>
                            <div class="py-2 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div
                                    class="inline-block min-w-full overflow-hidden align-middle border-b border-gray-200 shadow sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200 whitespace-nowrap">
                                        <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Key</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Mã Đại lý Cha</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Mã Đại lý</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Đại lý</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Kích hoạt</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Gói</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(item, k) in paginateKeys.data" :key="k">
                                            <td class="px-4 py-4 text-sm text-gray-500 text-center">{{ item.key_id }}</td>
                                            <td class="px-4 py-4 text-sm text-gray-500 truncate max-w-[160px]">{{ item.key }}</td>
                                            <td class="px-4 py-4 text-sm text-gray-500 text-center">{{ item.coupon_parent }}</td>
                                            <td class="px-4 py-4 text-sm text-gray-500 text-center">{{ item.agent_code }}</td>
                                            <td class="px-4 py-4 text-sm text-gray-500 text-center">{{ item.agent_name }}</td>
                                            <td class="px-4 py-4 text-sm text-gray-500 text-center">{{ getStatus(item.is_used) }}</td>
                                            <td class="px-4 py-4 text-sm text-gray-500 text-center">{{ item.config_aff_name }}</td>
                                            <td class="px-4 py-4 text-sm text-gray-500 text-center truncate max-w-[120px]">{{ item.email }}</td>
                                            <td class="px-4 py-4 text-sm text-gray-500 text-center">{{ item.created_at_formatted }}</td>
                                            <td class="px-4 py-4 text-sm font-medium text-center">
                                                <a :href="route('admin.keys.edit', { id: item.key_id })" class="text-indigo-600 hover:text-indigo-900">Chỉnh sửa</a>
                                                <a href="#" @click="confirmDeleteKey(item.key_id)" class="text-red-600 hover:text-red-900 ml-4">Xoá</a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <Pagination class="mt-6" :links="paginateKeys.links" />
                                </div>
                            </div>
                        </div>

                    </div>


                    <Modal :show="modalConfirmDeleteUser.isShow" @close="closeModal">
                        <div class="p-12 text-center">
                            <p> Bạn có muốn xóa key này không?</p>
                            <div v-if="$page.props.errors.message" class="mt-5 text-red-500">
                                {{ $page.props.errors.message }}
                            </div>
                            <div class="mt-10 m-auto flex justify-between w-1/2">
                                <DangerButton class="cursor-pointer text-red-600 hover:text-red-900" @click="submitDeleteLesson">
                                    Xoá
                                </DangerButton>
                                <SecondaryButton class="cursor-pointer" @click="closeModal"> Huỷ </SecondaryButton>
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
    import { router, usePage } from '@inertiajs/vue3';
    import { ref, onMounted, nextTick, reactive} from 'vue'

    import Master from "../../../Layouts/Admin/Master.vue";
    import Pagination from './Components/Pagination.vue';
    import SearchableSelect from '../../../Components/SearchableSelect.vue';

    const { paginateKeys } = defineProps({
        paginateKeys: Object,
        configAffs: Array,
        agents: Array
    })
    const urlParams = new URLSearchParams(window.location.search)
    const page = usePage();

    const modalConfirmDeleteUser = reactive({
        isShow: false,
    })
    const showSearch = ref(true);
    const form_search = reactive({
        email: urlParams.has('email') ? urlParams.get('email') : null,
        key: urlParams.has('key') ? urlParams.get('key') : null,
        package: urlParams.has('package') ? urlParams.get('package') : '',
        status: urlParams.has('status') ? urlParams.get('status') : '',
        agent: urlParams.has('agent') ? urlParams.get('agent') : '',
        startDate: urlParams.get('startDate') || '',
        endDate: urlParams.get('endDate') || '',
    });
    const agentSelected = urlParams.has('agent') ? urlParams.get('agent') : '';
    console.log('agentSelected', agentSelected)
    const selectedId = ref(agentSelected);
    const handleAgentChange = (selectedAgent) => {
        form_search.agent = selectedAgent ? selectedAgent.id : '';
    }

    const toggleSearch = () => {
        showSearch.value = !showSearch.value;
    };

    const form_delete = reactive({
        key_id: null,
        _method: 'DELETE',
    })

    const closeModal = () => {
        form_delete.key_id = null
        modalConfirmDeleteUser.isShow = false
    }

    const confirmDeleteKey = (key_id) => {
        page.props.errors = []
        form_delete.key_id = key_id
        modalConfirmDeleteUser.isShow = true
    }

    const submitDeleteLesson = () => {
        router.post(route('admin.keys.destroy', { key_id: form_delete.key_id }), form_delete, {
            onSuccess: (page) => {
                modalConfirmDeleteUser.isShow = false
            },
        })
    }

    const clearSearch = () => {
        form_search.search = null
        submit()
    }
    const submit = () => {
        router.get(route('admin.keys.index'), form_search)
    }
    const downloadCsv = () => {
        const queryParams = new URLSearchParams(form_search).toString();
        window.location.href = `${route('admin.keys.export')}?${queryParams}`;
    };
    const getStatus = (is_user) => {
        return is_user ? "Đã kích hoạt" : "Chưa kích hoạt";
    }

</script>
