<template>
    <div>
        <!-- Modal Overlay and Modal Box -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
            <div class="bg-white w-full max-w-lg md:max-w-xl lg:max-w-3xl p-6 rounded-lg shadow-lg relative mx-4" style="max-height: 90vh; overflow-y: auto;">

                <!-- Modal Header -->
                <h2 class="text-2xl font-semibold mb-4 text-blue-900">Sửa AI</h2>
                <!-- Close Button -->
                <button @click="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-black">
                    ✕
                </button>
                <!-- Form Inside the Modal -->
                <form @submit.prevent="submitForm">
                    <!-- ID Field with Image Upload next to it -->
                    <div class="flex flex-col md:flex-row mb-4">
                        <div class="w-full md:w-3/4 pr-0 md:pr-2 mb-4 md:mb-0">
                            <label class="block text-gray-700">Tên AI</label>
                            <input v-model="assistant.name" type="text" placeholder="Nhập..." class="w-full p-2 border rounded-lg text-gray-700">
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

                    <!-- Name Field -->
                    <div class="mb-4 ">
                        <label class="block text-gray-700">Loại AI</label>
                        <select v-model="assistant.type" class="w-full p-2 border rounded-lg text-gray-700  text-black">
                            <option v-for="(item, index) in types" :key="index" :value="item.value">{{ item.label }}</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Tên nghiệp vụ</label>
                        <SelectBoxOccupationEdit
                            v-model="form.occupation_id"
                            :operation_id="form.operation_id"
                            :name="form.occupation_name"
                        ></SelectBoxOccupationEdit>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Tên ngành nghề</label>
                        <SelectBoxOperationEdit
                            v-model="form.operation_id"
                            :occupation_id="form.occupation_id"
                            :name="form.operation_name"
                        ></SelectBoxOperationEdit>
                    </div>

                    <!-- Description Field -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Mô tả</label>
                        <textarea rows="8" cols="50" v-model="assistant.description" placeholder="Nhập mô tả ngành nghề..." class="w-full p-2 border rounded-lg text-gray-700"></textarea>
                    </div>

                    <div class="mt-4">
                        <button @click="showCriteriaModal = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700" type="button">
                            + Thêm Tiêu Chí
                        </button>
                    </div>
                    <div v-for="(criteria, index) in criteriaList" :key="index" class="mt-4">
                        <div v-if="criteria.type === 'input'" class="mb-2">
                            <div v-if="!criteria.editingLabel" class="mb-2">
                                <label class="font-semibold text-black ">{{ criteria.label || 'Nhập tiêu đề tiêu chí tại đây' }}</label>
                                <button @click="criteria.editingLabel = true" class="text-blue-500 ml-2">✏️</button>
                            </div>
                            <div v-else>
                                <input type="text" v-model="criteria.label" class="w-full p-2 border border-gray-300 rounded-lg text-black mb-2" placeholder="Nhập nhãn tiêu chí vào đây. đây là 1 dạng câu hỏi ví dụ: Ngân sách dành cho chiến dịch này?">
                                <button @click="criteria.editingLabel = false" class="bg-green-500 text-white px-2 py-1 mt-1 rounded hover:bg-green-600 mb-2">Lưu</button>
                            </div>

                            <!-- Name của Tiêu Chí -->
                            <div v-if="!criteria.editingName" class="mb-2">
                                <label class="font-semibold text-black">{{ criteria.name || 'Nhập tên tiêu chí tại đây'}}</label>
                                <button @click="criteria.editingName = true" class="text-blue-500 ml-2">✏️</button>
                            </div>
                            <div v-else>
                                <input type="text" v-model="criteria.name" class="w-full p-2 border border-gray-300 rounded-lg text-black mb-2" placeholder="Nhập tên tiêu chí vào đây . Đây là 1 dạng câu trả lời ví dụ: Ngân sách  ">
                                <button @click="criteria.editingName = false" class="bg-green-500 text-white px-2 py-1 mt-1 rounded hover:bg-green-600 mb-2">Lưu</button>
                            </div>

                            <input type="text" v-model="criteria.placeholder" class="w-full p-2 border border-gray-300 rounded-lg text-black">
                        </div>

                        <div v-else-if="criteria.type === 'select'" class="mb-2 text-black">
                            <div v-if="!criteria.editingLabel">
                                <label class="font-semibold text-black">{{ criteria.label || 'Nhập tiêu đề tiêu chí tại đây' }}</label>
                                <button @click="criteria.editingLabel = true" class="text-blue-500 ml-2">✏️</button>
                            </div>
                            <div v-else>
                                <input type="text" v-model="criteria.label" class="w-full p-2 border border-gray-300 rounded-lg text-black mb-2" placeholder="Nhập nhãn tiêu chí vào đây.">
                                <button @click="criteria.editingLabel = false" class="bg-green-500 text-white px-2 py-1 mt-1 rounded hover:bg-green-600 mb-2">Lưu</button>
                            </div>

                            <!-- Name của Tiêu Chí -->
                            <div v-if="!criteria.editingName" class="mb-2">
                                <label class="font-semibold text-black">{{ criteria.name || 'Nhập tên tiêu chí tại đây' }}</label>
                                <button @click="criteria.editingName = true" class="text-blue-500 ml-2">✏️</button>
                            </div>
                            <div v-else>
                                <input type="text" v-model="criteria.name" class="w-full p-2 border border-gray-300 rounded-lg text-black mb-2" placeholder="Nhập tên tiêu chí vào đây ">
                                <button @click="criteria.editingName = false" class="bg-green-500 text-white px-2 py-1 mt-1 rounded hover:bg-green-600 mb-2">Lưu</button>
                            </div>

                            <div class="flex items-center space-x-2 mb-2">
