<template>
    <Head title="Quản lý khách hàng" />
    <Layout :breadcrumbs="breadcrumbsData">
        <div class="flex flex-col lg:flex-row gap-2 justify-between mb-6">
            <div class="w-full grid grid-cols-2 gap-2">
                <VueDatePicker class="rounded-2xl" @update:model-value="handleDate" range auto-apply :close-on-auto-apply="false" :enable-time-picker="false" locale="vi" v-model="form_search.created_at" :placeholder="'Chọn ngày'"></VueDatePicker>
                <div class="w-full">
                    <VueMultiselect @update:model-value="updateSelected" v-model="selectedFilters" :options="filterOptions" :multiple="true" :close-on-select="false" :clear-on-select="false" label="name" track-by="value"> </VueMultiselect>
                </div>
            </div>
            <form @submit.prevent="submit" class="w-full lg:w-1/3">
                <input placeholder="Tìm kiếm..." class="w-full border p-2 border-gray-300 text-black rounded-3xl focus:ring focus:ring-opacity-0 focus:ring-indigo-500" type="text" v-model="form_search.search" />
            </form>
        </div>
        <div class="text-black w-full border rounded-xl overflow-hidden mb-5 md:mb-10">
            <table class="w-full drop-shadow-xl">
                <thead class="table-fixed">
                    <tr class="bg-ai3goc-sec text-white text-xs lg:text-base">
                        <th class="py-3">STT</th>
                        <th class="hidden md:table-cell">Số điện thoại</th>
                        <th class="md:hidden">SĐT</th>
                        <th>Họ tên</th>
                        <th>Trạng thái</th>
                        <th>Chi tiết</th>
                        <th>Fanpage</th>
                        <th class="hidden md:table-cell">Trạng thái xử lý</th>
                        <th class="hidden md:table-cell">Ghi chú</th>
                        <th class="md:hidden">Hành động</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <tr v-for="(item, index) in listUser" :key="item.id" :class="item.appointment && 'bg-primary-lurcinn/20'" class="border-b-2 text-xs lg:text-base">
                        <td class="text-center py-3">{{ (props.data?.current_page - 1) * 10 + index + 1 }}</td>
                        <td class="text-center">{{ item.phone ? item.phone : "Chưa cập nhật" }}</td>
                        <td class="text-center">{{ item.name }}</td>
                        <td class="text-center">
                            <img class="mx-auto" v-if="item.conversation_status === 'hot'" src="/assets/img/icon/hot.png" alt="">
                            <img class="mx-auto" v-if="item.conversation_status === 'cool'" src="/assets/img/icon/cool.png" alt="">
                        </td>
                        <td class="text-center">
                            <a :href="route('management.customer.detail', { id: item.id })" class="underline text-[#702EE7]"> Xem </a>
                        </td>
                        <td class="text-center">
                            {{ item.page_name ? item.page_name : "---" }}
                        </td>
                        <td class="hidden md:table-cell text-center">
                            <select
                                v-model="userSaleContactStatusSelectedValues[item.id]"
                                class="bg-white border-primary-lurcinn block mt-1 text-[14px] appearance-none w-full text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                            >
                                <option v-for="optionItem in listUserSaleContactStatus" :value="optionItem.id">{{ optionItem.name }}</option>
                            </select>
                        </td>
                        <td class="hidden md:flex text-sm gap-2 md:text-base px-4 py-2 rounded-r-[16px] items-center">
                            <textarea v-model="item.contact_note" class="rounded-xl w-full mr-2" placeholder="Nhập nội dung"/>
                            <button
                                @click="saveContactNote(item)"
                                class="bg-ai3goc-sec text-white rounded-xl p-3 hover:opacity-90 w-1/4 h-fit"
                            >
                                Lưu
                            </button>
                            <button
                                @click="handelDeleteUserSave(item)"
                                class="bg-red-400 text-white rounded-xl p-3 hover:bg-red-500 w-1/4 h-fit"
                            >
                                Xoá
                            </button>
                        </td>
                        <td class="md:hidden">
                            <div class="flex items-center justify-center h-full">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"
                                    @click="showModalEditContactStatus(item)"
                                >
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                </svg>
                                <img class="ml-1" @click="handelDeleteUserSave(item)" src="/assets/img/my_assistant/delete.png" alt="">
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td class="py-2"></td>
                        <td class="py-2"></td>
                        <td class="py-2"></td>
                        <td class="py-2"></td>
                        <td class="py-2"></td>
                        <td class="py-2"></td>
                        <td class="py-2 hidden md:block"></td>
                        <td class="py-2"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination :totalPage="totalPage" :initialPage="currentPage" @updatePage="fetchData" />
    </Layout>
    <Modal :show="editContactStatusModal" closeClass="top-0 md:-top-4" :maxWidth=" modalStep === 1 ? 'xl' : '5xl'" custom-class="w-full ml-3 mr-3" @close="closeModalEditContactStatus()">
        <div class="bg-white p-5 rounded-[10px] md:rounded-[25px] flex flex-col gap-[20px] mt-5">
            <div>
                Trạng thái xử lý:
                <select
                    v-model="userSaleContactStatusSelectedValues[userSaleModal.id]"
                    class="bg-white border-primary-lurcinn block mt-1 text-[14px] appearance-none w-full text-black py-2 px-4 pr-8 rounded-[10px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                >
                    <option v-for="optionItem in listUserSaleContactStatus" :value="optionItem.id">{{ optionItem.name }}</option>
                </select>
            </div>
            <div>
                <textarea v-model="userSaleModal.contact_note" class="rounded-xl w-full mr-2" placeholder="Nhập nội dung"/>
            </div>
            <button
                @click="saveContactNote(userSaleModal)"
                class="bg-primary-color text-white rounded-xl p-3 hover:opacity-90 w-1/4 h-fit"
            >
                Lưu
            </button>
        </div>
    </Modal>
    <div v-if="isLoading" class="fixed inset-0 flex items-center justify-center z-[104] bg-black bg-opacity-30">
        <div class="loader"></div>
    </div>
