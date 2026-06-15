<template>
    <div :class="mode != 'basic' ? 'block' : 'w-0 h-0 opacity-0'">
        <div id="section_buss" :class="open ? 'flex-col lg:rounded-[35px] items-start' : 'flex-row lg:rounded-full items-center'" class="bg-white shadow-xl md:px-[80px] md:py-[20px] p-3 flex justify-between mt-4">
            <div class="flex items-center gap-2 mb-2 text-xs lg:text-sm mt-2 text-black">
                <Step class="flex-shrink-0" :step="2" step-name="Thiết lập thông tin và hiệu chỉnh" />
            </div>
            <div v-if="open" class="w-full lg:w-4/5 self-center" :class="mainSectionOpen && content ? '' : 'hidden'">
                <div class="py-10">
                    <div class="flex flex-col relative">
                        <!-- Container cho editor với id cố định -->
                        <Loading v-if="loading" position="absolute" msg="Đang phân tích"/>
                        <div id="editor-container-sub" class="w-full border border-gray-200 rounded-lg mb-4"></div>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 mb-2 text-xs lg:text-sm mt-2 text-black" v-if="open && mainSectionOpen && content">
                <div class="flex items-center gap-1">
                    <div class="flex items-center justify-center gap-1 py-1 px-2 rounded-full min-w-[60px]">
                        <img src="/assets/img/icon_ai.png" alt="step" class="w-5 h-auto" />
                    </div>
                    <div>
                        <div class="flex">
                            <span class="font-bold text-sm lg:text-base">{{titleStep}}</span><!--v-if-->
                        </div>
                        <!--v-if-->
                    </div>
                </div>
            </div>
            <div class="w-full lg:w-4/5 self-center" v-if="open && mainSectionOpen && content">
                <ContentProductAd v-if="projectType != 'music'" :form-data="contentProductAd" :handle-button-click="handleSubmitMediaCampaign" @sendDataInfo="sendDataInfo" ref="refContentProductAd"
                            :loading-submit="loading" />
                <MusicSection @getFormBigIdeas="getFormBigIdeas" @submitForm="submitForm" @getFormLyric="getFormLyric" ref="refMusic" v-if="projectType == 'music'"  :handle-button-click="handleSubmitMusic" :toggleBigIdeaMusic="toggleBigIdeaMusic" :loading="false" />
            </div>
            <div v-if="open" @click="toggleMainSection"  class="self-end flex-shrink-0 text-aiwow-sec font-medium flex items-center cursor-pointer text-xs md:text-sm">
                <span class="hidden md:block">{{ mainSectionOpen ? "Thu gọn" : "Hiển thị" }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, onMounted, nextTick } from 'vue';
    import Step from '@/Components/Step.vue'
    import suneditor from "suneditor";
    import "suneditor/dist/css/suneditor.min.css";
    import ContentProductAd from "./ContentProductAd.vue";
    import MusicSection from "./MusicSection.vue";
    const props = defineProps({
        open: {
            type: Boolean,
            default: false
        },
        formData: {
            type: Object,
            default: () => ({})
        },
        projectType: {
            type: String,
            default: ""
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

    const formData = ref("");
    const content = ref("")
    const refMusic = ref("")
    const refContentProductAd = ref("")
    const scrollIntoView = () => {
        const targetSection = document.getElementById('section_buss');
        targetSection.scrollIntoView({ behavior: "smooth" });
    }
    const titleStep = ref("Bổ sung thông tin")
    if(props.projectType == 'campaign') {
        titleStep.value  = "Chọn mục tiêu Chiến dịch truyền thông"
    }
    if(props.projectType == 'music') {
        titleStep.value  = "Tạo ý tưởng tổng quan"
    }
    const mainSectionOpen = ref(true);
    const emit = defineEmits(['submit']);
    const isEditorInitialized = ref(false);
    const hideSection = () => {
        mainSectionOpen.value = false
    }

    const validateForm = () => {
        if (!form.value.businessName?.trim()) {
            return false;
        }
        return true;
    };

    const toggleMainSection = () => {
        mainSectionOpen.value = !mainSectionOpen.value;
    };
    const sunEditorInstance = ref(null);
    const loading = ref(false)
    const initEditor = (information_project = "", isSuccess = false) => {
        content.value = information_project
        if(isSuccess) {
            loading.value = false
        }
        // Nếu editor đã được khởi tạo thì không khởi tạo lại
        if (isEditorInitialized.value) {
            sunEditorInstance.value.setContents(information_project);
            return;
        }

        nextTick(() => {
            // Đảm bảo container tồn tại
            const containers = document.querySelectorAll("#editor-container-sub");
            if (containers.length === 0) {
                console.log("Editor container not found");
                return;
            }
            // Nếu có nhiều hơn 1 container, xóa các container thừa
            if (containers.length > 1) {
                for (let i = 1; i < containers.length; i++) {
                    containers[i].remove();
                }
            }
            const container = containers[0];
            // Cleanup editor cũ nếu có
            if (sunEditorInstance.value) {
                sunEditorInstance.value.destroy();
                sunEditorInstance.value = null;
                container.innerHTML = '';
            }
            // Xóa hết nội dung container
            container.innerHTML = '';
            // Tạo textarea mới
            const textarea = document.createElement("textarea");
            container.appendChild(textarea);
            // Khởi tạo SunEditor mới
            sunEditorInstance.value = suneditor.create(textarea, {
                height: "auto",
                minHeight: "300px",
                width: "100%",
                buttonList: [],
                defaultStyle: `
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                font-size: 15px;
                line-height: 1.6;
                color: #333;
                padding: 20px;
            `,
                hideToolbar: true,
            });
            // Set nội dung
            sunEditorInstance.value.setContents(information_project);
            // Đánh dấu đã khởi tạo
            isEditorInitialized.value = true;
        });
    };
    const mockFormDataPoetry = [
        {
            name: "key_message",
            type: "textarea",
            label: "Thông điệp chính",
            value: "",
            placeholder: "Thông điệp chính cần nhấn mạnh là nội dung trọng tâm bạn muốn khách hàng ghi nhớ",
        },
        {
            name: "keywords",
            type: "text",
            label: "Từ khóa (key)",
            value: "",
            placeholder: "Từ khóa hoặc những từ/câu đặc biệt muốn có trong bài thơ? Hãy viết ngắn gọn, cách nhau dấu phẩy",
        },
        {
            name: "trending_context",
            type: "text",
            label: "Bối cảnh",
            value: "",
            placeholder: "Bối cảnh là xu hướng hoặc sự kiện để bài viết chạm được tới tâm trí đám đông nhanh nhất và dễ nhất.",
        },
        {
            name: "cta",
            type: "select",
            label: "Thêm CTA (Lời Kêu Gọi Hành Động)",
            value: "",
            options: [
                { label: "Mua Ngay", value: "Mua Ngay" },
                { label: "Khuyến khích liên kết và tương tác", value: "Khuyến khích liên kết và tương tác" },
                { label: "Thêm Chia Sẻ", value: "Thêm Chia Sẻ" },
                { label: "Tăng bình luận", value: "Tăng bình luận" },
            ],
        },
    ];
    const mockFormDataContentProductAd = [
        {
            name: "trending_context",
            type: "text",
            label: "Bối cảnh",
            value: "",
            placeholder: "Bối cảnh là xu hướng hoặc sự kiện để bài viết chạm được tới tâm trí đám đông nhanh nhất và dễ nhất."
        },
        {
            name: "key_message",
            type: "textarea",
            label: "Thông điệp chính",
            value: "",
            placeholder: "Thông điệp chính là nội dung trọng tâm bạn muốn khách hàng ghi nhớ."
        },
        {
            name: "promotion_gift",
            type: "text",
            label: "Ưu đãi và quà tặng",
            value: "",
            placeholder: "Ưu đãi/quà tặng giúp tăng sức hút và thúc đẩy hành động."
        },
        {
            name: "cta",
            type: "select",
            label: "Thêm CTA (Lời Kêu Gọi Hành Động)",
            value: "",
            options: [
                { label: "Mua Ngay", value: "Mua Ngay" },
                { label: "Khuyến khích liên kết và tương tác", value: "Khuyến khích liên kết và tương tác" },
                { label: "Đăng Ký tham gia", value: "Đăng Ký tham gia" },
                { label: "Xem Chi Tiết", value: "Xem Chi Tiết" },
                { label: "Liên Hệ Chúng Tôi", value: "Liên Hệ Chúng Tôi" },
                { label: "Thêm Chia Sẻ", value: "Thêm Chia Sẻ" },
                { label: "Tăng bình luận", value: "Tăng bình luận" },
            ],
        },
    ];

    const mokCampaign = ref([
        {
            name:"",
            options:[
                {
                    "label": "Quảng Bá Sản Phẩm/Dịch Vụ",
                    "isActive": true
                },
                {
                    "label": "Tăng Nhận Thức Thương Hiệu",
                    "isActive": false
                },
                {
                    "label": "Thúc Đẩy Tương Tác Khách Hàng",
                    "isActive": false
                },
                {
                    "label": "Tạo Lead và Chuyển Đổi",
                    "isActive": false
                },
                {
                    "label": "Tạo Dẫn Đường Cho Trang Đích",
                    "isActive": false
                },
                {
                    "label": "Xây Dựng và Nuôi Dưỡng Mối Quan Hệ Khách Hàng",
                    "isActive": false
                },
                {
                    "label": "Tối Ưu Hóa Cho SEO",
                    "isActive": false
                },
                {
                    "label": "Giáo Dục Khách Hàng",
                    "isActive": false
                },
                {
                    "label": "Hỗ Trợ Chiến Dịch Marketing",
                    "isActive": false
                },
                {
                    "label": "Xây Dựng Cộng Đồng",
                    "isActive": false
                },
                {
                    "label": "Thúc Đẩy Bán Hàng và Tạo Doanh Thu",
                    "isActive": false
                },
                {
                    "label": "Phản Hồi và Giải Quyết Vấn Đề của Khách Hàng",
                    "isActive": false
                },
                {
                    "label": "Phá vỡ rào cản khách hàng",
                    "isActive": false
                },
                {
                    "label": "Tạo Nội Dung Viral",
                    "isActive": false
                },
                {
                    "label": "Phát Triển Thương Hiệu Cá Nhân",
                    "isActive": false
                },
                {
                    "label": "Tạo Uy Tín và Chứng Minh Chuyên Môn",
                    "isActive": false
                },
                {
                    "label": "Tối Ưu Hóa Trải Nghiệm Khách Hàng",
                    "isActive": false
                },
                {
                    "label": "Thúc Đẩy Văn Hóa Doanh Nghiệp",
                    "isActive": false
                },
                {
                    "label": "Tác Động Xã Hội và Trách Nhiệm Cộng Đồng",
                    "isActive": false
                }
            ],
            type: "radio",
            label: "",
            value: "",
        }
    ])
    const contentProductAd = ref(mockFormDataContentProductAd)
    if(props.projectType == "poetry") {
        contentProductAd.value = ref(mockFormDataPoetry)
    }
    if(props.projectType == "campaign") {
         contentProductAd.value = ref(mokCampaign)
    }

    const updateDataMusic = (data, type = 1, isSuccess = false) => {
        if(refMusic.value) {
            let formData = {
                overview:data
            }
            if(type == 2) {
                formData = {
                    big_ideas:data
                }
            }
            if(type == 3) {
                formData = {
                    lyric:data
                }
            }
            refMusic.value.updateData(formData, isSuccess)
        }
    }
    const getFormBigIdeas = (goal, so_luong) => {
        emit("getFormBigIdeas", goal, so_luong)
    }

    const getFormLyric = (y_tuong) => {
        emit("getFormLyric", y_tuong)
    }

    const submitForm = (data) => {
        emit("submitForm", data)
    }

    const updateDataDetail = (data) => {
        if(refContentProductAd.value) {
            if(props.projectType == "campaign") {
                refContentProductAd.value.updateData(data)
                contentProductAd.value = data
                return
            }
            if(data && data.length > 5) {
                refContentProductAd.value.updateData(data)
                contentProductAd.value = data
            } else {
                if(props.projectType == "poetry") {
                    refContentProductAd.value.updateData(mockFormDataPoetry)
                } else {
                    refContentProductAd.value.updateData(mockFormDataContentProductAd)
                }
            }
        }
    }
    const updateIsLoading = () => {
        if(refContentProductAd.value) {
            refContentProductAd.value.updateIsLoading()
        }
        mainSectionOpen.value = false
    }
    const sendDataInfo = (data) => {
        const content =  sunEditorInstance.value.getContents();
        data.content = content
        const formData = new FormData()
        formData.append("id", props.projectId)
        var typeName = "Bài viết Quảng cáo sản phẩm"
        var dataSubmit = {}
        data.forEach(item => {
            dataSubmit[item.name] = item.value;
        });
        if(props.projectType == "poetry") {
            formData.append("meta_data", JSON.stringify(data))
            typeName = "Thơ Quảng cáo sản phẩm"
        } else if(props.projectType == "article") {
            formData.append("meta_data", JSON.stringify(data))
        } else if(props.projectType == "campaign") {
            const options = data[0].options
            var optionName = ""
            for(var i = 0; i < options.length; i++) {
                if(options[i].isActive) {
                    optionName = options[i].label
                }
            }
            dataSubmit = {
                "muc_tieu":optionName
            }
            typeName = "Chiến dịch Truyền thông"
            const saveData = [
                {
                    name:"",
                    options:options,
                    type: "radio",
                    label: "",
                    value: "",
                }
            ];
            formData.append("meta_data", JSON.stringify(saveData))
            console.log(saveData)
        }
        emit("updateProject", formData)
        emit('submitInfo', dataSubmit, typeName);
    }
    onMounted(() => {
        initEditor(); // Khởi tạo editor
    });
    defineExpose({ initEditor, toggleMainSection, updateDataDetail, updateDataMusic, updateIsLoading });
</script>
