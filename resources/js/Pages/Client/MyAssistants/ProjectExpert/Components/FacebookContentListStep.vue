<template>
    <div :class="mainSectionOpen ? 'flex-col lg:rounded-[35px] items-start' : 'flex-row lg:rounded-full items-center'" class="bg-white shadow-xl md:px-[80px] md:py-[20px] p-3 flex justify-between mt-4">
        <!-- Header -->

        <div @click="toggleMainSection" class="cursor-pointer flex justify-between items-center md:max-w-full line-clamp-1 gap-2 mb-2 text-xs lg:text-sm mt-2 text-black">
            <Step class="flex-shrink-0" :step="6" stepName="Xem kết quả và hiệu chỉnh" />
        </div>
        <div v-if="mainSectionOpen" class="w-full lg:w-4/5 self-center">
            <p>Nội dung đã tạo</p>
            <div class="page w-full self-center page flex flex-row relative items-center">
                <input
                    type="checkbox" :checked="isCheckAll" @click="checkAll()" class="form-radio rounded-full border-gray-300 text-[#FF9500] focus:ring-[#FF9500] w-5 h-5 cursor-pointer mt-3"
                />
                <select class="ml-10 border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#2A5F4C] focus:border-[#2A5F4C] bg-white w-20 color-black" @change="handleLimit">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <p class="total-page ml-2 mt-0 flex items-center">
                    <span class="color-gray flex items-center">{{dataPage.from}} - {{dataPage.to}} / {{dataPage.total}}
                        <span class="pre ml-2 fz-25" :class="dataPage.prev_page_url ? 'color-black cursor-pointer':''" @click="prevPage()">
                            <svg
        xmlns="http://www.w3.org/2000/svg"
        width="24"
        height="24"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <polyline points="15 18 9 12 15 6"></polyline>
      </svg>
                        </span>
                        <span class="next fz-25 ml-2" :class="dataPage.next_page_url ? 'color-black cursor-pointer':''" @click="nextPage()">
                            <svg
        xmlns="http://www.w3.org/2000/svg"
        width="24"
        height="24"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <polyline points="9 18 15 12 9 6"></polyline>
      </svg>
                        </span>
                    </span>
                </p>
                <img src="/assets/img/icon/option.png" alt="icon" class="w-10 h-10 absolute r-10 cursor-pointer" @click="toggleDelete">
                <span class="box-delete cursor-pointer r-10" v-if="showDelete" @click="modalDelete">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" class="absolute mt-1">
                    <path d="M2.17188 4.34375H3.62015H15.2064" stroke="#F55027" stroke-width="1.44828" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13.759 4.34186V14.4798C13.759 14.8639 13.6064 15.2323 13.3348 15.5039C13.0632 15.7755 12.6949 15.9281 12.3107 15.9281H5.06937C4.68526 15.9281 4.31689 15.7755 4.04528 15.5039C3.77368 15.2323 3.62109 14.8639 3.62109 14.4798V4.34186M5.79351 4.34186V2.89359C5.79351 2.50948 5.94609 2.14111 6.2177 1.8695C6.4893 1.5979 6.85768 1.44531 7.24178 1.44531H10.1383C10.5224 1.44531 10.8908 1.5979 11.1624 1.8695C11.434 2.14111 11.5866 2.50948 11.5866 2.89359V4.34186" stroke="#F55027" stroke-width="1.44828" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7.24219 7.96484V12.3097" stroke="#F55027" stroke-width="1.44828" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10.1387 7.96484V12.3097" stroke="#F55027" stroke-width="1.44828" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="ml-5 color-red">Xóa bài viết</span></span>
            </div>
            <!-- Form Content -->
            <div v-if="open" class="w-full self-center">
                <form @submit.prevent="handleSubmit" class="flex flex-col gap-6">
                    <div class="space-y-2 mt-10">
                        <div v-for="(option, index) in facebookContents"
                             :key="index"
                             class="relative flex flex-row box-item">
                              <input
                                    type="checkbox"
                                    :checked="checkContent(index)"
                                    @click="selectAction(index)"
                                    :value="option"
                                    class="form-radio rounded-full border-gray-300 text-[#FF9500] focus:ring-[#FF9500] w-5 h-5 cursor-pointer mt-5"
                                />
                                <p @click="showDetail({id:option.id, index:option.index})" class="flex flex-col ml-10 content-fb cursor-pointer text-black" :class="option.statusClass">
                                    <span class="title font-bold cursor-pointer">{{option.index}}. {{option.title}}</span>
                                    <span class="content">{{option.goal}}</span>
                                </p>
                        </div>
                         <PostFormListContentSection @postFacebook="handlePostFacebook" :projectId="props.projectId"/>
                    </div>
                </form>
            </div>
        </div v-if="mainSectionOpen" class="w-full lg:w-4/5 self-center">

        <!-- Toggle Button -->
        <div :class="mainSectionOpen ? 'self-end' : ''" class="text-ai3goc-primary mt-2 font-medium flex items-center cursor-pointer text-xs md:text-sm" @click="toggleMainSection">
                  <span class="hidden md:block">{{ mainSectionOpen ? "Thu gọn" : "Hiển thị" }}</span>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': mainSectionOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
    </div>

    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" v-if="showModalDelete">
        <div class="bg-white py-6 px-4 md:p-8 shadow-lg w-[320px] md:w-[480px] xl:w-[580px] rounded-[40px]" >
            <div class="flex items-center gap-4 md:gap-8 justify-center">
                <h2 class="justify-center items-center color-red">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" class="float-left mt-3">
                    <path d="M2.17188 4.34375H3.62015H15.2064" stroke="#F55027" stroke-width="1.44828" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13.759 4.34186V14.4798C13.759 14.8639 13.6064 15.2323 13.3348 15.5039C13.0632 15.7755 12.6949 15.9281 12.3107 15.9281H5.06937C4.68526 15.9281 4.31689 15.7755 4.04528 15.5039C3.77368 15.2323 3.62109 14.8639 3.62109 14.4798V4.34186M5.79351 4.34186V2.89359C5.79351 2.50948 5.94609 2.14111 6.2177 1.8695C6.4893 1.5979 6.85768 1.44531 7.24178 1.44531H10.1383C10.5224 1.44531 10.8908 1.5979 11.1624 1.8695C11.434 2.14111 11.5866 2.50948 11.5866 2.89359V4.34186" stroke="#F55027" stroke-width="1.44828" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7.24219 7.96484V12.3097" stroke="#F55027" stroke-width="1.44828" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10.1387 7.96484V12.3097" stroke="#F55027" stroke-width="1.44828" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="font-bold ml-2">Xác nhận xóa bài viết ?</span></h2>
            </div>
            <div class="flex items-center gap-4 md:gap-8 justify-center mt-6">
                <button @click="closePopupDelete" class="rounded-[16px] w-[200px] font-bold  bg-green text-white border-[2px] py-4 text-[14px] md:text-[16px]">
                    <span>Hủy</span>
                </button>
                <button @click="confirmDelete" class="rounded-[16px] w-[200px] font-bold  bg-red text-white border-[2px] py-4 text-[14px] md:text-[16px]">
                    <span>Xóa</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, watch, onMounted } from 'vue';
    import Step from '@/Components/Step.vue';
    import PostFormListContentSection from './ChildComponers/PostFormListContentSection.vue';
    import { toast } from "vue3-toastify";
    import { eventBus } from "@/lib/eventBus.js";
    import dayjs from "dayjs";

    const props = defineProps({
        open: {
            type: Boolean,
            required: true
        },
        loading: {
            type: Boolean,
            default: false
        },
        projectId : {
            type:Number,
            default: 0
        },
        contents: {
            type: Object,
            default: () => ({
                selectedAction: ''
            })
        }
    });
    const facebookContents = ref([])
    const emit = defineEmits(['submit', 'toggle']);
    const showDelete = ref(false)
    const showModalDelete = ref(false)

    const toggleDelete = () => {
        showDelete.value = !showDelete.value
    }
    const closePopupDelete = () => {
        showModalDelete.value = false
    }

    onMounted(() => {
        eventBus.on('showDetail', showDetail);
    });

    const confirmDelete = () => {
        var isDelete = false;
        for(var i = 0; i < facebookContents.value.length; i++) {
            if(facebookContents.value[i].isChecked) {
                isDelete = true;
                deleteFacebookContent(facebookContents.value[i].id)
            }
        }
        toast.success("Xóa thành công")
        showModalDelete.value = false
    }

    const deleteFacebookContent = async (id) => {
        var url = route("social.facebook.delete-facebook-content", {id:id});
        const response = await axios.get(url);
        callApiGetContent()
    }

    const modalDelete = () => {
        showModalDelete.value = true
        showDelete.value = false;
    }

    const checkContent = (index, option) => {
        return facebookContents.value[index].isChecked ? true : false
    }

    const formData = ref({
        selectedAction: props.contents?.selectedAction || ''
    });

    // Watch for changes in props.contents
    watch(() => props.contents, (newVal) => {
        formData.value = {
            selectedAction: newVal?.selectedAction || ''
        };
    }, { deep: true });
    const isCheckAll = ref(false)
    const checkAll = () => {
        isCheckAll.value = !isCheckAll.value
        for(var i = 0; i < facebookContents.value.length; i++) {
            facebookContents.value[i].isChecked = isCheckAll.value
        }
    }
    const selectAction = (index) => {
        facebookContents.value[index].isChecked = !facebookContents.value[index].isChecked
    };

    const showDetail = async (item) => {
        const response = await axios.get(route("social.facebook.facebook-content-detail", {id:item.id}));
        const facebookDetail = response.data.data
        if(facebookDetail.post_date) {
            const dateObject = new Date(facebookDetail.post_date.replace(" ", "T"));
            facebookDetail.select_date = dateObject
            facebookDetail.select_time = {
                hours: dayjs(dateObject).hour(),
                minutes: dayjs(dateObject).minute(),
                seconds: dayjs(dateObject).second(),
            }
        } else {
            facebookDetail.select_date = new Date(),
            facebookDetail.select_time =  {
                hours: dayjs().add(1, "hour").hour(),
                minutes: dayjs().minute(),
                seconds: dayjs().second(),
            }
        }

        emit("showSection", 6)
        facebookDetail.index = item.index
        emit('updateFaceBookContent', facebookDetail);
    }

    const validateForm = () => {
        if (!formData.value.selectedAction) {
            return false;
        }
        return true;
    };
    const total = ref(0)
    const limit = ref(10)
    const page = ref(1)
    const dataPage = ref(
        {
            current_page:1,
            total:0,
            from:0,
            to:0,
            next_page_url:"",
            prev_page_url:""
        }
    )
    const handleLimit = (event) => {
        limit.value = event.target.value
        page.value = 1
        callApiGetContent()
    }
    setInterval(async () => {
        if(total.value < 10 && facebookContents.value.length < 10 && props.open) {
            await callApiGetContent()
        }
    }, 20000)

    const nextPage = () => {
        if(!dataPage.value.next_page_url || dataPage.value.next_page_url == 'null') {
            return false
        }
        page.value = dataPage.value.current_page + 1
        callApiGetContent()
    }

    const prevPage = () => {
        if(!dataPage.value.prev_page_url  || dataPage.value.prev_page_url == 'null') {
            return false
        }
        page.value = dataPage.value.current_page - 1
        callApiGetContent()
    }

    const callApiGetContent = async () => {
        try {
            var url = route("social.facebook.list-content", {page:page.value, limit:limit.value, project_id:props.projectId});
            const response = await axios.get(url);
            if (response.data) {
                facebookContents.value = response.data.data.data
                dataPage.value.current_page = response.data.data.current_page
                dataPage.value.total = response.data.data.total
                dataPage.value.next_page_url = response.data.data.next_page_url
                if(dataPage.value.next_page_url == 'null') {
                    dataPage.value.next_page_url = ""
                }
                dataPage.value.prev_page_url = response.data.data.prev_page_url
                if(dataPage.value.prev_page_url == 'null') {
                    dataPage.value.prev_page_url = ""
                }
                dataPage.value.from = response.data.data.from
                dataPage.value.to = response.data.data.to
                if(response.data.data.total >= 10) {
                    total.value = 10
                    emit('showFBContent', true)
                }
                for(var i = 0; i < facebookContents.value.length; i++) {
                    facebookContents.value[i].statusClass = "bgc-gray border-gray";
                    if(facebookContents.value[i].status == 2) {
                        facebookContents.value[i].statusClass = "bgc-red border-red";
                    } else if(facebookContents.value[i].status == 3) {
                        facebookContents.value[i].statusClass = "bgc-green border-green";
                    }
                    facebookContents.value[i].index = (dataPage.value.current_page-1)* limit.value + i + 1
                }
            }
        } catch (error) {
            console.error("Error fetching project:", error);
        }
    }

    const handleSubmit = async () => {
        if (validateForm()) {
            console.log(formData.value.selectedAction)
            var totalPost = 0
            if(formData.value.selectedAction.value == "fanpage_content") {
                totalPost = await getTotalPost()
                if(totalPost < 10) {
                    emit('showFBContent', false)
                }
            }
            formData.value.selectedAction.totalPost = totalPost
            emit('submit', formData.value.selectedAction);
        }
    };

    const getTotalPost = async () => {
        const response = await axios.get(route("social.facebook.total-content", {project_id: props.projectId}));
        return response.data.total
    }
    callApiGetContent()
    defineExpose({ callApiGetContent });

    const mainSectionOpen = ref(props.open);

    const toggleMainSection = () => {
        mainSectionOpen.value = !mainSectionOpen.value;
      };
