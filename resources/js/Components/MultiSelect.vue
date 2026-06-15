<template>
    <div class="relative w-full">
        <!-- Các mục đã chọn -->
        <div class="flex flex-wrap gap-2 p-2 border rounded-lg bg-white">
            <div
                v-for="(option, index) in selectedOptions"
                :key="index"
                class="bg-blue-500 text-white px-2 py-1 rounded-full flex items-center gap-2"
            >
                {{ option.name }}
                <button
                    @click="removeOption(option)"
                    class="focus:outline-none"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Input tìm kiếm -->
            <input
                v-if="!inputHidden"
                type="text"
                v-model="searchText"
                @focus="dropdownVisible = true"
                @blur="hideDropdown"
                @input="filterOptions"
                placeholder="Lọc theo nghề nghiệp..."
                class="text-black w-full p-2 pl-10 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
            />
        </div>

        <!-- Dropdown danh sách tuỳ chọn -->
        <ul v-if="dropdownVisible" class="absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-40 overflow-y-auto">
            <li
                v-for="option in filteredOptions"
                :key="option.id"
                @click="selectOption(option)"
                class="p-2 hover:bg-blue-500 hover:text-white cursor-pointer text-black"
            >
                {{ option.name }}
            </li>
        </ul>
    </div>
</template>

<script setup>
    import { ref, computed } from 'vue'

    // Nhận props options từ component parent
    const props = defineProps({
        options: {
            type: Object,
            required: true
        }
    })
    const emit = defineEmits(['update:selectedOptions']);

    // Biến và hàm logic
    const searchText = ref('')
    const dropdownVisible = ref(false)
    const inputHidden = ref(false)
    const selectedOptions = ref([])

    // Lọc các tùy chọn dựa trên tìm kiếm và loại bỏ mục đã chọn
    const filteredOptions = computed(() => {
        const search = searchText.value.toLowerCase();

        // Kiểm tra nếu props.options tồn tại và là một mảng
        if (!props.options || !Array.isArray(props.options)) {
            return [];
        }

        return props.options.filter(option =>
            option.name.toLowerCase().includes(search) &&
            !selectedOptions.value.some(selected => selected.id === option.id)
        );
    });

    // Hàm để chọn một tùy chọn
    const selectOption = (option) => {
        selectedOptions.value.push(option)
        searchText.value = ''
        inputHidden.value = true
        setTimeout(() => {
            inputHidden.value = false
        }, 200)

        const selectedIds = selectedOptions.value.map(option => option.id);
        emit('update:selectedOptions', selectedIds);
    }

    // Hàm để xóa một tùy chọn
    const removeOption = (option) => {
        selectedOptions.value = selectedOptions.value.filter(
            selected => selected.id !== option.id
        )
        const selectedIds = selectedOptions.value.map(option => option.id);
        emit('update:selectedOptions', selectedIds);
    }

    // Ẩn dropdown sau khi mất focus
    const hideDropdown = () => {
        setTimeout(() => {
            dropdownVisible.value = false
        }, 200)
    }
</script>

<style scoped>
</style>
