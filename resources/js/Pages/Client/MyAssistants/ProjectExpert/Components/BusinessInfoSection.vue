<template>
    <div id="section_buss" :class="open ? 'flex-col lg:rounded-[35px] items-start' : 'flex-row lg:rounded-full items-center'" class="bg-white shadow-xl md:px-[80px] md:py-[20px] p-3 flex justify-between mt-4">
        <!-- Header -->
         <div @click="toggleMainSection" class="cursor-pointer flex justify-between items-center md:max-w-full line-clamp-1 gap-2 mb-2 text-xs lg:text-sm mt-2 text-black">
            <Step class="flex-shrink-0" :step="2" stepName="Thông tin doanh nghiệp" />
        </div>

        <!-- Form Content -->
        <div class="w-full lg:w-4/5 self-center" v-if="open && mainSectionOpen">
            <div class="flex flex-col gap-6">
                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            1. Tên Doanh nghiệp
                        </label>
                        <input
                            v-model="form.businessName"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            2. Lĩnh vực hoạt động
                        </label>
                          <select
                                v-model="form.businessType"
                                class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#2A5F4C] focus:border-[#2A5F4C] bg-white"
                            >
                            <option value="" disabled>Chọn lĩnh vực</option>
                            <option v-for="businessType in businessTypes" :key="businessType" :value="businessType">
                                {{ businessType }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            3. Quy mô doanh nghiệp
                        </label>
                          <select
                                v-model="form.businessScale"
                                class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#2A5F4C] focus:border-[#2A5F4C] bg-white"
                            >
                            <option value="" disabled>Chọn quy mô</option>
                            <option v-for="businessScale in businessScales" :key="businessScale" :value="businessScale">
                                {{ businessScale }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            4. Mô hình doanh nghiệp
                        </label>
                             <select
                                v-model="form.businessField"
                                class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#2A5F4C] focus:border-[#2A5F4C] bg-white"
                            >
                            <option value="" disabled>Chọn mô hình</option>
                            <option v-for="businessField in businessFields" :key="businessField" :value="businessField">
                                {{ businessField }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            5. USP - Điểm khác biệt của doanh nghiệp
                        </label>
                        <textarea
                            v-model="form.usp"
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            6. Mức giá sản phẩm/dịch vụ
                        </label>

                            <select
                                v-model="form.productLevel"
                                class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#2A5F4C] focus:border-[#2A5F4C] bg-white"
                            >
                            <option value="" disabled>Chọn mức giá</option>
                            <option v-for="productLevel in productLevels" :key="productLevel" :value="productLevel">
                                {{ productLevel }}
                            </option>
                        </select>
                    </div>

                    <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                            7. Định vị thương hiệu
                        </label>
                        <select
                                v-model="form.brandPosition"
                                class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#2A5F4C] focus:border-[#2A5F4C] bg-white"
                            >
                            <option value="" disabled>Chọn thương hiệu</option>
                            <option v-for="brandPosition in brandPositions" :key="brandPosition" :value="brandPosition">
                                {{ brandPosition }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            8. Website công ty/ doanh nghiệp:
                        </label>
                        <input
                            v-model="form.website"
                            type="text"
                            placeholder="Link"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md"
                        />
                    </div>

                    <div class="flex justify-center mt-6">
                        <button
                            type="submit"
                            :disabled="loadingSubmit"
                            class="px-6 py-2 bg-[#2A5F4C] text-white rounded-md hover:bg-[#224939] disabled:opacity-50"
                        >
                            Xác nhận
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="open" class="self-end text-ai3goc-sec mt-2 font-medium flex items-center cursor-pointer text-xs md:text-sm" @click="toggleMainSection">
                  <span class="hidden md:block">{{ mainSectionOpen ? "Thu gọn" : "Hiển thị" }}</span>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': mainSectionOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
    </div>

</template>

<script setup>
    import { ref } from 'vue';
    import Step from '@/Components/Step.vue'
    const props = defineProps({
        open: {
            type: Boolean,
            default: false
        },
        formData: {
            type: Object,
            default: () => ({})
        },
        projectId: {
            type: Number,
            default: 0
        },
        loadingSubmit: {
            type: Boolean,
            default: false
        }
    });
    const businessTypes = ref([
        "Thời trang",
        "Công nghệ",
        "Giáo dục",
        "Sức khỏe & Làm đẹp",
        "Dịch vụ tài chính",
        "Thực phẩm & Đồ uống",
        "Bất động sản",
        "Giải trí",
        "Nông nghiệp & Lâm nghiệp",
        "Y tế & Dược phẩm",
        "Giao thông & Logistics",
        "Du lịch & Lữ hành",
        "Xây dựng & Kiến trúc",
        "Truyền thông & Quảng cáo",
        "Thương mại điện tử",
        "Nhân sự & Tuyển dụng",
        "Luật & Dịch vụ Tư vấn",
        "Năng lượng & Môi trường",
        "Quốc phòng & An ninh",
    ])

    const businessScales = ref([
        "Cá nhân kinh doanh",
        "Startup (Dưới 20 nhân viên)",
        "Doanh nghiệp nhỏ (20 - 100 nhân viên)",
        "Doanh nghiệp vừa (100 - 500 nhân viên)",
        "Tập đoàn lớn (500+ nhân viên)",
    ])

    const businessFields = ref([
        "B2C (Bán lẻ cho khách hàng cá nhân)",
        "B2B (Bán hàng cho doanh nghiệp)",
        "D2C (Bán hàng trực tiếp đến khách hàng, không qua trung gian)",
        "Marketplace (Sàn thương mại điện tử)",
        "Dịch vụ (Cung cấp dịch vụ thay vì sản phẩm)",
        "Khác"
    ])

    const productLevels = ref([
        "Phân khúc cao cấp",
        "Trung cấp",
        "Bình dân",
        "Giá linh hoạt (tùy sản phẩm)",
    ])

    const brandPositions = ref([
        "Chuyên nghiệp, đẳng cấp",
        "Thân thiện, gần gũi",
        "Hài hước, sáng tạo",
        "Cảm xúc, truyền cảm hứng",
        "Khác"
    ])

    const scrollIntoView = () => {
        const targetSection = document.getElementById('section_buss');
        targetSection.scrollIntoView({ behavior: "smooth" });
    }
    const updateBussInfo = (data) => {
        form.value.businessName = data.company_name
        form.value.businessType = data.industry
        form.value.businessScale = data.company_size
        form.value.businessField = data.business_model
        form.value.usp = data.usp
        form.value.productLevel = data.price_range
        form.value.brandPosition = data.brand_positioning
        form.value.website = data.website
    }

    const mainSectionOpen = ref(true);
    const emit = defineEmits(['submit']);

    const form = ref({
        businessName: '',
        businessType: '',
        businessScale: '',
        businessField: '',
        usp: '',
        productLevel: '',
        brandPosition: '',
        website: ''
    });

    const hideSection = () => {
        mainSectionOpen.value = false
    }

    const validateForm = () => {
        if (!form.value.businessName?.trim()) {
            return false;
        }
        return true;
    };

    const handleSubmit = () => {
        const data = {
            company_name: form.value.businessName,
            industry: form.value.businessType,
            company_size: form.value.businessScale,
            business_model: form.value.businessField,
            usp: form.value.usp,
            price_range: form.value.productLevel,
            brand_positioning: form.value.brandPosition,
            website: form.value.website
        }
        emit("showSection", 2)
        const formData = new FormData()
        formData.append("id", props.projectId)
        formData.append("buss_info", JSON.stringify(data))
        emit("updateProject", formData)
        emit('submit', data);
    };
    const toggleMainSection = () => {
        mainSectionOpen.value = !mainSectionOpen.value;
    };
    defineExpose({ scrollIntoView, updateBussInfo, toggleMainSection });
</script>