<!--                                <select class="w-5/6 p-2 border border-gray-300 rounded-lg text-black"  v-model="criteria.selectedValue">-->
<!--                                    <option v-for="(item, index) in getParsedOptions(criteria.value)" :key="index" :value="item.value">-->
<!--                                        {{ item.text || 'text' }}-->
<!--                                    </option>-->
<!--                                </select>-->
                                <div class="mb-4">
                                    <label class="block text-gray-700 font-semibold">Danh sách giá trị:</label>
                                    <ul class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                                        <li
                                            v-for="(option, optionIndex) in criteria.value"
                                            :key="optionIndex"
                                            class="flex items-center justify-between bg-gray-100 px-3 py-2 rounded-lg shadow-sm"
                                        >
                                            <!-- Hiển thị giá trị -->
                                            <div class="flex items-center space-x-2 w-full">
                                                <span v-if="!option.editing" class="truncate w-full">
                                                    {{ option.text }}
                                                </span>
                                                <input
                                                    v-else
                                                    type="text"
                                                    v-model="option.text"
                                                    class="w-full p-1 border border-gray-300 rounded-md text-gray-700"
                                                    placeholder="Chỉnh sửa giá trị..."
                                                />
                                            </div>
                                            <!-- Nút chỉnh sửa -->
                                            <div class="flex items-center space-x-2">
                                                <button
                                                    v-if="!option.editing"
                                                    @click="option.editing = true"
                                                    class="text-blue-500 hover:text-blue-700 text-sm"
                                                >
                                                    ✏️
                                                </button>
                                                <button
                                                    type="button"
                                                    v-else
                                                    @click="saveOptionEdit(criteria, option)"
                                                    class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600 text-sm"
                                                >
                                                    Lưu
                                                </button>
                                                <button
                                                    type="button"
                                                    @click="removeOption(criteria, optionIndex)"
                                                    class="text-red-500 hover:text-red-700 text-sm"
                                                >
                                                    ❌
                                                </button>
                                            </div>
                                        </li>
                                    </ul>
                                    <button
                                        type="button"
                                        @click="showAddValueModal(index)"
                                        class="bg-blue-500 text-white px-4 py-2 mt-3 rounded-lg shadow hover:bg-blue-600"
                                    >
                                        + Thêm Giá Trị
                                    </button>
                                </div>

