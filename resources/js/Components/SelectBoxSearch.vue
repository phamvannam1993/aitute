<template>
    <div class="relative w-full">
        <input
            type="text"
            v-model="searchQuery"
            @input="debouncedSearch"
            @focus="handleFocus"
            @blur="handleBlur"
            placeholder="Tìm kiếm ngành nghề..."
            :class="{'bg-gray-200' : searchId}"
            class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-black"
        />

        <div
            v-if="showDropdown && (occupations.length || loading)"
            class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto"
            @mousedown.prevent
            @scroll="onScroll"
        >
            <div
                v-for="occupation in occupations"
                :key="occupation.id"
                class="cursor-pointer hover:bg-gray-200 p-2 text-black"
                @click="selectOccupation(occupation)"
            >
                {{ occupation.name }}
            </div>

            <div v-if="loading" class="text-center p-2 text-gray-500">
                Đang tải thêm...
            </div>
        </div>
    </div>
    <div v-if="!occupations.length && !loading && !onLoad" class="text-center p-2 text-gray-500">
        Không tìm thấy ngành nghề nào.
    </div>
</template>

<script setup>
    import { ref, watch, defineProps, defineEmits , onMounted} from 'vue';
    import axios from 'axios';
    import {debounce} from "lodash";

    const searchQuery = ref(''); // Lưu giá trị tìm kiếm
    const searchId = ref(''); // Lưu giá trị tìm kiếm
    const occupations = ref([]); // Lưu danh sách ngành nghề
    const showDropdown = ref(false); // Trạng thái hiển thị dropdown
    const occupationSelected = ref(false); // Trạng thái đã chọn ngành nghề
    const page = ref(1); // Theo dõi trang hiện tại
    const loading = ref(false); // Trạng thái đang tải
    const lastPage = ref(false); // Kiểm tra xem có còn trang nào để tải không
    const onLoad = ref(false);
    const props = defineProps({
        modelValue: Number,
        name: String,
        operation_id: Number
    });

    const emit = defineEmits(['update:modelValue']);

    // Hàm tải danh sách ngành nghề từ server
    const loadOccupations = async () => {
        if (loading.value || lastPage.value) return;

        loading.value = true;
        onLoad.value = false;
        try {
            const response = await axios.get('/admin/load-job-occupations', {
                params: {
                    search: searchQuery.value,
                    page: page.value,
                    operation_id: props.operation_id || null
                },
            });

            if (response.data.data.length) {
                occupations.value = [...occupations.value, ...response.data.data]; // Nối thêm vào danh sách hiện có
                page.value++;
                if (page.value > response.data.last_page) { // Kiểm tra nếu đã tới trang cuối cùng
                    lastPage.value = true;
                }
            } else {
                lastPage.value = true; // Nếu không còn dữ liệu, không tải nữa
            }
            console.log('occupations.value', occupations.value)

            console.log('occupations.length', occupations.value.length)
        } catch (error) {
            console.error(error);
        } finally {
            loading.value = false;
            searchId.value = '';
        }
    };

    // Hàm xử lý khi người dùng cuộn
    const onScroll = (event) => {
        const container = event.target;
        if (container.scrollTop + container.clientHeight >= container.scrollHeight) {
            loadOccupations();
        }
    };

    // Hàm tìm kiếm
    const onSearch = () => {
        occupations.value = [];
        page.value = 1;
        lastPage.value = false;
        loadOccupations(); // Tải lại dữ liệu khi tìm kiếm
    };
    const selectedOccupation = ref('');
    let isManualChange = ref('');
    watch(searchQuery, (newVal, oldvalue) => {
        console.log("isManualChange", isManualChange);
        console.log("selectedOccupation", selectedOccupation);
        console.log("newVal", newVal);
        if (isManualChange && selectedOccupation.value && newVal !== selectedOccupation.value.name) {
            selectedOccupation.value = null;
            emit('update:modelValue', null);
        }
        isManualChange.value = false;
    });

    // Hàm chọn ngành nghề
    const selectOccupation = (occupation) => {
        isManualChange.value = false;
        searchQuery.value = occupation.name;
        occupationSelected.value = true;
        showDropdown.value = false;
        searchId.value = occupation.id;
        selectedOccupation.value = {
            id: occupation.id,
            name: occupation.name
        };
        emit('update:modelValue', searchId.value);
    };

    // Hàm xử lý khi mất focus khỏi input
    const handleBlur = () => {
        setTimeout(() => {
            showDropdown.value = false;
        }, 200);
    };

    const handleFocus = () => {
        isManualChange.value = true;
        occupations.value = [];
        page.value = 1;
        lastPage.value = false;
        loadOccupations(); // Tải lại dữ liệu khi tìm kiếm
        showDropdown.value = true;
    };

    // Đồng bộ hóa modelValue từ parent với component
    // watch(() => props.modelValue, (newVal) => {
    //     searchQuery.value = newVal ? newVal : ''; // Cập nhật input theo giá trị từ parent nếu có
    // });

    // Khi component được mount, load occupation nếu có occupation_id
    onMounted(() => {
        if (props.modelValue) {
            onLoad.value = true;
            searchQuery.value = props.name;
            searchId.value = props.modelValue;
            emit('update:modelValue', props.modelValue);
        }
    });

    // Debounced function (trì hoãn 300ms)
    const debouncedSearch = debounce(onSearch, 300);
</script>

<style scoped>
    .select-box {
        height: 200px;
        overflow-y: scroll;
        border: 1px solid #ddd;
        padding: 10px;
    }
</style>

