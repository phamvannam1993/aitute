<template>
    <header class="flex items-center bg-ai3goc-bg-sec h-[100px] w-full justify-center">
        <div class="w-full lg:w-3/4 flex items-center justify-between gap-2">
            <div class="flex items-center justify-between md:justify-start w-full gap-4 px-2" >
                <div class="flex items-center gap-5">  
                    <button @click="$emit('toggle-sidebar')">
                        <img src="/assets/img/ai3goc/icon/menu.svg" alt="Menu" class="w-auto h-[18px] md:h-[32px]"/>
                    </button>
                    <a href="/" class="size-7 block md:hidden">
                        <svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3.375 10.125L11.8117 3.56315C12.8047 2.79078 14.1953 2.79078 15.1883 3.56315L23.625 10.125V22.5C23.625 23.0967 23.3879 23.669 22.966 24.091C22.544 24.5129 21.9717 24.75 21.375 24.75H5.625C5.02826 24.75 4.45597 24.5129 4.03401 24.091C3.61205 23.669 3.375 23.0967 3.375 22.5V10.125Z" stroke="#F49A23" stroke-width="1.83333" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M10.125 24.75V13.5H16.875V24.75" stroke="#F49A23" stroke-width="1.83333" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
                <div v-if="!isLoggedIn" class="flex items-center"
                :class="isSidebarOpen ? 'hidden' : ''"
                >
                    <a href="/login" class="bg-white text-black border-[3px] border-[#eaeaea] hover:border-orion-orange flex items-center gap-2 font-medium px-4 py-2 rounded-full text-[14px] hover:bg-orion-orange/60">
                        <img src="/assets/img/orion/icon/login.svg" alt="Logout" class="w-auto h-[22px]" />
                        <span class="text-[14px] font-semibold">Đăng nhập</span>
                    </a>
                </div>
            </div>
            <div  class="hidden md:flex items-center justify-end gap-4 md:flex"
            :class="{ 'hidden md:flex': isSidebarOpen, 'w-full': isLoggedIn }">
                <div  :class="isLoggedIn ? 'block' : 'hidden lg:block'" class="p-2 flex flex-col gap-1 md:gap-2 bg-black rounded-[16px]">
                    <div class="flex items-center gap-2">
                        <img src="/assets/img/orion/icon/header/user.svg" alt="Us" class="w-auto h-[16px] md:h-[24px]"/>
                        <span class="text-[12px] md:text-[16px] text-white font-medium">
                            {{ user?.email || 'User' }}
                        </span>
                    </div>
                    <div class="flex items-center bg-white rounded-full gap-2 min-w-52">
                        <img src="/assets/img/orion/icon/header/expired.svg" alt="Ex" class="w-auto h-[16px] md:h-[24px]"/>
                        <span class="text-[12px] md:text-[16px] text-[#656565] font-medium pr-4 md:pr-6 flex gap-1">
                            Còn {{ daysLeft !== null ? `${daysLeft}` : '0' }} ngày  
                            <span class="hidden lg:block text-[12px] md:text-[16px] text-[#656565] font-medium">
                                sử dụng
                            </span>
                        </span>
                    </div>
                </div>
                <div class="pb-2 pt-1 px-2 flex items-center gap-2 bg-black rounded-[16px] min-w-44">
                    <div class="rounded-[12px]">
                        <a :href="route('pricing.index')">
                            <img src="/assets/img/orion/icon/header/credits.svg" alt="Credit" class="min-w-[28px] size-[28px] lg:size-[44px]">
                        </a>
                    </div>
                    <div class="flex flex-col">
                        <p class="text-white font-bold text-[16px] lg:text-[24px] text-center w-full line-clamp-1">
                            {{ formattedUserCredit || '0' }}
                        </p>
                        <p class="text-orion-primary bg-white rounded-full pb-1 pt-0.5 px-4 text-center font-medium text-[10px] md:text-[12px] lg:text-[14px] min-w-40">
                            {{ formattedPackageName || 'Chưa đăng nhập' }}
                        </p>
                    </div>
                </div>   
            </div>
            <!-- <div class="flex items-center justify-between">
                <div class="flex items-center gap-1">
                    <a href="/">
                        <img
                            src="/assets/img/orion/icon/header/home.svg"
                            alt="Trang chủ"
                            class="w-auto h-[19px] md:h-[24px] min-h-[19px] min-w-[18px]"
                        />
                    </a>
                    <template v-for="(crumb, index) in processedBreadcrumbs" :key="index">
                        <span v-if="!crumb.link" class="font-medium text-black text-[16px] md:text-[20px] line-clamp-1">
                            / {{ crumb.text }}
                        </span>
                        <a v-else :href="route(crumb.link)" class="font-medium text-black text-[16px] md:text-[20px] line-clamp-1">
                            / {{ crumb.text }}
                        </a>
                    </template>
                </div>
            </div> -->
        </div>
    </header>
</template>

<script setup>
import { computed, watch, ref, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.auth.user)
const props = defineProps({
    credits: Number,
    breadcrumbs: {
        type: [String, Array],
        required: true,
    },
    isSidebarOpen: {
        type: Boolean,
        default: false,
    },
});

const isLoggedIn = ref(false);
const showActionSetting = ref(false)
const userCredit = ref(user.value?.credit || props.credits || 0)
const packageName = ref(user.value?.package_name)
const expiredAt = ref(user.value?.vip_expired_at)

const daysLeft = computed(() => {
    if (!expiredAt.value) return null 
    const currentDate = new Date()
    const expirationDate = new Date(expiredAt.value)
    const diffTime = expirationDate - currentDate 
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) 
    return diffDays > 0 ? diffDays : 0 
})

const formatNumber = (number) => {
    return new Intl.NumberFormat('vi-VN').format(number)
}

watch(() => props.credits, (newCredits) => {
    userCredit.value = newCredits
})

watch(() => userCredit.value, (newValue) => {
    console.log('User credit updated:', newValue)
})

const formattedUserCredit = computed(() => formatNumber(userCredit.value))

const processedBreadcrumbs = computed(() => {
    if (typeof props.breadcrumbs === 'string') {
        return [{ text: props.breadcrumbs }]
    }
    return props.breadcrumbs
})

const formattedPackageName = computed(() => {
    if (!packageName.value) return '';
    return packageName.value.replace(/aiwow/gi, '').trim(); 
});

const toggleActionSetting = () => {
    showActionSetting.value = !showActionSetting.value
}

const checkAuthStatus = async () => {
    try {
        const response = await axios.get("/auth-check");
        isLoggedIn.value = response.data.authenticated;
    } catch (error) {
        console.error("Failed to check authentication status:", error);
    }
};

onMounted(() => {
    checkAuthStatus();
});
</script>