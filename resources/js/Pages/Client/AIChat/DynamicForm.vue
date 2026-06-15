<template>
    <div class="dynamic-form bg-white p-6 rounded-2xl shadow-lg max-w-4xl mx-auto text-black">
        <div class="mb-10">
            <div class="prose prose-lg max-w-none">
                <div v-html="formattedTitle" class="formatted-content"></div>
            </div>
        </div>
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <div v-for="field in fields" :key="field.id"
                 class="form-field transition-all duration-200 rounded-xl" :class="isHistory && 'cursor-not-allowed'">
                <label :for="`${instanceId}-${field.id}`" :class="field.type === 'checkbox' && !field.options && 'hidden'" class="block text-base font-semibold mb-2">
                    {{ field.label }}
                    <span v-if="field.required" class="text-red-500 ml-1">*</span>
                </label>

                <input v-if="field.type === 'number' || field.type === 'text' || field.type === 'email'"
                       :type="field.type"
                       :id="`${instanceId}-${field.id}`"
                       v-model.string="formData[field.id]"
                       :placeholder="field.placeholder"
                       :required="field.required"
                       :disabled="isHistory"
                       :min="field.type === 'number' ? 1 : null"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">

                <input v-if="field.type === 'phone'"
                       type="text"
                       :id="`${instanceId}-${field.id}`"
                       v-model="formData[field.id]"
                       :placeholder="field.placeholder"
                       :disabled="isHistory"
                       :required="field.required"
                       @input="handlePhoneInput($event)"
                       maxlength="10"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">

                <div v-if="field.type === 'checkbox' && !field.options" class="flex items-center">
                    <input
                        type="checkbox"
                        :id="`${instanceId}-${field.id}`"
                        v-model="formData[field.id]"
                        :name="field.name"
                        :disabled="isHistory"
                        :value="field.label"
                        class="h-5 w-5 text-blue-500 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                    <label :for="`${instanceId}-${field.id}`" class="ml-2 text-sm text-gray-700">
                        {{ field.label }}
                    </label>
                </div>

                <input v-if="field.type === 'date'"
                    type="date"
                    :id="`${instanceId}-${field.id}`"
                    v-model="formData[field.id]"
                    :placeholder="field.placeholder"
                    :required="field.required"
                    :disabled="isHistory"
                    :min="today"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">

                <input v-if="field.type === 'time'"
                    type="time"
                    :id="`${instanceId}-${field.id}`"
                    v-model="formData[field.id]"
                    :placeholder="field.placeholder"
                    :required="field.required"
                    :disabled="isHistory"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">

                <!-- Radio buttons -->
                <div v-if="field.type === 'radio'" class="grid grid-cols-2 gap-4 mt-2">
                    <div v-for="option in field.options" :key="option.value" class="relative">
                        <input type="radio"
                               :id="`${instanceId}-${field.id}-${option.value}`"
                               :name="`${instanceId}-${field.id}`"
                               :value="option.value"
                               :checked="formData[field.id] === option.value"
                               @change="formData[field.id] = option.value"
                               :disabled="isHistory"
                               :required="field.required"
                               class="peer absolute opacity-0">
                        <label :for="`${instanceId}-${field.id}-${option.value}`"
                               class="block w-full p-1 lg:p-3 text-center border rounded-lg cursor-pointer transition-all duration-200
                                      peer-checked:bg-blue-50 peer-checked:border-blue-500 peer-checked:text-blue-600
                                      hover:bg-gray-50 h-full">
                            {{ option.label }}
                            <p v-if="option.signs" class="text-[10px] lg:text-xs text-gray-600">({{option.signs }})</p>
                        </label>
                    </div>
                </div>

                <!-- Multiselect -->
                <div v-if="field.type === 'multiselect'" class="grid grid-cols-2 gap-3 mt-2">
                    <div v-for="option in field.options" :key="option.value" class="relative">
                        <input type="checkbox"
                               :id="`${instanceId}-${field.id}-${option.value}`"
                               :value="option.value"
                               v-model="formData[field.id]"
                               :disabled="isHistory"
                               class="peer absolute opacity-0">
                        <label :for="`${instanceId}-${field.id}-${option.value}`"
                               class="block w-full p-3 text-sm border rounded-lg cursor-pointer transition-all duration-200
                                      peer-checked:bg-blue-50 peer-checked:border-blue-500 peer-checked:text-blue-600
                                      hover:bg-gray-50">
                            {{ option.label }}
                        </label>
                    </div>
                </div>

                <!-- Checkboxes -->
                <div v-if="field.type === 'checkbox'" class="grid grid-cols-2 gap-3 mt-2">
                    <div v-for="option in field.options" :key="option.value" class="relative">
                        <input type="checkbox"
                               :id="`${instanceId}-${field.id}-${option.value}`"
                               :value="option.value"
                               :checked="formData[field.id]?.includes(option.value)"
                               @change="toggleCheckbox(field.id, option.value)"
                                :disabled="isHistory"
                               class="peer absolute opacity-0">
                        <label :for="`${instanceId}-${field.id}-${option.value}`"
                               class="block w-full p-3 text-sm border rounded-lg cursor-pointer transition-all duration-200
                                      peer-checked:bg-blue-50 peer-checked:border-blue-500 peer-checked:text-blue-600
                                      hover:bg-gray-50">
                            {{ option.label }}
                        </label>
                    </div>
                </div>

                <!-- Textarea -->
                <textarea v-if="field.type === 'textarea'"
                          :id="`${instanceId}-${field.id}`"
                          v-model="formData[field.id]"
                          :placeholder="field.placeholder"
                           :disabled="isHistory"
                          rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </textarea>

                <!-- File input -->
                <div v-if="field.type === 'file'" class="mt-2 space-y-4">
                    <label v-if="!isHistory" class="block w-full px-4 py-3 border border-gray-300 rounded-lg cursor-pointer
                        hover:bg-gray-50 transition-all duration-200" :class="{ 'btn-pulseTest': disableSubmitImage }">
                        <input type="file"
                               :id="`${instanceId}-${field.id}`"
                               :multiple="field.multiple"
                               @change="handleFileChange($event, field)"
                               :disabled="isHistory"
                               :accept="field.accept || 'image/*'"
                               class="hidden">
                        <span class="flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Tải file ảnh
                        </span>
                    </label>

                    <!-- File list -->
                    <div v-if="formData[field.id] && formData[field.id].length > 0" class="space-y-2">
                        <div v-if="!field.multiple && formData[field.id].length > 1"
                             class="text-red-500 text-sm">
                            Chỉ được phép upload 1 file
                        </div>

                        <div v-for="(file, index) in formData[field.id]"
                             :key="index"
                             class="flex items-center justify-between p-3 border rounded-lg bg-gray-50">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 flex items-center justify-center">
                                    <img v-if="file.type.startsWith('image/')"
                                         :src="getFilePreview(file)"
                                         class="w-10 h-10 object-cover rounded"
                                         :alt="file.name">
                                    <svg v-else xmlns="http://www.w3.org/2000/svg"
                                         class="h-8 w-8 text-gray-400"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate md:truncate-none hidden md:block">
                                        {{ file.name }}
                                    </p>
                                    <p class="text-sm font-medium text-gray-900 truncate md:truncate-none md:hidden">
                                        {{ file.name.slice(0, 20) }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ formatFileSize(file.size) }}
                                    </p>
                                </div>
                            </div>
                            <button type="button"
                                    @click="removeFile(field.id, index)"
                                    class="p-1 rounded-full text-red-500 hover:bg-red-50 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-5 w-5"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <label v-if="field.type === 'note_form'" :for="`${instanceId}-${field.id}`" class="block text-base font-semibold mb-2">
                    {{ field.content }}
                </label>
            </div>

            <!-- Form buttons -->
            <div class="flex items-center justify-between gap-4 mt-8 pt-4 border-t" v-if="!isHistory">
