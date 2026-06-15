<template>
    <!-- Step 1: Nhập thông tin dự án -->
    <BasicInfoSection
        ref="childRef"
        :open="sections[0].open"
        :projectId="businessProjectId"
        :loading="loading"
        :is-loading-check="isLoadingCheckConversation"
        :mode="mode"
        :information_project="information_project"
        @showSection="handleShowSection"
        @updateProject="updateProjectExpert"
        @toggle="toggleSection(0)"
        @submit="handleProjectSubmit"
        @submitInfo="handleSubmitInfo"
    />

    <!-- Step 2: Hiệu chỉnh thông tin -->
    <BusinessInfoSection
        :open="sections[1].open"
        ref="childBusRef"
        :loading="loading"
         :projectId="businessProjectId"
         @showSection="handleShowSection"
         @toggle="toggleSection(1)"
         @updateProject="updateProjectExpert"
         @submit="handleSubmitAnalysicBusiness"
    />

    <!-- Step 3: Phân tích doanh nghiệp -->
    <ProductAnalysisSection
        ref="childSectionRef"
        :projectId="businessProjectId"
        :open="sections[2].open"
        @toggle="toggleSection(2)"
        :results="difyResults"
        @showSection="handleShowSection"
        @submit="handleShowAnalysisSection"
        @submitAnalysisSection="handleSubmitAnalysisSection"
        @updateProject="updateProjectExpert"
    />

    <!-- Step 4: Chiến lược nội dung ngắn gọn -->
    <CommunicationStrategy
        ref="childMediaRef"
        :open="sections[3].open"
        @showSection="handleShowSection"
        @toggle="toggleSection(3)"
        :contents="{ content: '' }"
        @submit="handleSubmitCommunicationStrategy"
        @updateProject="updateProjectExpert"
    />

    <!-- Step 5: Chọn thể loại nội dung -->
    <CreateActionStep
        :open="sections[4].open"
        ref="refCreateAction"
        :projectId="businessProjectId"
        :loading="loading"
        :contents="{ content: 'Nội dung từ component cha' }"
        @showSection="handleShowSection"
        @showFBContent = "handleShowFBContent"
        @submit="handleSubmitCreateActionStep"
        @updateProject="updateProjectExpert"
        @toggle="toggleOpen"
    />
    <!-- Step 6: Danh sách nội dung facebook-->
    <FacebookContentListStep
        ref="fbContentRef"
        v-if=sections[5].open
        :projectId="businessProjectId"
        @showSection="handleShowSection"
        :open="sections[5].open"
        :loading="loading"
        :contents="{ content: 'Nội dung từ component cha' }"
        @toggle="toggleOpen"
        @showFBContent = "handleShowFBContent"
        @updateFaceBookContent="handleUpdateFaceBookContent"
    />

    <PopupFacebookContent
        ref="popupFBRef"
        v-if=sections[6].open
        :projectId="businessProjectId"
        @showSection="handleShowSection"
        :facebookContentDetail="facebookContentDetail"
        :selectedProject="selectedProject"
        :open="sections[6].open"
        :loading="loading"
        :contents="{ content: 'Nội dung từ component cha' }"
        @toggle="toggleOpen"
    />

     <!-- Step 6: Thiết lập Phong cách và Nội dung -->
    <ContentStyleSetup
        v-if=sections[7].open
        :open="sections[7].open"
        :loading="loading"
        :contents="{ content: 'Nội dung từ component cha' }"
        @submit="handleSubmitContentStyleSetup"
        @toggle="toggleOpen"
    />

    <!-- Step 7: Kết quả và hiệu chỉnh -->
    <ResultStep
        v-if=sections[8].open
        :results="results"
        :open="sections[8].open"
        :loading="loading"
        :content="content"
        :projectId="businessProjectId"
        @updateProject="updateProjectExpert"
        @showPostForm="handleShowPostForm"
        @submit="handleSubmitContentStyleSetup"
        @toggle="toggleOpen"
    />
    <!-- Step 7: Xem kết quả và hiệu chỉnh -->
<!--    <ResultView-->

