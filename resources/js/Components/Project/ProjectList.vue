<template>
    <div ref="menuContainer">
        <div class="flex items-center gap-2">
            <div @click="reloadPage()"
                class="flex items-center w-fit gap-2 px-4 md:px-8 justify-center bg-ai3goc-bgclr py-1 lg:py-3 rounded-full text-xs lg:text-[19px] font-bold text-white cursor-pointer">
                <img src="/assets/img/my_assistant/new_project.png" alt="new project" />
                <span>Tạo dự án mới</span>
            </div>
            <div @click="toggleListProject"
                class="flex items-center w-fit gap-2 px-4 md:px-8 justify-center bg-ai3goc-primary py-1 lg:py-3 rounded-full text-xs lg:text-[19px] font-bold text-white cursor-pointer">
                <img src="/assets/img/my_assistant/project.png" alt="new project" />
                <span>Dự án của bạn</span>
            </div>
            <div @click="emit('showHistory')"
                class="flex items-center justify-center bg-white border-2 p-2 w-10 h-10 rounded-full cursor-pointer">
                <img src="/assets/svgs/icon_history.svg" alt="new project" class="object-contain"/>
            </div>
        </div>
        <div v-if="activeListProject && listProject.length > 0"
            class="absolute z-10 top-15 right-0 bg-white text-black rounded-2xl p-4 shadow-xl w-[240px] md:w-[320px]"
            ref="menuContainer">
            <div class=" bg-white h-[200px] lg:h-[300px] overflow-auto">
                <div v-for="(item, index) in listProject" :key="index" @click="selectProject(item)"
                    class="flex items-center p-2 rounded-[10px] cursor-pointer hover:bg-[#DEECFF]"
                    :class="item.isActive ? 'bg-[#D7EEFF]' : ''">
                    <img :src="item.image ?? '/assets/img/my_assistant/p_red.png'" class="w-8" />

                    <!-- Hiển thị input nếu đang chỉnh sửa, nếu không hiển thị tên -->
                    <input v-if="editingIndex === index" v-model="editedName" @blur="saveRename(index)"
                        @keyup.enter="saveRename(index)" @keyup.esc="cancelRename"
                        class="ml-2 flex-1 border border-gray-300 p-1 rounded-md focus:outline-none" @click.stop />

                    <p v-else class="ml-2 font-semibold line-clamp-1 flex-1" @dblclick="startRename(index, item.title)">
                        {{ item.title }}
                    </p>

                    <div class="flex justify-end cursor-pointer relative" @click="togglePopup(index)" @click.stop>
                        <button class="w-4">
                            <img v-if="item.isActive" src="/assets/img/my_assistant/active_action.png" class="min-w-1.5" />
                            <img v-else src="/assets/img/my_assistant/action.png" class="min-w-1.5" />
                        </button>

                        <div v-if="activePopup === index"
                            class="absolute right-0 top-10 bg-white shadow-xl rounded-lg w-[180px] p-2 z-50 overflow-y-auto">
                            <ul class="space-y-1">
                                <li class="p-2 hover:bg-gray-100 flex items-center cursor-pointer text-xs md:text-sm"
                                    @click="startRename(index, item.title)">
                                    <img src="/assets/img/my_assistant/edit.png" class="w-4 h-4 mr-2" />
                                    Đổi tên sản phẩm
                                </li>
                                <li class="p-2 hover:bg-red-100 text-red-500 flex items-center cursor-pointer text-xs md:text-sm"
                                    @click="deleteProject(item)">
                                    <img src="/assets/img/my_assistant/delete.png" class="w-4 h-4 mr-2" />
                                    Xóa sản phẩm
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-center w-full" v-if="next_page_url">
                <button @click="getListProjects()" class="px-2 md:px-4 py-2 rounded-2xl border-2 font-semibold text-sm md:text-base transition-all bg-orion-orange text-white border-orion-orange">Xem thêm</button>
            </div>
        </div>
        <div v-if="activeListProject && listProject.length === 0"
            class="absolute z-10 top-15 right-0 bg-white rounded-2xl p-4 shadow-xl w-[200px] md:w-[320px] text-center text-red-600">
            Chưa có sản phẩm nào
        </div>
    </div>

</template>