<!--                <button type="button"-->
<!--                        @click="$emit('cancel')"-->
<!--                        class="px-6 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50-->
<!--                               transition-all duration-200 text-gray-700">-->
<!--                    Hủy-->
<!--                </button>-->
                <p v-if="fields.some(item => item.type === 'file')" class="text-sm italic text-red-600 font-medium animate-pulse">
                    ⚠️ Vui lòng tải ảnh lên, sau đó ấn xác nhận và tiếp tục trò truyện được với Bác Sĩ A.I
                </p>
                <p v-else></p>
                <div class="flex items-center gap-4">
                    <button type="submit" :disabled="disableCheckboxReason" class="px-7 py-4 text-base lg:text-lg font-bold bg-primary-lurcinn text-white rounded-lg
                                hover:bg-primary-lurcinn/80 transition-all duration-200
                                disabled:bg-gray-300 disabled:cursor-not-allowed
                                shadow-sm hover:shadow-md" v-if="fields.some(item => item.id.includes('reasons'))">
                        Xem thêm các nguyên nhân khác
                    </button>
                    <button :disabled="disableSubmitImage || (fields.some(item => item.id.includes('reasons')) && !disableCheckboxReason)" type="submit"
                            class="px-7 py-4 text-base lg:text-lg font-bold bg-blue-600 text-white rounded-lg
                                hover:bg-blue-700 transition-all duration-200
                                disabled:bg-gray-300 disabled:cursor-not-allowed
                                shadow-sm hover:shadow-md">
                        Xác nhận
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>