<!--    />-->

    <!-- Step 8: Đăng bài -->
    <PostFinalForm
        ref="postFormRef"
        v-if=sections[9].open
        :templatePostForm="isGenAIStatus ? 'AutoPostAIForm' : 'BasicForm'"
        @beforeSubmit="beforeSubmitPostForm"
        @onSuccess="onSuccessPostForm"
    />

    <div v-if="!showFBContent"
        class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
         <LoadingCircle  loading-title="Hệ thống đang xử lý. Xin vui lòng đợi." />
    </div>

</template>

<script setup>
import ProjectHeader from "@/Components/Project/ProjectHeader.vue";
import Layout from "@/Layouts/Client/AiLayout.vue";
import DropdowRadix from "@/Components/DropdowRadix.vue";
import Loading from "@/Components/Loading.vue";
import LoadingCircle from "@/Components/LoadingCircle.vue";
import Modal from "@/Components/Modal.vue";
import SelectRadix from "@/Components/SelectRadix.vue";
import { Head, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { FileText, Trash2, Upload } from "lucide-vue-next";
import { marked } from "marked";
import suneditor from "suneditor";
import "suneditor/dist/css/suneditor.min.css";
import { computed, nextTick, onBeforeMount, onBeforeUnmount, onMounted, onUnmounted, onUpdated, reactive, ref, watch, defineEmits } from "vue";
import { toast } from "vue3-toastify";
import AIBackgroundComponent from "../../AIBackground/Component.vue";
import TextToImage from "../../AIImage/CreateImage.vue";
import MyAIComponent from "../../AIImage/MyAIComponent.vue";
import CreateMusic from "../../TextToSong/CreateMusic.vue";
import CreateForm from "../../TextToVideo/CreateForm.vue";
import AnalysisStep from "../AnalysisStep.vue";
import ContentProductAd from "../ContentProductAd.vue";
import MusicStep from "../MusicStep.vue";
import PoetryStep from "../PoetryStep.vue";
import PostForm from "@/Components/ShareAiText/PostForm.vue";
import { convertToSnakeCase } from "@/lib/utils";
import { inject } from "vue";
const helpers = inject("helpers");
import Step from "@/Components/Step.vue";
import MaintenanceModal from "@/Components/MaintenanceModal.vue";
import ProjectExpert from "./ProjectExpert.vue";
import AnalysisBusiness from "../AnalysisBusiness.vue";
import { eventBus } from "@/lib/eventBus.js";

// import cac step
import BasicInfoSection from './Components/BasicInfoSection.vue'
import BusinessInfoSection from './Components/BusinessInfoSection.vue'
import ProductAnalysisSection from './Components/ProductAnalysisSection.vue';
import CommunicationStrategy from './Components/CommunicationStrategy.vue';
import CreateActionStep from './Components/CreateActionStep.vue';
import FacebookContentListStep from './Components/FacebookContentListStep.vue';
import ContentStyleSetup from './Components/ContentStyleSetup.vue';
import ResultView from './Components/ResultView.vue';
import PostFinalForm from './Components/PostFinalForm.vue';
import PopupFacebookContent from './Components/PopupFacebookContent.vue';
import ResultStep from './Components/ResultStep.vue';

const props = defineProps({
    project: Object,
})

const information_project= ref("")
const { props: pageProps } = usePage();
const results = ref({
    content:"",
    images:[
        {
            imageRef:""
        },
        {
            imageRef:""
        },
        {
            imageRef:""
        },
         {
            imageRef:""
        },
    ]
})

const showFBContent = ref(true)
const handleShowFBContent = (isShow = true) => {
    showFBContent.value = isShow
}

let urlParams = new URLSearchParams(window.location.search);
const mode = ref(urlParams.get("mode") || "basic");
const listProject = ref(props.project);
const sections = ref([
    { open: true },
    { open: false },
    { open: false },
    { open: false },
    { open: false },
    { open: false },
    { open: false },
    { open: false },
    { open: false },
    { open: false },
    { open: false },
]);

const basicInfo = ref("")
const action = ref("")
const handleDetailProject = async (data) => {
    businessProjectId.value = data.id
    const response = await axios.get(route('ai-business.project-expert-detail', {id:businessProjectId.value}));
    const project = response.data.project
    selectedProject.value = project
    userId.value = project.user_id
    if(project.answers) {
        sections.value[1].open = true;
        basicInfo.value = {
            title: project.title,
            description: project.description,
            productLink: project.url,
            image_product: {
                file:null,
                url:project.image_product
            },
            model_product: {
                file:null,
                url:project.model_product
            },
            files: project.files,
            answers: project.answers
        }
        conversationId.value = project.answers.conversation_id ?  project.answers.conversation_id : ""
        if (childRef.value) {
            childRef.value.updateBasicInfo(basicInfo.value);
        }

        if(childBusRef.value && project.buss_info) {
            childBusRef.value.updateBussInfo(project.buss_info);
        }
    }
    if(project.expert_info) {
        childRef.value.initEditor(project.expert_info);
        sections.value[2].open = true;
        if(childSectionRef.value && project.analysis_sections) {
            childSectionRef.value.updateAnalysisSections(project.analysis_sections);
        }
    }
    if(project.communication_strategy) {
        sections.value[3].open = true;
        sections.value[4].open = true;
        await updateDataMedia(project.communication_strategy, true)
    }
    sections.value[5].open = false;
    sections.value[8].open = false;
    if(project.results) {
        results.value = project.results;
        if(refCreateAction.value) {
            refCreateAction.value.updateAction(results.value.action)
        }
        if(results.value.action && results.value.action == "fanpage_content") {
            sections.value[5].open = true;
        } else if(results.value.action && results.value.action == "fanpage_ads") {
            sections.value[8].open = true;
            sections.value[9].open = true;
        }
    }
}

const userId = ref(null)
const messagesMap = reactive({});
const childBusRef = ref("")
const formProject = reactive({
    id: null,
    title: "",
    description: "",
    image_product: "",
    productLink: "",
    files: [],
});
const sunEditorInstance = ref(null);
const pendingEditorContent = ref(null);
const isEditorInitialized = ref(false);
const conversationId = ref(null);
const businessProjectId = ref(null);
const selectedOption = ref("");
const loading = ref(false);
const options = reactive([
    {
        label: "Văn bản",
        subOptions: ["Status Fanpage", "Status profile", "Làm thơ chúc Tết"],
        isDropdownOpen: false,
    },
    { label: "Âm nhạc", subOptions: [], isDropdownOpen: false },
    { label: "Hình ảnh", subOptions: [], isDropdownOpen: false },
    { label: "Video", subOptions: [], isDropdownOpen: false },
    { label: "Chiến dịch", subOptions: [], isDropdownOpen: false },
]);


const formDataSteps = ref({});

const toggleSection = (index) => {
    sections.value[index].open = !sections.value[index].open;
};
const fbContentRef = ref(null)
const refCreateAction = ref(null)
const handleShowSection = (index, isOpen = true) => {
    if(index == 6 && !isOpen) {
        if(fbContentRef.value) {
            fbContentRef.value.callApiGetContent()
        }
    }
    if(index == 1 && isOpen) {
        if(childBusRef.value) {
            childBusRef.value.scrollIntoView()
        }
    }
    if(index == 2 && isOpen) {
        if(childSectionRef.value) {
            childSectionRef.value.scrollIntoView()
        }
    }
    if(index == 3 && isOpen) {
        if(childMediaRef.value) {
            childMediaRef.value.scrollIntoView()
        }
    }

    if(index == 4 && isOpen) {
        if(refCreateAction.value) {
            refCreateAction.value.scrollIntoView()
        }
    }
    sections.value[index].open = isOpen
}
const selectedProject = ref("")
const handleProjectSubmit = async (data, step, isStartChat = true) => {
    try {
        if (!mode.value) {
            toast.error("Vui lòng chọn chế độ để sử dụng tính năng.");
            return;
        }

        // Validate
        if (!data.title?.trim()) {
            toast.error("Vui lòng nhập tiêu đề sản phẩm");
            return;
        }

        // Convert to FormData
        const apiFormData = new FormData();
        apiFormData.append('title', data.title);
        apiFormData.append('description', data.description);
        apiFormData.append('url', data.productLink);

        if (data.image_product?.file) {
            apiFormData.append('image_product', data.image_product.file);
        }

        if (data.model_product?.file) {
            apiFormData.append('model_product', data.model_product.file);
        }

        if (data.files?.length) {
            apiFormData.append('files', JSON.stringify(data.files));
        }

        if (data.answers) {
            apiFormData.append('answers', JSON.stringify(data.answers));
        }

        if (businessProjectId.value) {
            apiFormData.append("id", businessProjectId.value);
        }

        const response = await axios.post(
            route(businessProjectId.value ? "ai-business.update-project-expert" : "ai-business.create-project-expert"),
            apiFormData,
            {
                headers: { "Content-Type": "multipart/form-data" }
            }
        );

        if (response.data?.data) {
            const project = response.data.data;
            selectedProject.value = project
            businessProjectId.value = project.id;
            userId.value = project.user_id;

            if (isStartChat) {
                await startChat(data);
            }

            toast.success("Cập nhật thành công!");
        }

    } catch (error) {
        console.error("Error:", error);
        if (error.response?.status === 422) {
            sections.value[1].open = true;
            const errors = error.response.data.errors;
            if (errors) {
                toast.error(Object.values(errors)[0][0]);
            }
        } else {
            sections.value[1].open = true;
            toast.error(error.response?.data?.message || "Thao tác thất bại!");
        }
    }
};

const handleShowAnalysisSection = async (section) => {
    const formData = new FormData();
    const query = {
        currentStep:"buoc3",
        mode:mode.value,
        phan_tich_san_pham:section.title,
        currentSubStep:"buoc3_"+section.id,
    }

    const payload = {
        query: JSON.stringify(query),
    };
    const response = await callApiChatStream(payload);
    // Process stream
    const reader = response.body.getReader();
    const decoder = new TextDecoder();
    let buffer = "";
    let messages = "";

    while (true) {
        const { value, done } = await reader.read();
        if (done) break;

        buffer += decoder.decode(value, { stream: true });
        const lines = buffer.split("\n");
        buffer = lines.pop() || "";

        for (const line of lines) {
            if (line.trim().startsWith("data: ")) {
                try {
                    const jsonStr = line.trim().slice(5);
                    const data = JSON.parse(jsonStr);

                    if (data.event === "message" && data.answer) {
                        messages += data.answer;
                        await updateConentSection(section, messages, false);
                    }

                    if (data.event === "workflow_started" && data.conversation_id) {
                        conversationId.value = data.conversation_id;
                    }

                    if (data.event === "workflow_finished") {
                        await new Promise(resolve => setTimeout(resolve, 100));
                        await calculateStreamCredit(messages, conversationId.value);
                    }
                } catch (e) {
                    console.error("JSON parse error:", e);
                    continue;
                }
            }
        }
    }

    // Process remaining buffer
    if (buffer.trim()) {
        try {
            const data = JSON.parse(buffer.trim().slice(5));
            if (data.event === "message" && data.answer) {
                messages += data.answer;
                await updateConentSection(section, messages, false);
            }
        } catch (e) {
            console.warn("Final buffer parse error:", e);
        }
    }
    await updateConentSection(section, "", true);
    if(childBusRef.value) {
        childBusRef.value.toggleMainSection()
    }
}

const handleSubmitAnalysisSection = async () => {
    const formData = new FormData();
    const query = {
        currentStep:"buoc4",
        mode:mode.value,
    }

    const payload = {
        query: JSON.stringify(query),
    };
    const response = await callApiChatStream(payload);
    // Process stream
    const reader = response.body.getReader();
    const decoder = new TextDecoder();
    let buffer = "";
    let messages = "";

    while (true) {
        const { value, done } = await reader.read();
        if (done) break;

        buffer += decoder.decode(value, { stream: true });
        const lines = buffer.split("\n");
        buffer = lines.pop() || "";

        for (const line of lines) {
            if (line.trim().startsWith("data: ")) {
                try {
                    const jsonStr = line.trim().slice(5);
                    const data = JSON.parse(jsonStr);

                    if (data.event === "message" && data.answer) {
                        messages += data.answer;
                        await updateDataMedia(messages);
                    }

                    if (data.event === "workflow_started" && data.conversation_id) {
                        conversationId.value = data.conversation_id;
                    }

                    if (data.event === "workflow_finished") {
                        await new Promise(resolve => setTimeout(resolve, 100));
                        await calculateStreamCredit(messages, conversationId.value);
                    }
                } catch (e) {
                    console.error("JSON parse error:", e);
                    continue;
                }
            }
        }
    }

    // Process remaining buffer
    if (buffer.trim()) {
        try {
            const data = JSON.parse(buffer.trim().slice(5));
            if (data.event === "message" && data.answer) {
                messages += data.answer;
                await updateDataMedia(messages);
            }
        } catch (e) {
            console.warn("Final buffer parse error:", e);
        }
    }
    if (childSectionRef.value) {
        childSectionRef.value.updateSuccess();
    }
    await updateDataMedia(messages, true);
    formData.append('id', businessProjectId.value);
    formData.append('communication_strategy', messages);
    await updateProjectExpert(formData)
    if(childSectionRef.value) {
        childSectionRef.value.toggleMainSection()
    }
}

const handleSubmitContentStyleSetup = async (data) => {
    const formData = new FormData();
    results.value.content = ""
    const query = {
        "currentStep": "buoc5",
        "mode": "expert",
        "action": "Tạo 1 Status Fanpage quảng cáo Sản phẩm",
        "context": data.context,
        "goal": data.goal,
        "customerObject": data.customerObject,
        "mainMessage": data.mainMessage,
        "emotion": data.emotion,
        "style": data.style,
        "articleFormat": data.articleFormat,
        "sale": data.sale,
        "cta":  data.cta,
        "currentSubStep": "buoc5_1"
    }
    const payload = {
        query: JSON.stringify(query),
    };
    const response = await callApiChatStream(payload);
    // Process stream
    const reader = response.body.getReader();
    const decoder = new TextDecoder();
    let buffer = "";
    let messages = "";

    while (true) {
        const { value, done } = await reader.read();
        if (done) break;

        buffer += decoder.decode(value, { stream: true });
        const lines = buffer.split("\n");
        buffer = lines.pop() || "";

        for (const line of lines) {
            if (line.trim().startsWith("data: ")) {
                try {
                    const jsonStr = line.trim().slice(5);
                    const data = JSON.parse(jsonStr);

                    if (data.event === "message" && data.answer) {
                        messages += data.answer;
                         results.value.content = messages
                    }

                    if (data.event === "workflow_started" && data.conversation_id) {
                        conversationId.value = data.conversation_id;
                    }

                    if (data.event === "workflow_finished") {
                        await new Promise(resolve => setTimeout(resolve, 100));
                        await calculateStreamCredit(messages, conversationId.value);
                    }
                } catch (e) {
                    console.error("JSON parse error:", e);
                    continue;
                }
            }
        }
    }

    // Process remaining buffer
    if (buffer.trim()) {
        try {
            const data = JSON.parse(buffer.trim().slice(5));
            if (data.event === "message" && data.answer) {
                messages += data.answer;
                results.value.content = messages
            }
        } catch (e) {
            console.warn("Final buffer parse error:", e);
        }
    }
    handleShowSection(8)
    handleShowSection(9)
}

const callApiChatStream = async (payload) => {
     // Try primary endpoint
    payload.conversation_id = conversationId.value
    let response = await fetch(route("ai-business.sendChatStreamingExpert"), {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        },
        body: JSON.stringify(payload),
    });
    return response
}

