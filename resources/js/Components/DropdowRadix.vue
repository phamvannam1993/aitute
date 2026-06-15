<script setup>
import {
    DropdownMenuArrow,
    DropdownMenuContent,
    DropdownMenuPortal,
    DropdownMenuRoot,
    DropdownMenuTrigger
} from 'radix-vue'

const props = defineProps({
    contentClass: {
        type: String,
        default: ''
    },
    arrowClass: {
        type: String,
        default: ''
    },
    open: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    }
})
const emit = defineEmits(['update:open'])
</script>

<template>
    <DropdownMenuRoot
        :class="{ 'cursor-not-allowed': disabled }"
        :open="open" @update:open="emit('update:open', $event)">
        <DropdownMenuTrigger>
            <slot name="trigger"></slot>
        </DropdownMenuTrigger>

        <DropdownMenuPortal>
            <DropdownMenuContent
                :class="[contentClass]"
                :side-offset="5">
                <slot name="content"></slot>
                <DropdownMenuArrow :class="['fill-white', arrowClass]" />
            </DropdownMenuContent>
        </DropdownMenuPortal>
    </DropdownMenuRoot>
</template>
