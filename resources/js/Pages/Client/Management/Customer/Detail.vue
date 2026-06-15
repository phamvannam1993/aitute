<template>
    <Head title="Chi tiết khách hàng" />
    <Layout :breadcrumbs="breadcrumbsData">
        <form @submit.prevent="handleSubmit">
            <section class="flex gap-5 items-center">
                <div class="flex gap-2 items-center">
                    <div class="text-[#207A91] border border-[#207A91] rounded-full px-2 pt-1 pb-2 hidden md:block">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-robot" viewBox="0 0 16 16">
                            <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5M3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.6 26.6 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.93.93 0 0 1-.765.935c-.845.147-2.34.346-4.235.346s-3.39-.2-4.235-.346A.93.93 0 0 1 3 9.219zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a25 25 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25 25 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135"/>
                            <path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2zM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5"/>
                        </svg>
                    </div>
                    <p class="text-lg lg:text-xl font-bold text-black">Chi tiết lịch sử tư vấn với "{{ user.page_name }}"</p>
                </div>
                <button class="w-[150px] md:w-[170px] ml-auto text-white border-secondary-color bg-ai3goc-sec px-5 md:px-10 py-2 rounded-xl hover:scale-105 transform transition duration-300">Cập nhật</button>
            </section>

            <section class="rounded-3xl drop-shadow-lg bg-white text-black mt-4 mb-8">
                <div class="grid grid-cols-[120px_1fr] lg:grid-cols-[300px_1fr] border-b-2 border-[#D4D4D4] bg-ai3goc-primary rounded-t-3xl">
                    <div class="p-3 lg:p-4 text-white rounded-tl-3xl">Họ tên</div>
                    <div class="pl-4 lg:pl-10 p-3 lg:p-4 bg-white rounded-tr-3xl">
                        <input class="w-full p-3 border text-black border-gray-300 rounded-[13px] shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                        type="text"
                        v-model.trim="user.name"
                        required
                        pattern="^[\p{L}\s]+$"
                        @invalid="(event) => { event.target.setCustomValidity('Vui lòng nhập Tên hợp lệ!') }"
                        @input="(event) => { event.target.setCustomValidity('') }"/>
                    </div>
                </div>
                <div class="grid grid-cols-[120px_1fr] lg:grid-cols-[300px_1fr] border-b-2 border-[#D4D4D4] bg-ai3goc-primary">
                    <div class="p-3 lg:p-4 text-white">Tuổi</div>
                    <div class="pl-4 lg:pl-10 p-3 lg:p-4 bg-white">
                        <input class="w-full p-3 border text-black border-gray-300 rounded-[13px] shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                        type="number" v-model="user.age" :min="0" />
                    </div>
                </div>
                <div class="grid grid-cols-[120px_1fr] lg:grid-cols-[300px_1fr] border-b-2 border-[#D4D4D4] bg-ai3goc-primary">
                    <div class="p-3 lg:p-4 text-white">
                        <span class="block lg:hidden">SĐT</span>
                        <span class="hidden lg:block">Số điện thoại</span>
                    </div>
                    <div class="pl-4 lg:pl-10 p-3 lg:p-4 bg-white">
                        <input class="w-full p-3 border text-black border-gray-300 rounded-[13px] shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                        type="text"
                        v-model="user.phone"
                        pattern="^(?:\+84|0)(3[2-9]|5[689]|7[06-9]|8[1-689]|9[0-46-9])[0-9]{7}$"
                        @invalid="(event) => { event.target.setCustomValidity('Vui lòng nhập Số điện thoại hợp lệ!') }"
                        @input="(event) => { event.target.setCustomValidity('') }"/>
                    </div>
                </div>
                <div class="grid grid-cols-[120px_1fr] lg:grid-cols-[300px_1fr] border-b-2 border-[#D4D4D4] bg-ai3goc-primary items-center">
                    <div class="p-3 lg:p-4 text-white">Lịch hẹn với khách hàng</div>
                    <div class="pl-4 lg:pl-10 p-3 lg:p-4 bg-white h-full">
                        <div class="w-full flex flex-col ">
                            <VueDatePicker class="rounded-2xl" auto-apply :close-on-auto-apply="false" :format="timeFormat"
                            :min-date="minDate"
                            :enable-time-picker="true" locale="vi" v-model="user.appointment" :placeholder="'Chọn ngày'"></VueDatePicker>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-[120px_1fr] lg:grid-cols-[300px_1fr] border-b-2 border-[#D4D4D4] bg-ai3goc-primary items-center">
                    <div class="p-3 lg:p-4 text-white">Tình trạng</div>
                    <div class="pl-4 lg:pl-10 p-3 lg:p-4 bg-white">
                        <div class="w-full p-3 text-black border-gray-300 ">
                            <img v-if="user.conversation_status === 'hot'" src="/assets/img/icon/hot.png" alt="">
                            <img v-if="user.conversation_status === 'cool'" src="/assets/img/icon/cool.png" alt="">
                        </div>
                    </div>
                </div>
            </section>
        </form>
        <section class="mb-10 md:mb-20 text-black">
            <div class="w-full bg-white rounded-[25px] min-w-2/3 py-2 px-3 shadow-xl">
                <div class="flex justify-start gap-2 items-center w-full border-b-2 pb-1">
                    <img src="/assets/img/orion/logo/logo.svg" class="size-[30px] max-sm:hidden" />
                    <div>
                        <h2 class="text-black font-bold text-[18px] lg:text-[30px] leading-[48px]">Lịch sử chat cùng "{{ user.page_name }}"</h2>
                    </div>
                </div>
                <div ref="messageContainer" class="p-4 h-[50vh] md:h-[70vh] overflow-y-auto text-xs lg:text-sm">
                    <div class="text-black flex justify-center">
                        <button v-if="hasNextPage" @click="loadMoreMessage" class="text-center border-[1px] border-gray-300 px-4 py-2 w-fit rounded-full">
                            <span v-if="isLoading">Đang tải...</span>
                            <span v-else>Hiển thị tin nhắn cũ hơn</span>
                        </button>
                    </div>
                    <div v-for="(message, index) in messages" :key="index">
                        <!-- Message từ user -->
                        <div v-if="message.sender === 'user' && message.is_hidden !== 'yes'" class="flex flex-row-reverse justify-start items-start gap-1 mb-4 mt-3">
                            <div class="flex flex-col items-end gap-2">
                                <!-- Text message hoặc Form -->
                                <template v-if="message.content">
                                    <!-- Form -->
                                    <template v-if="checkJsonString(message.content)">
                                        <div v-if="message.path_image">
                                        </div>
                                        <DynamicForm
                                            v-else
                                            :fields="JSON.parse(message.content)"
                                            :isHistory="true"
                                        />
                                    </template>

                                    <!-- Regular text -->
                                    <template v-else>
                                        <div class="text-white bg-ai3goc-primary px-[17px] py-[12px] rounded-[15px] pb-7 relative min-w-[100px] mt-4">
                                            <div class="break-words text-base" v-html="message.beauty_content"></div>
                                        </div>
                                    </template>
                                </template>

                                <!-- Images -->
                                <div v-if="hasImages(message)"
                                        class="mt-2 grid grid-cols-1 gap-2 max-w-[300px] w-full">
                                    <!-- Temporary Images -->
                                    <template v-if="message.tempImageUrls?.length">
                                        <div v-for="(url, imgIndex) in message.tempImageUrls"
                                                :key="`temp-${imgIndex}`"
                                                class="relative aspect-square">
                                            <img :src="url"
                                                    class="w-full h-full object-cover rounded-lg cursor-pointer hover:opacity-90 transition-opacity duration-200"
                                                    @click="openImagePreview(url)"
                                                    alt="Uploaded image"/>
                                        </div>
                                    </template>

                                    <!-- Stored Images -->
                                    <template v-else-if="message.path_image_url">
                                        <div class="relative aspect-square">
                                            <img :src="message.path_image_url"
                                                    class="w-full h-full object-cover rounded-lg cursor-pointer hover:opacity-90 transition-opacity duration-200"
                                                    @click="openImagePreview(message.path_image_url)"
                                                    alt="Stored image"/>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Message từ AI -->
                        <div v-else-if="message.is_hidden !== 'yes'" class="flex justify-start items-start gap-1">
                            <div class="flex-1">
                                <template v-if="message?.content">
                                    <template v-if="checkJsonString(message?.content)">
                                        <div class="bg-[#F1F1F1] px-[17px] py-[12px] rounded-[15px] pb-7 min-w-[100px]">
                                            <DynamicForm
                                                :fields="JSON.parse(message?.content)"
                                                :isHistory="true"
                                            />
                                        </div>
                                    </template>

                                    <template v-else>
                                        <div class="bg-[#F1F1F1] px-[17px] py-[12px] rounded-[15px] pb-7 min-w-[100px]">
                                            <div class="break-words text-base" v-html="message?.content"></div>
                                        </div>
                                    </template>
                                </template>
                            </div>
                        </div>
                    </div>
                    <DynamicForm v-if="showForm && formFields.length" :fields="formFields" :name="nameForm" @submit="handleFormSubmit" @cancel="handleFormCancel" class="mt-4 p-4 bg-white rounded-lg shadow" />
                </div>
            </div>
        </section>

        <div v-if="isZoomImage" class="fixed inset-0 flex items-center justify-center z-[60] bg-black bg-opacity-30">
            <div class="bg-white rounded-3xl w-10/12 h-[90%] relative text-[#37009A] p-6">
                <div class="flex gap-2 items-center">
                    <img src="/assets/img/ai_bacsi/icon_ai_bacsi.png" alt="Xem thêm" class="size-[32px] lg:size-[47px]" />
                    <p class="text-lg lg:text-xl font-bold">Hình ảnh</p>
                </div>
                <div class="flex justify-center items-center h-[90%]">
                    <img :src="imageZoom" alt="Zoom image" class="h-full object-contain rounded-3xl" />
                </div>
                <button @click="isZoomImage = false" class="absolute top-4 right-4 bg-white text-black text-2xl rounded-full">
                    <img src="/assets/svgs/close_button.svg" alt="close button" class="size-12" />
                </button>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from "@/Layouts/Client/Layout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onBeforeMount } from "vue";
import axios from "axios";
import { toast } from "vue3-toastify";
import { marked } from "marked";
import DynamicForm from '../../AIChat/DynamicForm.vue';
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

const props = defineProps({ data: Object });
const user = ref(props.data);
const breadcrumbsData = [
    { text: "Quản lý khách hàng", link: "management.customer.index" },
    { text: "Chi tiết khách hàng", link: "management.customer.detail", params: {'id': user.value.id}}
];
const imageZoom = ref(null);
const isZoomImage = ref(false);
const currentPage = ref(1);
const hasNextPage = ref(false);
const isLoading = ref(false);
const messages = ref([]);
const minDate = new Date();
const formatMessage = (message) => {
    try {
        // Check if content is JSON
        const parsedContent = JSON.parse(message.content);

        // Nếu là array và có action "bds_appointment"
        if (Array.isArray(parsedContent) &&
            parsedContent.some(item => item.type === 'action' && (item.content === 'bds_appointment' || item.content === 'input_info' || listFormHidden.value.includes(item.content)))) {

                // Nếu là kết quả phân tích da
            if(parsedContent.some(item => item.message === 'Skin suceess request')){
                return {
                    ...message,
                    skinData: parsedContent.find(item => item.message === 'Skin suceess request' && item.status === 200)
                };
            }

             // Check form buy để ẩn khi hiển thị lịch sử
            const isBuyForm = checkFormBuyProduct(parsedContent);
            console.log("🚀 ~ parsedContent.some ~ isBuyForm:", isBuyForm)

            // Preserve the JSON content
            return {
                ...message,
                isForm: true, // Flag để nhận biết đây là form
                formData: parsedContent, // Lưu parsed data để render form
                content: (message.sender === 'ai' && isBuyForm) ? null : message.content // Xóa content để render form
            };
        }

        // Nếu là JSON nhưng không phải form, format as normal
        message.content = marked(message.content);
        return message;
    } catch (e) {
        // Nếu không phải JSON, format bằng marked
        if (message.sender === 'ai') {
            message.content = marked(message.content);
        }
        return message;
    }
};

const checkFormBuyProduct = (message) => message.some(item => item.type === 'action' && listFormHidden.value.includes(item.content));

const handleBack = () => {
    window.location.href = route("management.patient.index");
};

const handleZoomImage = (item) => {
    imageZoom.value = item.image_url;
    isZoomImage.value = true;
};

const handleUpdateUser = async () => {
    try {
        const res = await axios.post(route("management.customer.updateUserSale"), { user_sale: user.value });
        if (res.status === 200) {
            if (res.data.success) {
                toast.success(res.data.message);
            }else{
                toast.error(res.data.message);
            }
        }
    } catch (error) {
        toast.error(error.response.data.message || "Đã xảy ra lỗi.");
    }
};

const checkJsonObject = (str) => {
    return str.trim().startsWith("{") && str.trim().endsWith("}");
};
const loadMoreSession = async () => {
    if (hasNextPageSessions.value) {
        currentPageSessions.value++;
        isLoading.value = true;

        try {
            const response = await axios.get("/ai-chat/chat/history?page=" + currentPageSessions.value);
            hasNextPageSessions.value = response?.data?.data?.next_page_url || null;
            const newSessions = response.data.data.data || [];
            if (newSessions.length > 0) {
                history.value = [...history.value, ...newSessions];
            }
        } catch (error) {
            console.error("Đã xảy ra lỗi khi lấy lịch sử tin nhắn:", error.message);
        } finally {
            isLoading.value = false;
        }
    } else {
        console.log("Không còn trang tiếp theo.");
    }
};

onBeforeMount(async () => {
    try {
        const response = await axios.get(
            route("management.customer.messageHistories", {
                conversationId: props.data.conversation_id,
                page: currentPage.value,
            })
        );
        if (response?.data?.data?.next_page_url) {
            hasNextPage.value = response.data.data.next_page_url;
        }
        messages.value = response.data.data.data
            .sort((a, b) => a.id - b.id) // Sorting by id
            .map((messages) => formatMessage(messages));
        console.log("🚀 ~ onBeforeMount ~ response.data.data.data:", response.data.data.data);
    } catch (error) {
        console.error("Đã xảy ra lỗi khi lấy lịch sử tin nhắn:", error.message);
    }
});

const loadMoreMessage = async () => {
    if (hasNextPage.value) {
        currentPage.value++;
        isLoading.value = true;

        try {
            const response = await axios.get(
                route("management.customer.messageHistories", {
                    conversationId: props.data.conversation_id,
                    page: currentPage.value,
                })
            );
            hasNextPage.value = response?.data?.data?.next_page_url || null;
            const newMessages =
                response.data.data.data
                    .sort((a, b) => a.id - b.id) // Sorting by id
                    .map((messages) => formatMessage(messages)) || [];
            if (newMessages.length > 0) {
                messages.value = [...newMessages, ...messages.value];
            }
        } catch (error) {
            console.error("Đã xảy ra lỗi khi lấy lịch sử tin nhắn:", error.message);
        } finally {
            isLoading.value = false;
        }
    } else {
        console.log("Không còn trang tiếp theo.");
    }
};

const checkJsonString = (str) => {
    try {
        const parsed = JSON.parse(str);
        return Array.isArray(parsed) && parsed.length > 0;
    } catch (e) {
        return false;
    }
};

// Helper function to check if message has images
const hasImages = (message) => {
    return (message.tempImageUrls && message.tempImageUrls.length > 0) ||
        (message.path_image && message.path_image.length > 0);
};

// Helper function to get array of image paths
const getImagePaths = (pathString) => {
    return pathString.split(',').map(path => path.trim()).filter(Boolean);
};
const listFormHidden = ref(['buy_info', 'checkbox_form', 'bds_appointment', 'call_appointment']);

const formatDate = (dateString) => {
    if (!dateString) return "";
    const date = new Date(dateString);
    return date.toLocaleString("vi-VN", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    }).replace(",", "");
};
const handleSubmit = () => {
    handleUpdateUser()
}
</script>
