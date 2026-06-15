<template>
    <div class="relative inline-block" ref="dropdownRef">
        <button @click="toggleDropdown" class="flex  gap-1 md:gap-2 items-center border-2 text-ai3goc-primary border-ai3goc-primary   md:px-3 rounded-xl w-full justify-center max-w-[100px] md:max-w-[200px] md:min-w-[165px] md:h-[36px]">

            <div class="w-6 h-6 rounded-full bg-white flex items-center justify-center">
                <Image class="text-ai3goc-primary" />
            </div>
            <span class="text-[10px] lg:text-sm">{{ selectedImage.value }}</span>
            <svg :class="isDropdownOpen ? 'rotate-0' : 'rotate-180'" width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 1L5 5L9 1" stroke="#C5C5C5" />
            </svg>
        </button>
        <div v-if="isDropdownOpen" class="absolute mt-2 bg-white border-2 border-[#D4D4D4] rounded-[20px] shadow-lg w-full z-10">
            <div v-for="image in listImageType" :key="image.key" class="px-4 py-2 hover:bg-gray-100 rounded-[20px] cursor-pointer text-black" @click="selectImage(image)">
                {{ image.value }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { Image } from 'lucide-vue-next';
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
const props = defineProps({
    defaultSelectedImage: Object,
});
const listImageType = [
    {
        key:"",
        value:"Kho ảnh"
    },
    {
        key:"my_ai_image",
        value:"Ảnh tự sướng"
    },
    {
        key:"swap face",
        value:"Đổi khuôn mặt"
    },
];
const emit = defineEmits(['selectImage']);
const isDropdownOpen = ref(false);
const selectedImage = ref(listImageType[0]);
const dropdownRef = ref(null);
const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};
const selectImage = (image) => {
    selectedImage.value = image;
    emit('selectImage', image);
    isDropdownOpen.value = false;
};
const closeDropdown = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isDropdownOpen.value = false;
    }
};
watch(() => props.defaultSelectedImage, () => {
    selectedImage.value = listImageType.find(image => image.key === props.defaultSelectedImage.key) || listImageType[0];
});
onMounted(() => {
    document.addEventListener('click', closeDropdown);
});
onBeforeUnmount(() => {
    document.removeEventListener('click', closeDropdown);
});
</script>
