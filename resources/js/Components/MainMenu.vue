<template>
    <div  class="w-full flex flex-col items-center justify-center mt-4 mb-4 md:mt-0 ">
        <img src="/assets/img/logo-large.png" alt="Logo" class="w-auto h-[80px] md:h-[100px]" />
        <h1 class="text-[16px] md:text-[20px] xl:text-[24px] font-semibold text-[#0747C8] text-center mb-2">
            Đơn giản thôi mà !
        </h1>
    </div>
    <div class="flex items-end justify-between w-full px-[10px] xl:px-[120px] !mb-[30px]">
        <!-- <div class="flex flex-col items-center space-y-2">
            <a :href="route('ai-image.index')">
                <img src="/assets/img/home_action/image-icon.png" alt="AI Ảnh" class="w-[28px] md:w-[40px] xl:w-[60px] h-auto" />
            </a>
            <span class="text-[12px] md:text-[16px] xl:text-[24px] font-semibold text-[#0747C8] text-center">Ảnh</span>
        </div> -->
        <div class="flex flex-col items-center space-y-2 relative">
            <a href="javascript:void(0);" @click.stop="showDropdownImage = !showDropdownImage">
                    <img src="/assets/img/aiwow/image_ai.png" alt="Xem thêm" class="w-[28px] md:w-[40px] xl:w-[60px] h-auto" />
                </a>
                <span class="text-[12px] md:text-[16px] xl:text-[24px] font-semibold text-[#0747C8] text-center flex items-center gap-2">
                     <p class="text-[12px] md:text-[16px] xl:text-[24px]">Ảnh</p>
                    <span v-if="!showDropdownImage">
                        <img src="/assets/img/aiwow/dropdown_icon.png" alt="Xem thêm" />
                    </span>
                    <span v-else>
                        <img src="/assets/img/aiwow/up_icon.png" alt="Xem thêm" />
                    </span>
                </span>

            <div v-if="showDropdownImage" ref="dropdown" class="absolute left-0 xl:right-auto top-[50px] md:top-[70px] xl:top-[100px] bg-white border border-[#0747C8] rounded-lg shadow-lg w-[280px] text-black z-50">
                <ul class="py-2" @click.stop>
                    <li class="flex items-center px-4 py-2 hover:bg-[#f0f0f0]">
                        <img src="/assets/img/aiwow/ai_image/text_to_image.png" alt="Chatbot" class="w-[16px] h-[16px] mr-2" />
                        <a :href="route('ai-image.index')">Tạo ảnh từ văn bản</a>
                    </li>
                    <li class="flex items-center px-4 py-2 hover:bg-[#f0f0f0]">
                        <img src="/assets/img/aiwow/ai_image/background.png" alt="Audio" class="w-[16px] h-[16px] mr-2" />
                        <a :href="route('ai-background.indexV2')">Hình phối cảnh</a>
                    </li>
                    <li class="flex items-center px-4 py-2 hover:bg-[#f0f0f0]">
                        <img src="/assets/img/aiwow/ai_image/change_face.png" alt="Image to image" class="w-[16px] h-[16px] mr-2" />
                        <a :href="route('ai-image.faceSwap')">Đổi khuôn mặt</a>
                    </li>
                    <li class="flex items-center px-4 py-2 hover:bg-[#f0f0f0]">
                        <img src="/assets/img/aiwow/ai_image/image_to_video.png" alt="Image to video" class="w-[16px] h-[16px] mr-2" />
                        <a :href="route('ai-video.img2Video')">Ảnh thành video</a>
                    </li>
                    <li class="flex items-center px-4 py-2 hover:bg-[#f0f0f0]">
                        <img src="/assets/img/aiwow/ai_image/image_to_image.png" alt="Image to video" class="w-[16px] h-[16px] mr-2" />
                        <a :href="route('ai-image.imageToImage')">Tạo ảnh từ ảnh</a>
                    </li>
                    <li class="flex items-center px-4 py-2 hover:bg-[#f0f0f0]">
                        <img src="/assets/img/home_action/image-icon.png" alt="Image to video" class="w-[16px] h-[16px] mr-2" />
                        <a :href="route('ai-image.photoBeauty')">Làm đẹp ảnh</a>
                    </li>
                    <!-- <li class="flex items-center px-4 py-2 hover:bg-[#f0f0f0]">
                        <img src="/assets/img/home_action/image-icon.png" alt="Image to video" class="w-[16px] h-[16px] mr-2" />
                        <a href="javascript:void(0)" @click="showConfirmFineTune">Hình A.I của tôi</a>
                    </li> -->
                </ul>
            </div>
        </div>

        <div class="flex flex-col items-center space-y-2">
            <a href="javascript:void(0)" @click="showConfirmFineTune">
                <img src="/assets/img/aiwow/studio.png" alt="AI Chat" class="w-[28px] md:w-[40px] xl:w-[60px] h-auto" />
            </a>
            <span class="text-[12px] md:text-[16px] xl:text-[24px] font-semibold text-[#0747C8] text-center">Tự sướng</span>
        </div>

        <!-- <div class="flex flex-col items-center space-y-2">
            <a :href="route('creatomate.index')"
            >
                <img src="/assets/img/home_action/video-icon.png" alt="AI Chat" class="w-[28px] md:w-[40px] xl:w-[60px] h-auto" />
            </a>
            <span class="text-[12px] md:text-[16px] xl:text-[24px] font-semibold text-[#0747C8] text-center">Video</span>
        </div> -->
        <div class="flex flex-col items-center space-y-2 relative">
            <a href="javascript:void(0);" @click.stop="showDropdownVideo = !showDropdownVideo">
                <img src="/assets/img/aiwow/video_ai.png" alt="Xem thêm" class="w-[28px] md:w-[40px] xl:w-[60px] h-auto" />
            </a>
            <span class="text-[12px] md:text-[16px] xl:text-[24px] font-semibold text-[#0747C8] text-center flex items-center gap-2">
                <p>Video</p>
                <span v-if="!showDropdownVideo">
                    <img src="/assets/img/aiwow/dropdown_icon.png" alt="Xem thêm" />
                </span>
                <span v-else>
                    <img src="/assets/img/aiwow/up_icon.png" alt="Xem thêm" />
                </span>
            </span>

            <div v-if="showDropdownVideo" ref="dropdown" class="absolute left-0 xl:right-auto top-[50px] md:top-[70px] xl:top-[100px] bg-white border border-[#0747C8] rounded-lg shadow-lg w-[280px] text-black z-50">
                <ul class="py-2" @click.stop>
                    <li class="flex items-center px-4 py-2 hover:bg-[#f0f0f0]">
                        <img src="/assets/img/aiwow/ai_video/video_to_video.png" alt="" class="w-[16px] h-[16px] mr-2" />
                        <a :href="route('text-to-video.index')">Video chuyên sâu</a>
                    </li>
                    <li class="flex items-center px-4 py-2 hover:bg-[#f0f0f0]">
                        <img src="/assets/img/aiwow/ai_video/video_to_video.png" alt="Chatbot" class="w-[16px] h-[16px] mr-2" />
                        <a :href="route('creatomate.index')">Tạo video mẫu sẵn</a>
                    </li>
                    <li class="flex items-center px-4 py-2 hover:bg-[#f0f0f0]">
                            <img src="/assets/img/aiwow/ai_video/link_to_video.png" alt="Audio" class="w-[16px] h-[16px] mr-2" />
                            <a :href="route('ai-video.url-to-video.create')">Tạo video từ link</a>
                        </li>
                    <li class="flex items-center px-4 py-2 hover:bg-[#f0f0f0]">
                        <img src="/assets/img/aiwow/film_ai.png" alt="Audio" class="w-[16px] h-[16px] mr-2" />
                        <a :href="route('ai-video.index')">Phim</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="flex flex-col items-center space-y-2">
            <a :href="route('mc-virtual.index')">
                <img src="/assets/img/aiwow/virtual_mc.png" alt="AI Chat" class="w-[28px] md:w-[40px] xl:w-[60px] h-auto" />
            </a>
            <span class="text-[12px] md:text-[16px] xl:text-[24px] font-semibold text-[#0747C8] text-center">MC ảo</span>
        </div>

         <div class="flex flex-col items-center space-y-2">
            <a :href="route('lipsync.lipsync')" class="flex flex-col items-center space-y-2">
                <img src="/assets/img/aiwow/audio_ai.png" alt="lipsync" class="w-[28px] md:w-[40px] xl:w-[60px] h-auto" />
                <span class="text-[12px] md:text-[16px] xl:text-[24px] font-semibold text-[#0747C8] text-center">Tôi là A.I</span>
            </a>
        </div>

        <div class="flex flex-col items-center space-y-2 relative">
            <a href="javascript:void(0);" @click.stop="toggleDropdown">
                <img src="/assets/img/aiwow/more_ai.png" alt="Xem thêm" class="w-[28px] md:w-[40px] xl:w-[60px] h-auto" />
            </a>
            <span class="text-[12px] md:text-[16px] xl:text-[24px] font-semibold text-[#0747C8] text-center flex items-center gap-2">
                <p>AI Khác</p>
                <span v-if="!showDropdown">
                    <img src="/assets/img/aiwow/dropdown_icon.png" alt="Xem thêm" />
                </span>
                <span v-else>
                    <img src="/assets/img/aiwow/up_icon.png" alt="Xem thêm" />
                </span>
            </span>

            <div v-if="showDropdown" ref="dropdown" class="absolute right-[10px] xl:right-auto top-[50px] md:top-[70px] xl:top-[100px] bg-white border border-[#0747C8] rounded-lg shadow-lg w-[280px] text-black z-50">
                <ul class="py-2" @click.stop>
                    <li class="flex items-center px-4 py-2 hover:bg-[#f0f0f0]">
                        <img src="/assets/img/aiwow/other_ai/chatbot.png" alt="Chatbot" class="w-[16px] h-[16px] mr-2" />
                        <a :href="route('ai-chat.index')">Chatbot</a>
                    </li>
                    <li class="flex items-center px-4 py-2 hover:bg-[#f0f0f0]">
                        <img src="/assets/img/aiwow/other_ai/voice.png" alt="Audio" class="w-[16px] h-[16px] mr-2" />
                        <a :href="route('ai-audio.index')">Giọng nói</a>
                    </li>
                    <li class="flex items-center px-4 py-2 hover:bg-[#f0f0f0]">
                        <img src="/assets/img/aiwow/other_ai/text_to_music.png" alt="Image to video" class="w-[16px] h-[16px] mr-2" />
                        <a :href="route('text-to-music.index')">Âm nhạc từ văn bản</a>
                    </li>
                    <!-- Text To Song -->
                    <li class="flex items-center px-4 py-2 hover:bg-[#f0f0f0]">
                        <img src="/assets/img/aiwow/other_ai/text_to_song.png" alt="Image to video" class="w-[16px] h-[16px] mr-2" />
                        <a :href="route('text-to-song.index')">Bài hát từ văn bản</a>
                    </li>
                    <!-- Lipsync -->
                    <li class="flex items-center px-4 py-2 hover:bg-[#f0f0f0]">
                        <a  href="javascript:void(0)" @click.stop="createTokenAndRedirect" target="_blank" class="flex items-center w-full">
                            <img src="/assets/img/aiwow/other_ai/slide.png" alt="AI Chat" class="w-[16px] h-[16px] mr-2" />
                            Slide
                        </a>
                    </li>
                    <li class="flex items-center px-4 py-2 hover:bg-[#f0f0f0]">
                        <img src="/assets/img/aiwow/other_ai/poster.png" alt="Image to video" class="w-[16px] h-[16px] mr-2" />
                        <a :href="route('ai-image-advertisement.index')">Tạo banner/poster</a>
                    </li>
                    <!-- <li class="flex items-center px-4 py-2 hover:bg-[#f0f0f0]" @click="showPopup">
                        <img src="/assets/img/home_action/action_more/more_1.png" alt="Text to landing page" class="w-[16px] h-[16px] mr-2" />
                        <a>Văn bản thành landing page</a>
                    </li>
                    <li class="flex items-center px-4 py-2 hover:bg-[#f0f0f0]" @click="showPopup">
                        <img src="/assets/img/home_action/action_more/more_2.png" alt="Text to website" class="w-[16px] h-[16px] mr-2" />
                        <a>Văn bản thành trang web</a>
                    </li>
                    <li class="flex items-center px-4 py-2 hover:bg-[#f0f0f0]" @click="showPopup">
                        <img src="/assets/img/home_action/action_more/more_5.png" alt="Text to 3D" class="w-[16px] h-[16px] mr-2" />
                        <a>Văn bản thành 3D</a>
                    </li>
                    <li class="flex items-center px-4 py-2 hover:bg-[#f0f0f0]" @click="showPopup">
                        <img src="/assets/img/home_action/action_more/more_6.png" alt="VR, AR" class="w-[16px] h-[16px] mr-2" />
                        <a>VR, AR</a>
                    </li> -->
                </ul>
            </div>
        </div>
        <ComingSoon v-if="isShowPopup" @close="close" />
        <ModelEnterUrl :isShow="showModelEnterUrl" :handleIsShowFunc="handleShowEnterUrl" />
        <PopupConfirmFineTune v-if="isShowConfirmModal" :message="'Để tạo phiên bản hình ảnh A.I của bạn thì bạn phải đào tạo ảnh mẫu. Chi phí đào tạo ảnh mẫu cho 1 lần với 1 mẫu là 250.000 VNĐ. Bạn có muốn tiếp tục không?'" @close="isShowConfirmModal = false" />
        <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="acceptUpgrade"/>
        <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup"
                        @buyCredit="handleBuyCredit"
                        @cancel="handleBuyCancel"
                        :currentCredit = "pageProps.auth.user.credit || 0"
                        :additionalCredit = "additionalCredit"
        />
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import ComingSoon from './ComingSoon.vue';
import ModelEnterUrl from './ModelEnterUrl.vue'
import PopupConfirmFineTune from '@/Components/PopupConfirmFineTune.vue';

