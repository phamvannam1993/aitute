<template>
    <div class="relative w-full">
        <div class="relative">
            <div class="relative">
                <div
                    @click="toggleDropdown"
                    class="w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg cursor-pointer flex items-center justify-between"
                >
                    <div class="flex items-center justify-between w-full">
                        <span>{{ selectedOption ? selectedOption.name : placeholder }}</span>

                        <!-- Thêm nút xóa khi đã có lựa chọn -->
                        <div class="flex items-center">
                            <button
                                v-if="selectedOption"
                                @click.stop="clearSelection"
                                class="mr-2 text-gray-400 hover:text-gray-600"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>
                <!-- Rest of the code remains the same -->
            </div>

            <!-- Dropdown -->
            <div v-if="isOpen" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg">
                <!-- Search input -->
                <div class="p-2 border-b">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none">
                                <path d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <input
                            type="text"
                            v-model="searchQuery"
                            :placeholder="searchPlaceholder"
                            class="w-full pl-10 pr-4 py-2 text-sm text-gray-700 bg-gray-50 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500"
                            @click.stop
                        />
                    </div>
                </div>

                <!-- Options list -->
                <ul class="max-h-60 overflow-y-auto">
                    <li
                        v-for="option in filteredOptions"
                        :key="option.id"
                        @click="selectOption(option)"
                        class="px-4 py-2 hover:bg-blue-50 cursor-pointer text-gray-700"
                    >
                        {{ option.name }}
                    </li>
                    <li v-if="filteredOptions.length === 0" class="px-4 py-2 text-gray-500 text-center">
                        Không tìm thấy kết quả
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, computed, onMounted, onBeforeUnmount } from 'vue'

    const props = defineProps({
        options: {
            type: Array,
            required: true,
            default: () => []
        },
        placeholder: {
            type: String,
            default: 'Chọn một mục'
        },
        searchPlaceholder: {
            type: String,
            default: 'Tìm kiếm...'
        },
        modelValue: {
            type: [String, Number],
            default: ''
        }
    })

    const emit = defineEmits(['update:modelValue', 'change'])

    const searchQuery = ref('')
    const isOpen = ref(false)

    // Hàm chuẩn hóa chuỗi để tìm kiếm
    const normalizeStr = (str) => {
        return str.toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '') // Bỏ dấu tiếng Việt
            .replace(/đ/g, 'd').replace(/Đ/g, 'D') // Xử lý chữ đ
    }

    // Computed Properties với chuỗi đã được chuẩn hóa
    const filteredOptions = computed(() => {
        const query = normalizeStr(searchQuery.value.trim())
        if (!query) return props.options

        return props.options.filter(option => {
            const normalizedName = normalizeStr(option.name)
            return normalizedName.includes(query)
        })
    })

    const selectedOption = computed(() => {
        return props.options.find(option => Number(option.id) === Number(props.modelValue))
    })

    // Methods
    const toggleDropdown = () => {
        isOpen.value = !isOpen.value
        if (isOpen.value) {
            searchQuery.value = ''
        }
    }

    const selectOption = (option) => {
        emit('update:modelValue', option.id)
        emit('change', option)
        isOpen.value = false
        searchQuery.value = ''
    }
    // Thêm method clearSelection
    const clearSelection = () => {
        emit('update:modelValue', '')
        emit('change', null)
    }

    // Click outside to close dropdown
    const handleClickOutside = (event) => {
        const selectBox = event.target.closest('.relative')
        if (!selectBox) {
            isOpen.value = false
        }
    }

    // Lifecycle hooks
    onMounted(() => {
        document.addEventListener('click', handleClickOutside)
    })

    onBeforeUnmount(() => {
        document.removeEventListener('click', handleClickOutside)
    })

</script>

<style scoped>
    .max-h-60 {
        max-height: 15rem;
    }
</style>
