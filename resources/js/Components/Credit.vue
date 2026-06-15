<template>
    <div class="flex items-center gap-4">
        <!-- Gói trải nghiệm -->

        <!-- Credit -->
        <div class="flex items-center bg-[#000000] bg-opacity-5 gap-3 rounded-3xl pr-1 md:pr-2 lg:pr-3 p-2">
            <div class="rounded-full">
                <img src="/assets/img/aiwow/credit_icon.png" alt="Credit"
                    class="size-[24px] md:size-[28px] lg:size-[36px]">
            </div>
            <div class="py-1 text-center">
                <span class="text-[#092A99] font-bold text-[16px] lg:text-[24px]">
                    {{ userCredit || '0' }}
                </span>
                <div class="items-center text-center bg-white rounded-full">
                    <span class="text-[#2C75E3] font-medium text-[14px] md:text-[15px]">
                        {{ packageName || '' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, watch, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.auth.user)
const props = defineProps({
    credits: Number
});

const userCredit = ref(user.value?.credit || props.credits || 0)
const packageName = ref(user.value?.package_name)
// Watch for changes in the credits prop to update userCredit
watch(() => props.credits, (newCredits) => {
    userCredit.value = newCredits
})

// Log the user credit whenever it changes
watch(() => userCredit.value, (newValue) => {
    console.log('User credit updated:', newValue)
})
</script>