<!--                                <button @click="showAddValueModal(index)" class="bg-blue-500 text-white px-3 py-2 rounded-lg hover:bg-blue-600 " type="button">-->
<!--                                    + Thêm-->
<!--                                </button>-->
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" v-model="criteria.multiple" :id="'allowMultiple_' + index" class="cursor-pointer">
                                <label :for="'allowMultiple_' + index" class="text-black cursor-pointer">Cho phép chọn nhiều</label>
                            </div>
                        </div>
                        <button @click="removeCriteria(index)" class="text-red-600 mt-2">Xóa Tiêu Chí</button>
                    </div>

                    <div class="mb-4">
                        <h3 class="block text-lg font-semibold text-gray-700 mb-2">Các bước thực hiện</h3>
                        <div v-for="(step, index) in step_ais" :key="index" class="bg-white p-4 mb-2 rounded-lg shadow relative">
                            <button v-if="index > 0" @click.prevent="removeStep(index)" class="bg-red-500 text-white px-3 py-1 rounded-md shadow hover:bg-red-600 transition duration-150">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                            <!-- Tên Step -->
                            <div class="mb-2">
                                <label class="block text-gray-700">Tên Step</label>
                                <input v-model="step.name" type="text" placeholder="Nhập tên step..." class="w-full p-2 border rounded-lg text-gray-700">
                            </div>
                            <!-- Mô tả Step -->
                            <div>
                                <label class="block text-gray-700">Mô tả</label>
                                <textarea v-model="step.description" rows="9" placeholder="Nhập mô tả..." class="w-full p-2 border rounded-lg text-gray-700"></textarea>
                            </div>
                        </div>


                        <!-- Nút Thêm Bước -->
                        <button @click.prevent="addStep" class="bg-blue-500 text-white px-4 py-2 rounded-md shadow hover:bg-blue-600 transition duration-150">
                            + Thêm Bước
                        </button>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-4">
                        <div class="flex items-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       :checked="Boolean(assistant?.is_public)"
                                       @change="togglePublic(assistant)"
                                       class="sr-only peer"
                                    >

                                <div class="w-[52px] h-[26px] bg-gray-200 rounded-full peer
                                    peer-checked:bg-blue-600
                                    peer-checked:after:translate-x-[26px]
                                    after:content-['']
                                    after:absolute
                                    after:top-[2px]
                                    after:left-[2px]
                                    after:bg-white
                                    after:rounded-full
                                    after:h-[22px]
                                    after:w-[22px]
                                    after:transition-all">
                                </div>

                                <span class="ml-3 text-[15px] text-gray-700 text-red-600">
                                      Bạn có muốn public AI này không ?
                                </span>
                            </label>
                        </div>
                        <button @click="closeModal" type="button" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 w-full md:w-auto">
                            Hủy
                        </button>
                        <button type="submit" :disabled="isSubmitting" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 w-full md:w-auto">
                            <span v-if="isSubmitting">
                                <i class="spinner"></i> Đang gửi...
                              </span>
                            <span v-else>
                                Lưu
                              </span>

                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div v-if="showCriteriaModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white p-5 rounded-lg w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Chọn loại tiêu chí</h3>
            <div class="mb-4">
                <label class="flex items-center space-x-2 text-black">
                    <input type="radio" v-model="selectedCriteriaType" value="input">
                    <span>Dạng Text</span>
                </label>
            </div>
            <div class="mb-4">
                <label class="flex items-center space-x-2 text-black">
                    <input type="radio" v-model="selectedCriteriaType" value="select">
                    <span>Dạng Select Box</span>
                </label>
            </div>
            <div class="flex justify-end space-x-2">
                <button @click="addCriteria" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700">OK</button>
                <button @click="closeModalCriteria" class="bg-gray-300 px-4 py-2 rounded-lg hover:bg-gray-400">Hủy</button>
            </div>
        </div>
    </div>
</template>


<script setup>
    import { ref, onMounted, toRaw, computed, watch } from 'vue';
    import {router, useForm} from '@inertiajs/vue3';
    import SelectBoxOccupationEdit from "../AIAssistant/SelectBoxOccupationEdit.vue";
    import SelectBoxOperationEdit from "../AIAssistant/SelectBoxOperationEdit.vue";
    import { defineProps, defineEmits } from 'vue';
    import { toast } from 'vue3-toastify';

    const props = defineProps({
        assistant: Object,
        showModal: {
            type: Boolean,
            required: true
        }
    });

    console.log(" props.assistant.occupation_id",  props.assistant.occupation_id)
    console.log(" props.assistant.operation_id",  props.assistant.operation_id)
    const form = useForm({
        occupation_id: props.assistant.occupation_id ,
        occupation_name: props.assistant.occupation.name || '' ,
        operation_id: props.assistant.operation_id,
        operation_name: props.assistant.operation.name || '',
    });

    const initializeCriteriaList = (criteria) => {
        if (!Array.isArray(criteria)) return []; // Nếu không phải là mảng, trả về mảng trống

        return criteria.map(item => {
            return {
                ...item,
                // Kiểm tra và parse `value` nếu cần
                value: getParsedOptions(item.value),
            };
        });
    };

    const types = ref([
        { value: 'text', label: 'Văn bản' },
        { value: 'image', label: 'Hình ảnh' },
        { value: 'video', label: 'Video' }
    ]);
    const emit = defineEmits(['close']);
    const imageUrl = ref(props.assistant.thumbnail_url || null);
    let imageFile = null;

    const step_ais = ref(props.assistant.step_ais || [{ name: '', description: '' }]); // Khởi tạo steps từ dữ liệu assistant
    const showCriteriaModal = ref(false);
    const selectedCriteriaType = ref("input");
    const getParsedOptions = (value) => {
        try {
            // Kiểm tra nếu `value` đã là một mảng, trả về nó trực tiếp
            if (Array.isArray(value)) {
                return value;
            }

            // Kiểm tra nếu `value` là một chuỗi JSON hợp lệ và không bị trống
            if (typeof value === 'string' && value.trim() !== '') {
                const parsed = JSON.parse(value);
                console.log("Array.isArray(parsed)", Array.isArray(parsed), parsed)
                // Đảm bảo rằng kết quả sau khi parse là mảng
                return Array.isArray(parsed) ? parsed : [];
            }

            // Nếu `value` là chuỗi trống, trả về mảng rỗng
            return [];
        } catch (error) {
            console.error("Lỗi khi parse JSON:", error);
            return []; // Trả về mảng rỗng nếu có lỗi
        }
    };
    const criteriaList = ref(initializeCriteriaList(props.assistant.criteria));
