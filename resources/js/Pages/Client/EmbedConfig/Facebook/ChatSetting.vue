<template>
    <div class="flex flex-col mx-auto p-3 md:p-5 mt-5 mb-5 bg-white md:rounded-3xl shadow-lg text-[14px] md:text-[16px]">
        <h1 class="md:text-[20px] font-bold md:mb-5 text-gray-800">2. Cài đặt Chat bot</h1>
        <div class="grid grid-cols-1 gap-8">
            <div class="py-3 md:py-5 rounded-lg">
                <div class="grid grid-cols-12 gap-2 items-center">
                    <h2 class="col-span-12 md:col-span-2 md:text-xl font-semibold text-gray-700">Giọng điệu trả lời</h2>
                    <form class="col-span-12 md:col-span-10 flex flex-1 grid grid-cols-10 gap-3 md:gap-4" action="" @submit.prevent="submitForm">
                        <select v-model="toneConfig" class="col-span-7 md:col-span-8 bg-white block appearance-none w-full text-black px-4 pr-8 rounded-[10px] focus:outline-none focus:bg-white focus:border-gray-500">
                            <option v-for="(tone, index) in toneConfigs" :value="index">{{ tone }}</option>
                        </select>
                        <button class="col-span-3 md:col-span-2 bg-ai3goc-sec text-white rounded-xl w-full py-2 md:py-3 hover:opacity-90 h-fit">
                            Lưu
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <!-- <div class="grid grid-cols-1 gap-8">
            <div class="py-5 rounded-lg">
                <div class="grid grid-cols-12 items-center">
                    <h2 class="col-span-2 text-xl font-semibold text-gray-700">Tạo link chat box</h2>
                    <input type="text" v-model="chatBoxIframeUrl" class="col-span-8 bg-white block text-[14px] appearance-none flex-1 text-black py-2 px-4 pr-8 rounded-[10px] focus:outline-none focus:bg-white focus:border-gray-500">
                    <button @click="generateChatBoxIframeUrl" class="col-span-2 bg-ai3goc-sec text-white w-full rounded-xl p-3 hover:opacity-90 h-fit ml-5">
                        Tạo link
                    </button>
                </div>
            </div>
        </div> -->
        <hr class="d-block border-[1px]"/>
        <div class="py-1 md:py-5 rounded-lg">
            <div class="flex gap-10 items-center md:px-6 lg:px-0">
                <div class="flex flex-col md:flex-row md:items-center md:gap-3 text-[14px] md:text-[16px]">
                    <h2 class="font-semibold text-gray-700">Tài liệu training AI</h2>
                    <p class="text-gray-500">(Hỗ trợ TXT, PDF, XLSX, XLS, CSV. Tối đa 15MB mỗi tệp)</p>
                </div>
                <button type="button" @click="handleClickUploadDocument"
                    class="ml-auto px-7 md:px-10 flex flex-nowrap items-center font-bold py-1.5 justify-center gap-2 bg-transparent text-[#207A91] rounded-xl backdrop-blur-sm transition-colors hover:scale-105 border-2 border-[#207A91]">
                    <img src="/assets/img/orion/icon/upload-moss-green.svg" class="size-6 md:size-5 xl:size-6" />
                    <span class="text-[12px] xl:text-[14px] text-nowrap">Tải file</span>
                </button>
            </div>
            <input type="file" ref="fileInput" accept=".pdf,.txt,.csv,.xls,.xlsx" @change="handleChangeUploadDocument" class="hidden" multiple/>
            <div class="text-black w-full border rounded-xl overflow-hidden mb-2 mt-3 md:mb-5 md:mt-5">
                <table class="w-full drop-shadow-xl">
                    <thead class="table-fixed">
                        <tr class="bg-ai3goc-sec text-white text-xs lg:text-base">
                            <th class="py-3">STT</th>
                            <th>File</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr v-for="(item, index) in documents" :key="item.id" :class="item.appointment && 'bg-primary-lurcinn/20'" class="border-b-2 text-xs lg:text-base">
                            <td class="text-center py-3">{{ (currentPage - 1) * 10 + index + 1 }}</td>
                            <td class="text-center">
                                <a class="text-[#207A91]" :href="route('chat-training-documents.download-document', {id:item.id})" target="_blank">{{ item.name }}</a>
                            </td>
                            <td>
                                <div class="flex items-center justify-center h-full">
                                    <svg @click="deleteDocument(item.id)" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash cursor-pointer text-red-500" viewBox="0 0 16 16">
                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                    </svg>
                                </div>

                            </td>
                        </tr>
                        <tr v-if="documents.length === 0">
                            <td class="text-center p-3" colspan="100%">
                                Không có dữ liệu
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-sm text-gray-500">
                Tổng số: {{ totalRecords }}
            </div>
            <Pagination :totalPage="totalPage" :initialPage="currentPage" @updatePage="fetchData" />
        </div>
    </div>
    <div v-if="isLoading" class="fixed inset-0 flex items-center justify-center z-[104] bg-black bg-opacity-30">
        <div class="loader"></div>
    </div>
    <Popup
        v-if="showDelete"
        title="Cảnh báo !"
        message="Bạn có chắc chắn muốn xóa tài liệu này không?"
        cancelButtonText="Huỷ"
        acceptButtonText="Đồng ý"
        @cancel="closePopup"
        @accept="submitDeleteDocument"
    />
