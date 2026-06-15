<template>
    <div class="flex flex-col flex-1 items-center gap-6" ref="dropdownContainer">
        <Project v-if="isShowProject" />
        <ChatComponent v-else-if="isShowGuru" />
        <!-- <AppPackage v-else /> -->
        <Assistants v-else :aiAssistants="aiAssistants" :listAi="listAi" :aiAssistantLinks="aiAssistantLinks"/>
        <ComingSoon v-if="isShowPopup" @close="close" />
    </div>
    <ModelEnterUrl :isShow="showModelEnterUrl" :handleIsShowFunc="handleShowEnterUrl" :message_alert_pictory="props.message_alert_pictory" />
    <LoginFormModal ref="loginFormModalRef" />
    <PopupConfirmFineTune v-if="isShowConfirmModal" :message="'Để tạo phiên bản hình ảnh A.I của bạn thì bạn phải đào tạo ảnh mẫu. Chi phí đào tạo ảnh mẫu cho 1 lần với 1 mẫu là 250.000 VNĐ. Bạn có muốn tiếp tục không?'" @close="isShowConfirmModal = false" />
    <PopupAff v-if="showAffKeyPopup" :message="popupMessage" @close="showAffKeyPopup = false" :canClose="true" :acceptUpgrade="acceptUpgrade"/>
    <CreditBuyModal :showBuyCreditPopup="showBuyCreditPopup"
                    @buyCredit="handleBuyCredit"
                    @cancel="handleBuyCancel"
                    :currentCredit = "pageProps?.auth?.user?.credit || 0"
                    :additionalCredit = "additionalCredit"
    />
    <PackageModel
        :message="'Tài khoản của bạn không được phép sử dụng chức năng này. Vui lòng nâng cấp tài khoản để sử dụng!'"
        v-if="showModelPackage"
        @close="showModelPackage = false"
        @openBuy="openCreditPackageModal()"
    />
    <CreditPackageModal ref="creditPackageModalRef" />
</template>

<script setup>
import ComingSoon from '@/Components/ComingSoon.vue';
import debounce from 'lodash/debounce';
import { defineEmits, onMounted, onUnmounted, ref, watch, computed , nextTick} from 'vue';
import AIText from '../AIText/AIText.vue';
import BodyHomePage from './Components/BodyHomePage.vue';
import ModelEnterUrl from '@/Components/ModelEnterUrl.vue'
import PopupConfirmFineTune from '@/Components/PopupConfirmFineTune.vue';
import {Link, usePage } from "@inertiajs/vue3";
import {toast} from "vue3-toastify";
import CreditBuyModal from '../../../Components/ModalBuyCredit/BuyCredit.vue';
import PopupAff from '@/Components/PopupAff.vue';
import PackageModel from '@/Components/PackageModel.vue';
import CreditPackageModal from "../../Auth/Components/CreditPackageModal.vue";
import Project from '../MyAssistants/Project.vue';
import AppPackage from './Components/AppPackage/Index.vue';
import Assistants from '@/Components/Assistants/Index.vue';

import Collection from './Components/Collection.vue'

import LoginFormModal from './Components/LoginFormModal.vue';
import ChatComponent from '../AIChat/ChatComponent.vue';

const props = defineProps({
    aiAssistants: Object,
    listAi: Array,
    aiAssistantLinks: Array,
    aff_key_missing: Boolean,
    message_alert_pictory: String,
    my_ai_assistant: Object,
    my_ai_image_models: Array,
    showProject: Boolean,
    canShowProject: Boolean,
});

const { props: pageProps } = usePage();
const auth = computed(() => pageProps.value.auth);
const emit = defineEmits(['fetchData', 'update-credits', 'update-isHome']);

const searchOccupationsQuery = ref('');

const searchAssistantsQuery = ref('');
const selectedOccupation = ref('');
const displayedAssistants = ref([]);
const displayedOccupations = ref([]);
const loadingSearch = ref(false);
const loadingDetail = ref(false);
const loginFormModalRef = ref(null);
const dropdownContainer = ref(null);

const isShowPopup = ref(false);