</template>

<script setup>
import Layout from "@/Layouts/Client/Layout.vue";
import {Head} from "@inertiajs/vue3";
import { ref, onMounted, reactive, watch, computed } from "vue";
import Pagination from "@/Components/Pagination.vue";
import VueDatePicker from "@vuepic/vue-datepicker";
import { router } from "@inertiajs/vue3";
import "@vuepic/vue-datepicker/dist/main.css";
import VueMultiselect from "vue-multiselect";
import { toast } from "vue3-toastify";
import Modal from "@/Components/Modal.vue";
const userSaleContactStatusDefault = 1

const props = defineProps({ user: Object, data: Object, query: Object, bankAccounts: Object, listUserSaleContactStatus: Object });
const masterUser = ref(props.user);
const listUserSaleContactStatus = ref(props.listUserSaleContactStatus);
const userSaleContactStatusSelectedValues = ref([]);
const listUser = ref(props.data.data);
const totalPage = ref(props.data?.last_page || 1);
const currentPage = ref(props.data?.current_page || 1);
const breadcrumbsData = [{ text: "Quản lý khách hàng", link: "management.customer.index" }];
const urlParams = new URLSearchParams(window.location.search);
const aiDoctorStatus = ref(masterUser.value.ai_doctor_status);
const aiDoctorMode = ref(masterUser.value.ai_doctor_mode);
const isVisibleByStatus = ref(masterUser.value.ai_doctor_status == 1);
const isLoading = ref(false);
const fileInput = ref(props.bankAccounts?.qr_code_url || null);
const imageUrl = ref(props.bankAccounts?.qr_code_url || null);
const bankName = ref(props.bankAccounts?.bank_name || "")
const accountName = ref(props.bankAccounts?.account_name || "")
const address = ref(masterUser.value.address)
const phone = ref(masterUser.value.phone)
const accountNumber = ref(props.bankAccounts?.account_number || "")
const imageFile = ref();

