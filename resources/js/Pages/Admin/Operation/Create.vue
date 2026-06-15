<template>
    <Master>
        <div
            class="bg-[url('../../public/assets/img/admin/operation-create.png')] bg-cover bg-center relative py-7 px-6 min-h-screen w-full"
        >
            <div
                class="flex flex-col lg:flex-row items-start justify-between lg:items-end mb-5 space-y-4 lg:space-y-0 lg:space-x-4"
            >
                <div class="w-full lg:w-3/4 flex flex-col space-y-2">
                    <div class="flex items-center space-x-2 text-white">
                        <img
                            src="/assets/img/admin/icon/home-white.svg"
                            alt="Trang chủ"
                            class="w-auto h-[19px]"
                        />
                        <span class="font-medium text-white">/ Nghiệp vụ</span>
                        <span class="font-medium text-white"
                            >/ Tạo nghiệp vụ mới</span
                        >
                    </div>
                </div>
            </div>
            <div
                class="bg-white rounded-[30px] md:w-2/3 md:min-h-4/5 mx-auto p-4 md:px-24 md:pt-10 md:pb-8"
            >
                <h1 class="text-[#092A99] font-semibold text-[25px]">
                    Tạo nghiệp vụ mới
                </h1>
                <form @submit.prevent="showConfirmation" class="bg-white">
                    <div class="mb-4 flex justify-between items-end gap-8 flex-wrap md:flex-nowrap">
                        <div class="w-full md:w-2/3">
                            <label
                                class="block text-gray-700 text-sm font-bold mb-2"
                                for="id"
                            >
                                Tên nghiệp vụ
                            </label>
                            <input
                                v-model="form.name"
                                class="shadow appearance-none border rounded-[15px] w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="id"
                                type="text"
                                placeholder="Nhập Tên nghiệp vụ..."
                            />
                        </div>
                        <div class="w-full md:w-1/4 flex justify-center items-center relative">
                            <div class="relative w-24 h-24 bg-blue-100 rounded-lg overflow-hidden flex items-center justify-center">
                                <!-- Display image preview or a placeholder -->
                                <img :src="imageUrl" v-if="imageUrl" class="object-cover w-full h-full" />
                                <span v-if="!imageUrl" class="text-gray-400">Ảnh</span>

                                <!-- File Input (Hidden, but triggers when icon is clicked) -->
                                <input type="file" @change="onImageChange" class="absolute inset-0 opacity-0 cursor-pointer" />

                                <!-- Upload Icon (Positioned in the bottom right corner) -->
                                <div class="absolute bottom-1 right-1 bg-white p-1 rounded-full">
                                    <img src="/assets/img/admin/icon_upload.png" class="w-6 h-6">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-full mb-4">
                        <label
                            class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                            for="choose-job"
                        >
                            Chọn ngành nghề
                        </label>
                        <div class="relative">
<!--                            <select-->
<!--                                class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded-[15px] leading-tight focus:outline-none focus:bg-white focus:border-gray-500"-->
<!--                                id="choose-job"-->
<!--                            >-->
<!--                                <option>New Mexico</option>-->
<!--                                <option>Missouri</option>-->
<!--                                <option>Texas</option>-->
<!--                            </select>-->
                            <SelectBoxSearch v-model="form.occupation_id"></SelectBoxSearch>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label
                            class="block text-gray-700 text-sm font-bold mb-2"
                            for="operation-name"
                        >
                            Mô tả
                        </label>
                        <textarea
                            name=""
                            id=""
                            v-model="form.description"
                            cols="50"
                            rows="8"
                            class="shadow appearance-none border rounded-[15px] w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        ></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-8">
                        <a
                            :href="route('admin.operation.index')"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        >
                            Quay lại
                        </a>
                        <button
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            type="submit"
                        >
                            Lưu
                        </button>
                    </div>
                </form>
                <!-- Dialog xác nhận -->
                <div v-if="showConfirmationDialog" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                    <div class="bg-white p-6 rounded shadow-md">
                        <h3 class="text-lg font-semibold mb-4 text-black">Xác nhận</h3>
                        <p class="text-black">Bạn có chắc chắn muốn tạo nghiệp vụ không?</p>
                        <div class="mt-6 flex justify-end space-x-4">
                            <button @click="confirmSubmit" :disabled="isSubmitting" class="bg-green-500 text-white px-4 py-2 rounded">
                             <span v-if="isSubmitting">
                                <i class="spinner"></i> Đang gửi...
                              </span>
                             <span v-else>
                                Gửi
                              </span>

                            </button>
                            <button @click="cancelSubmit" class="bg-gray-300 text-black px-4 py-2 rounded">Hủy</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Master>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import { toast } from "vue3-toastify";
import SelectBoxSearch from "../../../Components/SelectBoxSearch.vue";
import Master from "../../../Layouts/Admin/Master.vue";


// Reactive data
const imageUrl = ref(null);

const showConfirmationDialog = ref(false);
const isSubmitting = ref(false);
const form = useForm({
    occupation_id: '',
    name: '',
    description: '',
    image: null,
});


// Function to preview the uploaded image
const onImageChange = (event) => {
    const file = event.target.files[0];

    if (!file) {
        return;
    }

    // Kiểm tra định dạng file
    const validImageTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    if (!validImageTypes.includes(file.type)) {
        // Thông báo lỗi nếu file không hợp lệ
        toast.error('File ảnh phải có định dạng: jpeg, png, jpg, gif.');
        form.image = null; // Xóa ảnh khỏi form nếu không hợp lệ
        imageUrl.value = ''; // Xóa preview nếu có ảnh đã được hiển thị
        return;
    }

    // Kiểm tra kích thước file (giới hạn 10MB)
    if (file.size > 10 * 1024 * 1024) { // 10MB = 10 * 1024 * 1024 bytes
        toast.error('File ảnh không được vượt quá 10MB.');
        form.image = null;
        imageUrl.value = '';
        return;
    }

    // Nếu file hợp lệ, lưu ảnh vào form và hiển thị preview
    form.image = file;

    const reader = new FileReader();
    reader.onload = (e) => {
        imageUrl.value = e.target.result; // Hiển thị preview ảnh
    };
    reader.readAsDataURL(file);
};

// Function to show confirmation dialog
const showConfirmation = () => {
    showConfirmationDialog.value = true;
};

const confirmSubmit = async () => {
    isSubmitting.value = true;
    await form.post(route('admin.operation.store'), {
        onSuccess: (response) => {
            console.log('Form submitted successfully', response.props.flash.success);
            toast.success(response.props.flash.success);
            setTimeout(() => {
                isSubmitting.value = false;
            }, 500)
            cancelSubmit();
        },
        onError: (errors) => {
            isSubmitting.value = false;
            cancelSubmit();
            console.log('Validation errors:', errors);
            Object.values(form.errors).forEach(error => {
                toast.error(error);
            });
        }
    });
};

// Function to cancel submission
const cancelSubmit = () => {
    showConfirmationDialog.value = false;  // Đóng dialog
};
</script>
<style scoped>
    .spinner {
        border: 2px solid rgba(0, 0, 0, 0.1);
        border-left-color: #09f;
        border-radius: 50%;
        width: 12px;
        height: 12px;
        display: inline-block;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        100% {
            transform: rotate(360deg);
        }
    }
</style>