import {usePage } from "@inertiajs/vue3";
import {toast} from "vue3-toastify";
import CreditBuyModal from '../Components/ModalBuyCredit/BuyCredit.vue';
import PopupAff from '@/Components/PopupAff.vue';

const showDropdown = ref(null);
const showDropdownImage = ref(false);
const showDropdownVideo = ref(false);
const isShowPopup = ref(false);
const showModelEnterUrl = ref(false);

const { props: pageProps } = usePage();
const auth = computed(() => pageProps.value.auth);

const handleShowEnterUrl = () => {
    showModelEnterUrl.value = !showModelEnterUrl.value;
}

const dropdown = ref(null)
const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value
}

const showPopup = () => {
    isShowPopup.value = true;
}

const close = () => {
    isShowPopup.value = false;
};

const handleClickOutside = (event) => {
  if (dropdown.value && !dropdown.value.contains(event.target)) {
    showDropdown.value = false;
    showDropdownImage.value = false;
    showDropdownImage.value = false;
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

async function createTokenAndRedirect(event) {
    event.preventDefault();

    const email = 'giaovien1@gmail.com';
    const code = 'aiwow';
    const secretKey = import.meta.env.VITE_SERCRET_KEY;

    try {
        const response = await fetch('https://slide.aiwow.com.vn/api/createToken', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email, code, secretKey }),
        });

        if (!response.ok) {
            const errorData = await response.json();
            console.error('Error creating token:', errorData);
            alert('Lỗi tạo token: ' + errorData.message);
            return;
        }

        const data = await response.json();
        const { userToken, token } = data;

        localStorage.setItem('userToken', userToken);
        localStorage.setItem('token', token);

        const redirectUrl = `https://slide.aiwow.com.vn/login-token?email=${encodeURIComponent(email)}&code=${encodeURIComponent(code)}&secretKey=${encodeURIComponent(secretKey)}&userToken=${encodeURIComponent(userToken)}&token=${encodeURIComponent(token)}`;

        window.location.href = redirectUrl;
    } catch (error) {
        console.error('Unexpected error:', error);
        alert('Có lỗi xảy ra: ' + error.message);
    }
}