const filterOptions = [
    { value: "email", name: "Có email" },
    { value: "phone", name: "Có số điện thoại" },
    { value: "appointment", name: "Có lịch hẹn" },
];
const editContactStatusModal = ref(false);
const userSaleModal = ref(Object);

onMounted(() => {
    const map = new Map();
    listUser.value.forEach(item => {
        map.set(item.id, item.user_sale_contact_status ? item.user_sale_contact_status.id : userSaleContactStatusDefault);
    });

    userSaleContactStatusSelectedValues.value = Object.fromEntries(map);
});

const handelDeleteUserSave = async (item) => {
    const ok = confirm('Bạn có chắc chắn muốn xóa người dùng này không?');
      if (ok) {
        const response = await axios.post(route('management.customer.delete', { id: item.id }));
        if (response.data.success) {
            const idx = listUser.value.findIndex(u => u.id === item.id);
            if (idx !== -1) {
                listUser.value.splice(idx, 1);
            }
            toast.success("Cập nhật thành công");
        }
      } else {
        console.log('Cancelled');
      }

}

const options = ["list", "of", "options"];
switch (true) {
    case urlParams.has("created_at[]"):
        var created_at = urlParams.getAll("created_at[]");
        break;
    case urlParams.has("created_at"):
        var created_at = urlParams.get("created_at");
        break;
    default:
        var created_at = props.query.created_at ? props.query.created_at : "";
        break;
}
switch (true) {
    case urlParams.has("filter[]"):
        var filter = urlParams.getAll("filter[]");
        break;
    case urlParams.has("filter"):
        var filter = urlParams.get("filter");
        break;
    default:
        var filter = props.query.filter ? props.query.filter : "";
        break;
}

const form_search = reactive({
    search: urlParams.has("search") ? urlParams.get("search") : null,
    created_at: created_at,
    filter: filter,
});
const selectedFilters = ref([]);

watch(
    () => props.query.filter,
    (newFilters) => {
        if (Array.isArray(newFilters)) {
            selectedFilters.value = filterOptions.filter((opt) => newFilters.includes(opt.value));
        }
    },
    { immediate: true }
);

const handleDate = (modelData) => {
    form_search.created_at = modelData;
    submit();
};

//fix bug paging
const fetchData = (page) => {
    // Update current page
    currentPage.value = page;

    // Create a copy of form_search to use for navigation
    const searchParams = {
        page: page,
        search: form_search.search,
    };

    // Handle date range correctly
    if (form_search.created_at && Array.isArray(form_search.created_at)) {
        searchParams.created_at = form_search.created_at;
    } else if (form_search.created_at) {
        searchParams.created_at = form_search.created_at;
    }

    // Handle filters correctly
    if (form_search.filter && Array.isArray(form_search.filter)) {
        searchParams.filter = form_search.filter;
    } else if (form_search.filter) {
        searchParams.filter = form_search.filter;
    }

    // Use Inertia's router to navigate with the proper parameters
    router.get(route("management.customer.index"), searchParams);
};

const updateSelected = (item) => {
    selectedFilters.value = item;
    form_search.filter = item.map((opt) => opt.value);
    console.log("🚀 ~ updateSelected ~ form_search.filter:", form_search.filter);
    submit();
};

const submit = () => {
    router.get(route("management.customer.index"), form_search);
};

const updateSettingUser = async () => {
    isLoading.value = true;
    const payload = {
        ai_doctor_status: aiDoctorStatus.value,
        ai_doctor_mode: aiDoctorMode.value,
    };

    if (aiDoctorStatus.value == 0) {
        isVisibleByStatus.value = false;
    } else {
        isVisibleByStatus.value = true;
    }

    try {
        const response = await axios.post(route("management.update-setting-user"), payload);

        if (response.data.success) {
            toast.success("Cập nhật trạng thái thành công");
            isLoading.value = false;
        }
    } catch (error) {
        isLoading.value = false;
        console.error("Đã xảy ra lỗi");
    }
};
let allowedExtension = ["image/jpeg", "image/jpg", "image/png"];

