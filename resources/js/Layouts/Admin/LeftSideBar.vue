<template>
    <div class="lg:w-1/6 w-full lg:h-screen bg-white p-5 flex flex-col justify-between">
        <!-- Logo -->
        <div>
            <div class="flex justify-center items-center mb-10">
                <a href="#">
                    <img src="/assets/img/admin/logo.png" alt="Logo" class="w-auto h-20 w-[132px] h-[41px]">
                    <!-- Adjust width and height as needed -->
                </a>
            </div>
            <!-- Navigation Links -->
            <nav class="space-y-4">
                <a :class="[routeName == 'admin.index' ? activeClass : '']"
                    class="flex items-center space-x-2 text-lg p-2 text-[#000000] rounded-lg hover:bg-blue-700 hover:text-white"
                    :href="route('admin.index')">
                    <img src="/assets/img/admin/icon_left_home.png" alt="Trang chủ" class="w-auto h-[19px]">
                    <span>Trang chủ</span>
                </a>

                <a :class="[routeName == 'admin.job.index' ? activeClass : '']" :href="route('admin.job.index')"
                    class="flex items-center space-x-2 text-lg p-2 text-[#000000] rounded-lg hover:bg-blue-700 hover:text-white">
                    <img src="/assets/img/admin/job.png" alt="JOB" class="w-auto h-[19px]">
                    <span>Ngành nghề</span>
                </a>
                <a :href="route('admin.operation.index')"
                    :class="[routeName == 'admin.operation.index' ? activeClass : '']"
                    class="flex items-center space-x-2 text-lg p-2 text-[#000000] rounded-lg hover:bg-blue-700 hover:text-white">
                    <img src="/assets/img/admin/business.png" alt="JOB" class="w-auto h-[19px]">
                    <span>Nghiệp vụ phòng ban</span>
                </a>
                <a :href="route('admin.ai-assistants.index')"
                    :class="(routeName == 'admin.ai-assistants.index' || routeName == 'admin.ai-assistants.run') ? activeClass : ''"
                    class="flex items-center space-x-2 text-lg p-2 text-[#000000] rounded-lg hover:bg-blue-700 hover:text-white">
                    <img src="/assets/img/admin/ai.png" alt="JOB" class="w-auto h-[19px]">
                    <span>Vị trí công việc</span>
                </a>
                <a href="#"
                    class="flex items-center space-x-2 text-lg p-2 text-[#000000] rounded-lg hover:bg-blue-700 hover:text-white">
                    <img src="/assets/img/admin/note.png" alt="JOB" class="w-auto h-[19px]">
                    <span>Thống kê chi tiết</span>
                </a>
                <a v-if="can(['users.index'])" :href="route('admin.users.index')"
                    :class="(routeName.includes('admin.users')) ? activeClass : ''"
                    class="flex items-center space-x-2 text-lg p-2 text-[#000000] rounded-lg hover:bg-blue-700 hover:text-white">
                    <img src="/assets/img/home_action/virtual-mc-icon.png" alt="JOB" class="w-auto h-[19px]">
                    <span>Người dùng</span>
                </a>
                <a v-if="can(['activity-logs.index'])" :href="route('admin.activity-logs.index')"
                    :class="(routeName.includes('admin.activity-logs')) ? activeClass : ''"
                    class="flex items-center space-x-2 text-lg p-2 text-[#000000] rounded-lg hover:bg-blue-700 hover:text-white">
                    <img src="/assets/svgs/aiwow/sidebar_history.svg" alt="Lịch sử người dùng" class="w-auto h-[22px]">
                    <span>Lịch sử người dùng</span>
                </a>
                <a v-if="can(['keys.index'])" :href="route('admin.keys.index')"
                    :class="(routeName.includes('admin.keys')) ? activeClass : ''"
                    class="flex items-center space-x-2 text-lg p-2 text-[#000000] rounded-lg hover:bg-blue-700 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none"
                        stroke="gold" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="8" cy="15" r="5"></circle>
                        <rect x="13" y="12" width="8" height="2" rx="1"></rect>
                        <rect x="16" y="8" width="2" height="4" rx="1"></rect>
                    </svg>
                    <span>Tạo Key</span>
                </a>
                <a v-if="can(['my-ai-image-collections.index'])" :href="route('admin.my-ai-image-collections.index')"
                    :class="(routeName.includes('admin.my-ai-image-collections')) ? activeClass : ''"
                    class="flex items-center space-x-2 text-lg p-2 text-[#000000] rounded-lg hover:bg-blue-700 hover:text-white">
                    <img src="/assets/img/aiwow/image_ai.png" alt="JOB" class="w-auto h-[19px]">
                    <span>Bộ sưu tập</span>
                </a>

                <a v-if="can(['my-ai-image-template-categories.index'])"
                    :href="route('admin.my-ai-image-template-categories.index')"
                    :class="(routeName.includes('admin.my-ai-image-template-categories')) ? activeClass : ''"
                    class="flex items-center space-x-2 text-lg p-2 text-[#000000] rounded-lg hover:bg-blue-700 hover:text-white">
                    <img src="/assets/img/aiwow/image_ai.png" alt="JOB" class="w-auto h-[19px]">
                    <span>Danh mục ảnh</span>
                </a>
            </nav>
        </div>

        <!-- Account Section at the Bottom -->
        <div>
            <a href="#"
                class="flex items-center space-x-2 text-lg p-2 text-[#000000] rounded-lg hover:bg-blue-700 hover:text-white">
                <img src="/assets/img/admin/setting.png" alt="Settings" class="w-auto h-[19px]">
                <span>Tài khoản</span>
            </a>
            <button @click="logout"
                class="flex items-center w-full space-x-2 text-lg p-2 text-[#000000] rounded-lg hover:bg-blue-700 hover:text-white">
                <svg width="24" height="24" viewBox="0 0 90 90" fill="none" xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink">
                    <rect width="90" height="90" fill="url(#pattern0_4577_565)" />
                    <defs>
                        <pattern id="pattern0_4577_565" patternContentUnits="objectBoundingBox" width="1" height="1">
                            <use xlink:href="#image0_4577_565" transform="scale(0.0111111)" />
                        </pattern>
                        <image id="image0_4577_565" width="90" height="90"
                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFoAAABaCAYAAAA4qEECAAAACXBIWXMAAAsTAAALEwEAmpwYAAACyElEQVR4nO2dPWtUQRSGJxZGwT+gFjYBZeeIrb9Js2JwzlpuF8EmhZWV/8FC/AUaixgLqzTqOQiKVVwlpLoyGz9AyILunTln77wPnHIvM88e3vsxs3dDAAAAAAAAAIAhsDE+WKd7eodYdiPLN2LtVqHifKyySxPZHE3fng2eubEllynJG2tptHTJfp5LcNvJg5Csv2S/dtnZJ3FhLUd7rVGS28EblPSVtRjquWKSl8EblGRmLYb6riSz4A1zKVymgjeshRBE28sidLS9SEJ0qIsK3rAWQhBtL4vQ0fYiCdGhLip4w1oIQbS9LEJH24skRIe6qOANayEE0fayCB09jOi4uSXnY5LHPxd4v1DSh3lpD9HB/YrOkv/+XGR5VkR2ux3drZ22ulREdsvRQUm+nvb53mU3LZplZ9ExepXdsuiN8cF6lllFdsuiM3mzDbE8XXi8pM+vTN+dC8vQuuhqsq2FkAPRVWRbCyEnoovLthZCjkQXlW0thJyJLia7qoCkxzHJ5Cp/uLSMCC/z+qdLv5qir7PeLy249rwiywNXAyLW7trd9xeLG648r5j0k6sBEWtXIzKqzyvpR18D4oFGR5JtVwOik2//OMvGyXBAFZq+vGP/onHDwuVF4xacy3c0Hipx+ejAY1Iun9F48M/lT4ZYyuI6Vx0x6aNFx8DiLPcgetqdiUm+V5Gc6fPalVZMNCU9qiI5tB4drE+qSG5d9Gj6+cJcdu7s+fYw2cEmR+5f9B+6tVCalju6KtZCCKLtZRE62l4kITrURQVvWAshiLaXRehoe5GE6FAXFbxhLYQg2l4WoaPtRRKiQ11U8Ia1EGpG9BBfAst6GLwxf227vZiuz4osL4I3KMl4gKJvBZevnmfZH4zkJHsuXz3/+88UBiA7Jtmr+auC/2K+NWoimznfVuoEmWSWx5zjwm0nAwAAAAAAAEAw4gcS+x6dU3vJpwAAAABJRU5ErkJggg==" />
                    </defs>
                </svg>
                <span>Đăng xuất</span>
            </button>
        </div>
    </div>
</template>
<script setup>
import { ref } from 'vue';
import { useAuthorization } from './../../lib/useAuthorization';
const { can } = useAuthorization();

const routeName = route().current()
const activeClass = ref('bg-blue-700 text-white')

const logout = async () => {
    try {
        await axios.post(route('admin.logout'), {}, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        window.location.href = '/login';
    } catch (error) {
        console.error('Đăng xuất thất bại:', error);
    }
}
</script>
