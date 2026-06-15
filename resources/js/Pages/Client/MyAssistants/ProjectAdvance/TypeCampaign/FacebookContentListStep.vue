<template>
    <div :class="mainSectionOpen ? 'flex-col lg:rounded-[35px] items-start' : 'flex-row lg:rounded-full items-center'" class="bg-white shadow-xl flex justify-between mt-4">
        <!-- Header -->

        <div @click="toggleMainSection" class="cursor-pointer md:px-[80px] md:py-[20px] p-3 w-full flex justify-between items-center md:max-w-full line-clamp-1 text-xs lg:text-sm text-black">
            <Step class-name="md:text-[19px]" class="flex-shrink-0" :step="props.step.step" :stepName="props.step.stepName" />
        </div>
        <hr v-if="mainSectionOpen" class="border-gray-300 w-full">
        <div v-if="mainSectionOpen" class="w-full lg:w-4/5 self-center">
            <p class="text-[#1E405A] mt-[20px] md:ml-0 ml-3">Nội dung đã tạo:</p>
            <div class="page w-full self-center page flex flex-row relative items-center">
                <input
                    type="checkbox" :checked="isCheckAll" @click="checkAll()" class="form-radio rounded-full border-gray-300 text-[#FF9500] focus:ring-[#FF9500] w-5 h-5 cursor-pointer mt-3"
                />
                <select class="text-black md:ml-10 ml-3 border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#2A5F4C] focus:border-[#2A5F4C] bg-white w-20" @change="handleLimit">
                    <option value="7">7</option>
                    <option value="14">14</option>
                    <option value="21">21</option>
                    <option value="28">28</option>
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
                <img src="/assets/img/option.png" alt="icon" class="w-10 h-10 absolute r-10 cursor-pointer" @click.stop="toggleDelete">
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
                <form class="flex flex-col gap-6">
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
                                <p @click="showDetail({id:option.id, index:option.index})" class="flex flex-col md:ml-10 ml-3 content-fb cursor-pointer text-black" :class="option.statusClass">
                                    <span class="title font-bold cursor-pointer">{{option.index}}. {{option.title}}</span>
                                    <span class="content">{{option.goal}}</span>
                                </p>
                        </div>
                        <div class="text-center text-[#1E405A]">Vui lòng click vào từng bài để kiểm duyệt, chọn ngày giờ đăng.</div>
                        <div v-if="!showFBContent"
                             class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
                            <LoadingCircle  size="w-16 h-16" :percenLoadingVideo="progress" loading-title="Xin vui lòng đợi." />
                        </div>
                    </div>
                </form>
            </div>
        </div v-if="mainSectionOpen" class="w-full lg:w-4/5 self-center">

        <!-- Toggle Button -->
        <div v-if="open" :class="mainSectionOpen ? 'self-end' : ''" class="md:px-[80px] md:py-[20px] p-3 text-[#207A91] mt-2 font-medium flex items-center cursor-pointer text-xs md:text-sm" @click="toggleMainSection">
                  <span class="hidden md:block">{{ mainSectionOpen ? "Thu gọn" : "" }}</span>
                  <svg v-if="mainSectionOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': mainSectionOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