</template>


<script setup>
    import {ref, onMounted } from 'vue';
    import axios from 'axios';
    import { toast } from "vue3-toastify";
    import 'vue3-toastify/dist/index.css';
    import Pagination from '@/Components/Pagination.vue';
    import Popup from '@/Components/Popup/Index.vue'

    const props = defineProps({
        toneConfigs: Array,
        fanpage_dify_apps:Array,
        documents: Array,
        totalPage: Number,
        currentPage: Number,
        user: Object
    });

    const documents = ref(props.documents?.data || []);
    const totalPage = ref(props.documents?.last_page || 1);
    const currentPage = ref(props.documents?.current_page || 1);
    const totalRecords = ref(props.documents?.total || 0);
    const isLoading = ref(false);
    const fileInput = ref(null);
    const toneConfig = ref(props.user.tone_config || 1);
    const fanpage_dify_app = ref(1);
    const chatBoxIframeUrl = ref("");
    const submitForm = async () => {
        isLoading.value = true;
        const form = {
            tone_config: toneConfig.value
        }
        try {
            const response = await axios.post(route('chat-training-documents.save-chat-config'), form);
            toast.success(response.data.message || 'Cập nhật thành công!');
        } catch (error) {
            toast.error(error.response.data.message || 'Cập nhật thất bại!');
        } finally {
            isLoading.value = false;
        }
    };

    const handleClickUploadDocument = async () => {
        fileInput.value?.click();
    }

    const handleChangeUploadDocument = async (e) => {
        isLoading.value = true;
        try {
            const formData = new FormData();
            const file = e.target.files;
            for (let i = 0; i < file.length; i++) {
                formData.append('documents[]', file[i]);
            }
            formData.append('fanpage_dify_app', fanpage_dify_app.value);

            const response = await axios.post(route('chat-training-documents.upload-document'), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            toast.success(response.data.message || 'Cập nhật thành công!');
            fetchData();
            isLoading.value = false;
        } catch (error) {
            if (error.response && error.response.status === 422) {
                const errors = error.response.data.errors;
                for (let key in errors) {
                    if (errors.hasOwnProperty(key)) {
                        toast.error(errors[key][0]);
                    }
                }
            } else {
                toast.error('Cập nhật thất bại!');
            }
            console.error('Cập nhật thất bại:', error.response.data);
            isLoading.value = false;
        } finally {
            // Reset file input value
            if (fileInput.value) {
                fileInput.value.value = '';
            }
        }
    }

    const generateChatBoxIframeUrl = async () =>
    {
        isLoading.value = true;
        try {
            const response = await axios.post(route('chat-training-documents.generate-chat-box-iframe'));
            chatBoxIframeUrl.value = response.data.url;
            toast.success(response.data.message || 'Tạo link thành công!');
            isLoading.value = false;
        } catch (error) {
            toast.error(error.response.data.message || 'Tạo link thất bại!');
            isLoading.value = false;
        } finally {
        }
    }

    const showDelete = ref(false)
    const documentRemoveId = ref(0)
    const deleteDocument = (id) => {
        showDelete.value = true
        documentRemoveId.value = id
    }
    const closePopup = () => {
        showDelete.value = false
    }

    const submitDeleteDocument = async () => {
        isLoading.value = true;
        showDelete.value = false;
        try {
            const response = await axios.post(route('chat-training-documents.delete-document', { id: documentRemoveId.value }));
            toast.success(response.data.message || 'Xóa thành công!');
            fetchData();
            isLoading.value = false;
        } catch (error) {
            toast.error(error.response.data.message || 'Xóa thất bại!');
            isLoading.value = false;
        } finally {
        }
    }

    const fetchData = async (page=1) => {
        currentPage.value = page;
        isLoading.value = true;
        try {
            const response = await axios.get(route('chat-training-documents.chat-training-documents', { page: currentPage.value, fanpage_dify_app: fanpage_dify_app.value }));
            documents.value = response.data.documents.data;
            totalPage.value = response.data.documents.last_page;
            currentPage.value = response.data.documents.current_page;
            totalRecords.value = response.data.documents.total;
        } catch (error) {
            toast.error(error.response.data.message || 'Lấy dữ liệu thất bại!');
        } finally {
            isLoading.value = false;
        }
    }

    const handleChangeDifyApp = () => {
        fetchData();
    }

    onMounted(() => {
        fetchData();
    });

</script>

<style scoped>
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .spinner {
        border: 6px solid rgba(255, 255, 255, 0.3);
        border-left-color: #ffffff;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        100% {
            transform: rotate(360deg);
        }
    }
</style>
