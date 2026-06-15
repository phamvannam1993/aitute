<template>
    <div class="mb-8">
        <div class="bg-white w-full rounded-[25px] shadow-lg py-[16px] md:py-[40px] px-[20px] md:px-[60px] mt-4 ">
            <h2 class="text-center mb-4 text-[14px] md:text-[18px] xl:text-[24px] font-semibold text-black">
                Hãy chọn lĩnh vực bạn quan tâm
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-8">
                <div
                    v-for="(field, index) in fields"
                    :key="index"
                    @click="toggleField(field.id)"
                    :class="{
                      'relative w-full h-auto rounded-lg cursor-pointer hover:shadow-lg transition overflow-hidden mx-auto': true,
                      'col-span-2 h-[112px] md:col-span-1 md:h-auto': index === fields.length - 1
                    }">

                    <div class="flex justify-between items-center bg-[#E2E2EAE0] absolute top-0 left-0 right-0 px-2 z-10">
                        <span class="text-[#545252] text-[10px] md:text-[14px] xl:text-[20px] font-medium text-center w-full">{{ field.name }}</span>

                        <div
                            class="w-4 h-4 rounded-full border-2"
                            :class="isSelected(field.id) ? 'bg-green-500 border-white' : 'bg-[#666666] border-[#666666]'">
                        </div>
                    </div>

                    <img :src="field.image" :alt="field.name" class="w-full h-full object-cover rounded-[10px]" />
                </div>
            </div>
        </div>
        <div v-if="showConfirm" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50 text-black">
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <img src="/assets/img/logo-small.png" alt="Icon" class="w-30 h-12 mb-4" />
                <h2 class="text-lg font-bold mb-4">Xác nhận hành động</h2>
                <p class="mb-4">Bạn có muốn tắt chọn hiển thị chọn lĩnh vực khi đăng nhập không?</p>
                <label class="flex items-center space-x-2 mb-4">
                    <input type="checkbox" v-model="disableDisplayOnLogin" class="form-checkbox h-5 w-5 text-blue-600">
                    <span class="text-gray-700">Tắt hiển thị chọn lĩnh vực khi đăng nhập</span>
                </label>
                <div class="flex justify-end space-x-4">
                    <button @click="closeConfirm" class="px-4 py-2 bg-gray-300 rounded-md">Hủy</button>
                    <button @click="submitSelection" class="px-4 py-2 bg-blue-600 text-white rounded-md">Xác nhận</button>
                </div>
            </div>
        </div>
        <div class="w-full flex justify-center">
            <button
                class="mt-6 w-[128px] bg-[#092A99] hover:bg-blue-600 text-white py-2 text-[16px] rounded-[3px]"
                @click="openConfirm"
            >
                Xác nhận
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, defineEmits } from 'vue'
import { toast } from 'vue3-toastify';

const props = defineProps({
    fields: Array
})

const selectedFields = ref([])

const emit = defineEmits()
const showConfirm = ref(false);
const disableDisplayOnLogin = ref(false);
const toggleField = (id) => {
    if (isSelected(id)) {
        selectedFields.value = selectedFields.value.filter(i => i !== id)
    } else {
        selectedFields.value.push(id)
    }
    console.log(selectedFields.value)
}

const isSelected = (id) => {
    return selectedFields.value.includes(id)
};

const openConfirm = () => {
    showConfirm.value = true;
};

const closeConfirm = () => {
    showConfirm.value = false;
};

const submitSelection = async () => {
    if (selectedFields.value.length > 0) {
        try {
            const response = await axios.post(route('home.enterprise-field'), {
                field_ids: selectedFields.value,
                first_login: !disableDisplayOnLogin.value,
            })

            if (response.status === 200) {
                toast.success('Chọn lĩnh vực thành công!', {
                    position: 'top-right',
                    autoClose: 3000,
                });
                closeEnterpriseField();
            }
        } catch (error) {
            console.error(error)
        }
    } else {
        toast.error('Vui lòng chọn ít nhất một lĩnh vực!', {
            position: 'top-right',
            autoClose: 3000,
        });
    }
}

const closeEnterpriseField = () => {
    emit('closeEnterpriseField')
}
</script>