const showModelEnterUrl = ref(props.message_alert_pictory || false);
const showDropdownVideo = ref(false);

const isShowProject = ref(false);
const isShowGuru = ref(false);

const checkProjectView = () => {
    const urlParams = new URLSearchParams(window.location.search);
    isShowProject.value = urlParams.get('view') === 'project' && props.canShowProject;
    isShowGuru.value = urlParams.get("view") === "guru";
};

onMounted(() => {
    checkProjectView(); // Kiểm tra khi component được mounted
    window.addEventListener('toggleProject', checkProjectView); // Lắng nghe sự kiện
    window.addEventListener('toggleGuru', checkProjectView); // Lắng nghe sự kiện
});

onUnmounted(() => {
    window.removeEventListener('toggleProject', checkProjectView); // Xóa event listener khi component bị hủy
    window.removeEventListener('toggleGuru', checkProjectView); // Xóa event listener khi component bị hủy
});


const handleShowEnterUrl = () => {
    showModelEnterUrl.value = !showModelEnterUrl.value;
}

const handleOpenModelEnterUrl = () => {
    showModelEnterUrl.value = true;
}

const showPopup = () => {
    isShowPopup.value = true;
}

const close = () => {
    isShowPopup.value = false;
};
const selectedAssistant = ref(null);
const aiAssistant = ref(null);
const job = ref(null);
const operation = ref(null);
const selectedAssistantComponent = ref(null);
const fanpages = ref(null);
const showDropdown = ref(false);
const showDropdownImage = ref(false);
const pageWhenSelected = ref('');
const handleUpdateCredits = (newCredits) => {
    emit('update-credits', newCredits)
}
const searchAssistants = async () => {
      try {
        loadingSearch.value = true;
        console.log("selectedOccupation", selectedOccupation.value?.id || '');
        const response = await axios.post('/search-assistants', {
            search: searchAssistantsQuery.value,
            occupation_id: selectedOccupation.value?.id || ''
        });
        displayedAssistants.value = response.data;
      } catch (error) {
        console.error('Lỗi khi tìm kiếm trợ lý ảo:', error);
      } finally {
        loadingSearch.value = false;
      }
};

const searchOccupations = async () => {
    try {
        loadingSearch.value = true;
        const response = await axios.post('/search-occupations', { search: searchOccupationsQuery.value });
        displayedOccupations.value = response.data;
    } catch (error) {
        console.error('Lỗi khi tìm kiếm ngành nghề:', error);
    } finally {
        loadingSearch.value = false;
    }
};

let isManualChange = ref('');
const debouncedSearchAssistants = debounce(searchAssistants, 500);
const debouncedSearchOccupations = debounce(searchOccupations, 500);
watch(searchOccupationsQuery, (newVal) => {
    if (isManualChange && selectedOccupation.value && newVal !== selectedOccupation.value.name) {
        selectedOccupation.value = null;
    }
    isManualChange.value = false;
});

const onInputOccupationChange = () => {
    isManualChange.value = true;
    debouncedSearchOccupations();
};

const selectOccupations = (occupation) => {
    isManualChange.value = false;
    searchOccupationsQuery.value = occupation.name;
    selectedOccupation.value = {
        id: occupation.id,
        name: occupation.name
    };
    console.log("selectedOccupation", selectedOccupation.value);
    setTimeout(() => {
        displayedOccupations.value = [];
    }, 100);
};

const handleSelectAssistant = async (assistant) => {
    if(props.aff_key_missing){
        loginFormModalRef.value.openModal();
        return false;
    }
    if(assistant){

        pageWhenSelected.value = 3;
        searchAssistantsQuery.value = '';
        loadingDetail.value = true;
        displayedAssistants.value = [];
        selectedAssistant.value = null;
        try {
        const response = await axios.get(route('ai-text.detail', { assistantId: assistant.id }));
        selectedAssistant.value = response.data.ai_assistant;
        aiAssistant.value = response.data.ai_assistant;
        job.value = response.data.job;
        operation.value = response.data.operation;
        fanpages.value = response.data.fanpages;

        if (aiAssistant.value.type === 'text') {
            selectedAssistantComponent.value = AIText;
            await nextTick(() => {
                const element = document.getElementById("scroll-target");
                if (element) {
                    element.scrollIntoView({ behavior: "smooth" });
                }
            });
        }

        } catch (error) {
        console.error('Lỗi khi chọn trợ lý ảo:', error);
        } finally {
        loadingDetail.value = false;
        }
    }
};