<script setup>
    // Script giữ nguyên không thay đổi
    import { ref, defineProps, defineEmits, onMounted , onBeforeUnmount, computed, watch} from 'vue';
    import { marked } from "marked";

    const props = defineProps({
        name: String,
        fields: {
            type: Array,
            required: true
        },
        isHistory: {
            type: Boolean,
            default: false
        },
        user: {
            type: Object,
            default: null
        },
        agentUser: {
            type: Object,
            default: null
        }
    });

    const disableCheckboxReason = ref(false);
    const today = new Date().toISOString().split('T')[0];

    console.log(props.isHistory);


    // Tạo một ID duy nhất cho mỗi instance của form
    const instanceId = 'form-' + Math.random().toString(36).substr(2, 9);

    const emit = defineEmits(['submit', 'cancel']);
    const formData = ref({});
    const filePreviewUrls = ref({});
    const disableSubmitImage = ref(props.fields.some(field => field.type === 'file'));
    const getFilePreview = (file) => {
        if (!filePreviewUrls.value[file.name]) {
            filePreviewUrls.value[file.name] = URL.createObjectURL(file);
        }
        return filePreviewUrls.value[file.name];
    };

    const formattedTitle = computed(() => {
        const titleContent = getTitleForm();
        return titleContent ? marked(titleContent) : '';
    });

    const getTitleForm = () => {
        const titleFormField = props.fields.find(field => field.type === 'title_form');
        return titleFormField?.content || '';
    };

    const handleFileChange = (event, field) => {
        const allowedExtensions = ["image/png", "image/jpeg", "image/jpg"];
        const newFiles = Array.from(event.target.files);

        // Kiểm tra xem tất cả các tệp có phải là hình ảnh hợp lệ không
        const isValid = newFiles.every(file => allowedExtensions.includes(file.type));

        if (!isValid) {
            alert("Vui lòng chọn lại ảnh có định dạng .png, .jpg, .jpeg");
            return false;
        }

        if (!formData.value[field.id]) {
            formData.value[field.id] = [];
        }

        // Sử dụng trực tiếp field.multiple
        if (!field.multiple) {
            // Single file mode
            formData.value[field.id] = [newFiles[0]];
            if (newFiles.length > 1) {
                alert("Chỉ được phép tải lên 1 ảnh");
            }
        } else {
            // Multiple files mode
            formData.value[field.id] = [...formData.value[field.id], ...newFiles];
        }

        disableSubmitImage.value = false;
        event.target.value = "";
    };
    const toggleCheckbox = (fieldId, value) => {
        if (!formData.value[fieldId]) {
            formData.value[fieldId] = [];
        }
        const index = formData.value[fieldId].indexOf(value);
        if (index === -1) {
            formData.value[fieldId].push(value);
        } else {
            formData.value[fieldId].splice(index, 1);
        }
    };

    const removeFile = (fieldId, index) => {
        const file = formData.value[fieldId][index];
        if (filePreviewUrls.value[file.name]) {
            URL.revokeObjectURL(filePreviewUrls.value[file.name]);
            delete filePreviewUrls.value[file.name];
        }
        formData.value[fieldId] = formData.value[fieldId].filter((_, i) => i !== index);
        disableSubmitImage.value = true;
    };
    const handleSubmit = () => {
        // Tạo object để chứa dữ liệu form
        const formDataObj = {};

        // Duyệt qua tất cả các field trong formData
        Object.entries(formData.value).forEach(([fieldName, value]) => {
            if (Array.isArray(value) && value.length > 0 && value[0] instanceof File) {
                // Nếu là file, chỉ lưu tên file vào formDataObj
                formDataObj[fieldName] = value.map(file => file.name);
            } else {
                // Nếu không phải file, lưu giá trị nguyên bản
                formDataObj[fieldName] = value;
            }
        });

        // Tách riêng các file để gửi
        const filesObj = {};
        Object.entries(formData.value).forEach(([fieldName, value]) => {
            if (Array.isArray(value) && value.length > 0 && value[0] instanceof File) {
                filesObj[fieldName] = value;
            }
        });
        formDataObj.addressSpa = props.agentUser ? props.agentUser.address : '';
        // Emit sự kiện submit với dữ liệu form và files
        emit('submit', {
            formData: formDataObj,
            files: filesObj
        });
    };
    const formatFileSize = (bytes) => {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    };

    const initializeFormData = () => {
        props.fields.forEach(field => {
            if (['action', 'title_form'].includes(field.type)) return;

            switch (field.type) {
                case 'multiselect':
                case 'checkbox':
                    formData.value[field.id] = field.value || [];
                    break;

                case 'file':
                    formData.value[field.id] = [];
                    break;

                case 'radio':
                case 'textarea':
                case 'text':
                case 'number':
                case 'email':
                case 'phone':
                    formData.value[field.id] = field.value || '';
                    break;

                default:
                    formData.value[field.id] = field.value || '';
            }
        });

        if (props.user) {
            formData.value['name'] = props.user.name;
            formData.value['phone'] = props.user.phone;
            formData.value['email'] = props.user.email;
        }
    };

    onMounted(() => {
        initializeFormData();
    });

    watch(() => props.fields, () => {
        initializeFormData();
    }, { deep: true, immediate: true });


    const handlePhoneInput = (event) => {
        let value = event.target.value;
        value = value.replace(/\D/g, '');
        value = value.slice(0, 10);
        event.target.value = value;
        const fieldId = event.target.id;
        formData.value[fieldId] = value;
    };

    onBeforeUnmount(() => {
        Object.values(filePreviewUrls.value).forEach(url => {
            URL.revokeObjectURL(url);
        });
    });

    watch(formData, (newValue) => {
        // Kiểm tra xem có bất kỳ trường nào là checkbox và có giá trị không
        const hasCheckbox = Object.keys(newValue).some(key => {
            const field = props.fields.find(f => f.id === key);
            return field && field.type === 'checkbox' && newValue[key].length > 0 && field.id.includes('reasons');
        });

        // Nếu có checkbox, không cho phép submit
        disableCheckboxReason.value = hasCheckbox;
    }, { deep: true });
