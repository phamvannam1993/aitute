<template>
    <div class="relative w-full">
        <Header :breadcrumbs="breadcrumbs" :credits="credits"/>
        <div class="w-full pt-[2px] lg:pt-[4px]">
            <Menu />
        </div>
<!--        <section class="w-full flex flex-col items-center justify-center mt-4 mb-4 md:mt-0">-->
<!--            <img src="/assets/img/ai3goc/banner/home-new.svg" alt="Logo" class="w-full" />-->
<!--        </section>-->
        <slot></slot>
    </div>
    <ComingSoon v-if="isShowPopup" @close="close" />
    <ModelEnterUrl :isShow="showModelEnterUrl" :handleIsShowFunc="handleShowEnterUrl" />
    <PopupConfirmFineTune v-if="isShowConfirmModal" :message="'Để tạo phiên bản hình ảnh A.I của bạn thì bạn phải đào tạo ảnh mẫu. Chi phí đào tạo ảnh mẫu cho 1 lần với 1 mẫu là 250.000 VNĐ. Bạn có muốn tiếp tục không?'" @close="isShowConfirmModal = false" />
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import ComingSoon from "./ComingSoon.vue";
import ModelEnterUrl from "./ModelEnterUrl.vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import Credit from "@/Components/Credit.vue";
import Header from "@/Components/Header/Header.vue";
import Menu from "@/Components/Menu/Index.vue";

const props = defineProps({
    credits: Number,
    breadcrumbs: {
        type: [String, Array],
    },
});

const isShowConfirmModal = ref(false)

console.log(props.breadcrumbs);

// // Check if a route exists by name
// const checkRoute = (routeName) => {
//   return router.hasRoute(routeName);
// };

// const routeExists = checkRoute('home'); // Replace 'home' with your route name
// console.log(routeExists ? 'Route exists' : 'Route does not exist');

const showDropdown = ref(null);
const showDropdownImage = ref(false);
const showDropdownVideo = ref(false);
const isShowPopup = ref(false);
const showModelEnterUrl = ref(false);

const handleShowEnterUrl = () => {
    showModelEnterUrl.value = !showModelEnterUrl.value;
};

const dropdown = ref(null);
const showPopup = () => {
    isShowPopup.value = true;
};

const close = () => {
    isShowPopup.value = false;
};

const handleClickOutside = (event) => {
    if (dropdown.value && !dropdown.value.contains(event.target)) {
        showDropdown.value = false;
        showDropdownImage.value = false;
        showDropdownImage.value = false;
    }
};

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
onMounted(() => {
    document.addEventListener("click", handleClickOutsideNew);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutsideNew);
});

async function createTokenAndRedirect(event) {
    event.preventDefault();

    const email = "giaovien1@gmail.com";
    const code = "aiwow";
    const secretKey = import.meta.env.VITE_SERCRET_KEY;

    try {
        const response = await fetch("https://slide.aiwow.com.vn/api/createToken", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ email, code, secretKey }),
        });

        if (!response.ok) {
            const errorData = await response.json();
            console.error("Error creating token:", errorData);
            alert("Lỗi tạo token: " + errorData.message);
            return;
        }

        const data = await response.json();
        const { userToken, token } = data;

        localStorage.setItem("userToken", userToken);
        localStorage.setItem("token", token);

        const redirectUrl = `https://slide.aiwow.com.vn/login-token?email=${encodeURIComponent(email)}&code=${encodeURIComponent(code)}&secretKey=${encodeURIComponent(secretKey)}&userToken=${encodeURIComponent(userToken)}&token=${encodeURIComponent(token)}`;

        window.location.href = redirectUrl;
    } catch (error) {
        console.error("Unexpected error:", error);
        alert("Có lỗi xảy ra: " + error.message);
    }
}

const handleOpenModelEnterUrl = () => {
    showModelEnterUrl.value = true;
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
