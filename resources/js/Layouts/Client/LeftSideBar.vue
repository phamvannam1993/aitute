<template>
    <div class="z-[101] transition-all duration-200" :class="activeMenu ? 'w-full absolute md:w-[15%] xl:relative' : 'w-0 h-10'">
        <!-- Toggle Menu Button
        <div v-if="!activeMenu" @click="toggleMenu"
            class="fixed top-[.75rem] md:top-10 left-0 bg-transparent rounded-r-lg cursor-pointer w-10 h-10 flex items-center justify-center border border-[#D4D4D4]">
            <img class="my-2" src="/assets/img/orion/icon/sidebar/menu.svg" alt="Open Menu" />
        </div> -->

        <!-- Sidebar -->
        <div :class="[activeMenu ? 'w-full md:w-full p-4' : 'w-0 ']" class="sticky top-0 transition-all duration-200 bg-white flex flex-col justify-between overflow-y-auto ">
            <!-- Header -->
            <div class="flex flex-col">
                <!-- Navigation -->
                <nav class="space-y-2">
                    <template v-for="(item, index) in menuItems" :key="index">
                        <a @click="gotoPage(item.routeName, item.link); toggleChildren(item)"
                        :class="[!(item.children && item.children.length > 0) && item.isActive ? 'bg-orion-orange-hover text-black' : '', 'text-black']"
                        class="flex items-center cursor-pointer text-lg p-2 pl-4 rounded-2xl hover:bg-orion-orange-hover hover:text-black" :target="item.isExternal ? '_blank' : '_self'">
                            <span class="min-w-[30px] flex items-center justify-center">
                                <img :src="item.icon" :alt="item.title" class="w-auto h-[22px]" />
                            </span>
                            <span class="text-[14px] font-semibold">{{ item.title }}</span>
                            <span v-if="item.children && item.children.length > 0" class="ml-auto">
                                <svg v-if="item.isActive" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-up" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/>
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                                </svg>
                            </span>
                        </a>
                        <template v-if="item.isActive">
                            <a v-for="child in item.children" :class="[child.isActive ? 'bg-orion-orange-hover text-black' : '', 'text-black']"
                            @click="gotoPage(child.routeName, child.link)" :target="child.isExternal ? '_blank' : '_self'"
                            class="flex items-center cursor-pointer space-x-2 text-lg p-2 pl-4 rounded-2xl hover:bg-orion-orange-hover hover:text-black ml-10">
                                <span class="text-[14px] font-semibold">{{ child.title }}</span>
                            </a>
                        </template>
                    </template>
                    <div class="">
                        <button v-if="isLoggedIn" @click="logout" class="flex items-center w-full text-black space-x-2 text-lg p-2 pl-4 rounded-2xl hover:bg-orion-orange-hover hover:text-black">
                            <div class="min-w-[30px] flex items-center justify-center">
                                <img src="/assets/img/orion/icon/sidebar/auth.svg" alt="Logout" class="w-auto h-[22px]" />
                            </div>
                            <span class="text-[14px] font-semibold">Đăng xuất</span>
                        </button>
                        <button v-else @click="login" class="flex items-center text-black w-full space-x-2 text-lg p-2 pl-4 mt-3 rounded-2xl hover:bg-orion-orange-hover hover:text-black">
                            <div class="min-w-[30px] flex items-center justify-center">
                                <img src="/assets/img/orion/icon/sidebar/auth.svg" alt="Logout" class="w-auto h-[22px]" />
                            </div>
                            <span class="text-[14px] font-semibold">Đăng nhập</span>
                        </button>
                    </div>
                </nav>
            </div>

            <!-- Footer -->
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, defineEmits } from "vue";
import { eventBus } from "@/lib/eventBus.js";
import { usePage } from "@inertiajs/vue3";
const page = usePage();
const user = page.props.auth.user;

const props = defineProps({
  activeMenu: Boolean,
});

const emit = defineEmits(["toggle-menu"]);

const routeName = route().current();

const isLoggedIn = ref(false);

const menuItems = ref([
    {
        title: "Trang chủ",
        link: route("home.index"),
        icon: "/assets/img/orion/icon/sidebar/home.svg",
        isActive: routeName === "home.index",
        routeName: "home",
    },
    {
        title: "Quản lý social",
        link: route("calendar"),
        icon: "/assets/img/orion/icon/sidebar/social.svg",
        isActive: routeName === "calendar",
        routeName: "calendar",
    },
    {
        title: "Lịch sử người dùng",
        link: route("credit.history"),
        icon: "/assets/img/orion/icon/sidebar/lichsunguoidung.svg",
        isActive: routeName.startsWith("credit."),
        routeName: "credit",
    },
    {
        title: "Cài đặt Chatbot",
        link: route('customer-care.index'),
        icon: "/assets/img/orion/icon/sidebar/setting-chatbot.svg",
        isActive: routeName === "chat-training-documents.setting-agent" || routeName === "embed-config.facebook.index",
        routeName: "",
        /*children: [
            {
                title: "Cài đặt Chatbot",
                link: route("chat-training-documents.setting-agent"),
                isActive: routeName === "chat-training-documents.setting-agent",
                routeName: "chatbot-setting",
            },
            {
                title: "Quản lý Fanpages",
                link: route("embed-config.facebook.index"),
                isActive: routeName === "embed-config.facebook.index",
                routeName: "chatbot-setting",
            }
        ]*/
    },
    // {
    //     title: "Lịch sử chatbot",
    //     link: route("ai-chat.histories"),
    //     icon: "/assets/img/aiwow/other_ai/lichsuchatbot.svg",
    //     isActive: routeName === "ai-chat.histories",
    // },
    {
        title: "Hợp tác với AI SỐNG TỬ TẾ",
        link: "https://songtute.vn/",
        icon: "/assets/img/orion/icon/sidebar/contact.svg",
        isActive: routeName.startsWith("contact."),
        routeName: "contact",
        isExternal: true,
    },
    {
        title: "Nâng cấp tài khoản",
        link: route("pricing.index"),
        icon: "/assets/img/orion/icon/sidebar/upgrade.svg",
        isActive: routeName === "pricing.index",
        routeName: "pricing",
    },
    {
        title: "Tài khoản",
        link: route("settings.index"),
        icon: "/assets/img/orion/icon/sidebar/account.svg",
        isActive: routeName.startsWith("settings"),
        routeName: "settings",
    },
]);

const toggleMenu = () => {
    emit("toggle-menu");
};

const toggleChildren = (item) => {
    if (item.children && item.children.length > 0) {
        console.log('adasd');

        item.isActive = !item.isActive;
    }
}

const checkAuthStatus = async () => {
    try {
        const response = await axios.get("/auth-check");
        isLoggedIn.value = response.data.authenticated;
    } catch (error) {
        console.error("Failed to check authentication status:", error);
    }
};

defineExpose({
    toggleMenu,
});

onMounted(() => {
    checkAuthStatus();
});

const logout = async () => {
    try {
        await axios.post(
            "/logout",
            {},
            {
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                },
            }
        );
        window.location.href = "/login";
    } catch (error) {
        console.error("Đăng xuất thất bại:", error);
    }
};

const login = () => {
    window.location.href = "/login";
};

const gotoPage = (routeName, path) => {
    if (routeName == "pricing") {
        if (!user) {
            window.location = route("login");
            return true;
        } else if (user.package_code == "aff_trial") {
            eventBus.emit("popup-aff", { message: "Tài khoản của bạn không có quyền nạp kim cương!" });
            return true;
        }
    }

    if (path != '') {
        window.location = path;
    }
};
</script>
