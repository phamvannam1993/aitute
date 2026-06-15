<template>
    <div class="flex flex-col lg:flex-row items-center mt-12 px-4 lg:px-8 w-full" ref="dropdownContainer">
        <div class="flex items-center w-full justify-between">
            <div class="flex w-1/2 lg:w-1/3 items-center space-x-2 md:space-x-4">
                <div class="relative flex-grow-0">
                    <button @click="toggleDropdown('postDropdown')" type="button" class="flex items-center justify-start w-full py-2 text-[10px] lg:text-sm font-medium text-[#666666]" aria-expanded="true" aria-haspopup="true">
                        <span>{{ selectedItem }}</span>
                        <svg class="-mr-1 ml-1 h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div v-if="openDropdown === 'postDropdown'" class="absolute min-w-[200px] left-0 right-0 rounded-[5px] shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                        <div class="py-1 w-full" role="menu" aria-orientation="vertical" aria-labelledby="menu-button">
                            <a @click="displayComponent('post', 'Xem bài đăng')"  class="flex items-center px-2 py-1 text-[10px] lg:text-sm text-[#666666] hover:bg-gray-100" role="menuitem">
                                <img class="w-4 h-4 mr-2" src="/assets/svgs/post.svg" alt="icon">
                                Xem bài đăng
                            </a>
                            <a @click="displayComponent('calendar', 'Xem lịch')" class="flex items-center px-2 py-1 text-[10px] lg:text-sm text-[#666666] hover:bg-gray-100">
                                <img class="w-4 h-4 mr-2" src="/assets/svgs/calendar.svg" alt="icon">
                                Xem lịch
                            </a>
                            <a href="#" class="flex items-center px-2 py-1 text-[10px] lg:text-sm text-[#666666] hover:bg-gray-100" role="menuitem">
                                <img class="w-4 h-4 mr-2" src="/assets/svgs/grid-type.svg" alt="icon">
                                Chế độ xem dạng lưới
                            </a>
                            <a href="#" class="flex items-center px-2 py-1 text-[10px] lg:text-sm text-[#666666] hover:bg-gray-100" role="menuitem">
                                <img class="w-4 h-4 mr-2" src="/assets/svgs/list-type.svg" alt="icon">
                                Xem bài đăng
                            </a>
                            <a href="#" class="flex items-center px-2 py-1 text-[10px] lg:text-sm text-[#666666] hover:bg-gray-100" role="menuitem">
                                <img class="w-4 h-4 mr-2" src="/assets/svgs/chart.svg" alt="icon">
                                Chế độ xem phân tích
                            </a>
                            <div class="px-8 m-2"><div class=" border-[1px] border-[#999]"></div></div>
                            <a href="#" class="flex items-center px-2 py-1 text-[10px] lg:text-sm text-[#666666] hover:bg-gray-100" role="menuitem">
                                <img class="w-4 h-4 mr-2" src="/assets/svgs/add.svg" alt="icon">
                                Tạo chế độ xem tùy chỉnh
                            </a>
                            
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <img class="w-[26px] h-[26px] border-[1px] border-[#34A753] rounded-full" src="/assets/svgs/accountExample1.svg" alt="icon">
                    <img class="w-[26px] h-[26px] border-[1px] border-[#34A753] rounded-full" src="/assets/svgs/accountExample2.svg" alt="icon">
                    <button>
                        <img class="w-[26px] h-[26px] rounded-full" src="/assets/svgs/add.svg" alt="icon">
                    </button>
                </div>
            </div>
            <div class="hidden lg:flex items-center justify-center space-x-2 md:w-1/3">
                    <div class="flex items-center space-x-4">
                        <div class="relative flex flex-col items-center border-b-4 border-[#092A99] p-1">
                            <div class="absolute -bottom-1 -left-2 w-2 h-1 bg-[#092A99] rounded-tl-2xl"></div>
                            <div class="absolute -bottom-1 -right-2 w-2 h-1 bg-[#092A99] rounded-tr-2xl"></div>                
                            <img src="/assets/svgs/facebook.svg"class="w-8 h-8" alt="Avatar" />
                            <span class="text-[10px] lg:text-sm font-semibold text-[#092A99] mt-2">Tên tài khoản</span>
                        </div>
                        <div class="relative flex flex-col items-center border-b-4 border-transparent  p-2">
                            <img src="/assets/svgs/tiktok.svg" class="w-8 h-8" alt="Avatar" />
                            <span class="text-[10px] lg:text-sm font-semibold text-gray-700 mt-2">Tên tài khoản</span>
                        </div>
                        <div class="relative flex flex-col items-center border-b-4 border-transparent p-2">              
                            <img src="/assets/svgs/add.svg" class="w-12 h-12" alt="Avatar" />
                            <span class="text-[10px] lg:text-sm font-semibold text-gray-700">Thêm</span>
                        </div>
                    </div>
                    
            </div>
                <div class="flex items-center justify-end space-x-2 md:space-x-6 w-1/2 lg:w-1/3">
                    <div class="relative">
                        <img src="/assets/svgs/menuImage.svg" class="lg:w-8 w-4 lg:h-8 h-4 cursor-pointer" alt="icon" @click="toggleDropdown('menuDropdown')">
                    <!-- Dropdown menu -->
                        <div v-if="openDropdown === 'menuDropdown'" class="absolute min-w-[200px] left-0 mt-1 rounded-[5px] shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                            <div class="py-1 w-full" role="menu" aria-orientation="vertical" aria-labelledby="menu-button">
                                <a href="#" class="flex items-center px-2 py-1 text-[10px] lg:text-sm text-[#666666] hover:bg-gray-100" role="menuitem">
                                    <img class="w-4 h-4 mr-2" src="/assets/svgs/post.svg" alt="icon">
                                    Tải lên bài viết
                                </a>
                                <a href="#" class="flex items-center px-2 py-1 text-[10px] lg:text-sm text-[#666666] hover:bg-gray-100" role="menuitem">
                                    <img class="w-4 h-4 mr-2" src="/assets/svgs/calendar.svg" alt="icon">
                                    Tải xuống bài viết
                                </a>
                                <a href="#" class="flex items-center px-2 py-1 text-[10px] lg:text-sm text-[#666666] hover:bg-gray-100" role="menuitem">
                                    <img class="w-4 h-4 mr-2" src="/assets/svgs/grid-type.svg" alt="icon">
                                    Cài đặt trang
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="flex items-center space-x-1 cursor-pointer" @click="toggleDropdown('filter')">
                            <img src="/assets/svgs/filter.svg" class="w-4 h-4" alt="icon">
                            <span class="hidden lg:block text-[#666666] font-medium text-[10px] lg:text-sm text-nowrap">Lọc và sắp xếp</span>
                            <span class="lg:hidden text-[#666666] font-medium text-[10px] lg:text-xs text-nowrap">Lọc</span>
                        </div>
                        <!-- <div v-if="openFilter" class="absolute min-w-[280px] left-0 right-0 rounded-[5px] shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10 p-2">
                            <div class="flex justify-end">
                                <button>
                                    <img src="/assets/svgs/close.svg" class="w-4 h-4" alt="icon">
                                </button>
                            </div>
                            <div>
                                <span class="font-semibold text-[15px] text-[#545252]">
                                    Sắp xếp theo:
                                </span>
                                <div class="flex items-center justify-between bg-[#EFEFEF] rounded-[5px] px-4 py-2 mt-1 ">
                                    <span class="text-[10px] text-[#545252]">Lần tạo cuối cùng</span>
                                    <svg class="-mr-1 ml-1 h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#545252">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="px-2 m-2"><div class=" border-[1px] border-[#999]"></div></div>
                            <div>
                                <span class="font-semibold text-[15px] text-[#545252]">
                                    Lọc theo:
                                </span>
                                <div class="flex items-center justify-between bg-[#EFEFEF] rounded-[5px] px-4 py-2 mt-1 ">
                                    <span class="text-[10px] text-[#545252]">Trạng thái phê duyệt</span>
                                    <svg class="-mr-1 ml-1 h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#545252">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>

                        <div class="py-1 w-full" role="menu" aria-orientation="vertical" aria-labelledby="menu-button">
                            <a href="#" class="flex items-center px-2 py-1 text-[10px] text-[#666666] hover:bg-gray-100" role="menuitem">
                                <img class="w-4 h-4 mr-2" src="/assets/svgs/post.svg" alt="icon">
                                Xem bài đăng
                            </a>
                            <a @click="goToPage('calendar')" class="flex items-center px-2 py-1 text-[10px] text-[#666666] hover:bg-gray-100">
                                <img class="w-4 h-4 mr-2" src="/assets/svgs/calendar.svg" alt="icon">
                                Xem lịch
                            </a>
                            <a href="#" class="flex items-center px-2 py-1 text-[10px] text-[#666666] hover:bg-gray-100" role="menuitem">
                                <img class="w-4 h-4 mr-2" src="/assets/svgs/grid-type.svg" alt="icon">
                                Chế độ xem dạng lưới
                            </a>
                            <a href="#" class="flex items-center px-2 py-1 text-[10px] text-[#666666] hover:bg-gray-100" role="menuitem">
                                <img class="w-4 h-4 mr-2" src="/assets/svgs/list-type.svg" alt="icon">
                                Xem bài đăng
                            </a>
                            <a href="#" class="flex items-center px-2 py-1 text-[10px] text-[#666666] hover:bg-gray-100" role="menuitem">
                                <img class="w-4 h-4 mr-2" src="/assets/svgs/chart.svg" alt="icon">
                                Chế độ xem phân tích
                            </a>
                            
                            <a href="#" class="flex items-center px-2 py-1 text-[10px] text-[#666666] hover:bg-gray-100" role="menuitem">
                                <img class="w-4 h-4 mr-2" src="/assets/svgs/add.svg" alt="icon">
                                Tạo chế độ xem tùy chỉnh
                            </a>
                            
                        </div>
                    </div> -->
                    </div>
                    <!-- <div class="flex items-center space-x-1">
                        <img class="w-4 h-4" src="/assets/svgs/album.svg" alt="icon">
                        <span class=" text-[#666666] font-medium text-[10px] lg:text-sm text-nowrap">Thư viện ảnh</span>
                    </div> -->
                    <div class="flex items-center">
                        <button @click="togglePosting" class="px-2 lg:px-5 py-1.5 bg-[#34A753] rounded-[3px] flex items-center" >                                                 
                            <span class="md:hidden text-white font-semibold text-[10px]">+</span>
                            <span class="hidden md:block text-white font-semibold text-[10px] lg:text-sm text-nowrap">Tạo bài viết</span>
                        </button>
                    </div>
                </div>
        </div>
        <div class="block lg:hidden items-center justify-center space-x-2 mt-2">
                <div class="flex items-center space-x-4">
                    <div class="relative flex flex-col items-center border-b-4 border-[#092A99] p-1">
                        <div class="absolute -bottom-1 -left-2 w-2 h-1 bg-[#092A99] rounded-tl-2xl"></div>
                        <div class="absolute -bottom-1 -right-2 w-2 h-1 bg-[#092A99] rounded-tr-2xl"></div>                
                        <img src="/assets/svgs/facebook.svg"class="w-8 h-8" alt="Avatar" />
                        <span class="text-[10px] font-semibold text-[#092A99] mt-2">Tên tài khoản</span>
                    </div>
                    <div class="relative flex flex-col items-center border-b-4 border-transparent  p-2">
                        <img src="/assets/svgs/tiktok.svg" class="w-8 h-8" alt="Avatar" />
                        <span class="text-[10px] font-semibold text-gray-700 mt-2">Tên tài khoản</span>
                    </div>
                    <div class="relative flex flex-col items-center border-b-4 border-transparent p-2">              
                        <img src="/assets/svgs/add.svg" class="w-12 h-12" alt="Avatar" />
                        <span class="text-[10px] font-semibold text-gray-700">Thêm</span>
                    </div>
                </div>
                
        </div>
    </div> 
</template>

<script setup>
import { ref, defineEmits, onMounted, onBeforeUnmount } from 'vue'

defineProps({
  togglePosting: {
    type: Function,
    required: true,
  },
});
const emit = defineEmits()

const selectedItem = ref('Xem lịch');

const openDropdown = ref(null)
const dropdownContainer = ref(null);

const displayComponent = (component, item) => {
    selectedItem.value = item;
    openDropdown.value = false;
  emit('display', component);
};

const toggleDropdown = (dropdown) => {
    if (openDropdown.value && openDropdown.value !== dropdown) {
        openDropdown.value = null;
    } 
    openDropdown.value = openDropdown.value === dropdown ? null : dropdown;
    
    emit('open', openDropdown.value); 
};

const handleClickOutside = (event) => {
    if (dropdownContainer.value && !dropdownContainer.value.contains(event.target)) {
        openDropdown.value = null; 
    }
};

onMounted(() => {
    displayComponent('calendar', 'Xem lịch');
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});

</script>