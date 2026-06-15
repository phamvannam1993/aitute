<template>

    <Head title="AI WOW - Biến ý tưởng thành hiện thực" />
    <Layout :breadcrumbs="breadcrumbsData">
        <div class="flex justify-start items-center gap-2">
            <img src="/assets/img/ai3goc/logo/circle.svg" class="w-12" />
            <div>
                <h2 class="text-black font-bold text-sm md:text-2xl uppercase">
                    Tìm kiếm khách hàng
                </h2>
            </div>
        </div>
        <div class="min-h-[50vh] flex flex-col items-center justify-start pt-10 ">
            <!-- WRAPPER TÌM KIẾM -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full">
                <!-- Dropdown Tỉnh, thành phố (chính) -->
                <div class="relative text-black w-full sm:w-1/3" ref="locationContainer">
                    <button type="button"
                        class="border w-full border-[#D4D4D4] bg-white shadow-lg text-sm sm:text-sm justify-between font-bold rounded-full px-2 sm:px-4 py-3 flex items-center gap-2 focus:outline-none"
                        @click="toggleLocationDropdown">
                        <div class="flex items-center gap-2">
                            <img src="/assets/img/ai3goc/logo/img.svg" alt="" class="w-6">
                            <span v-if="selectedLocation">{{ selectedLocation }}</span>
                            <span v-else>Tỉnh, thành phố</span>
                        </div>
                        <span v-if="selectedLocation" @click.stop="clearLocation"
                            class="text-gray-500 hover:text-red-500 cursor-pointer px-1">✖</span>
                        <span v-else>
                            <svg width="24px" height="24px" viewBox="0 0 24 24" fill="#C5C5C5" xmlns="http://www.w3.org/2000/svg">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path d="M12 15l-4.243-4.243 1.415-1.414L12 12.172l2.828-2.829 1.415 1.414z"></path>
                            </svg>
                        </span>
                    </button>

                    <div v-if="showLocationDropdown"
                        class="absolute left-0 right-0 bg-white border border-gray-200 rounded-md shadow-md mt-1 z-20 w-full md:w-[520px]">
                        <div class="p-2 border-b border-gray-300">
                            <input type="text" v-model="locationSearchQuery" @input="updateSearchQuery"
                                placeholder="Nhập tỉnh, thành phố cần tìm"
                                class="w-full border-0 outline-none focus:ring-0" />
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 p-2 max-h-52 overflow-y-auto"
                            v-if="filteredLocations.length">
                            <div v-for="(location, index) in filteredLocations" :key="index"
                                class="flex items-center gap-2 px-2 py-1 hover:bg-gray-100 cursor-pointer"
                                @click="selectLocation(location, false)">
                                <span class="inline-block w-2 h-2 rounded-full bg-gray-400"></span>
                                <span>{{ location.name }}</span>
                            </div>
                        </div>
                        <div class="py-2 px-5 text-gray-500" v-else>
                            Không tìm thấy kết quả
                        </div>
                    </div>
                </div>

                <!-- Dropdown Quận, Huyện -->
                <div class="relative text-black w-full sm:w-1/3" ref="districtContainer">
                    <button type="button"
                        class="border w-full border-[#D4D4D4] bg-white shadow-lg text-sm sm:text-sm justify-between font-bold rounded-full px-2 sm:px-4 py-3 flex items-center gap-2 focus:outline-none"
                        :class="showDistrictSection ? '' : 'text-gray-500'"
                        @click="toggleDistrictDropdown" :disabled="showDistrictSection ? false : true">
                        <div class="flex items-center gap-2">
                            <span v-if="selectedDistrict">{{ selectedDistrict }}</span>
                            <span v-else>Quận, Huyện</span>
                        </div>
                        <span v-if="selectedDistrict" @click.stop="clearDistrict"
                            class="text-gray-500 hover:text-red-500 cursor-pointer px-1">✖</span>
                        <span v-else>
                            <svg width="24px" height="24px" viewBox="0 0 24 24" fill="#C5C5C5" xmlns="http://www.w3.org/2000/svg">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path d="M12 15l-4.243-4.243 1.415-1.414L12 12.172l2.828-2.829 1.415 1.414z"></path>
                            </svg>
                        </span>
                    </button>

                    <div v-if="showDistrictDropdown"
                        class="absolute left-0 right-0 bg-white border border-gray-200 rounded-md shadow-md mt-1 z-20 w-full md:w-[520px]">
                        <div class="p-2 border-b border-gray-300">
                            <input type="text" v-model="districtSearchQuery" @input="updateSearchQuery"
                                placeholder="Nhập quận, huyện cần tìm"
                                class="w-full border-0 outline-none focus:ring-0" />
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 p-2 max-h-52 overflow-y-auto"
                            v-if="filteredDistricts.length">
                            <div v-for="(district, index) in filteredDistricts" :key="index"
                                class="flex items-center gap-2 px-2 py-1 hover:bg-gray-100 cursor-pointer"
                                @click="selectDistrict(district, true)">
                                <span class="inline-block w-2 h-2 rounded-full bg-gray-400"></span>
                                <span>{{ district.name }}</span>
                            </div>
                        </div>
                        <div class="py-2 px-5 text-gray-500" v-else>
                            Không tìm thấy kết quả
                        </div>
                    </div>
                </div>

                <!-- Text Input Lĩnh vực -->
                <div class="relative text-black w-full sm:w-1/3">
                    <input type="text" id="linhVucInput" v-model="searchQuery" placeholder="Nhập lĩnh vực"
                        @keyup.enter="submitForm(true)"
                        class="border border-[#D4D4D4] text-sm sm:text-base shadow-lg h-[50px] rounded-full px-2 sm:px-6 py-3 w-full focus:outline-none focus:ring-2 focus:ring-purple-500" />
                    <button type="submit" @click="submitForm(true)" :disabled="loading"
                        class="absolute right-3 top-1/2 -translate-y-1/2 border-l-2 pl-2 flex items-center">
                        <img src="/assets/img/ai3goc/search.png" alt="" class="sm:w-8 w-6">
                    </button>
                </div>
            </div>

            <div v-if="results.length" class="border-t border-[#D4D4D4] w-full my-4"></div>
            <div  v-if="results.length || inputSearchCustomerClicked" class="md:w-full w-[340px] overflow-x-auto md:max-h-[600px] max-h-[400px] overflow-y-auto" style="border-radius: 6%">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                    <thead class="sticky top-0 z-10 bg-[#1E405A] text-white h-[70px]">
                        <tr>
                            <th scope="col" class="w-16 text-center px-4 py-2">STT</th>
                            <th scope="col" class="w-40 px-4 py-2">Tên</th>
                            <th scope="col" class="w-32 px-4 py-2">Số điện thoại</th>
                            <th scope="col" class="w-64 px-4 py-2">Địa chỉ</th>
                            <th scope="col" class="px-4 py-2">
                                <div class="flex justify-between items-center">
                                    <span class="flex-1"></span>
                                    <div class="relative">
                                        <input v-model="inputSearchCustomer" type="text" placeholder="Tìm khách hàng" class="h-[40px] text-black search-input pl-3 pr-10 py-1 rounded-lg w-64 text-sm">
                                        <button @click="submitForm(false)" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                            <svg style="width: 28px; height: 28px" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(item, index) in results" :key="index">
                            <td class="text-center px-4 py-2 border border-gray-300">{{ index + 1 }}</td>
                            <td class="px-4 py-2 border border-gray-300" style="width: 280px">{{ item.title }}</td>
                            <td class="px-4 py-2 border border-gray-300" style="width: 270px" >{{ item?.phoneNumber ? item?.phoneNumber : 'Không có' }}</td>
                            <td class="px-4 py-2 border border-gray-300" style="width: 600px">{{ item.address }}</td>
                            <td class="border border-gray-300 text-sm md:text-base px-4 py-2">
                                <div class="flex gap-2 items-center">
                                    <textarea v-model="item.note" class="rounded-xl p-2 border border-gray-300" placeholder="Nhập nội dung"></textarea>
                                    <button @click="saveNote(item)" class="bg-ai3goc-primary text-white rounded-xl p-3 hover:opacity-90 w-1/4">Lưu</button>
                                </div>
                            </td>
                        </tr>
                        <!-- Thêm các hàng khác tương tự -->
                    </tbody>
                </table>
            </div>
            <div v-else class="overflow-x-auto max-h-[60vh] w-full text-black mb-4">
                <table class="table-auto w-full rounded-lg border-separate border-spacing-y-2">
                    <thead class="">
                        <tr>
                            <th class="px-4 py-2 text-center text-sm md:text-base"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hover:bg-gray-50 bg-white">
                            <td
                                class="border border-gray-300 text-sm md:text-base px-4 py-2 rounded-[16px] text-center">
                                Không có dữ liệu
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="results.length" class="mb-6">
                <Pagination v-model="currentPage" :totalPage="totalPage" :click-handler="fetchData" :prev-text="'<'"
                    :next-text="'>'" />
            </div>
        </div>
        <Loading v-if="loading" />

    </Layout>