const handleSubmitAnalysicBusiness = async (data) => {
    data.mode = mode.value
    data.currentStep = "buoc2";
    const payload = {
        query: JSON.stringify(data),
    };
    payload.conversation_id = conversationId.value
    payload.mode = mode.value
    // Try primary endpoint
    const response = await callApiChatStream(payload);
    const section = {
        title:"Chân dung khách hàng (Customer Persona)",
        id:1
    }
    await handleShowAnalysisSection(section)

}
const apiUrl = import.meta.env.VITE_API_URL
const maxPost = import.meta.env.VITE_MAX_POST
const handleSubmitCreateActionStep = async (data) => {
    const query = {
        mode:mode.value,
        currentStep:"buoc5",
        userId:userId.value,
        projectId:businessProjectId.value,
        totalPost:data.totalPost ? data.totalPost : 0,
        domain:apiUrl,
        maxPost: parseInt(maxPost),
        action:data.label,
        "currentSubStep": data.currentSubStep,
    }
    const payload = {
        query: JSON.stringify(query),
    };
    payload.conversation_id = conversationId.value
    payload.mode = mode.value
    // Try primary endpoint
    const response = await callApiChatStream(payload);
}

const handleSubmitInfo = async(content) => {
    const formData = new FormData();
    const query = {
        currentStep: "buoc1",
        currentSubStep: "buoc1_2",
        mode: mode.value,
        content:content
    };

    const payload = {
        query: JSON.stringify(query),
    };
    payload.conversation_id = conversationId.value
    payload.mode = mode.value
    // Try primary endpoint
    let response = await fetch(route("ai-business.sendChatStreamingExpert"), {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        },
        body: JSON.stringify(payload),
    });
    if(childRef.value) {
        childRef.value.toggleMainSection();
    }
}