const handleFileChange = (event) => {
    const file = event.target.files[0].type;
    if (allowedExtension.indexOf(file) < 0) {
        alert("Vui lòng chọn lại ảnh có định dạng .png, .jpg, .jpeg");
        return false;
    }
    if (file) {
        imageFile.value = event.target.files[0];
        imageUrl.value = URL.createObjectURL(event.target.files[0]);
    }
};

const handleUpdateUserBank = async () => {
    try {
        const formData = new FormData();
        formData.append('bank_name', bankName.value);
        formData.append('account_name', accountName.value);
        formData.append('account_number', accountNumber.value);
        formData.append('address', address.value);
        formData.append('phone', phone.value);
        if(imageFile.value){
            formData.append('qr_code', imageFile.value);
        }else{
            if(!imageUrl.value){
                toast.error('Vui lòng tải ảnh lên')
                return;
            }
        }

        const response = await axios.post(route("management.customer.updateUserBank"), formData);
        if (response.data.success) {
            toast.success("Cập nhật thông tin thành công");
            isLoading.value = false;
        }
    } catch (error) {
        isLoading.value = false;
        if(error.response && error.response.status === 422) {
            toast.error(error.response.data.message);
        } else {
            toast.error("Đã xảy ra lỗi");
        }
    }
}

const saveContactNote = async (item) => {
    try {
        isLoading.value = true;
        const params = {
            id: item.id,
            user_sale_contact_status_id: userSaleContactStatusSelectedValues.value[item.id],
            contact_note: item.contact_note,
        };
        const response = await axios.post(route("management.customer.update", params));

        if (response.data.success) {
            toast.success("Cập nhật trạng thái thành công");
            isLoading.value = false
        }else{
            isLoading.value = false
            console.error("Đã xảy ra lỗi");
        }
    } catch (error) {
        isLoading.value = false
        console.error("Đã xảy ra lỗi");
    }
    editContactStatusModal.value = false;
}

const downloadQr = (link) => {
  window.open(link, '_blank');
}
const showModalEditContactStatus = (item) => {
    userSaleModal.value = item
    editContactStatusModal.value = true
}

const closeModalEditContactStatus = () => {
    editContactStatusModal.value = false;
};

const triggerFileInput = () => {
    fileInput.value?.click();
};

const isFormValid = computed(() => {
  return (
    bankName.value?.trim().length > 0 &&
    accountName.value?.trim().length > 0 &&
    accountNumber.value?.trim().length > 0 &&
    address.value?.trim().length > 0 &&
    phone.value?.length === 10 &&
    (imageFile.value instanceof File || imageUrl.value?.trim().length > 0)
  )
})


const handleInvalid = (event, text) => {
    const value = event.target.value;
    if (!value.trim()) {
        event.target.setCustomValidity(text);
    } else {
        event.target.setCustomValidity('');
    }
};

const handlePhoneInput = (event) => {
    event.target.setCustomValidity('');
    let value = event.target.value;

    // Loại bỏ mọi ký tự không phải số
    value = value.replace(/\D/g, '');

    // Nếu số đầu không phải 0, thêm 0 vào đầu
    if (value.length > 0 && value[0] !== '0') {
        value = '0' + value;
    }

    // Giới hạn tối đa 10 chữ số
    value = value.slice(0, 10);

    event.target.value = value;
    phone.value = value;

    if (!/^0\d{9}$/.test(value)) {
        event.target.setCustomValidity('Số điện thoại phải đủ 10 chữ số');
    } else {
        event.target.setCustomValidity('');
    }
};

</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style scoped>
.loader {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #702ee7;
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
}
</style>
