<template>
    <div v-if="modifiedLinks.length > 3">
        <div class="flex flex-wrap justify-center mb-2">
            <template v-for="(link, k) in filteredLinks" :key="k">
                <!-- Nút Previous -->
                <button
                    v-if="link.label === 'pagination.previous'"
                    @click="handlePagination(link)"
                    class="size-[28px] md:size-[36px] bg-white mx-1 border border-[#DEDEDE] rounded hover:border-[#2C75E3] focus:border-[#2C75E3] focus:text-[#2C75E3]"
                    :class="[link.url ? 'text-black' : 'text-[#DEDEDE]']"
                    :disabled="!link.url"
                >
                    &lt; <!-- Mũi tên trái -->
                </button>

                <!-- Nút Next -->
                <button
                    v-if="link.label === 'pagination.next'"
                    @click="handlePagination(link)"
                    class="size-[28px] md:size-[36px] bg-white mx-1 border border-[#DEDEDE] rounded hover:border-[#2C75E3] focus:border-[#2C75E3] focus:text-[#2C75E3]"
                    :class="[link.url ? 'text-black' : 'text-[#DEDEDE]']"
                    :disabled="!link.url"
                >
                    &gt; <!-- Mũi tên phải -->
                </button>

                <!-- Nút số trang -->
                <button
                    v-else-if="link.label !== 'pagination.previous' && link.label !== 'pagination.next'"
                    @click="handlePagination(link)"
                    class="size-[28px] md:size-[36px] mx-1 border rounded  focus:border-[#2C75E3]  focus:text-[#2C75E3]"
                    :class="[link.active ? 'text-white bg-[#2C75E3] hover:text-black hover:bg-white' : 'text-black bg-white hover:text-white hover:bg-[#2C75E3]']"
                    v-html="link.label"
                ></button>

                <!-- Hiển thị dấu "..." -->
                <span v-if="shouldAddEllipsisBefore(k)" class="size-[28px] leading-[28px] md:size-[36px] bg-white mx-1 text-gray-400 text-center">...</span>
                <span v-if="shouldAddEllipsisAfter(k)" class="size-[28px] leading-[28px]  md:size-[36px] bg-white mx-1 text-gray-400 text-center">...</span>
            </template>
        </div>
    </div>
</template>

<script setup>
    import { defineEmits, computed } from 'vue';

    const { links } = defineProps(['links']);
    const emit = defineEmits(['paginate']);

    const activeIndex = computed(() => {
        return links.findIndex(link => link.active);
    });

    const modifiedLinks = computed(() => {
        return links.map(link => {
            if (link.label.includes('Previous')) {
                return { ...link, label: 'pagination.previous' }; // Đổi thành nhãn chính xác
            } else if (link.label.includes('Next')) {
                return { ...link, label: 'pagination.next' }; // Đổi thành nhãn chính xác
            }
            return link;
        });
    });

    const filteredLinks = computed(() => {
        const totalLinks = modifiedLinks.value.length;

        if (totalLinks <= 5) {
            return modifiedLinks.value;
        }

        const result = [];

        if (totalLinks > 0) {
            result.push(modifiedLinks.value[0]);
        }

        if (activeIndex.value >= 0) {
            result.push(modifiedLinks.value[activeIndex.value]);
        }

        const start = Math.max(activeIndex.value + 1, 1);
        const end = Math.min(totalLinks - 1, activeIndex.value + 2);
        for (let i = start; i <= end; i++) {
            result.push(modifiedLinks.value[i]);
        }

        if (totalLinks > 1) {
            result.push(modifiedLinks.value[totalLinks - 1]);
        }

        return result;
    });

    const shouldAddEllipsisBefore = (index) => {
        const totalLinks = modifiedLinks.value.length;

        if (totalLinks > 5 && index === 0 && activeIndex.value > totalLinks - 5) {
            return true;
        }
        return false;
    }

    const shouldAddEllipsisAfter = (index) => {
        const totalLinks = modifiedLinks.value.length;

        if (totalLinks > 5 && index === filteredLinks.value.length - 2 && activeIndex.value < totalLinks - 5) {
            return true;
        }
        return false;
    }

    const handlePagination = (link) => {
        if (link.url) {
            const urlParams = new URLSearchParams(link.url.split('?')[1]);
            const page = urlParams.get('page') || 1;
            emit('paginate', page);
        }
    }
</script>
