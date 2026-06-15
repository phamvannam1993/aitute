<template>

    <Head title="Âm thanh thành văn bản - AI 3 GỐC - Cộng đồng AI tử tế" />
    <Layout :breadcrumbs="breadcrumbsData">
        <div class="flex flex-col md:flex-row justify-center items-center gap-4 xl:hidden mb-2">
            <a class="bg-white border-2 border-[#D4D4D4] w-fit md:px-4 md:py-2 flex gap-2 items-center p-2 rounded-lg text-[#9C9C9C] font-semibold text-xs md:text-base text-nowrap "
                :href="route('ai-audio.index')">
                <img src="/assets/img/aiwow/ai_audio/text_to_speech_disable.png" class="w-8 h-4" alt="">
                <span class="text-sm md:text-base font-bold">Văn bản thành giọng nói</span>
            </a>
            <a
                class="bg-ai3goc-primary md:px-4 md:py-2 flex gap-2 items-center p-2 rounded-lg text-white text-nowrap cursor-default w-fit">
                <img src="/assets/img/aiwow/ai_audio/speech_to_text_active.png" class="w-8 h-4" alt="">
                <span class="text-sm md:text-base font-bold">Giọng nói thành văn bản </span>
            </a>
        </div>
        <div
            class=" bg-white rounded-2xl shadow-[rgba(50,50,93,0.25)_0px_6px_12px_-2px,_rgba(0,0,0,0.3)_0px_3px_7px_-3px]  py-6 text-black font-lexend">
            <div class="flex flex-col md:flex-row justify-between md:items-center md:px-5 px-4">
                <div class="flex gap-1">
                    <div class="flex justify-start items-center gap-2">
                        <div class="w-[41px] h-full overflow-hidden rounded-[10px] border-[1px] border-[#d4d4d4]">
                            <img class="w-full h-auto object-top" src="/assets/img/ai3goc/logo/logo_img.svg" alt="Robot" />
                        </div>

                        <h2 class="text-black font-bold text-xl lg:text-xl 2xl:text-3xl leading-none">
                            Giọng nói A.I
                        </h2>
                    </div>
                </div>
                <div class="xl:flex gap-4 hidden">
                    <a :href="route('ai-audio.index')"
                        class="bg-[#FFFFFF] border-[#D4D4D4] border-[1px] md:px-4 md:py-2 flex gap-2 items-center p-2 rounded-xl text-[#9C9C9C] text-nowrap">
                        <img src="/assets/img/aiwow/ai_audio/text_to_speech_disable.png" alt="">
                        <span class="text-14 font-bold">Văn bản thành giọng nói</span>
                    </a>
                    <a
                        class="bg-ai3goc-primary md:px-4 md:py-2 flex gap-2 items-center p-2 rounded-xl text-white font-semibold text-xs md:text-base text-nowrap cursor-default">
                        <img src="/assets/img/aiwow/ai_audio/speech_to_text_active.png" alt="">
                        <span class="text-14 font-bold">Giọng nói thành văn bản</span>
                    </a>
                </div>

            </div>
            <p class="text-sm md:text-base text-ai3goc-sec font-bold md:px-5 px-4 my-4">Thực hiện theo các bước sau:</p>
            <div class="md:px-5 px-4">
                <form @submit.prevent="submit">
                    <div class="">
                        <label for="file-upload" class="text-xs lg:text-sm flex gap-1 items-center mb-5 font-semibold">
                            <Step :step="1" stepName="Tải file âm thanh"/>
                        </label>
                        <div class="flex justify-center">
                            <label for="file-upload"
                                class="flex flex-col items-center justify-center w-[11.5rem] h-[11.5rem] bg-[#FFFBE2] p-4 rounded-[41px] cursor-pointer hover:bg-[#f3eeca]">
                                <span class="text-[3rem] font-bold text-orion-primary   ">
                                    <img src="/assets/img/ai_audio/orion/upload.png" alt="">
                                </span>
                                <span v-if="!file" class="text-black mt-1 text-center text-[15px] font-medium">Tải lên
                                    Audio</span>
                                <span v-else class="text-black mt-1">Đổi Audio</span>
                            </label>
                            <input id="file-upload" type="file" accept=".aac,.mp3,.m4a,.wav" class="hidden"
                                @change="handleFileUpload" />
                        </div>

                    </div>
                    <div class="text-center mt-2 ">
                        <h3 class="w-48 sm:w-auto line-clamp-1 mx-auto italic text-ai3goc-bgclr text-[15px] font-medium"
                            v-if="file">{{ file.name }}</h3>
                    </div>
                    <div class="flex sm:items-center justify-between flex-col sm:flex-row mt-8">
                        <!-- Step nằm bên trái cùng -->
                        <div class="flex fc items-center gap-2">
                            <Step :step="2" stepName=""/>
                        </div>

                        <!-- Button nằm giữa màn hình -->
                        <div class="sm:absolute sm:left-1/2 sm:transform sm:-translate-x-1/2 self-center mt-2">
                            <button v-if="!isLoading" type="submit" :disabled="isLoading"
                                class="md:px-12 px-2 flex gap-2 items-center text-white py-2 rounded-[10px] bg-ai3goc-sec">
                                <img src="/assets/img/aiwow/ai_audio/speech_to_text_active.png"
                                    class="w-[32px] h-[12px]" alt="">
                                <span class="md:text-[15px] text-[10px] font-bold">Chuyển đổi giọng nói</span>
                            </button>

                            <button v-else :disabled="isLoading"
                                class="px-12 flex items-center text-white py-2 rounded-[10px] bg-ai3goc-primary">
                                <svg aria-hidden="true"
                                    class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                                    viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                        fill="currentColor" />
                                    <path
                                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                        fill="currentFill" />
                                </svg>
                            </button>
                        </div>
                    </div>

                </form>
            </div>
            <div class="border-t-2 border-[#D4D4D4] sm:mt-6 mt-4">
                <div v-if="result.data.length > 0" class="mt-4 md:px-5 px-4">
                    <h2 class="flex items-center gap-2">
                        <span class="text-sm font-semibold">Kết quả</span>
                    </h2>
                    <div @scroll="onScroll" class="max-h-[30vh] overflow-y-auto">
                        <div v-for="(item, index) in result.data" :key="index">
                            <div class="bg-[#EDEDED] rounded-xl p-6 mt-3 md:mx-5 mx-4">
                                <p class="whitespace-pre-wrap text-sm font-normal">{{ item.description }}</p>
                                <div class="flex flex-col sm:flex-row justify-between mt-4">
                                    <div class="flex gap-5 items-center">
                                        <img src="/assets/img/ai_text/like.png" class="w-4 cursor-pointer" alt="like">
                                        <img src="/assets/img/ai_text/dislike.png" class="w-4 cursor-pointer"
                                            alt="dislike">
                                        <img @click="prepareDeleteMessage(item.id)" src="/assets/img/ai_text/orion/delete.png"
                                            class="w-4 cursor-pointer" alt="delete">
                                    </div>
                                    <div class="flex gap-4 mt-4">
                                        <div @click="createPost(item.description)"
                                            class="flex items-center gap-1 cursor-pointer group">
                                            <img src="/assets/img/ai_text/orion/create.png" class="w-4 " alt="like">
                                            <span
                                                class="group-hover:text-orion-primary hidden 2xl:block text-xs font-medium">Tạo
                                                bài
                                                đăng</span>
                                        </div>
                                        <div class="flex items-center gap-1 cursor-pointer group">
                                            <img src="/assets/img/ai_text/orion/chat.png" class="w-4 " alt="like">
                                            <span
                                                class="group-hover:text-orion-primary hidden 2xl:block text-xs font-medium">Chat
                                                với
                                                Orion AI</span>
                                        </div>
                                        <div class="flex items-center gap-1 cursor-pointer group">
                                            <img src="/assets/img/ai_text/orion/share.png" class="w-4 " alt="like">
                                            <span
                                                class="group-hover:text-orion-primary hidden 2xl:block text-xs font-medium">Chia
                                                sẻ</span>
                                        </div>
                                        <div class="flex items-center gap-1 cursor-pointer group">
                                            <img src="/assets/img/ai_text/orion/heart.png" class="w-4 " alt="like">
                                            <span
                                                class="group-hover:text-orion-primary hidden 2xl:block text-xs font-medium">Yêu
                                                thích</span>
                                        </div>
                                        <div class="flex items-center gap-1 cursor-pointer group"
                                            @click="copyToClipboard(item.description)">
                                            <img src="/assets/img/ai_text/orion/copy.png" class="w-4 " alt="like">
                                            <span
                                                class="group-hover:text-orion-primary hidden 2xl:block text-xs font-medium">Sao
                                                chép</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="loading" class="text-center p-2 text-gray-500">Đang tải thêm...</div>
                        <div v-if="lastPage && !loading" class="text-center p-2 text-gray-500">Không có nội dung
                            nào.</div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
    <Popup
        v-if="showConfirmModal"
        title="Cảnh báo !"
        message="Bạn có chắc chắn muốn xóa văn bản này không?"
        cancelButtonText="Huỷ"
        acceptButtonText="Xoá"
        @cancel="cancelDelete"
        @accept="confirmDelete"
    />
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true"
        :acceptUpgrade="acceptUpgrade" />
