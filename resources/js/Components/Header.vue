<template>
    <header class="w-full p-2">
        <nav class="flex justify-between items-center relative">
            <div>
                <img src="../../../public/assets/img/logo.png" alt="logo" />
            </div>
            <NavList
                class="hidden md:flex space-x-4 justify-between items-center"
            />
            <MainButton
                class="hidden self-baseline md:block bg-brightRed text-white"
            >
                Login
            </MainButton>
            <DropDownNav />
        </nav>
    </header>
</template>
<script setup>
import DropDownNav from "./DropDownNav.vue";
import NavList from "./NavList.vue";
import MainButton from "./MainButton.vue";
</script>