console.log("criteriaListcriteriaList", criteriaList)
    const closeModalCriteria = () => {
        showCriteriaModal.value = false;
    };

    // Thêm một tiêu chí mới
    const addCriteria = () => {
        if (selectedCriteriaType.value === 'input' || selectedCriteriaType.value === 'select') {
            criteriaList.value.push({ type: selectedCriteriaType.value, value: '', label: '', name: '', editingLabel: false, multiple: false, value: [], selectedValue: null });
            closeModalCriteria();
        } else {
            alert("Vui lòng chọn loại tiêu chí.");
        }
    };

    const showAddValueModal = (index) => {
        if (criteriaList.value[index].type === 'select') {
            const newText = prompt("Nhập giá trị mới:");
            if (newText) {
                if (!Array.isArray(criteriaList.value[index].value)) {
                    criteriaList.value[index].value = [];
                }
                const newValue = criteriaList.value[index].value.length + 1;
                criteriaList.value[index].value.push({ value: newValue, text: newText });
                // Đặt giá trị mới thêm vào làm giá trị mặc định
                criteriaList.value[index].selectedValue = newValue;
            }
        } else {
            console.error('Tiêu chí không phải dạng select.');
        }
    };

    const removeCriteria = (index) => {
        criteriaList.value.splice(index, 1);
    };

    const togglePublic = (assistant) => {
        assistant.is_public = !assistant.is_public;
    };

    // Thêm một bước mới
    const addStep = () => {
        step_ais.value.push({ name: '', description: '' });
    };

    // Xóa bước
    const removeStep = (index) => {
        if (index > 0) {
            step_ais.value.splice(index, 1);
        }
    };

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

    const isSubmitting = ref(false);
    // Hàm gửi dữ liệu cập nhật occupation
    const submitForm = async () => {
        isSubmitting.value = true;
        try {
            console.log("form", form);
            const formData = new FormData();
            formData.append('occupation_id', form.occupation_id);
            formData.append('operation_id', form.operation_id);
            formData.append('type', props.assistant.type);
            formData.append('name', props.assistant.name);
            formData.append('description', props.assistant.description);
            formData.append('step_ais', JSON.stringify(step_ais.value));
            formData.append('criteria', JSON.stringify(criteriaList.value));
            formData.append('is_public', props.assistant.is_public);
            // Nếu người dùng đã chọn ảnh mới, thêm ảnh vào formData
            if (imageFile) {
                formData.append('thumbnail', imageFile);
            }
            // Gửi yêu cầu cập nhật
            await axios.post(`/admin/ai-assistants/${props.assistant.id}/update`, formData).then(response => {
                setTimeout(() => {
                    isSubmitting.value = false;
                }, 500)
                });

                toast.success('Cập nhật AI thành công!');
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
        } finally {
            isSubmitting.value = false;
        }
    };

    // Hàm đóng modal
    const closeModal = () => {
        emit('close'); // Gọi sự kiện đóng modal
    };

    // const computedCriteriaList = computed(() =>
    //     criteriaList.value.map(criteria => ({
    //         ...criteria,
    //         multiple: !!criteria.multiple // Chuyển đổi giá trị thành Boolean
    //     }))
    // );

    const removeOption = (criteria, optionIndex) => {
        criteria.value.splice(optionIndex, 1);
    }

    const saveOptionEdit = (criteria, option) => {
        option.editing = false;
        console.log(`Giá trị mới của tiêu chí ${criteria.label}: ${option.text}`);
    }
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