</script>

<style scoped>
    .form-radio {
        @apply rounded-full;
        @apply border-gray-300;
        @apply text-[#FF9500];
        @apply focus:ring-[#FF9500];
    }

    .form-radio:checked {
        @apply bg-[#FF9500];
        @apply border-[#FF9500];
    }
    .page {
        width: 100%;
        border-top: 1px solid;
        border-bottom: 1px solid;
        padding: 10px 0px;
        margin-top: 15px;
    }
    .content-fb {
        width: 100%;
        padding: 5px 20px;
        border-radius: 10px;
    }
    .border-red {
        border:1px solid #FF6565
    }
    .bgc-red {
        background: #F5CACA
    }
    .border-green {
        border:1px solid #34C759
    }
    .bgc-gray {
        border: 1px solid #d1d5db;
    }
    .color-gray {
        color: #d1d5db;
    }
    .bgc-green {
        background: #E0FFE8
    }
    .box-item {
        margin-bottom: 30px !important;
    }
    .r-10 {
        right:10px
    }
    .color-red {
        color:red;
    }
    .box-delete {
        position: absolute;
        border: 1px solid #c1c1c3;
        padding: 5px 10px;
        margin-top: 40px;
        border-radius: 10px;
        background: #ffffff;
    }
    .bg-red {
        background:red;
    }
    .bg-green {
        background:green
    }
    .mt-3 {
        margin-top: 3px;
    }
    .color-black {
        color:black
    }
    .fz-25 {
        font-size: 25px;
    }
    .color-green {
        color:greeen
    }
</style>

