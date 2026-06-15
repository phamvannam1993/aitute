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
                        <span class="font-medium text-white">/ Ngành nghề</span>
                        <span class="font-medium text-white"
                            >/ Tạo ngành nghề mới</span
                        >
                    </div>
                </div>
            </div>
            <div
                class="bg-white rounded-[30px] md:w-2/3 md:min-h-4/5 mx-auto p-4 md:px-24 md:pt-10 md:pb-8"
            >
                <h1 class="text-[#092A99] font-semibold text-[25px]">
                    Tạo ngành nghề mới
                </h1>
                <!-- Hiển thị lỗi chung -->
                <div v-if="Object.keys(form.errors).length">
                    <ul class="text-red-500">
                        <li v-for="(error, index) in Object.values(form.errors)" :key="index">{{ error }}</li>
                    </ul>
                </div>
                <!-- Hiển thị flash message -->
                <div v-if="successMessage" class="bg-green-500 text-white p-4 rounded-md mb-4">
                    {{ successMessage }}
                </div>

                <form @submit.prevent="showConfirmation" class="bg-white">
                    <div class="mb-4 flex justify-between items-end gap-8 flex-wrap md:flex-nowrap">
                        <div class="w-full md:w-2/3">
                            <label
                                class="block text-gray-700 text-sm font-bold mb-2"
                            >
                                Tên ngành nghề
                            </label>
                            <input
                                v-model="form.name"
                                class="shadow appearance-none border rounded-[15px] w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                type="text"
                                placeholder="Nhập Tên ngành nghề..."
                            />
                        </div>
                        <!-- Upload Image Section aligned to the right of the ID field -->
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
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Lĩnh vực ngành nghề</label>
                        <select v-model="form.category_id" class="shadow appearance-none border rounded-[15px] w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option v-for="(item, index) in categories" :key="index" :value="item.id">{{ item.name }}</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label
                            class="block text-gray-700 text-sm font-bold mb-2"
                        >
                            Mô tả
                        </label>
                        <textarea
                            v-model="form.description"
                            cols="50"
                            rows="8"
                            class="shadow appearance-none border rounded-[15px] w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        ></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-8">
                        <a
                            :href="route('admin.job.index')"
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
                        <p class="text-black">Bạn có chắc chắn muốn tạo ngành nghề này không?</p>
                        <div class="mt-6 flex justify-end space-x-4">
                            <button @click="confirmSubmit" class="bg-green-500 text-white px-4 py-2 rounded">Có</button>
                            <button @click="cancelSubmit" class="bg-gray-300 text-black px-4 py-2 rounded">Hủy</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Master>
</template>

<script setup>
    import { ref } from "vue";
    import {useForm, usePage} from "@inertiajs/vue3";
    import Master from "../../../Layouts/Admin/Master.vue";
    import {onMounted} from "@vue/runtime-core";
    import { toast } from 'vue3-toastify';

    const props = defineProps({
        categories: Object
    });

    const showConfirmationDialog = ref(false);  // Biến trạng thái để hiển thị dialog xác nhận

    // Reactive data
    const imageUrl = ref(null);
    const successMessage = ref(null);
    // Sử dụng useForm để quản lý form và lỗi
    const form = useForm({
        name: '',
        category_id: '1',
        description: '',
        image: null,
    });
    const pageProps = usePage().props;
    console.log('Validation pageProps:', pageProps);
    // Function to handle form submission using Inertia
    const submitForm = () => {
        form.post(route('admin.job.store'), {
            // Nếu form được gửi thành công
            onSuccess: (response) => {
                console.log('Form submitted successfully', response.props.flash.success);
                toast.success(response.props.flash.success);

                successMessage.value = response.props.flash.success; // Lưu message vào biến successMessage
                // Đặt timeout để ẩn thông báo sau 2 giây (2000 milliseconds)
                setTimeout(() => {
                    successMessage.value = null;
                }, 2000); // 2 giây
            },
            // Nếu có lỗi từ server
            onError: (errors) => {
                console.log('Validation errors:', errors);
                Object.values(form.errors).forEach(error => {
                    toast.error(error);
                });
            }
        });
    };

    // Function to show confirmation dialog
    const showConfirmation = () => {
        showConfirmationDialog.value = true;
    };

    // Function to confirm and submit form
    const confirmSubmit = () => {
        showConfirmationDialog.value = false;  // Ẩn dialog
        submitForm();  // Thực hiện submit form
    };

    // Function to cancel submission
    const cancelSubmit = () => {
        showConfirmationDialog.value = false;  // Đóng dialog
    };

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

</script>