</template>


<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import axios from "axios";
import Layout from "@/Layouts/Client/Layout.vue";
import Loading from "@/Components/Loading.vue";
import Pagination from '../../../Components/Pagination.vue';
import { toast } from "vue3-toastify";
const locationSearchQuery = ref("");
const selectedLocation = ref("");
const searchQuery = ref("");
const inputSearchCustomer = ref("");
const inputSearchCustomerClicked = ref(false);
const showLocationDropdown = ref(false);
const results = ref([]);
const linhVucContainer = ref(null);
const locationContainer = ref(null);
const loading = ref(false);
const currentPage = ref(1);
const totalPage = ref(1);
const districts = ref([]);
const districtSearchQuery = ref("");
const selectedDistrict = ref("");
const selectedLocationValue = ref("");
const selectedDistrictValue = ref("");
const showDistrictDropdown = ref(false);
const showDistrictSection = ref(false);
const districtContainer = ref(null);
const props = defineProps({
    provices: Array
});

const toggleLocationDropdown = () => {
    showLocationDropdown.value = !showLocationDropdown.value;
};
const toggleDistrictDropdown = () => {
    showDistrictDropdown.value = !showDistrictDropdown.value;
};

const selectLocation = (location, isFetch = true) => {
    selectedLocation.value = location.name;
    selectedLocationValue.value = location.value;
    // locationSearchQuery.value = location;
    showLocationDropdown.value = false;
    // open district dropdown
    clearDistrict()
    showDistrictSection.value = true;
    districts.value = location.districts
    if (isFetch) {
        fetchData(1);
    }
};
const selectDistrict = (distrcit, isFetch = true) => {
    selectedDistrict.value = distrcit.name;
    selectedDistrictValue.value = distrcit.value
    showDistrictDropdown.value = false;
    if (searchQuery.value != '' && isFetch) {
        fetchData(1);
    }
};

