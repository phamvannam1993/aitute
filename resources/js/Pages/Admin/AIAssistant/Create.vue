<template>
    <Master>
        <div
            class="bg-[url('../../public/assets/img/aiwow/layout_background.png')] bg-cover bg-center relative py-7 px-6 min-h-screen w-full"
        >
            <div
                class="flex flex-col lg:flex-row items-start justify-between lg:items-end mb-5 space-y-4 lg:space-y-0 lg:space-x-4"
            >
                <div class="w-full lg:w-3/4 flex flex-col space-y-2">
                    <div class="flex items-center space-x-2 text-white">
                        <img src="/assets/img/admin/icon_left_home.png" alt="Trang chủ" class="w-auto h-[19px]">
                        <span class="font-medium text-[#2C75E3]">/ AI</span>
                        <span class="font-medium text-[#2C75E3]"
                            >/ Tạo AI mới</span
                        >
                    </div>
                </div>
            </div>
            <div
                class="bg-white rounded-[30px] md:w-2/3 md:min-h-4/5 mx-auto p-4 md:px-24 md:pt-10 md:pb-8"
            >
                <h1 class="text-[#092A99] font-semibold text-[25px]">
                    Tạo AI mới
                </h1>
                <form @submit.prevent="showConfirmation">
                    <!-- Grid Layout -->
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                        <!-- Tên AI Field (aligned with the bottom of the image) -->
                        <div class="col-span-2 flex flex-col justify-end">
                            <label for="tenAI" class="block text-sm font-medium text-gray-700">Tên AI</label>
                            <input v-model="form.name" type="text" id="tenAI" placeholder="Nhập tên AI..." class="text-black mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring focus:border-blue-300">
                        </div>

                        <div class="col-span-2 flex flex-col justify-end">
                            <label for="tenAI" class="block text-sm font-medium text-gray-700">Loại AI</label>
                            <select v-model="form.type" class="rounded-xl border-[#5FB2FF] text-black">
                                <option v-for="(item, index) in types" :key="index" :value="item.value">{{ item.label }}</option>
                            </select>
                        </div>

                        <!-- Ảnh Upload Field -->
                        <div class="col-span-1 flex justify-center md:justify-end">
                            <div class="relative w-32 h-32 bg-blue-200 rounded-md flex items-center justify-center text-blue-500">
                                <img :src="imageUrl" v-if="imageUrl" class="object-cover w-full h-full" />
                                <span v-if="!imageUrl" class="text-sm">Ảnh</span>
                                <input @change="onImageChange" type="file" class="absolute inset-0 opacity-0 cursor-pointer">
                                <div class="absolute bottom-1 right-1 bg-white p-1 rounded-full">
                                    <img src="/assets/img/admin/icon_upload.png" class="w-6 h-6">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700">Chọn ngành nghề</label>
                        <SelectBoxSearch v-model="form.occupation_id" :operation_id = "form.operation_id"></SelectBoxSearch>
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700">Chọn nghiệp vu</label>
                        <SelectBoxOperation v-model="form.operation_id" :occupation_id = "form.occupation_id"></SelectBoxOperation>
                    </div>

                    <div class="mt-6">
                        <label for="moTa" class="block text-sm font-medium text-gray-700">Mô tả</label>
                        <textarea
                            v-model="form.description"
                            cols="50"
                            rows="8"
                            id="moTa" placeholder="Nhập mô tả ngành nghề..." class="text-black mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:border-blue-300"></textarea>
                    </div>

                    <div class="mt-4">
                        <button @click="showCriteriaModal = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700" type="button">
                            + Thêm Tiêu Chí
                        </button>
                    </div>

                    <!-- Tiêu Chí Fields -->
                    <div v-for="(criteria, index) in criteriaList" :key="index" class="mt-4">
                        <div class="panel p-4 bg-blue-50 rounded-lg shadow-md">
                            <!-- Tiêu Chí Dạng Text -->
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
                                    <label class="font-semibold text-black">{{ criteria.name || 'Nhập tên tiêu chí tại đây' }}</label>
                                    <button @click="criteria.editingName = true" class="text-blue-500 ml-2">✏️</button>
                                </div>
                                <div v-else>
                                    <input type="text" v-model="criteria.name" class="w-full p-2 border border-gray-300 rounded-lg text-black mb-2" placeholder="Nhập tên tiêu chí vào đây . Đây là 1 dạng câu trả lời ví dụ: Ngân sách  ">
                                    <button @click="criteria.editingName = false" class="bg-green-500 text-white px-2 py-1 mt-1 rounded hover:bg-green-600 mb-2">Lưu</button>
                                </div>

                                <input type="text" v-model="criteria.placeholder" class="w-full p-2 border border-gray-300 rounded-lg text-black" placeholder="Nhập placeholder vào đây">
                            </div>

                            <!-- Tiêu Chí Dạng Select Box -->
                            <div v-else-if="criteria.type === 'select'" class="mb-2">
                                <div v-if="!criteria.editingLabel">
                                    <label class="font-semibold text-black">{{ criteria.label || 'Nhập tiêu đề tiêu chí tại đây' }}</label>
                                    <button @click="criteria.editingLabel = true" class="text-blue-500 ml-2">✏️</button>
                                </div>
                                <div v-else>
                                    <input type="text" v-model="criteria.label" class="w-full p-2 border border-gray-300 rounded-lg text-black mb-2" placeholder="Nhập nhãn tiêu chí vào đây. đây là 1 dạng câu hỏi ví dụ: Ưu đãi bạn muốn đưa ra?">
                                    <button @click="criteria.editingLabel = false" class="bg-green-500 text-white px-2 py-1 mt-1 rounded hover:bg-green-600 mb-2">Lưu</button>
                                </div>
                                <!-- Name của Tiêu Chí -->
                                <div v-if="!criteria.editingName" class="mb-2">
                                    <label class="font-semibold text-black">{{ criteria.name || 'Nhập tên tiêu chí tại đây' }}</label>
                                    <button @click="criteria.editingName = true" class="text-blue-500 ml-2">✏️</button>
                                </div>
                                <div v-else>
                                    <input type="text" v-model="criteria.name" class="w-full p-2 border border-gray-300 rounded-lg text-black mb-2" placeholder="Nhập tên tiêu chí vào đây . Đây là 1 dạng câu trả lời ví dụ: Ưu đãi Khuyến Mãi">
                                    <button @click="criteria.editingName = false" class="bg-green-500 text-white px-2 py-1 mt-1 rounded hover:bg-green-600 mb-2">Lưu</button>
                                </div>

                                <div class="flex items-center space-x-2 mb-2">
                                    <!-- Checkbox để lựa chọn cho phép chọn nhiều -->
                                    <!-- Select Box -->
