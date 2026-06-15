<template>
    <div class="z-[101] transition-all duration-200 h-full"
        :class="activeMenu ? 'w-full absolute md:w-[280px] xl:relative' : 'w-0'">
        <!-- Toggle Menu Button -->
        <div v-if="!activeMenu" @click="toggleMenu"
            class="fixed top-[.75rem] md:top-10 left-0 bg-transparent rounded-r-lg cursor-pointer w-10 h-10 flex items-center justify-center border border-[#D4D4D4]">
            <img class="pl-2 my-2" src="/assets/img/open_menu.png" alt="Open Menu" />
        </div>

        <!-- Sidebar -->
        <div
            :class="[activeMenu ? 'w-full md:w-[280px] p-4' : 'w-0', 'fixed left-0 top-0 pt-10 bottom-0 lg:relative transition-all duration-200 h-screen bg-white flex flex-col justify-between overflow-y-auto border-r border-[#D4D4D4]']">
            <!-- Header -->
            <div class="flex flex-col">
                <div class="mb-6 flex items-center justify-center relative">
                    <a :href="route('home.index')" class="flex-shrink-0">
                        <img src="/assets/svgs/aiwow/logo.svg" alt="Logo" class="w-[132px] h-[41px]" />
                    </a>
                    <div @click="toggleMenu"
                        class="absolute right-0 top-0 cursor-pointer w-10 h-10 flex items-center justify-center">
                        <img src="/assets/img/close_menu.png" alt="Close Menu" />
                    </div>
                </div>

                <div @click="handleNewClick"
                    class="flex items-center gap-2 justify-center bg-[#0E68EF] py-3 rounded-2xl text-[15px] font-bold mb-4 text-white cursor-pointer">
                    <img src="/assets/img/my_assistant/new_project.png" alt="new project" />
                    <span>Tạo dự án mới</span>
                </div>
                <div class="flex items-center gap-2 justify-start font-semibold">
                    <img src="/assets/img/aiwow/homepage/collection.png" class="w-4" alt="collection" />
                    <span>Dự án của bạn</span>
                </div>

                <!-- Navigation -->
                <div class="space-y-2 mt-3" ref="menuContainer">
                    <template v-for="(item, index) in menuItems" :key="index">
                        <div class="flex items-center p-2  rounded-[10px]" :class="item.isActive ? 'bg-[#D7EEFF]' : ''">
                            <img :src="item.image" />
                            <p class="ml-2 font-semibold">{{ item.name }}</p>
                            <div class="ml-auto cursor-pointer relative" @click="togglePopup(index)">
                                <img v-if="item.isActive" src="/assets/img/my_assistant/active_action.png" />
                                <img v-else src="/assets/img/my_assistant/action.png" />

                                <div v-if="activePopup === index"
                                    class="absolute right-0 top-10 bg-white shadow-lg rounded-lg w-[150px] p-2 z-50">
                                    <ul>
                                        <li class="p-2 hover:bg-gray-100 flex items-center cursor-pointer"
                                            @click="copyProject(index)">
                                            <img src="/assets/img/my_assistant/copy.png" class="w-4 h-4 mr-2" />
                                            Copy
                                        </li>
                                        <li class="p-2 hover:bg-gray-100 flex items-center cursor-pointer"
                                            @click="renameProject(index)">
                                            <img src="/assets/img/my_assistant/edit.png" class="w-4 h-4 mr-2" />
                                            Đổi tên dự án
                                        </li>
                                        <li class="p-2 hover:bg-red-100 text-red-500 flex items-center cursor-pointer"
                                            @click="deleteProject(index)">
                                            <img src="/assets/img/my_assistant/delete.png" class="w-4 h-4 mr-2" />
                                            Xóa dự án
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Footer -->
        </div>
    </div>
</template>

<script setup>
import "suneditor/dist/css/suneditor.min.css";
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";
import Footer from "../Home/Components/Footer.vue";

const props = defineProps({
    menuItems: Array,
});

const emit = defineEmits(["createNewProject"]);

const menuItems = ref(props.menuItems)
const menuContainer = ref(null);
const activeMenu = ref(true);
const activePopup = ref(null);
const toggleMenu = () => {
    activeMenu.value = !activeMenu.value;
};

const togglePopup = (index) => {
  activePopup.value = activePopup.value === index ? null : index;
};

const closePopup = (event) => {
  if (menuContainer.value && !menuContainer.value.contains(event.target)) {
    activePopup.value = null;
  }
};

// Hàm xử lý sao chép dự án
const copyProject = (index) => {
  alert(`Copy project: ${menuItems.value[index].name}`);
};

// Hàm xử lý đổi tên dự án
const renameProject = (index) => {
  const newName = prompt(`Rename project: ${menuItems.value[index].name}`);
  if (newName) menuItems.value[index].name = newName;
};

// Hàm xử lý xóa dự án
const deleteProject = (index) => {
  if (confirm(`Are you sure you want to delete ${menuItems.value[index].name}?`)) {
    menuItems.value.splice(index, 1);
  }
};

const handleNewClick = () => {
   location.reload();
};

onMounted(() => {
  document.addEventListener("click", closePopup);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", closePopup);
});

</script>

<style scoped></style>