const updateSearchQuery = () => {

};

const filteredLocations = computed(() => {
    if (!locationSearchQuery.value) return props.provices;
    return props.provices.filter(provice =>
        provice.name.toLowerCase().includes(locationSearchQuery.value.toLowerCase())
    );
});
const filteredDistricts = computed(() => {
    if (!districtSearchQuery.value) return districts.value;

    return districts.value.filter(district =>
        district.name.toLowerCase().includes(districtSearchQuery.value.toLowerCase())
    );
});
const submitForm = (isClearInputCustomerSearch = false) => {
    inputSearchCustomerClicked.value = true;
    if (isClearInputCustomerSearch) {
        inputSearchCustomer.value = '';
    }
    fetchData(1);
};
const clearLocation = () => {
    selectedLocation.value = "";
    locationSearchQuery.value = "";
    selectedLocationValue.value = 0;
    showDistrictSection.value = false;
    // clear district
    clearDistrict()
    showLocationDropdown.value = true
};
const clearDistrict = () => {
    selectedDistrict.value = "";
    districtSearchQuery.value = "";
    selectedDistrictValue.value = 0;
};
const fetchData = async (page = 1) => {
    try {
        loading.value = true;
        // const baseUrl = window.location.origin;
        const params = {
            key: searchQuery.value,
            location: selectedLocation.value,
            provice_id: selectedLocationValue.value,
            district_id: selectedDistrictValue.value,
            district: selectedDistrict.value,
            input_customer_search: inputSearchCustomer.value,
            limit: 20,
            page: page,

        };
        // const response = await axios.get(route("google-search.searchKey", params));
        // // const apiUrl = `${baseUrl}/api/google-search/search-key?key=${encodeURIComponent(searchQuery.value)}&location=${encodeURIComponent(selectedLocation.value)}&limit=20&page=${page}&district=${encodeURIComponent(districtQuery.value)}`;
        // // const response = await axios.get(apiUrl);
        // results.value = response.data.data.data.map((v) => {
        //     return {
        //         id: v.id,
        //         title: v.title,
        //         phoneNumber: v?.phoneNumber,
        //         address: v.address,
        //         note: v?.user_note ? v.user_note.note : '',
        //     };
        // });
        const res = await axios.get(route('google-search.getData', params));
        results.value = res.data.data;
        currentPage.value = res.data.current_page;
        totalPage.value = res.data.last_page;
    } catch (error) {
        console.error("Error fetching data:", error);
    } finally {
        loading.value = false;
    }
};
const saveNote = async (item) => {
    try {
        loading.value = true;
        const params = {
            google_search_id: item.id,
            note: item.note,
        };
        const response = await axios.get(route("google-search.note", params));
        // check response
        if (response.data.status) {
            toast.success("Ghi chú nội dung thành công");
        } else {
            toast.error("Ghi chú nội dung thất bại, vui lòng thử lại");
        }
    } catch (error) {
        console.error("Error fetching data:", error);
    } finally {
        loading.value = false;
    }
};

const handleClickOutside = (event) => {
    if (linhVucContainer.value && !linhVucContainer.value.contains(event.target)) {
        showLinhVucDropdown.value = false;
    }
    if (locationContainer.value && !locationContainer.value.contains(event.target)) {
        showLocationDropdown.value = false;
    }
    if (districtContainer.value && !districtContainer.value.contains(event.target)) {
        showDistrictDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});
onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});
</script>

<style scoped>
/* Style chung cho table, list, ... */
table th,
table td {
    padding: 10px;
}

ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
}

li {
    cursor: pointer;
}

li:hover {
    background-color: #f0f0f0;
}
</style>