const handleOpenModelEnterUrl = () => {
    showModelEnterUrl.value = true;
}

const showAffKeyPopup = ref(false);
const isShowConfirmModal = ref(false)
const showBuyCreditPopup = ref(false)
const additionalCredit = ref(0);
const showBuyCreditModal = () => {
    showBuyCreditPopup.value = true;
};
const handleBuyCancel = () => {
    showBuyCreditPopup.value = false;
};

const handleBuyCredit = () => {
    window.location.href = '/pricing'
}

const popupMessage = ref('Tài khoản của bạn đã hết hạn. Vui lòng nâng cấp tài khoản để sử dụng!')
const acceptUpgrade = ref(true)
const checkCredit = async () => {
    try {
        const formData = new FormData();
        formData.append("model", 'my_ai_image');
        formData.append("type", "my_ai_image");
        const response = await axios.post(route("credit.check_credit"), formData);
        if (response.data.success) {
            return true;
        } else if (response.data.is_vip_expired) {
            showAffKeyPopup.value = true;
            if (response.data.affCode == "aff_trial") {
                popupMessage.value = " Gói của bạn đã hết hạn. Xin vui lòng gia hạn tài khoản để tiếp tục sử dụng tính năng này.";
                acceptUpgrade.value = false;
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

const showConfirmFineTune = async () => {
    try {
        const response = await axios.get(route('ai-image.has-my-ai-image'));
        const hasImage = response.data.exists;

        if (hasImage) {
            window.location.href = route('ai-image.my-ai-image')
        } else {
            const hasEnoughCredit = await checkCredit();
            console.log(hasEnoughCredit)
            if (!hasEnoughCredit) {
                return;
            }
            isShowConfirmModal.value = !isShowConfirmModal.value;
        }
    } catch (error) {
        console.error("Lỗi khi gọi API:", error);
    }
};

</script>
