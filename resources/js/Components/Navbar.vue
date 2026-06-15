<template>
    <nav class="flex justify-between px-4 md:px-14 items-center relative">
        <div class="logo">
            <img class="w-40" src="../../../public/assets/img/logo.png" alt="Sunnyside Logo" />
        </div>

        <button @click="toggleMenu">
            <img
                src="../assets/icon-hamburger.svg"
                alt="Hamburger Icon"
                class="md:hidden"
            />
        </button>

        <div
            class="absolute top-20 left-0 w-full md:w-auto px-4 md:px-0 md:static md:block md:visible md:opacity-100 duration-200 ease-in-out"
            :class="[isActive ? activeClass : 'invisible opacity-0']"
        >
            <ul
                class="menu flex flex-col md:flex-row justify-around items-center gap-x-12 gap-y-4 md:gap-y-0 text-gray-500 md:text-white text-base bg-white md:bg-transparent pt-8 pb-12 md:py-0"
            >
                <li><a href="#">About</a></li>
                <li><a href="#">Services</a></li>
                <li><a href="#">Projects</a></li>

                <li class="mt-4 md:mt-0">
                    <a
                        href="#"
                        class="contact-btn py-4 px-8 bg-yellow-300 md:bg-white rounded-full uppercase text-black hover:text-white font-bold"
                    >
                        Contact
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script>
import { ref } from "vue";

export default {
    setup() {
        const isActive = ref(false);
        const activeClass = "visible opacity-100";

        const toggleMenu = () => {
            isActive.value = !isActive.value;
        };

        return {
            isActive,
            activeClass,
            toggleMenu,
        };
    },
};
</script>

<style lang="scss" scoped>
nav {
    .menu {
        @media (max-width: 768px) {
            &::before {
                content: "";
                width: 0;
                height: 0;
                position: absolute;
                top: -2rem;
                right: 1rem;
                border-bottom: 1rem solid white;
                border-right: 1rem solid white;
                border-left: 1rem solid transparent;
                border-top: 1rem solid transparent;
            }
        }
    }
}

.contact-btn {
    font-family: "Fraunces", serif;

    &:hover {
        background-color: lightskyblue;
    }
}
</style>