const hideOccupationsSuggest = () => {
    setTimeout(() => {
        displayedOccupations.value = [];
    }, 200);
};

const hideAssistantsSuggest = () => {
    setTimeout(() => {
        displayedAssistants.value = [];
    }, 200);
};
const dropdown = ref(null)
const toggleDropdown = (type) => {
  if (type === 'other') {
    showDropdown.value = !showDropdown.value;
    showDropdownImage.value = false;
    showDropdownVideo.value = false;
  } else if (type === 'image') {
    showDropdownImage.value = !showDropdownImage.value;
    showDropdown.value = false;
    showDropdownVideo.value = false;
  } else if (type === 'video') {
    showDropdownVideo.value = !showDropdownVideo.value;
    showDropdown.value = false;
    showDropdownImage.value = false;
  }
};
const dropdownRefs = {
    image: ref(null),
    video: ref(null),
    other: ref(null),
};
const handleClickOutsideNew = (event) => {
  const dropdownKeys = Object.keys(dropdownRefs);
  dropdownKeys.forEach((key) => {
    if (dropdown.value && !dropdown.value.contains(event.target)) {
        if (key === 'other') showDropdown.value = false;
        if (key === 'image') showDropdownImage.value = false;
        if (key === 'video') showDropdownVideo.value = false;
    }
  })
}

const checkAffKeyMissing = () => {
    if(props.aff_key_missing){
        // Tạo array chứa các URL từ env
        const affUrls = [
            import.meta.env.VITE_PACKAGE_TRIAL,
            import.meta.env.VITE_PACKAGE_STANDARD,
            import.meta.env.VITE_PACKAGE_ADVANCED,
            import.meta.env.VITE_PACKAGE_PROFESSIONAL
        ];

        const links = dropdownContainer.value.querySelectorAll('a');
        links.forEach(link => {
            const isAffLink = affUrls.some(affUrl => link.href.includes(affUrl));
            if (!isAffLink) {
                link.addEventListener('click', event => {
                    event.preventDefault();
                    loginFormModalRef.value.openModal();
                });
            }
        });
    }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutsideNew)
  checkAffKeyMissing()
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutsideNew)
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
        if (!pageProps.auth.user) return ;
        const response = await axios.get(route('ai-image.has-my-ai-image'));
        const hasImage = response.data.exists;

        if (hasImage) {
            window.location.href = route('ai-image.my-ai-image')
        } else {
            const hasEnoughCredit = await checkCredit();
            if (!hasEnoughCredit) {
                return;
            }
            window.location.href = route('ai-image.training-my-ai-image')
        }
    } catch (error) {
        console.error("Lỗi khi gọi API:", error);
    }
};

const showModelPackage = ref(false);
const creditPackageModalRef = ref(null);
const handleModelPackage = () => {
    showModelPackage.value = true;
};

const openCreditPackageModal = () => {
    showModelPackage.value = false
    creditPackageModalRef.value.openModal();
};

onMounted(() => {
  const params = new URLSearchParams(window.location.search);
  if (params.get('showProject') === 'true') {
    showProject.value = true;
  }
});

watch([isShowProject, isShowGuru, isShowPopup], ([newProject, newGuru, newPopup]) => {
    const isHome = !newProject && !newGuru && !newPopup;
    console.log('isHome:', isHome);
    emit("update-isHome", isHome);
}, { immediate: true });

</script>

<style scoped>
.ai-items w-full {
  box-shadow: 0px 3px 3px rgba(0, 0, 0, 0.15);
}

.showMore {
  box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
}

@keyframes rotation {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
.loader {
  animation: rotation 1s linear infinite;
}
</style>
