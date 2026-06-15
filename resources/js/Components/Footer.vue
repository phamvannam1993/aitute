<template>
    <footer class="bg-veryDarkBlue p-10 text-white w-full">
        <div class="container mx-auto flex flex-col-reverse md:flex-row justify-between space-y-8 md:space-y-0">
            <!-- Logo and social links container -->
            <div class="flex flex-col items-center space-y-8">
                <div class="md:hidden text-center">
                    Copyright © 2022, All Rights Reserved
                </div>
                <!-- Logo -->
                <div>
                    <img
                        class="h-16 md:h-20"
                        src="../../../public/assets/img/logo.png"
                        alt="Logo"
                    />
                </div>
                <!-- Social Links Container -->
                <div class="flex space-x-4">
                    <!-- Link 1 -->
                    <a href="#" aria-label="Facebook">
                        <img
                            class="w-8"
                            src="../../../public/assets/img/icon-facebook.svg"
                            alt="Facebook"
                        />
                    </a>
                    <!-- Link 2 -->
                    <a href="#" aria-label="YouTube">
                        <img
                            class="w-8"
                            src="../../../public/assets/img/icon-youtube.svg"
                            alt="YouTube"
                        />
                    </a>
                    <!-- Link 3 -->
                    <a href="#" aria-label="Twitter">
                        <img
                            class="w-8"
                            src="../../../public/assets/img/icon-twitter.svg"
                            alt="Twitter"
                        />
                    </a>
                    <!-- Link 4 -->
                    <a href="#" aria-label="Pinterest">
                        <img
                            class="w-8"
                            src="../../../public/assets/img/icon-pinterest.svg"
                            alt="Pinterest"
                        />
                    </a>
                    <!-- Link 5 -->
                    <a href="#" aria-label="Instagram">
                        <img
                            class="w-8"
                            src="../../../public/assets/img/icon-instagram.svg"
                            alt="Instagram"
                        />
                    </a>
                </div>
            </div>

            <!-- List Container -->
            <div class="flex flex-col md:flex-row space-y-8 md:space-y-0 md:space-x-10">
                <ul class="space-y-4">
                    <li class="hover:text-brightRed text-lg">
                        <a href="#">Home</a>
                    </li>
                    <li class="hover:text-brightRed text-lg">
                        <a href="#">Pricing</a>
                    </li>
                    <li class="hover:text-brightRed text-lg">
                        <a href="#">Products</a>
                    </li>
                    <li class="hover:text-brightRed text-lg">
                        <a href="#">About</a>
                    </li>
                </ul>
                <ul class="space-y-4">
                    <li class="hover:text-brightRed text-lg">
                        <a href="#">Careers</a>
                    </li>
                    <li class="hover:text-brightRed text-lg">
                        <a href="#">Community</a>
                    </li>
                    <li class="hover:text-brightRed text-lg">
                        <a href="#">Privacy Policy</a>
                    </li>
                </ul>
            </div>

            <!-- Input Container -->
            <div class="flex flex-col items-center md:items-start">
                <form class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-2">
                    <input
                        type="text"
                        class="rounded-full p-4 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-brightRed"
                        placeholder="Updated in your inbox"
                    />
                    <MainButton
                        class="bg-brightRed text-white rounded-full py-2 px-6"
                    >
                        Go
                    </MainButton>
                </form>
                <div class="hidden md:block mt-5 text-center">
                    Copyright © 2022, All Rights Reserved
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
</script>
