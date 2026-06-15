<script setup>
import { computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    minWidth: {
        type: String,
        default: 'full',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
    customClass: {
        type: String,
        default: '',
    },
    alignItems: {
        type: String,
        default: '!items-start sm:!items-center',
    },
});

const emit = defineEmits(['close']);

watch(
    () => props.show,
    () => {
        if (props.show) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = null;
        }
    },
);

const close = () => {
    if (props.closeable) {
        emit('close');
    }
};

const closeOnEscape = (e) => {
    if (e.key === 'Escape' && props.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    document.body.style.overflow = null;
});

const maxWidthClass = computed(() => {
    return {
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
        '3xl': 'sm:max-w-3xl',
        '4xl': 'sm:max-w-4xl',
        '5xl': 'sm:max-w-5xl',
        'xl-4xl': 'md:max-w-[90%] xl:max-w-4xl',
        'xl-6xl': 'md:max-w-[90%] xl:max-w-6xl',
        '90%': 'max-w-[90%]',
    }[props.maxWidth];
});

const minWidthClass = computed(() => {
    return {
        'w-full': 'w-full',
    }[props.minWidth];
});
</script>

<template>
    <Teleport to="body">
        <Transition leave-active-class="duration-200">
            <div v-show="show" :class="[alignItems]"
                class="flex fixed inset-0 z-[103] overflow-y-auto px-1 py-2 sm:px-0" scroll-region>
                <Transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0"
                    enter-to-class="opacity-100" leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100" leave-to-class="opacity-0">
                    <div v-show="show" class="fixed inset-0 transform transition-all" @click="close">
                        <div class="absolute inset-0 bg-gray-500 opacity-75" />
                    </div>
                </Transition>

                <Transition enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100" leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <div v-show="show"
                        class="transform rounded-[25px] bg-white transition-all mx-auto sm:w-full my-auto"
                        :class="[maxWidthClass, customClass, minWidthClass]">
                        <svg class="absolute top-2 z-10 w-8 right-5 cursor-pointer transform transition-all" v-if="show" @click="close" width="29" height="29" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="29" height="29" rx="14.5" fill="#F49A23"/>
                        <path d="M19.7294 9.26978L9.26953 19.7297" stroke="white" stroke-width="3" stroke-linecap="square" stroke-linejoin="round"/>
                        <path d="M9.26953 9.26978L19.7294 19.7297" stroke="white" stroke-width="3" stroke-linecap="square" stroke-linejoin="round"/>
                        </svg>

<!--                        <img v-if="show" src="/assets/img/close2.png" @click="close" alt="closebtn"-->
<!--                            class="absolute top-2 z-10 w-8 right-5 cursor-pointer transform transition-all" />-->
                        <slot v-if="show" />
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
