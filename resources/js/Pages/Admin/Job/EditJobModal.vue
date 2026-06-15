<template>
    <div>
        <!-- Modal Overlay and Modal Box -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
            <div class="bg-white w-full max-w-lg md:max-w-xl lg:max-w-2xl p-6 rounded-lg shadow-lg relative mx-4">

                <!-- Modal Header -->
                <h2 class="text-2xl font-semibold mb-4 text-blue-900">Sửa ngành nghề</h2>

                <!-- Close Button -->
                <button @click="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-black">
                    ✕
                </button>

                <!-- Form Inside the Modal -->
                <form @submit.prevent="submitForm">
                    <!-- ID Field with Image Upload next to it -->
                    <div class="flex flex-col md:flex-row mb-4">
                        <div class="w-full md:w-3/4 pr-0 md:pr-2 mb-4 md:mb-0">
                            <label class="block text-gray-700">Tên ngành nghề</label>
                            <input  v-model="occupation.name"  type="text" placeholder="Nhập Tên ngành nghề..." class="w-full p-2 border rounded-lg bg-gray-200 text-gray-700">
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
                        <select v-model="occupation.category_id" class="shadow appearance-none border rounded-[15px] w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option v-for="(item, index) in categories" :key="index" :value="item.id">{{ item.name }}</option>
                        </select>
                    </div>
                    <!-- Description Field -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Mô tả</label>
                        <textarea rows="8" cols="50" v-model="occupation.description" placeholder="Nhập mô tả ngành nghề..." class="w-full p-2 border rounded-lg text-gray-700"></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-4">
                        <button @click="closeModal" type="button" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 w-full md:w-auto">
                            Hủy
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 w-full md:w-auto">
                            Lưu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>


<script setup>
    import { ref } from 'vue';
    import { router } from '@inertiajs/vue3';
    import { defineProps, defineEmits } from 'vue';
    import { toast } from 'vue3-toastify';

    const props = defineProps({
        occupation: Object,
        categories: Object,
        showModal: {
            type: Boolean,
            required: true
        }
    });

    const emit = defineEmits(['close']);
    const imageUrl = ref(props.occupation.image_url || null);
    let imageFile = null;

    // Xử lý khi người dùng chọn ảnh mới
    const onImageChange = (event) => {
        const file = event.target.files[0];  // Lấy file được chọn

        if (!file) {
            return;
        }

        // Kiểm tra định dạng file
        const validImageTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!validImageTypes.includes(file.type)) {
            toast.error('File ảnh phải có định dạng: jpeg, png, jpg, gif.');
            imageFile.value = null;  // Xóa file ảnh không hợp lệ
            imageUrl.value = '';     // Xóa URL preview nếu có ảnh trước đó
            return;
        }

        // Kiểm tra kích thước file (giới hạn 10MB)
        if (file.size > 10 * 1024 * 1024) {  // 10MB = 10 * 1024 * 1024 bytes
            toast.error('File ảnh không được vượt quá 10MB.');
            imageFile.value = null;  // Xóa file ảnh không hợp lệ
            imageUrl.value = '';     // Xóa URL preview nếu có ảnh trước đó
            return;
        }

        // Nếu file hợp lệ, lưu file vào biến imageFile
        imageFile = file;

        // Hiển thị preview ảnh bằng cách tạo URL tạm thời từ file
            imageUrl.value = URL.createObjectURL(file);

        toast.success('File hợp lệ, bạn có thể tiếp tục.');
    };

    // Hàm gửi dữ liệu cập nhật occupation
    const submitForm = async () => {
        try {
            const formData = new FormData();
            formData.append('name', props.occupation.name);
            formData.append('category_id', props.occupation.category_id);
            formData.append('description', props.occupation.description);

            // Nếu người dùng đã chọn ảnh mới, thêm ảnh vào formData
            if (imageFile) {
                formData.append('image', imageFile);
            }
            // Gửi yêu cầu cập nhật
            await axios.post(`/admin/job/${props.occupation.id}/update`, formData);
            toast.success('Cập nhật ngành nghề thành công!');
            emit('close');
            // Gửi sự kiện `updateData` lên component cha
            emit('updateData');
        } catch (error) {
            console.error('Error updating occupation:', error);
            toast.error('Cập nhật thất bại! Vui lòng thử lại.');
            if (error.response && error.response.data.errors) {
                Object.values(error.response.data.errors).forEach(errorMsg => {
                    toast.error(errorMsg);
                });
            }
        }
    };

    // Hàm đóng modal
    const closeModal = () => {
        emit('close'); // Gọi sự kiện đóng modal
    };
</script>

<style scoped>
</style>