<!--                                    <select class="w-5/6 p-2 border border-gray-300 rounded-lg text-black my-2" v-model="criteria.selectedValue">-->
<!--                                        <option v-for="(value, index) in criteria.values" :key="index" :value="index + 1">{{ value.text }}</option>-->
<!--                                    </select>-->
                                    <div class="mb-4">
                                        <label class="block text-gray-700 font-semibold">Danh sách giá trị:</label>
                                        <ul class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                                            <li v-for="(option, optionIndex) in criteria.values" :key="optionIndex" class="flex items-center justify-between bg-gray-100 px-3 py-2 rounded-lg shadow-sm">
                                                <div class="flex items-center space-x-2 w-full">
                                                    <span v-if="!option.editing" class="truncate w-full text-black">{{ option.text }}</span>
                                                    <input v-else type="text" v-model="option.text" class="w-full p-1 border border-gray-300 rounded-md text-gray-700" placeholder="Chỉnh sửa giá trị...">
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <button type="button"  v-if="!option.editing" @click="option.editing = true" class="text-blue-500 hover:text-blue-700 text-sm">✏️</button>
                                                    <button type="button"  v-else @click="saveOptionEdit(criteria, option)" class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600 text-sm">Lưu</button>
                                                    <button type="button"  @click="removeOption(criteria, optionIndex)" class="text-red-500 hover:text-red-700 text-sm">❌</button>
                                                </div>
                                            </li>
                                        </ul>
                                        <button type="button" @click="showAddValueModal(index)" class="bg-blue-500 text-white px-4 py-2 mt-3 rounded-lg shadow hover:bg-blue-600">+ Thêm Giá Trị</button>
                                    </div>

