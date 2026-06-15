<script setup>
import { Link } from "@inertiajs/vue3";
import Header from "../../Components/Header.vue";
import Footer from "../../Components/Footer.vue";
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-between items-center pt-6 sm:pt-0 bg-gray-100">
        <Header></Header>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <slot />
        </div>
        <Footer></Footer>
    </div>
</template>

<style scoped>
.multicolor-text {
    background: linear-gradient(to left, violet, indigo, blue, green, orange, red);
    -webkit-background-clip: text;
    color: transparent;
}
</style>
