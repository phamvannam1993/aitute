<template>
    <HamburgerButton @click="showDropDown = !showDropDown" />
    <NavList
        class="md:hidden p-2 m-0 flex flex-col items-center absolute right-0 top-full w-full bg-white shadow-sm shadow-gray-600 rounded transition-transform duration-500 scale-0 origin-top-right"
        :class="{ 'scale-100': showDropDown }"
    />
</template>
<script setup>
import { ref } from "vue";

import NavList from "./NavList.vue";
import HamburgerButton from "./HamburgerButton.vue";

const showDropDown = ref(false);
</script>