<!--                                    <button @click="showAddValueModal(index)" class="bg-blue-500 text-white px-3 py-2 rounded-lg hover:bg-blue-600" type="button">+ Thêm</button>-->
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" v-model="criteria.multiple" :id="'allowMultiple_' + index"  class="cursor-pointer">
                                    <label :for="'allowMultiple_' + index" class="text-black cursor-pointer">Cho phép chọn nhiều</label>
                                </div>
                            </div>
                            <button @click="removeCriteria(index)" class="text-red-600 mt-2">Xóa Tiêu Chí</button>
                        </div>

                    </div>


                    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mt-5">
                        <div class="col-span-2 flex flex-col justify-end">
                            <button @click.prevent="generateSteps()" class="bg-blue-500 text-white px-4 py-2 rounded-md shadow hover:bg-blue-600 transition duration-150">Tạo step sử dụng AI</button>
                        </div>

                        <div class="col-span-2 flex flex-col justify-end">
                            <label for="tenAI" class="block text-sm font-medium text-gray-700">Mô hình AI</label>
                            <select v-model="form.ai_type" class="rounded-xl border-[#5FB2FF] pl-4 text-black cursor-pointer">
                                <option v-for="(item, index) in models" :key="index">
                                    {{ item }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2 mt-5">
                        <span class="ml-1 font-semibold my-2 text-black">Chọn tài liệu để làm nội dung tham khảo</span>
                        <div class="flex my-3  items-center">
                            <div class="flex items-center space-x-4  rounded-lg">
                                <label class="cursor-pointer inline-flex items-center  p-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-blue-600 bg-white hover:bg-blue-50">
                                    <img src="/assets/svgs/upload-icon.svg" class="pr-2"/>
                                    <v-template v-if="files.length > 0">
                                        <ul>
                                            <li v-for="(file, index) in files" :key="index">{{ file.name }}</li>
                                        </ul>
                                    </v-template>
                                    <v-template v-else>
                                        Chọn tài liệu
                                    </v-template>
                                    <input type="file" multiple class="hidden" @change="handleFileUpload" />

                                </label>
                            </div>
                        </div>
                    </div>


                    <div class="mt-6 space-y-4">
                        <h2 class="text-lg font-semibold mb-4 text-gray-700">Các bước thực hiện</h2>
                        <div v-for="(step, index) in step_ais" :key="index" class="bg-white shadow rounded-lg p-6 relative">
                            <div class="absolute top-2 right-2">
                                <button v-if="index > 0" @click.prevent="removeStep(index)" class="bg-red-500 text-white px-3 py-1 rounded-md shadow hover:bg-red-600 transition duration-150">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Tên Step</label>
                                <input v-model="step.name" type="text" placeholder="Nhập tên step..." class="w-full mt-1 text-black block border border-gray-300 rounded-lg p-2 focus:border-blue-400 focus:ring focus:ring-blue-200 transition duration-150">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Mô tả</label>
                                <textarea v-model="step.description" rows="9" placeholder="Nhập mô tả..." class="w-full mt-1 text-black block border border-gray-300 rounded-lg p-2 focus:border-blue-400 focus:ring focus:ring-blue-200 transition duration-150"></textarea>
                            </div>
                        </div>

                        <button @click.prevent="addStep" class="bg-blue-500 text-white px-4 py-2 rounded-md shadow hover:bg-blue-600 transition duration-150">+ Thêm bước</button>
                    </div>

                    <!-- Lưu Button -->
                    <div class="mt-6 flex justify-end">
                        <div class="flex items-center pr-6">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       v-model="form.is_public"
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
                        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-md shadow-md hover:bg-blue-600">Lưu</button>
                    </div>
                </form>
                <!-- Dialog xác nhận -->
                    <div v-if="showConfirmationDialog" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white p-6 rounded shadow-md">
                            <h3 class="text-lg font-semibold mb-4 text-black">Xác nhận</h3>
                            <p class="text-black">Bạn có chắc chắn muốn tạo AI không?</p>
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
                    <button @click="closeModal" class="bg-gray-300 px-4 py-2 rounded-lg hover:bg-gray-400">Hủy</button>
                </div>
            </div>
        </div>

        <div v-if="loadingSearch" class="loading-overlay">
            <div class="spinner"></div>
        </div>
    </Master>
</template>

<script setup>
import { ref } from "vue";
import {Head, useForm} from "@inertiajs/vue3";
import Master from "../../../Layouts/Admin/Master.vue";
import {toast} from "vue3-toastify";
import SelectBoxSearch from "../../../Components/SelectBoxSearch.vue";
import SelectBoxOperation from "./SelectBoxOperation.vue"
// Reactive data
const imageUrl = ref(null);

const showConfirmationDialog = ref(false);
const showCriteriaModal = ref(false);
const selectedCriteriaType = ref("input");
const criteriaList = ref([]);

const isSubmitting = ref(false);
const step_ais = ref([{ name: '', description: '' }]);
const loadingSearch = ref(false);
const file = ref(null);
const form = useForm({
    occupation_id: '',
    operation_id: '',
    name: '',
    type: 'text',
    description: '',
    thumbnail: null,
    step_ais: step_ais.value,
    ai_type: "Claude-3.5 Sonnet",
    is_public: true,
});
const selectedModel = ref("Claude-3.5 Sonnet");
const models = ["Claude-3.5 Sonnet", "GPT-4o mini", "GPT-4o"];
const types = ref([
    { value: 'text', label: 'Văn bản' },
    { value: 'image', label: 'Hình ảnh' },
    { value: 'video', label: 'Video' }
]);

const addCriteria = () => {
    if (selectedCriteriaType.value === 'input' || selectedCriteriaType.value === 'select') {
        criteriaList.value.push({ type: selectedCriteriaType.value, value: '', label: '', name: '', editingLabel: false, multiple: false, values: [], selectedValue: null , placeholder: ''});
        closeModal();
    } else {
        alert("Vui lòng chọn loại tiêu chí.");
    }
};

const showAddValueModal = (index) => {
    if (criteriaList.value[index].type === 'select') {
        const newText = prompt("Nhập giá trị mới:");
        if (newText) {
            if (!Array.isArray(criteriaList.value[index].values)) {
                criteriaList.value[index].values = [];
            }

            const newValue = criteriaList.value[index].values.length + 1;
            console.error('Tiêu chí không phải dạng select.', criteriaList.value[index].values);
            criteriaList.value[index].values.push({ value: newValue, text: newText });
            // Đặt giá trị mới thêm vào làm giá trị mặc định
            criteriaList.value[index].selectedValue = newValue;
        }
    } else {
        console.error('Tiêu chí không phải dạng select.');
    }
    console.log("criteriaList", criteriaList, index)

};

// Lưu giá trị sau khi chỉnh sửa
const saveOptionEdit = (criteria, option) => {
    option.editing = false;
};

// Xóa giá trị
const removeOption = (criteria, optionIndex) => {
    console.log("criteria", criteria, optionIndex)
    criteria.values.splice(optionIndex, 1);
};



// Hàm đóng modal
const closeModal = () => {
    showCriteriaModal.value = false;
};

// Hàm xóa tiêu chí
const removeCriteria = (index) => {
    criteriaList.value.splice(index, 1);
};
const steps = ref([]);
const generateSteps = async () => {
    try {
        loadingSearch.value = true;
        const formData = new FormData();
        formData.append('occupation_id', form.occupation_id);
        formData.append('mo_ta', form.description);
        formData.append('operation_id', form.operation_id);
        formData.append('model', form.ai_type || 'Claude-3.5 Sonnet');
        // Append multiple files to formData
        files.value.forEach((file, index) => {
            formData.append(`file[${index}]`, file);
        });

        const response = await axios.post(route('admin.generateStep'), formData);
        console.log("response", response);
        if (response.data.success) {
            step_ais.value = [];
            response.data.steps.forEach((step) => {
                step_ais.value.push({ name: step.step, description: step.promt });
            });
            console.log("step_ais", step_ais)
        } else {
            alert('Error: ' + response.data.message)
        }
    } catch (error) {
        if (error.response && error.response.data.errors) {
            Object.values(error.response.data.errors).forEach(errorMsg => {
                toast.error(errorMsg);
            });
        }
        console.error('Error generating steps:', error);
    } finally {
    setTimeout(() => {
        loadingSearch.value = false;
    }, 500);
}
};

const addStep = () => {
    step_ais.value.push({ name: '', description: '' });
};

const removeStep = (index) => {
    if (index > 0) {
        step_ais.value.splice(index, 1);
    }
};


// Function to show confirmation dialog
const showConfirmation = () => {
    showConfirmationDialog.value = true;
};
const confirmSubmit = async () => {
    isSubmitting.value = true;

    const invalidSteps = step_ais.value.some(step => !step.name || !step.description);
    if (invalidSteps) {
        toast.error('Vui lòng nhập đầy đủ tên và mô tả cho các bước.');
        isSubmitting.value = false;
        return;
    }

    const formData = new FormData();
    formData.append('occupation_id', form.occupation_id);
    formData.append('operation_id', form.operation_id);
    formData.append('name', form.name);
    formData.append('type', form.type);
    formData.append('description', form.description);
    formData.append('ai_type', form.ai_type || 'Claude-3.5 Sonnet');
    formData.append('is_public', form.is_public);

    step_ais.value.forEach((step, index) => {
       formData.append(`step_ais[${index}][name]`, step.name);
       formData.append(`step_ais[${index}][description]`, step.description);
    });
    console.log("criteriaList", criteriaList.value);
    criteriaList.value.forEach((criteria, index) => {
        formData.append(`criteria[${index}][type]`, criteria.type);
        formData.append(`criteria[${index}][label]`, criteria.label || '');
        formData.append(`criteria[${index}][name]`, criteria.name || '');
        if (criteria.type === 'input') {
            formData.append(`criteria[${index}][value]`, criteria.value);
            formData.append(`criteria[${index}][placeholder]`, criteria.placeholder);
        } else if (criteria.type === 'select') {
            formData.append(`criteria[${index}][value]`, JSON.stringify(criteria.values));
            // Gửi giá trị đã chọn (nếu có)
            if (criteria.selectedValue) {
                formData.append(`criteria[${index}][selectedValue]`, criteria.selectedValue);
            }
            formData.append(`criteria[${index}][multiple]`, criteria.multiple ? 'true' : 'false');
        }
    });
    if (files.value && files.value.length > 0) {
        files.value.forEach((file, index) => {
            formData.append(`file[${index}]`, file);
        });
    }

    try {
        const response = await axios.post(route('admin.ai-assistants.store'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        });

        if (response && response.data && response.data.success) {
            toast.success('Dữ liệu đã được gửi thành công.');
            window.location.href = response.data.redirect;
            setTimeout(() => {
                isSubmitting.value = false;
            }, 500);
            cancelSubmit();
        } else {
            toast.error('Đã xảy ra lỗi trong quá trình gửi dữ liệu.');
        }
    } catch (error) {
        console.error('Error during submission:', error);

        if (error.response && error.response.data && error.response.data.message) {
            toast.error(error.response.data.message);
        } else {
            toast.error('Đã xảy ra lỗi không xác định trong quá trình gửi.');
        }
    } finally {
        isSubmitting.value = false;
    }
};

const files = ref([]); // Store multiple files
const handleFileUpload = (event) => {
    files.value = Array.from(event.target.files); // Store all selected files

    // Validate each file
    const validFileTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    files.value.forEach((file, index) => {
        if (!validFileTypes.includes(file.type)) {
            toast.error('File phải có định dạng: doc, docx, pdf.');
            files.value.splice(index, 1); // Remove invalid file
        }

        // else if (file.size > 10 * 1024 * 1024) {
        //     toast.error('File không được vượt quá 10MB.');
        //     files.value.splice(index, 1); // Remove oversized file
        // }
    });
};

// const handleFileUpload = (event) => {
//     const selectedFile = event.target.files[0];
//
//     if (!selectedFile) {
//         return;
//     }
//
//     const validFileTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
//
//     if (!validFileTypes.includes(selectedFile.type)) {
//         toast.error('File phải có định dạng: doc, docx, pdf.');
//         file.value = null;
//         return;
//     }
//
//     if (selectedFile.size > 10 * 1024 * 1024) {
//         toast.error('File không được vượt quá 10MB.');
//         file.value = null;
//         return;
//     }
//
//     file.value = selectedFile || null;
// };

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
        form.thumbnail = null; // Xóa ảnh khỏi form nếu không hợp lệ
        imageUrl.value = ''; // Xóa preview nếu có ảnh đã được hiển thị
        return;
    }

    // Kiểm tra kích thước file (giới hạn 10MB)
    // if (file.size > 10 * 1024 * 1024) { // 10MB = 10 * 1024 * 1024 bytes
    //     toast.error('File ảnh không được vượt quá 10MB.');
    //     form.thumbnail = null;
    //     imageUrl.value = '';
    //     return;
    // }

    // Nếu file hợp lệ, lưu ảnh vào form và hiển thị preview
    form.thumbnail = file;

    const reader = new FileReader();
    reader.onload = (e) => {
        imageUrl.value = e.target.result; // Hiển thị preview ảnh
    };
    reader.readAsDataURL(file);
};
</script>
<style scoped>

    .card {
        background-color: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        padding: 20px;
        position: relative;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
    }

    .card-body {
        margin-top: 15px;
    }

    .card-footer {
        display: flex;
        justify-content: flex-end;
        margin-top: 10px;
    }

    button i {
        margin-right: 4px;
    }

    textarea:focus, input:focus {
        outline: none;
        box-shadow: 0 0 0 2px rgba(3, 102, 214, 0.3);
    }

    button[disabled] {
        background-color: #ccc;
        cursor: not-allowed;
    }
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

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .spinner {
        border: 6px solid rgba(255, 255, 255, 0.3);
        border-left-color: #ffffff;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        100% {
            transform: rotate(360deg);
        }
    }
</style>