const calculateStreamCredit = async (fullAnswer, conversationId) => {
    try {
        const response = await fetch(route("ai-business.credit.calculate-stream"), {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
            body: JSON.stringify({
                content: fullAnswer,
                screen:38,
                conversation_id: conversationId,
            }),
        });
        const result = await response.json();
        if (!result.success) {

        }
        return result;
    } catch (error) {
        console.error("Error calculating stream credit:", error);
        throw error;
    }
};

const updateProjectExpert = async (formData) => {
    try {
        const response = await axios.post(
            route("ai-business.update-project-expert"),
            formData,
            {
                headers: { "Content-Type": "multipart/form-data" }
            }
        );
        //toast.success("Cập nhật thành công")
    } catch(error) {
        //toast.error("Cập nhật dự án thất bại")
    }
}

const checkCredit = async () => {
    try {
        const formData = new FormData();
        formData.append("model", "suggestBusiness");
        formData.append("type", "suggestBusiness");
        // Gọi API để kiểm tra credit của user
        const response = await axios.post(route("credit.check_credit"), formData);
        if (response.data.success) {
            // Đủ credit để tiếp tục
            return true;
        } else if (response.data.is_vip_expired) {
            showAffKeyPopup.value = true;
            if (response.data.affCode == "aff_trial") {
                popupMessage.value = " Gói của bạn đã hết hạn. Xin vui lòng gia hạn tài khoản để tiếp tục sử dụng tính năng này.";
                acceptUpgrade.value = false;
            }
            return false;
        } else {
            const currentCredit = pageProps.auth.user.credit || 0;
            const additionalCredit = response?.data?.data?.total_price - currentCredit;
            eventBus.emit("popup-buy-credit", { currentCredit, additionalCredit, showBuyCreditPopup: true });
            return false;
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi khi kiểm tra credit:", error);
        toast.error("Không thể kiểm tra credit. Vui lòng thử lại.");
        if (error.response && error.response.status === 422) {
            const errors = error.response.data.errors;
            Object.values(errors).forEach((errorMessages) => {
                if (Array.isArray(errorMessages)) {
                    errorMessages.forEach((error) => {
                        toast.error(error);
                    });
                } else {
                    toast.error(errorMessages);
                }
            });
        } else {
            if (error.response) {
                toast.error(error.response.data.message || "Đã xảy ra lỗi.");
            } else if (error.request) {
                toast.error("Không thể kết nối đến server.");
            } else {
                toast.error("Lỗi: " + error.message);
            }
        }
        return false;
    }
};
const postFormRef = ref(null)
const handleShowPostForm = (postForm) => {
    if(postFormRef.value) {
        postFormRef.value.openPostDetail(postForm);
    }
}

const startChat = async (data) => {
    // Check prerequisites
    const hasEnoughCredit = await checkCredit();
    if (!hasEnoughCredit) return;

    loading.value = true;
    try {
        const query = {
            currentStep: "buoc1",
            currentSubStep: "buoc1_1",
            mode: mode.value,
            ten_san_pham_dich_vu: data.title,
            gioi_thieu_san_pham_dich_vu: data.description,
            url: data.productLink,
            san_pham_da_tung_duoc_ra_mat: data.answers?.['5.1'],
            do_nhan_dien_thuong_hieu: data.answers?.['5.2'],
            khach_hang_trong_pheu_chuyen_doi: data.answers?.['5.3']?.join(', '),
            suy_nghi_khach_hang_ve_san_pham: data.answers?.['5.4'],
            dinh_huong_tiep_theo: data.answers?.['5.5']
        };

        const files = data.files
        const payload = {
            query: JSON.stringify(query),
            files,
            project_id: businessProjectId.value
        };

        // Try primary endpoint
        let response = await callApiChatStream(payload)
        // Fallback to secondary endpoint if needed
        if (!response.ok) {
            response = await fetch(route('ai-business.send-ai-business-stream'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            });
        }

        // Process stream
        const reader = response.body.getReader();
        const decoder = new TextDecoder();
        let buffer = "";
        let messages = "";

        while (true) {
            const { value, done } = await reader.read();
            if (done) break;

            buffer += decoder.decode(value, { stream: true });
            const lines = buffer.split("\n");
            buffer = lines.pop() || "";

            for (const line of lines) {
                if (line.trim().startsWith("data: ")) {
                    try {
                        const jsonStr = line.trim().slice(5);
                        const data = JSON.parse(jsonStr);

                        if (data.event === "message" && data.answer) {
                            messages += data.answer;
                            information_project.value = messages.trim();
                            await updateEditor(messages);
                        }

                        if (data.event === "workflow_started" && data.conversation_id) {
                            conversationId.value = data.conversation_id;
                        }

                        if (data.event === "workflow_finished") {
                            await new Promise(resolve => setTimeout(resolve, 100));
                            await calculateStreamCredit(messages, conversationId.value);
                        }
                    } catch (e) {
                        console.error("JSON parse error:", e);
                        continue;
                    }
                }
            }
        }

        // Process remaining buffer
        if (buffer.trim()) {
            try {
                const data = JSON.parse(buffer.trim().slice(5));
                if (data.event === "message" && data.answer) {
                    messages += data.answer;
                    information_project.value = messages.trim();
                    await updateEditor(messages);
                }
            } catch (e) {
                console.warn("Final buffer parse error:", e);
            }
        }

        // Update final data
        const formData = new FormData();
        formData.append('id', businessProjectId.value);
        formData.append('conversation_id', conversationId.value);
        formData.append('expert_info', messages);
        await updateProjectExpert(formData)
        await updateEditor(messages, true);
    } catch (error) {
        console.error("Chat error:", error);
        toast.error("Có lỗi xảy ra, vui lòng thử lại.");
    } finally {
        loading.value = false;
    }
};

const facebookContentDetail = ref(null)
const handleUpdateFaceBookContent = (data) => {
    facebookContentDetail.value = data
}

// Helper function to update editor
const updateEditor = async (content, isSuccess = false) => {
    if (sunEditorInstance.value) {
        try {
            sunEditorInstance.value.setContents(marked(content) || "");
        } catch (e) {
            console.warn("Editor update failed, reinitializing...");
            if (childRef.value) {
                childRef.value.initEditor(content.trim());
            }
            await nextTick();
            if (sunEditorInstance.value) {
                sunEditorInstance.value.setContents(marked(content) || "");
            }
        }
    } else {
        if (childRef.value) {
            childRef.value.initEditor(content.trim());
        }
        await nextTick();
        if (sunEditorInstance.value) {
            sunEditorInstance.value.setContents(marked(content) || "");
        }
    }
    if (childRef.value) {
        childRef.value.initEditor(content.trim(), isSuccess);
    }
};

const updateConentSection = async (section, content, isUpdate) => {
    if (childSectionRef.value) {
        childSectionRef.value.updateDataSection(section, content.trim(), isUpdate);
    }
}

const updateDataMedia = async (content, isUpdate = false) => {
    if (childMediaRef.value) {
        childMediaRef.value.updateDataMedia(content.trim(), isUpdate);
    }
}

const setValue = (project, isUpdate) => {
    if (isUpdate) {
        listProject.value = listProject.value?.map((item) => {
            if (item.id === project.id) {
                return project;
            }
            return item;
        });
    } else {
        listProject.value.push(project);
    }
    selectedProject.value = project;

};
const childRef = ref(null);
const popupFBRef = ref(null);
const childSectionRef = ref(null);
const childMediaRef = ref(null);
onMounted(() => {
    document.addEventListener("click", (e) => {
        if (!e.target.closest(".dropdown")) {
            options.forEach((option) => (option.isDropdownOpen = false));
        }
    });
    if (childRef.value) {
        childRef.value.initEditor();
    }
    if (mode.value) {
        sections.value[0].open = true;
        // sections[1].open = false;
        // sections[2].open = false;
        // sections[3].open = false;
    }
});

watch(
    () => sections.value[1].open,
    (newVal) => {
        if (newVal) {
            // Khởi tạo editor khi section được mở
            nextTick(() => {
                if (sunEditorInstance.value) {
                    sunEditorInstance.value.destroy();
                    sunEditorInstance.value = null;
                }
            });
        }
    }
);

onBeforeUnmount(() => {
    if (sunEditorInstance.value) {
        sunEditorInstance.value.destroy();
        sunEditorInstance.value = null;
    }
    // Xóa textarea element
    const textarea = document.getElementById("editor");
    if (textarea) {
        textarea.remove();
    }
});
defineExpose({ handleDetailProject });

</script>

<style>
.sun-editor-common {
    display: none !important;
}
.sun-editor {
    width: 100% !important;
}
.p-editor {
    --p-editor-content-color: #000000;
    /* Đặt màu mặc định cho văn bản */
    font-size: 14px;
}

.sun-editor-editable p,
.sun-editor-editable h1,
.sun-editor-editable h2,
.sun-editor-editable h3,
.sun-editor-editable h4,
.sun-editor-editable h5,
.sun-editor-editable h6,
.sun-editor-editable ul,
.sun-editor-editable ol,
.sun-editor-editable li,
.sun-editor-editable blockquote,
.sun-editor-editable pre,
.sun-editor-editable code,
.sun-editor-editable table,
.sun-editor-editable tr,
.sun-editor-editable th,
.sun-editor-editable td {
    font-size: 15px;
}

.sun-editor-editable h1 {
    color: #ff5733 !important;
    font-weight: bold;
}

.sun-editor {
    flex: 1 !important;
    border-radius: 12px;
    overflow: hidden;
}

#editor-container {
    min-height: 300px;
    background: white;
}

.sun-editor {
    width: 100% !important;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
}

.sun-editor-editable {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    font-size: 15px !important;
    line-height: 1.6 !important;
    color: #333 !important;
    padding: 20px !important;
    min-height: 300px;
    white-space: pre-wrap !important;
    word-wrap: break-word !important;
}

/* Hide toolbar */
.sun-editor .se-toolbar {
    display: none !important;
}

/* Hide unnecessary elements */
.sun-editor-common {
    display: none !important;
}

.sun-editor-editable hr {
    border: 0;
    height: 1px;
    background: #e2e8f0;
    margin: 1rem 0;
}

.sun-editor-editable em {
    font-style: italic;
    color: #4a5568;
}

.sun-editor-editable strong {
    font-weight: 600;
    color: #2d3748;
}

.sun-editor-editable br {
    display: block;
    content: "";
    margin-top: 0.5em;
}

.sun-editor-editable p {
    margin-bottom: 1em;
    line-height: 1.6;
}
.show-content li {
   margin-bottom: -10px !important;
}
.show-content p {
    margin-bottom: -25px !important;
}
.show-content ul {
    margin-bottom: -30px !important;
    margin-top: -10px !important;
}
.show-content {
    max-height:400px;
    overflow:auto
}
</style>