import {ref, watch, onMounted, computed} from 'vue';
import Step from '@/Components/Step.vue';
import { toast } from "vue3-toastify";
import { eventBus } from "@/lib/eventBus.js";
import dayjs from "dayjs";
import LoadingCircle from "@/Components/LoadingCircle.vue";

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
        },
        step: {
            type: Object,
            default: () => ({
                step: 6,
                stepName: 'Xem kết quả và hiệu chỉnh'
            })
        },
        isShowPostContent: {
            type: Boolean,
            default: true
        },
        maxPost: {
            type: Number,
            default: 7
        },
        showFBContent: {
            type: Boolean,
            default: true
        }
    });
    const showFBContent = ref(props.showFBContent);
    const open = ref(props.open);
    const mainSectionOpen = ref(props.open);
    watch(() => props.showFBContent, (newVal) => {
        showFBContent.value = newVal
    }, { deep: true });
    watch(() => props.open, (newVal) => {
        open.value = newVal
        mainSectionOpen.value = newVal
    }, { deep: true });
    const facebookContents = ref([])
    const emit = defineEmits(['submit', 'toggle']);
    const showDelete = ref(false)
    const showModalDelete = ref(false)
    const isDelete = ref(false);
    const toggleDelete = () => {
        isDelete.value = false;
        for(var i = 0; i < facebookContents.value.length; i++) {
            if(facebookContents.value[i].isChecked) {
                isDelete.value = true;
            }
        }
        showDelete.value = !showDelete.value;
    }
    const closePopupDelete = () => {
        showModalDelete.value = false
    }

    onMounted(() => {
        eventBus.on('showDetail', showDetail);
        document.addEventListener("click", (e) => {
           showDelete.value = false;
        });
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
        if (!isDelete.value) {
            toast.warning('Vui lòng chọn ít nhất 1 bài viết để xóa')
            return;
        }
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
        console.log('vaod day');
        emit("showSection", 6)
        facebookDetail.index = item.index
        console.log(facebookDetail, 'facebookDetail');
        emit('updateFaceBookContent', facebookDetail);
    }

    const validateForm = () => {
        if (!formData.value.selectedAction) {
            return false;
        }
        return true;
    };
    const total = ref(0)
    const limit = ref(7)
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
    const limitCallApi = ref(props.maxPost);
    watch(() => props.maxPost, newValue => {
        limitCallApi.value = newValue
    })

    // setInterval(async () => {
    //     if(total.value < limitCallApi.value && facebookContents.value.length < limitCallApi.value && props.open) {
    //         await callApiGetContent()
    //     }
    // }, 20000)
    const progress = ref(0);
    const callApiGetContentInterval = async (projectId) => {
        total.value = 0;
        facebookContents.value = [];
        await callApiGetContent(projectId);
        if (showFBContent.value) return;
        progress.value = 0;

        const duration = 120000 * (limitCallApi.value / 7);
        const intervalTime = 1000;
        let elapsedTime = 0;

        const intervalId = setInterval(() => {
            elapsedTime += intervalTime;

            if (elapsedTime < duration) {
                progress.value = Math.floor((elapsedTime / duration) * 100);
            } else {
                clearInterval(intervalId);
                progress.value = 99;
            }
        }, intervalTime);
        const apiIntervalId = setInterval(async () => {
            if (total.value < limitCallApi.value && facebookContents.value.length < limitCallApi.value && open.value) {
                await callApiGetContent(projectId);
            } else {
                clearInterval(intervalId);
                clearInterval(apiIntervalId);
            }
        }, 7000);
    }


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

    const callApiGetContent = async (projectId) => {
        try {
            var url = route("social.facebook.list-content", {page:page.value, limit:limit.value, project_id: (projectId || props.projectId)});
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
                if(response.data.data.total >= props.maxPost) {
                    total.value = props.maxPost
                    showFBContent.value = true;
                    emit("toggleShowFBContent")
                }
                for(var i = 0; i < facebookContents.value.length; i++) {
                    facebookContents.value[i].statusClass = "bgc-gray border-gray";
                    if(facebookContents.value[i].status == 2) {
                        facebookContents.value[i].statusClass = "bgc-red border-red";
                    } else if(facebookContents.value[i].status == 3) {
                        facebookContents.value[i].statusClass = "bgc-greeen border-greeen";
                    }
                    facebookContents.value[i].index = (dataPage.value.current_page-1)* limit.value + i + 1
                }
            }
        } catch (error) {
            console.error("Error fetching project:", error);
        }
    }

    const getTotalPost = async () => {
        const response = await axios.get(route("social.facebook.total-content", {project_id: props.projectId}));
        return response.data.total
    }
    const toggleMainSection = () => {
        mainSectionOpen.value = !mainSectionOpen.value;
    };
    const setOpenMainSection = () => {
        mainSectionOpen.value = true;
    }
    defineExpose({ callApiGetContent, setOpenMainSection, callApiGetContentInterval });
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
    .border-greeen {
        border:1px solid #34C759
    }
    .bgc-gray {
        border: 1px solid #d1d5db;
    }
    .color-gray {
        color: #d1d5db;
    }
    .bgc-greeen {
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
</style>