</script>
<style scoped>
    /* Add these styles for markdown formatting */
    .formatted-content :deep(h1) {
        @apply text-2xl font-bold mb-6;
    }

    .formatted-content :deep(h2) {
        @apply text-xl font-semibold mt-6 mb-4;
    }

    .formatted-content :deep(ul) {
        @apply space-y-2 my-4 list-disc list-inside;
    }

    .formatted-content :deep(li) {
        @apply leading-relaxed;
    }

    .formatted-content :deep(p) {
        @apply mb-4 leading-relaxed text-base;
    }

    .formatted-content :deep(strong) {
        @apply font-medium;
    }

    /* Add spacing between sections */
    .formatted-content :deep(* + h2) {
        @apply mt-8;
    }

    /* Style for bullet points */
    .formatted-content :deep(ul li) {
        @apply pl-4;
    }

    /* Add some hover effects */
    .formatted-content :deep(li:hover) {
        @apply bg-gray-50 rounded-lg transition-colors duration-200;
    }
    .btn-pulseTest {
    animation: pulseTest 0.5s infinite 0s cubic-bezier(0.5, 0, 0, 1);
    box-shadow: 0 0 0 0 #702EE7;
}

@keyframes pulseTest {
    to {
        box-shadow: 0 0 0 8px rgba(255, 255, 255, 0);
    }
}

.btn-pulseTest-min {
    animation: pulseTestMin 0.5s infinite 0s cubic-bezier(0.5, 0, 0, 1);
    box-shadow: 0 0 0 0 #702EE7;
}
</style>