<script setup>
import { onBeforeUnmount, onMounted, onUnmounted, ref } from "vue";
import axios from "axios";
import { toast } from "vue3-toastify";
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
    mode: {
        type: String,
        default: null
    },
});
const mode = ref(props.mode)
const page = usePage();
const loadingReadmore = ref(false)
const next_page_url = ref("")
const activeListProject = ref(false);
const listProject = ref([]);
const menuContainer = ref(null)
const getListProjects = async () => {
    try {
        var url = route('ai-business.get-list-project')
        if(mode.value == "expert") {
            url =  route('ai-business.get-list-project-expert')
        }
        loadingReadmore.value = true
        if(next_page_url.value) {
            url = next_page_url.value
        }
        const response = await axios.get(url, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (response.status === 200) {
            const data = response.data.data.data
            for(var i = 0; i < data.length; i++) {
                listProject.value.push(data[i])
            }
            next_page_url.value = response.data.data.next_page_url
            loadingReadmore.value = false
        } else {
            loadingReadmore.value = false
            console.error('Không thể lấy danh sách sản phẩm:', response.data.message);
            return [];
        }
    } catch (error) {
        loadingReadmore.value = false
        console.error('Lỗi khi gọi API danh sách sản phẩm:', error.response?.data || error.message);
        return [];
    }
}

const emit = defineEmits(['selectProject'])
const closeListProject = (event) => {
    if (menuContainer.value && !menuContainer.value.contains(event.target)) {
        activeListProject.value = false;
    }
};

const reloadPage = () => {
    location.reload()
}

const toggleListProject = () => {
    next_page_url.value = null;
    listProject.value = [];
    getListProjects();
    activeListProject.value = !activeListProject.value;
};

const handleClickOutside = (event) => {
    if (menuContainer.value && !menuContainer.value.contains(event.target)) {
        activeListProject.value = false;
    }
};
const selectProject = (item) => {
    emit('selectProject', item);
    activeListProject.value = false; // Đóng danh sách sau khi chọn
};
/********* Start block popup ************/
const activePopup = ref(null);
const togglePopup = (index) => {
    activePopup.value = activePopup.value === index ? null : index;
};

const closePopup = (event) => {
    if (menuContainer.value && !menuContainer.value.contains(event.target)) {
        activePopup.value = null;
    }
};
/********* End block popup ************/


/********* Start block rename ************/
const editingIndex = ref(null);
const editedName = ref("");
const startRename = (index, currentName) => {
    editingIndex.value = index;
    editedName.value = currentName;
};

const saveRename = async (index) => {
    if (!editedName.value.trim()) return;

    try {
        var url = route("ai-business.update-project")
        if(mode.value == "expert") {
            url = route("ai-business.update-project-expert")
        }
        const response = await axios.post(url, {
            id: listProject.value[index].id,
            title: editedName.value.trim(),
        });

        if (response.status === 200) {
            listProject.value[index].title = editedName.value.trim();
        }
    } catch (error) {
        console.error("Lỗi khi đổi tên dự án:", error);
        toast.error(error.response?.data?.message || "Có lỗi xảy ra khi cập nhật dự án");
    }

    editingIndex.value = null;
};

const cancelRename = () => {
    editingIndex.value = null;
};
/********* End block rename ************/


/********* Start block delete project ************/
const deleteProject = async (item) => {
    if (!confirm(`Bạn có muốn xóa sản phẩm ${item.title}?`)) {
        return;
    }
    try {
        // Tiến hành xóa project
        var url = route("ai-business.projects.destroy", item.id)
        if(mode.value == "expert") {
            url = route("ai-business.projects-expert.destroy", item.id)
        }
        const response = await axios.delete(url);
        if (response.data.success) {
            toast.success("Xóa sản phẩm thành công");
            if (page.props.auth.user) {
                listProject.value = listProject.value.filter((p) => p.id !== item.id);
            }
        }
    } catch (error) {
        console.error("Lỗi khi xóa sản phẩm:", error);
        if (error.response) {
            switch (error.response.status) {
                case 404:
                    toast.error("Không tìm thấy sản phẩm này");
                    // Cập nhật lại danh sách khi project không tồn tại
                    listProject.value = listProject.value.filter((p) => p.id !== item.id);
                    break;
                case 403:
                    toast.error("Bạn không có quyền xóa sản phẩm này");
                    break;
                default:
                    toast.error(error.response.data.message || "Đã xảy ra lỗi khi xóa sản phẩm");
            }
        } else if (error.request) {
            toast.error("Không thể kết nối đến server");
        } else {
            toast.error("Lỗi: " + error.message);
        }
    }
};
/********* Start block delete project ************/

onMounted(() => {
    getListProjects();
    document.addEventListener("click", handleClickOutside);
    document.addEventListener("click", closePopup);
    document.addEventListener("click", closeListProject);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener("click", closePopup);
    document.removeEventListener("click", closeListProject);
});

defineExpose({
    toggleListProject
})
</script>