</template>

<script setup>
import { ref, onBeforeMount } from "vue";
import Layout from "@/Layouts/Client/Layout.vue";
import axios from "axios";
import { Head } from "@inertiajs/vue3";
import { toast } from 'vue3-toastify';
import PopupAff from '@/Components/PopupAff.vue';
import Popup from '@/Components/Popup/Index.vue'
import Step from "@/Components/Step/Index.vue";


const credits = ref(0);
const breadcrumbsData = [
    { text: "Giọng nói", link: "ai-audio.index" },
];

const voiceTypes = ref([
    { name: "Giọng nam 1", type: "vi-VN-Neural2-D" },
    { name: "Giọng nữ 1", type: "vi-VN-Neural2-A" },
    { name: "Giọng nam 2", type: "vi-VN-Wavenet-D" },
    { name: "Giọng nữ 2", type: "vi-VN-Wavenet-A" },
])
const selectedVoice = ref(voiceTypes.value[0].type)

let messageIdToDelete = ref(null);
const file = ref(null);
const isLoading = ref(false);
const isDeleteLoading = ref(false);
const result = ref({ data: [] });
const assistant_id = ref(null);

// Load on scroll
const loading = ref(false);
const page = ref(1);
const lastPage = ref(false);
const showAffKeyPopup = ref(false);
const popupMessage = ref('Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!')
const acceptUpgrade = ref(true);
const handleFileUpload = (event) => {
    file.value = event.target.files[0];
    console.log(file.value)
};
const checkCredit = async () => {
    try {
        const formData = new FormData();
        formData.append('model', "creatomate");
        formData.append('type', 'creatomate');
        formData.append('number_result', 30);
        // Gọi API để kiểm tra credit của user
        const response = await axios.post(route("credit.check_credit"), formData);
        if (response.data.success) {
            // Đủ credit để tiếp tục
            return true;
        } else if (response.data.is_vip_expired) {
            showAffKeyPopup.value = true
            if (response.data.affCode == 'aff_trial') {
                popupMessage.value = " Gói của bạn đã hết hạn. Xin vui lòng gia hạn tài khoản để tiếp tục sử dụng tính năng này."
                acceptUpgrade.value = false
            }
            return false;
        } else {
            additionalCredit.value = response?.data?.data?.total_price - pageProps.auth.user.credit;
            // Không đủ credit, hiển thị modal yêu cầu nạp thêm
            showBuyCreditModal();
            return false;
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi khi kiểm tra credit:", error);
        toast.error("Không thể kiểm tra credit. Vui lòng thử lại.");
        if (error.response && error.response.status === 422) {
            const errors = error.response.data.errors;
            Object.values(errors).forEach(errorMessages => {
                if (Array.isArray(errorMessages)) {
                    errorMessages.forEach(error => {
                        toast.error(error);
                    });
                } else {
                    toast.error(errorMessages);
                }
            });
        } else {
            if (error.response) {
                toast.error((error.response.data.message || 'Đã xảy ra lỗi.'));
            } else if (error.request) {
                toast.error('Không thể kết nối đến server.');
            } else {
                toast.error('Lỗi: ' + error.message);
            }
        }
        return false;
    }
};

const submit = async () => {
    if (!file.value) {
        toast.error('Bạn cần thêm file âm thanh để chuyển đổi')
        return;
    }

    isLoading.value = true;
    const formData = new FormData();
    formData.append('file', file.value);

    try {
        const response = await axios.post(route('ai-audio.upload'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });

        if (response.data.success) {
            result.value.data.unshift({
                id: response.data.data.id,
                description: response.data.data.description,
            });
            file.value = null;
            toast.success('File đã được chuyển đổi thành công.');
            console.log(result.value);
            credits.value = response.data.data.credits;
        } else {
            console.error("Phản hồi không thành công:", response.data);
        }
    } catch (error) {
        console.error("Đã xảy ra lỗi khi gửi file:", error.message);
    } finally {
        isLoading.value = false;
    }
}


const onScroll = (event) => {
    const container = event.target;
    if (container.scrollTop + container.clientHeight >= container.scrollHeight) {
        loadMore();
    }
};

const loadMore = async () => {
    if (loading.value || lastPage.value) return;
    loading.value = true;
    try {
        const response = await axios.get(
            route("ai-audio.load", { page: result.value.current_page + 1, type: 'text' }),
        );
        if (response.data.data && response.data.data.data.length) {
            result.value.data = [...result.value.data, ...response.data.data.data];
            result.value.current_page += 1;
            page.value++;
            if (page.value > response.data.data.last_page) {
                lastPage.value = true;
            }
        } else {
            lastPage.value = true;
        }
        console.log(response.data.data)
    } catch (error) {
        console.log(error.message)
    } finally {
        loading.value = false;
    }
}

const prepareDeleteMessage = (id) => {
    messageIdToDelete.value = id;
    showConfirmModal.value = true;
    console.log(messageIdToDelete.value)
};

const showConfirmModal = ref(false);
const confirmDelete = async () => {
    let response;
    isDeleteLoading.value = true

    response = await axios.post(
        route("ai-audio.delete", { id: messageIdToDelete.value }), { id: messageIdToDelete.value }
    );
    if (response.status === 200) {
        const index = result.value.data.findIndex(item => item.id === messageIdToDelete.value);
        if (index !== -1) {
            result.value.data.splice(index, 1);
        }
        toast.success("Xóa thành công");
    }
    isDeleteLoading.value = false
    showConfirmModal.value = false;
};

const cancelDelete = () => {
    showConfirmModal.value = false;
};

const createPost = (message) => {
    if (message) {
        localStorage.setItem('postContent', message);
        window.location.href = '/page-management';
    } else {
        alert("Vui lòng tạo một hình ảnh trước khi tạo bài đăng.");
    }
}

const copyToClipboard = (item) => {
    navigator.clipboard.writeText(item).then(() => {
        toast.success('Đã sao chép vào clipboard!');
    }).catch(err => {
        toast.error('Sao chép thất bại');
        console.error('Sao chép thất bại:', err);
    });
};

onBeforeMount(async () => {
    const urlPath = window.location.pathname;
    const segments = urlPath.split('/');
    assistant_id.value = segments[segments.length - 1];
    try {
        const url = route("ai-audio.load", { page: 1, type: 'text' });
        const response = await axios.get(url);
        result.value = response.data.data;
        console.log(result.value.data);
    } catch (error) {
        console.error("Đã xảy ra lỗi khi lấy lịch sử tin nhắn:", error.message);
    }
});

</script>

<style scoped></style>
